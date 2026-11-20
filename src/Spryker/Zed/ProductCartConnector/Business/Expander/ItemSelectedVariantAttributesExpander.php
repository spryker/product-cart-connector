<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Business\Expander;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Spryker\Zed\ProductCartConnector\Business\Resolver\ItemSelectedVariantAttributesResolverInterface;
use Spryker\Zed\ProductCartConnector\Persistence\ProductCartConnectorRepositoryInterface;

class ItemSelectedVariantAttributesExpander implements ItemSelectedVariantAttributesExpanderInterface
{
    public function __construct(
        protected ProductCartConnectorRepositoryInterface $productCartConnectorRepository,
        protected ItemSelectedVariantAttributesResolverInterface $itemSelectedVariantAttributesResolver
    ) {
    }

    /**
     * @param array<int, array<string, mixed>> $productConcreteLocalizedAttributes
     */
    public function expandItems(CartChangeTransfer $cartChangeTransfer, array $productConcreteLocalizedAttributes): CartChangeTransfer
    {
        $concreteAttributesIndexedByItem = [];

        foreach ($cartChangeTransfer->getItems() as $itemIndex => $itemTransfer) {
            $concreteAttributesIndexedByItem[$itemIndex] = $this->getConcreteAttributes($itemTransfer, $productConcreteLocalizedAttributes);
        }

        $superAttributeKeys = $this->getSuperAttributeKeys($concreteAttributesIndexedByItem);

        foreach ($cartChangeTransfer->getItems() as $itemIndex => $itemTransfer) {
            $itemTransfer->setSelectedVariantAttributes(
                $this->itemSelectedVariantAttributesResolver->resolveSelectedVariantAttributes($concreteAttributesIndexedByItem[$itemIndex], $superAttributeKeys),
            );
        }

        return $cartChangeTransfer;
    }

    /**
     * The localized values of the current locale win, as in the product storage, so the cart reports the variant the storefront shows.
     *
     * @see \Spryker\Zed\Product\Business\Attribute\AttributeMerger::merge()
     *
     * @param array<int, array<string, mixed>> $productConcreteLocalizedAttributes
     *
     * @return array<string, mixed>
     */
    protected function getConcreteAttributes(ItemTransfer $itemTransfer, array $productConcreteLocalizedAttributes): array
    {
        return array_replace(
            $itemTransfer->getConcreteAttributes(),
            $productConcreteLocalizedAttributes[$itemTransfer->getId()] ?? [],
        );
    }

    /**
     * @param array<int, array<string, mixed>> $concreteAttributesIndexedByItem
     *
     * @return array<string, string>
     */
    protected function getSuperAttributeKeys(array $concreteAttributesIndexedByItem): array
    {
        $attributeKeys = [];

        foreach ($concreteAttributesIndexedByItem as $concreteAttributes) {
            foreach (array_keys($concreteAttributes) as $attributeKey) {
                $attributeKeys[$attributeKey] = (string)$attributeKey;
            }
        }

        if (!$attributeKeys) {
            return [];
        }

        $superAttributeKeys = [];

        foreach ($this->productCartConnectorRepository->getSuperAttributeKeys(array_values($attributeKeys)) as $superAttributeKey) {
            $superAttributeKeys[$superAttributeKey] = $superAttributeKey;
        }

        return $superAttributeKeys;
    }
}
