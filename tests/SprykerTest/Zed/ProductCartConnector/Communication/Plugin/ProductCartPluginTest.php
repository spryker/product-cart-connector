<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductCartConnector\Communication\Plugin;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ItemTransfer;
use Spryker\Zed\ProductCartConnector\Communication\Plugin\ProductCartPlugin;
use SprykerTest\Zed\ProductCartConnector\ProductCartConnectorCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductCartConnector
 * @group Communication
 * @group Plugin
 * @group ProductCartPluginTest
 * Add your own group annotations below this line
 */
class ProductCartPluginTest extends Unit
{
    protected const string PRODUCT_ABSTRACT_NAME = 'Abstract product name';

    protected const string UNKNOWN_SKU = 'product-cart-plugin-test-unknown-sku';

    /**
     * @uses \Spryker\Zed\ProductCartConnector\ProductCartConnectorConfig::BRAND_ATTRIBUTE_KEY
     */
    protected const string BRAND_ATTRIBUTE_KEY = 'brand';

    protected const string BRAND = 'Samsung';

    protected const string LOCALIZED_BRAND = 'Samsung DE';

    protected const string CONCRETE_BRAND = 'Samsung Mobile';

    protected const string CONCRETE_LOCALIZED_BRAND = 'Samsung Mobile DE';

    protected const array NON_SCALAR_BRAND = ['Samsung'];

    protected const string EMPTY_BRAND = '';

    protected const string ATTRIBUTE_KEY_PATTERN = 'test_selected_attribute_%s_%s';

    protected const string SUPER_ATTRIBUTE_VALUE_COLOR = 'Red';

    protected const string SUPER_ATTRIBUTE_VALUE_STORAGE = '64 GB';

    protected const string LOCALIZED_SUPER_ATTRIBUTE_VALUE_COLOR = 'Rot';

    protected const string REGULAR_ATTRIBUTE_VALUE = 'Not a variant';

    protected ProductCartConnectorCommunicationTester $tester;

    public function testExpandItemsSetsTheNameOfTheAbstractProductInTheCurrentLocale(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProductWithAbstractLocalizedAttributes(static::PRODUCT_ABSTRACT_NAME, $this->tester->getCurrentLocale());

        // Act
        $itemTransfer = $this->expandItemOfSku($productConcreteTransfer->getSkuOrFail());

        // Assert
        $this->assertSame(static::PRODUCT_ABSTRACT_NAME, $itemTransfer->getAbstractName());
    }

    /**
     * The abstract product has a name in another locale only: the item reports no abstract name, not that name and not the abstract SKU.
     */
    public function testExpandItemsLeavesTheAbstractNameEmptyWhenTheAbstractProductHasNoNameInTheCurrentLocale(): void
    {
        // Arrange
        $otherLocaleTransfer = $this->tester->findLocaleOtherThan($this->tester->getCurrentLocale());
        if ($otherLocaleTransfer === null) {
            $this->markTestSkipped('The test needs a second available locale.');
        }
        $productConcreteTransfer = $this->tester->haveProductWithAbstractLocalizedAttributes(static::PRODUCT_ABSTRACT_NAME, $otherLocaleTransfer);

        // Act
        $itemTransfer = $this->expandItemOfSku($productConcreteTransfer->getSkuOrFail());

        // Assert
        $this->assertNull($itemTransfer->getAbstractName());
    }

    public function testExpandItemsSetsTheBrandOfTheAbstractProduct(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProductWithAbstractLocalizedAttributes(
            static::PRODUCT_ABSTRACT_NAME,
            $this->tester->getCurrentLocale(),
            [static::BRAND_ATTRIBUTE_KEY => static::BRAND],
        );

        // Act
        $itemTransfer = $this->expandItemOfSku($productConcreteTransfer->getSkuOrFail());

        // Assert
        $this->assertSame(static::BRAND, $itemTransfer->getBrand());
    }

    public function testExpandItemsTakesTheBrandOfTheAbstractLocalizedAttributesOverTheAbstractOnes(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProductWithAbstractLocalizedAttributes(
            static::PRODUCT_ABSTRACT_NAME,
            $this->tester->getCurrentLocale(),
            [static::BRAND_ATTRIBUTE_KEY => static::BRAND],
            [static::BRAND_ATTRIBUTE_KEY => static::LOCALIZED_BRAND],
        );

        // Act
        $itemTransfer = $this->expandItemOfSku($productConcreteTransfer->getSkuOrFail());

        // Assert
        $this->assertSame(static::LOCALIZED_BRAND, $itemTransfer->getBrand());
    }

    public function testExpandItemsTakesTheBrandOfTheConcreteProductOverTheAbstractLocalizedOne(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProductWithConcreteAttributes(
            static::PRODUCT_ABSTRACT_NAME,
            $this->tester->getCurrentLocale(),
            [static::BRAND_ATTRIBUTE_KEY => static::LOCALIZED_BRAND],
            [static::BRAND_ATTRIBUTE_KEY => static::CONCRETE_BRAND],
        );

        // Act
        $itemTransfer = $this->expandItemOfSku($productConcreteTransfer->getSkuOrFail());

        // Assert
        $this->assertSame(static::CONCRETE_BRAND, $itemTransfer->getBrand());
    }

