<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Persistence;

use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use Spryker\Zed\ProductCartConnector\Persistence\Propel\Mapper\ProductAbstractMapper;

/**
 * @method \Spryker\Zed\ProductCartConnector\ProductCartConnectorConfig getConfig()
 * @method \Spryker\Zed\ProductCartConnector\Persistence\ProductCartConnectorRepositoryInterface getRepository()
 */
class ProductCartConnectorPersistenceFactory extends AbstractPersistenceFactory
{
    public function getProductAbstractPropelQuery(): SpyProductAbstractQuery
    {
        return SpyProductAbstractQuery::create();
    }

    public function createProductAbstractMapper(): ProductAbstractMapper
    {
        return new ProductAbstractMapper();
    }

    public function getProductAttributeKeyPropelQuery(): SpyProductAttributeKeyQuery
    {
        return SpyProductAttributeKeyQuery::create();
    }
}
