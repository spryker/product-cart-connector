<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Business\Expander;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Spryker\Zed\ProductCartConnector\ProductCartConnectorConfig;

class ItemBrandExpander implements ItemBrandExpanderInterface
{
    public function __construct(protected ProductCartConnectorConfig $productCartConnectorConfig)
    {
    }

    /**
     * @param array<int, \Generated\Shared\Transfer\ProductAbstractTransfer> $productAbstractTransfers
     * @param array<int, array<string, mixed>> $productConcreteLocalizedAttributes
     */
    public function expandItems(
        CartChangeTransfer $cartChangeTransfer,
        array $productAbstractTransfers,
        array $productConcreteLocalizedAttributes
    ): CartChangeTransfer {
        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            $attributes = $this->mergeAttributes(
                $itemTransfer,
                $productAbstractTransfers[$itemTransfer->getIdProductAbstract()] ?? null,
                $productConcreteLocalizedAttributes[$itemTransfer->getId()] ?? [],
            );
            $itemTransfer->setBrand($this->findBrand($attributes));
        }

        return $cartChangeTransfer;
    }

    /**
     * The order of the product storage, so the cart reports the brand the storefront shows:
     * abstract < abstract localized < concrete < concrete localized, the localized ones in the current locale.
     *
     * @see \Spryker\Zed\Product\Business\Attribute\AttributeMerger::merge()
     *
     * @param array<string, mixed> $productConcreteLocalizedAttributes
     *
     * @return array<string, mixed>
     */
    protected function mergeAttributes(
        ItemTransfer $itemTransfer,
        ?ProductAbstractTransfer $productAbstractTransfer,
        array $productConcreteLocalizedAttributes
    ): array {
        $productAbstractLocalizedAttributes = [];
        $productAbstractAttributes = [];

        if ($productAbstractTransfer !== null) {
            $productAbstractAttributes = $productAbstractTransfer->getAttributes();
            $productAbstractLocalizedAttributes = $this->getProductAbstractLocalizedAttributes($productAbstractTransfer);
        }

        return array_replace(
            $productAbstractAttributes,
            $productAbstractLocalizedAttributes,
            $itemTransfer->getConcreteAttributes(),
            $productConcreteLocalizedAttributes,
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function getProductAbstractLocalizedAttributes(ProductAbstractTransfer $productAbstractTransfer): array
    {
        $productAbstractLocalizedAttributes = [];

        // The reader loads the localized attributes of the current locale only.
        foreach ($productAbstractTransfer->getLocalizedAttributes() as $localizedAttributesTransfer) {
            $productAbstractLocalizedAttributes = array_replace($productAbstractLocalizedAttributes, $localizedAttributesTransfer->getAttributes());
        }

        return $productAbstractLocalizedAttributes;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    protected function findBrand(array $attributes): ?string
    {
        $brand = $attributes[$this->productCartConnectorConfig->getBrandAttributeKey()] ?? null;
        // Scalar, as the product storage client reads it, so a numeric brand is reported in the cart as on the storefront.
        $hasBrand = is_scalar($brand) && (string)$brand !== '';

        if (!$hasBrand) {
            return null;
        }

        return (string)$brand;
    }
}