    public function testExpandItemsTakesTheBrandOfTheConcreteLocalizedAttributesOverTheConcreteOnes(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProductWithConcreteAttributes(
            static::PRODUCT_ABSTRACT_NAME,
            $this->tester->getCurrentLocale(),
            [static::BRAND_ATTRIBUTE_KEY => static::LOCALIZED_BRAND],
            [static::BRAND_ATTRIBUTE_KEY => static::CONCRETE_BRAND],
            [static::BRAND_ATTRIBUTE_KEY => static::CONCRETE_LOCALIZED_BRAND],
        );

        // Act
        $itemTransfer = $this->expandItemOfSku($productConcreteTransfer->getSkuOrFail());

        // Assert
        $this->assertSame(static::CONCRETE_LOCALIZED_BRAND, $itemTransfer->getBrand());
    }

    public function testExpandItemsLeavesTheBrandEmptyWhenTheAbstractProductHasNoBrand(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProductWithAbstractLocalizedAttributes(
            static::PRODUCT_ABSTRACT_NAME,
            $this->tester->getCurrentLocale(),
            [static::BRAND_ATTRIBUTE_KEY => static::EMPTY_BRAND],
        );

        // Act
        $itemTransfer = $this->expandItemOfSku($productConcreteTransfer->getSkuOrFail());

        // Assert
        $this->assertNull($itemTransfer->getBrand());
    }

    public function testExpandItemsLeavesTheBrandEmptyWhenTheBrandIsNotAScalar(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProductWithAbstractLocalizedAttributes(
            static::PRODUCT_ABSTRACT_NAME,
            $this->tester->getCurrentLocale(),
            [static::BRAND_ATTRIBUTE_KEY => static::NON_SCALAR_BRAND],
        );

        // Act
        $itemTransfer = $this->expandItemOfSku($productConcreteTransfer->getSkuOrFail());

        // Assert
        $this->assertNull($itemTransfer->getBrand());
    }

    public function testExpandItemsSetsTheSuperAttributesOrderedByAttributeKey(): void
    {
        // Arrange
        $firstSuperAttributeKey = $this->haveAttributeKey('a', true);
        $secondSuperAttributeKey = $this->haveAttributeKey('b', true);
        $regularAttributeKey = $this->haveAttributeKey('c', false);
        $productConcreteTransfer = $this->tester->haveProductWithConcreteAttributes(
            static::PRODUCT_ABSTRACT_NAME,
            $this->tester->getCurrentLocale(),
            [],
            [
                $secondSuperAttributeKey => static::SUPER_ATTRIBUTE_VALUE_STORAGE,
                $regularAttributeKey => static::REGULAR_ATTRIBUTE_VALUE,
                $firstSuperAttributeKey => static::SUPER_ATTRIBUTE_VALUE_COLOR,
            ],
        );

        // Act
        $itemTransfer = $this->expandItemOfSku($productConcreteTransfer->getSkuOrFail());

        // Assert
        $this->assertSame(
            [$firstSuperAttributeKey => static::SUPER_ATTRIBUTE_VALUE_COLOR, $secondSuperAttributeKey => static::SUPER_ATTRIBUTE_VALUE_STORAGE],
            $itemTransfer->getSelectedVariantAttributes(),
        );
    }

    public function testExpandItemsTakesTheLocalizedValueOfASuperAttributeInTheCurrentLocale(): void
    {
        // Arrange
        $superAttributeKey = $this->haveAttributeKey('a', true);
        $productConcreteTransfer = $this->tester->haveProductWithConcreteAttributes(
            static::PRODUCT_ABSTRACT_NAME,
            $this->tester->getCurrentLocale(),
            [],
            [$superAttributeKey => static::SUPER_ATTRIBUTE_VALUE_COLOR],
            [$superAttributeKey => static::LOCALIZED_SUPER_ATTRIBUTE_VALUE_COLOR],
        );

        // Act
        $itemTransfer = $this->expandItemOfSku($productConcreteTransfer->getSkuOrFail());

        // Assert
        $this->assertSame([$superAttributeKey => static::LOCALIZED_SUPER_ATTRIBUTE_VALUE_COLOR], $itemTransfer->getSelectedVariantAttributes());
    }

    public function testExpandItemsLeavesTheProductDetailsEmptyForAnItemWithoutAProduct(): void
    {
        // Act
        $itemTransfer = $this->expandItemOfSku(static::UNKNOWN_SKU);

        // Assert
        $this->assertNull($itemTransfer->getAbstractName());
        $this->assertNull($itemTransfer->getBrand());
        $this->assertSame([], $itemTransfer->getSelectedVariantAttributes());
    }

    protected function expandItemOfSku(string $sku): ItemTransfer
    {
        $cartChangeTransfer = (new ProductCartPlugin())->expandItems($this->tester->createCartChangeTransferWithItemOfSku($sku));

        return $cartChangeTransfer->getItems()->offsetGet(0);
    }

    protected function haveAttributeKey(string $keyPrefix, bool $isSuper): string
    {
        $attributeKey = sprintf(static::ATTRIBUTE_KEY_PATTERN, $keyPrefix, uniqid());
        $this->tester->haveAttributeKey($attributeKey, $isSuper);

        return $attributeKey;
    }
}
