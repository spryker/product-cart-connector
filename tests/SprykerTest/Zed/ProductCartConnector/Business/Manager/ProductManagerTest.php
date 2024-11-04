<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductCartConnector\Business\Manager;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Spryker\Zed\ProductCartConnector\Business\Expander\ProductExpander;
use Spryker\Zed\ProductCartConnector\Dependency\Facade\ProductCartConnectorToLocaleInterface;
use Spryker\Zed\ProductCartConnector\Dependency\Facade\ProductCartConnectorToProductInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductCartConnector
 * @group Business
 * @group Manager
 * @group ProductManagerTest
 * Add your own group annotations below this line
 */
class ProductManagerTest extends Unit
{
    /**
     * @var string
     */
    public const CONCRETE_SKU = 'concrete sku';

    /**
     * @var string
     */
    public const ABSTRACT_SKU = 'abstract sku';

    /**
     * @var string
     */
    public const ID_PRODUCT_CONCRETE = 'id product concrete';

    /**
     * @var string
     */
    public const ID_PRODUCT_ABSTRACT = 'id product abstract';

    /**
     * @var string
     */
    public const PRODUCT_NAME = 'product name';

    /**
     * @return void
     */
    public function testExpandItemsMustAddProductIdToAllCartItems(): void
    {
        $changeTransfer = $this->getChangeTransfer();

        $productConcreteTransfer = new ProductConcreteTransfer();
        $productConcreteTransfer->setSku(static::CONCRETE_SKU);
        $productConcreteTransfer->setAbstractSku(static::ABSTRACT_SKU);
        $productConcreteTransfer->setFkProductAbstract(1);

        $productManager = $this->getProductManager($productConcreteTransfer, static::PRODUCT_NAME);
        $result = $productManager->expandItems($changeTransfer);

        $changedItemTransfer = $result->getItems()[0];
        $this->assertSame($productConcreteTransfer->getIdProductConcrete(), $changedItemTransfer->getId());
    }

    /**
     * @return void
     */
    public function testExpandItemsMustAddAbstractSkuToAllCartItems(): void
    {
        $changeTransfer = $this->getChangeTransfer();

        $productConcreteTransfer = new ProductConcreteTransfer();
        $productConcreteTransfer->setSku(static::CONCRETE_SKU);
        $productConcreteTransfer->setAbstractSku(static::ABSTRACT_SKU);

        $productConcreteTransfer->setFkProductAbstract(1);

        $productManager = $this->getProductManager($productConcreteTransfer, static::PRODUCT_NAME);
        $result = $productManager->expandItems($changeTransfer);

        $changedItemTransfer = $result->getItems()[0];
        $this->assertSame($productConcreteTransfer->getAbstractSku(), $changedItemTransfer->getAbstractSku());
    }

    /**
     * @return void
     */
    public function testExpandItemsMustAddAbstractIdToAllCartItems(): void
    {
        $changeTransfer = $this->getChangeTransfer();

        $productConcreteTransfer = new ProductConcreteTransfer();
        $productConcreteTransfer->setSku(static::CONCRETE_SKU);
        $productConcreteTransfer->setIdProductConcrete(static::ID_PRODUCT_ABSTRACT);
        $productConcreteTransfer->setFkProductAbstract(1);
        $productConcreteTransfer->setAbstractSku(static::ABSTRACT_SKU);

        $productManager = $this->getProductManager($productConcreteTransfer, static::PRODUCT_NAME);
        $result = $productManager->expandItems($changeTransfer);

        $changedItemTransfer = $result->getItems()[0];
        $this->assertSame($productConcreteTransfer->getFkProductAbstract(), $changedItemTransfer->getIdProductAbstract());
    }

    /**
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    private function getChangeTransfer(): CartChangeTransfer
    {
        $changeTransfer = new CartChangeTransfer();
        $itemTransfer = new ItemTransfer();
        $itemTransfer->setSku(static::CONCRETE_SKU);
        $changeTransfer->addItem($itemTransfer);

        return $changeTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $returnValue
     * @param string $localizedName
     *
     * @return \Spryker\Zed\ProductCartConnector\Business\Expander\ProductExpander
     */
    public function getProductManager(ProductConcreteTransfer $returnValue, string $localizedName): ProductExpander
    {
        $mockProductFacade = $this->getMockProductFacade();

        $mockProductFacade->expects($this->once())
            ->method('getRawProductConcreteTransfersByConcreteSkus')
            ->will($this->returnValue([$returnValue]));

        $mockProductFacade->expects($this->once())
            ->method('getLocalizedProductConcreteName')
            ->will($this->returnValue($localizedName));

        $mockLocaleFacade = $this->getMockLocaleFacade();
        $mockLocaleFacade->expects($this->once())
            ->method('getCurrentLocale')
            ->will($this->returnValue(new LocaleTransfer()));

        return new ProductExpander($mockLocaleFacade, $mockProductFacade);
    }

    /**
     * @return \Spryker\Zed\ProductCartConnector\Dependency\Facade\ProductCartConnectorToProductInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockProductFacade(): ProductCartConnectorToProductInterface
    {
        return $this->getMockBuilder(ProductCartConnectorToProductInterface::class)
            ->onlyMethods([
                'getProductConcrete',
                'getLocalizedProductConcreteName',
                'hasProductAbstract',
                'hasProductConcrete',
                'isProductConcreteActive',
                'getRawProductConcreteBySku',
                'getRawProductConcreteTransfersByConcreteSkus',
                'getProductUrls',
                'getRawProductAbstractTransfersByAbstractSkus',
                'getProductConcretesByCriteria',
            ])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \Spryker\Zed\ProductCartConnector\Dependency\Facade\ProductCartConnectorToLocaleInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockLocaleFacade(): ProductCartConnectorToLocaleInterface
    {
        return $this->getMockBuilder(ProductCartConnectorToLocaleInterface::class)
            ->onlyMethods(['getCurrentLocale'])
            ->disableOriginalConstructor()
            ->getMock();
    }
}
