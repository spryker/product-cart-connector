<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Business\Expander;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Spryker\Zed\ProductCartConnector\Business\Reader\ProductAbstractReaderInterface;
use Spryker\Zed\ProductCartConnector\Dependency\Facade\ProductCartConnectorToLocaleInterface;
use Spryker\Zed\ProductCartConnector\Dependency\Facade\ProductCartConnectorToProductInterface;

class ProductExpander implements ProductExpanderInterface
{
    /**
     * @var \Spryker\Zed\ProductCartConnector\Dependency\Facade\ProductCartConnectorToLocaleInterface
     */
    protected $localeFacade;

    /**
     * @var \Spryker\Zed\ProductCartConnector\Dependency\Facade\ProductCartConnectorToProductInterface
     */
    protected $productFacade;

    public function __construct(
        ProductCartConnectorToLocaleInterface $localeFacade,
        ProductCartConnectorToProductInterface $productFacade,
        protected ProductAbstractReaderInterface $productAbstractReader,
        protected ItemAbstractNameExpanderInterface $itemAbstractNameExpander,
        protected ItemBrandExpanderInterface $itemBrandExpander,
        protected ItemSelectedVariantAttributesExpanderInterface $itemSelectedVariantAttributesExpander
    ) {
        $this->localeFacade = $localeFacade;
        $this->productFacade = $productFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function expandItems(CartChangeTransfer $cartChangeTransfer)
    {
        $productConcreteTransfers = $this->productFacade->getRawProductConcreteTransfersByConcreteSkus($this->getConcreteSkus($cartChangeTransfer));
        $productConcreteTransfers = $this->indexProductConcretesBySku($productConcreteTransfers);
        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            $productConcreteTransfer = $productConcreteTransfers[$itemTransfer->getSku()] ?? null;
            if (!$productConcreteTransfer) {
                continue;
            }

            $this->assertProductConcreteTransfer($productConcreteTransfer);

            $this->expandItemWithProductConcrete($productConcreteTransfer, $itemTransfer);
        }

        return $this->expandItemsWithProductDetails(
            $cartChangeTransfer,
            $this->getCurrentLocaleAttributesIndexedByIdProductConcrete($productConcreteTransfers),
        );
    }

    /**
     * Runs after the concrete product data is set: it needs the abstract product ID, the concrete product ID and the concrete attributes of the items.
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     * @param array<int, array<string, mixed>> $productConcreteLocalizedAttributes
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    protected function expandItemsWithProductDetails(
        CartChangeTransfer $cartChangeTransfer,
        array $productConcreteLocalizedAttributes
    ): CartChangeTransfer {
        $productAbstractTransfers = $this->productAbstractReader->getProductAbstractsWithCurrentLocaleAttributesIndexedByIdProductAbstract($cartChangeTransfer);

        $this->itemAbstractNameExpander->expandItems($cartChangeTransfer, $productAbstractTransfers);
        $this->itemBrandExpander->expandItems($cartChangeTransfer, $productAbstractTransfers, $productConcreteLocalizedAttributes);

        return $this->itemSelectedVariantAttributesExpander->expandItems($cartChangeTransfer, $productConcreteLocalizedAttributes);
    }

    /**
     * The concrete products are read with their localized attributes in every locale, so the current locale's are taken from them
     * instead of being read again. A product without localized attributes in the current locale is left out.
     *
     * @param array<string, \Generated\Shared\Transfer\ProductConcreteTransfer> $productConcreteTransfers
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getCurrentLocaleAttributesIndexedByIdProductConcrete(array $productConcreteTransfers): array
    {
        $idCurrentLocale = $this->localeFacade->getCurrentLocale()->getIdLocale();
        $currentLocaleAttributes = [];

        foreach ($productConcreteTransfers as $productConcreteTransfer) {
            foreach ($productConcreteTransfer->getLocalizedAttributes() as $localizedAttributesTransfer) {
                $isCurrentLocale = $localizedAttributesTransfer->getLocale()?->getIdLocale() === $idCurrentLocale;

                if (!$isCurrentLocale) {
                    continue;
                }

                $currentLocaleAttributes[$productConcreteTransfer->getIdProductConcreteOrFail()] = $localizedAttributesTransfer->getAttributes();
            }
        }

        return $currentLocaleAttributes;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductConcreteTransfer> $productConcreteTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductConcreteTransfer>
     */
    protected function indexProductConcretesBySku(array $productConcreteTransfers): array
    {
        $indexedProductConcreteTransfers = [];
        foreach ($productConcreteTransfers as $productConcreteTransfer) {
            $indexedProductConcreteTransfers[$productConcreteTransfer->getSku()] = $productConcreteTransfer;
        }

        return $indexedProductConcreteTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     *
     * @return void
     */
    protected function assertProductConcreteTransfer(ProductConcreteTransfer $productConcreteTransfer)
    {
        $productConcreteTransfer
            ->requireSku()
            ->requireAbstractSku()
            ->requireFkProductAbstract();
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return void
     */
    protected function expandItemWithProductConcrete(ProductConcreteTransfer $productConcreteTransfer, ItemTransfer $itemTransfer)
    {
        $localizedProductName = $this->productFacade->getLocalizedProductConcreteName(
            $productConcreteTransfer,
            $this->localeFacade->getCurrentLocale(),
        );

        $itemTransfer->setId($productConcreteTransfer->getIdProductConcrete())
            ->setSku($productConcreteTransfer->getSku())
            ->setIdProductAbstract($productConcreteTransfer->getFkProductAbstract())
            ->setAbstractSku($productConcreteTransfer->getAbstractSku())
            ->setConcreteAttributes($productConcreteTransfer->getAttributes())
            ->setName($localizedProductName);
    }

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return array<string>
     */
    protected function getConcreteSkus(CartChangeTransfer $cartChangeTransfer): array
    {
        $productConcreteSkus = array_map(
            function (ItemTransfer $itemTransfer) {
                return $itemTransfer->getSku();
            },
            $cartChangeTransfer->getItems()->getArrayCopy(),
        );

        return $productConcreteSkus;
    }
}
