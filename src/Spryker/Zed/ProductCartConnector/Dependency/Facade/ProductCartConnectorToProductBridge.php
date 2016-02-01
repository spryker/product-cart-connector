<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\ProductCartConnector\Dependency\Facade;

use Spryker\Zed\Product\Business\ProductFacade;
use Generated\Shared\Transfer\ProductConcreteTransfer;

class ProductCartConnectorToProductBridge implements ProductCartConnectorToProductInterface
{

    /**
     * @var ProductFacade
     */
    protected $productFacade;

    /**
     * @param \Spryker\Zed\Product\Business\ProductFacade $productFacade
     */
    public function __construct($productFacade)
    {
        $this->productFacade = $productFacade;
    }

    /**
     * @param string $concreteSku
     *
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    public function getProductConcrete($concreteSku)
    {
        return $this->productFacade->getProductConcrete($concreteSku);
    }

}
