<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Persistence;

use Orm\Zed\Product\Persistence\Map\SpyProductAbstractLocalizedAttributesTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductAbstractTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductAttributeKeyTableMap;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\ProductCartConnector\Persistence\ProductCartConnectorPersistenceFactory getFactory()
 */
class ProductCartConnectorRepository extends AbstractRepository implements ProductCartConnectorRepositoryInterface
{
    /**
     * @module Product
     *
     * @param list<int> $productAbstractIds
     *
     * @return array<int, \Generated\Shared\Transfer\ProductAbstractTransfer>
     */
    public function getProductAbstractsWithLocalizedAttributesIndexedByIdProductAbstract(array $productAbstractIds, int $idLocale): array
    {
        if (!$productAbstractIds) {
            return [];
        }

        // A left join, so an abstract product without localized attributes in the locale still brings its own attributes.
        $productAbstractRows = $this->getFactory()
            ->getProductAbstractPropelQuery()
            ->filterByIdProductAbstract_In($productAbstractIds)
            ->leftJoinSpyProductAbstractLocalizedAttributes()
            ->addJoinCondition('SpyProductAbstractLocalizedAttributes', 'SpyProductAbstractLocalizedAttributes.fk_locale = ?', $idLocale)
            ->select([
                SpyProductAbstractTableMap::COL_ID_PRODUCT_ABSTRACT,
                SpyProductAbstractTableMap::COL_ATTRIBUTES,
                SpyProductAbstractLocalizedAttributesTableMap::COL_NAME,
                SpyProductAbstractLocalizedAttributesTableMap::COL_ATTRIBUTES,
            ])
            ->find()
            ->getData();

        return $this->getFactory()
            ->createProductAbstractMapper()
            ->mapProductAbstractRowsToProductAbstractTransfersIndexedByIdProductAbstract($productAbstractRows);
    }

    /**
     * @module Product
     *
     * @param list<string> $attributeKeys
     *
     * @return list<string>
     */
    public function getSuperAttributeKeys(array $attributeKeys): array
    {
        if (!$attributeKeys) {
            return [];
        }

        $superAttributeKeyRows = $this->getFactory()
            ->getProductAttributeKeyPropelQuery()
            ->filterByKey_In($attributeKeys)
            ->filterByIsSuper(true)
            ->select([SpyProductAttributeKeyTableMap::COL_KEY])
            ->find()
            ->getData();
        $superAttributeKeys = [];

        foreach ($superAttributeKeyRows as $superAttributeKey) {
            $superAttributeKeys[] = (string)$superAttributeKey;
        }

        return $superAttributeKeys;
    }
}
