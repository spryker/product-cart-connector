<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Functional\Spryker\Zed\ProductCartConnector\Business\Plugin;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ChangeTransfer;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\ProductCartConnector\Business\ProductCartConnectorFacade;
use Spryker\Zed\Locale\Business\LocaleFacade;
use Orm\Zed\Tax\Persistence\SpyTaxRate;
use Orm\Zed\Tax\Persistence\SpyTaxSet;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Orm\Zed\Product\Persistence\SpyProduct;
use Orm\Zed\Product\Persistence\SpyProductLocalizedAttributes;

/**
 * @group Spryker
 * @group Zed
 * @group ProductCartConnector
 * @group Business
 * @group ProductCartPlugin
 */
class ProductCartPluginTest extends Test
{

    const SKU_PRODUCT_ABSTRACT = 'Abstract product sku';
    const SKU_CONCRETE_PRODUCT = 'Concrete product sku';
    const TAX_SET_NAME = 'Sales Tax';
    const TAX_RATE_NAME = 'VAT';
    const TAX_RATE_PERCENTAGE = 10;
    const CONCRETE_PRODUCT_NAME = 'Concrete product name';

    /**
     * @var ProductCartConnectorFacade
     */
    private $productCartConnectorFacade;

    /**
     * @var LocaleFacade
     */
    private $localeFacade;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->localeFacade = new LocaleFacade();
        $this->productCartConnectorFacade = new ProductCartConnectorFacade();
    }

    /**
     * @return void
     */
    public function testPluginExpandsCartItemWithExpectedProductData()
    {
        $localeName = Store::getInstance()->getCurrentLocale();
        $localeTransfer = $this->localeFacade->getLocale($localeName);

        $taxRateEntity = new SpyTaxRate();
        $taxRateEntity->setRate(self::TAX_RATE_PERCENTAGE)
            ->setName(self::TAX_RATE_NAME);

        $taxSetEntity = new SpyTaxSet();
        $taxSetEntity->addSpyTaxRate($taxRateEntity)
            ->setName(self::TAX_SET_NAME);

        $productAbstractEntity = new SpyProductAbstract();
        $productAbstractEntity->setSpyTaxSet($taxSetEntity)
            ->setAttributes('')
            ->setSku(self::SKU_PRODUCT_ABSTRACT);

        $localizedAttributesEntity = new SpyProductLocalizedAttributes();
        $localizedAttributesEntity->setName(self::CONCRETE_PRODUCT_NAME)
            ->setAttributes('')
            ->setFkLocale($localeTransfer->getIdLocale());

        $concreteProductEntity = new SpyProduct();
        $concreteProductEntity->setSpyProductAbstract($productAbstractEntity)
            ->setAttributes('')
            ->addSpyProductLocalizedAttributes($localizedAttributesEntity)
            ->setSku(self::SKU_CONCRETE_PRODUCT)
            ->save();

        $changeTransfer = new ChangeTransfer();
        $itemTransfer = new ItemTransfer();
        $itemTransfer->setSku(self::SKU_CONCRETE_PRODUCT);
        $changeTransfer->addItem($itemTransfer);

        $this->productCartConnectorFacade->expandItems($changeTransfer);

        $expandedItemTransfer = $changeTransfer->getItems()[0];

        $this->assertEquals(self::SKU_PRODUCT_ABSTRACT, $expandedItemTransfer->getAbstractSku());
        $this->assertEquals(self::SKU_CONCRETE_PRODUCT, $expandedItemTransfer->getSku());
        $this->assertEquals($productAbstractEntity->getIdProductAbstract(), $expandedItemTransfer->getIdProductAbstract());
        $this->assertEquals($concreteProductEntity->getIdProduct(), $expandedItemTransfer->getId());
        $expandedTSetTransfer = $expandedItemTransfer->getTaxSet();
        $this->assertNotNull($expandedTSetTransfer);
        $this->assertEquals(self::TAX_SET_NAME, $expandedTSetTransfer->getName());
    }

}
