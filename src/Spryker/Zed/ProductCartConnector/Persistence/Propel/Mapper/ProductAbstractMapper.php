<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\LocalizedAttributesTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Orm\Zed\Product\Persistence\Map\SpyProductAbstractLocalizedAttributesTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductAbstractTableMap;

class ProductAbstractMapper
{
    /**
     * @param array<array<string, mixed>> $productAbstractRows
     *
     * @return array<int, \Generated\Shared\Transfer\ProductAbstractTransfer>
     */
    public function mapProductAbstractRowsToProductAbstractTransfersIndexedByIdProductAbstract(array $productAbstractRows): array
    {
        $productAbstractTransfers = [];

        foreach ($productAbstractRows as $productAbstractRow) {
            $productAbstractTransfer = $this->mapProductAbstractRowToProductAbstractTransfer($productAbstractRow, new ProductAbstractTransfer());
            $productAbstractTransfers[$productAbstractTransfer->getIdProductAbstractOrFail()] = $productAbstractTransfer;
        }

        return $productAbstractTransfers;
    }

    /**
     * @param array<string, mixed> $productAbstractRow
     */
    protected function mapProductAbstractRowToProductAbstractTransfer(
        array $productAbstractRow,
        ProductAbstractTransfer $productAbstractTransfer
    ): ProductAbstractTransfer {
        $productAbstractTransfer
            ->setIdProductAbstract((int)$productAbstractRow[SpyProductAbstractTableMap::COL_ID_PRODUCT_ABSTRACT])
            ->setAttributes($this->decodeAttributes($productAbstractRow[SpyProductAbstractTableMap::COL_ATTRIBUTES]));

        $localizedName = $productAbstractRow[SpyProductAbstractLocalizedAttributesTableMap::COL_NAME];

        if ($localizedName === null) {
            return $productAbstractTransfer;
        }

        $localizedAttributesTransfer = (new LocalizedAttributesTransfer())
            ->setName($localizedName)
            ->setAttributes($this->decodeAttributes($productAbstractRow[SpyProductAbstractLocalizedAttributesTableMap::COL_ATTRIBUTES]));

        return $productAbstractTransfer->addLocalizedAttributes($localizedAttributesTransfer);
    }

    /**
     * @return array<string, mixed>
     */
    protected function decodeAttributes(?string $attributesJson): array
    {
        if (!$attributesJson) {
            return [];
        }

        return json_decode($attributesJson, true) ?: [];
    }
}
