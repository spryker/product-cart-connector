<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductCartConnector\Business\Plugin;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\UrlTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductCartConnector
 * @group Business
 * @group Plugin
 * @group Facade
 * @group ProductCartConnectorFacadeTest
 * Add your own group annotations below this line
 */
class ProductCartConnectorFacadeTest extends Unit
{
    /**
     * @var string
     */
    protected const PRODUCT_URL_EN = '/en/product-1';

    /**
     * @var \SprykerTest\Zed\ProductCartConnector\ProductCartConnectorBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testExpandItemTransfersWithUrlsForCartWithItem(): void
    {
        // Arrange
        $this->tester->haveLocale([LocaleTransfer::LOCALE_NAME => 'en_US']);

        $productAbstractTransfer = $this->tester->haveProductAbstract();

        $this->tester->haveProduct([
            ProductConcreteTransfer::FK_PRODUCT_ABSTRACT => $productAbstractTransfer->getIdProductAbstract(),
        ]);

        $productUrl = $this->tester->haveUrl([
            UrlTransfer::FK_LOCALE => $this->tester->getLocator()->locale()->facade()->getCurrentLocale()->getIdLocale(),
            UrlTransfer::FK_RESOURCE_PRODUCT_ABSTRACT => $productAbstractTransfer->getIdProductAbstract(),
            UrlTransfer::URL => static::PRODUCT_URL_EN,
        ]);

        $cartChangeTransfer = (new CartChangeTransfer())
            ->addItem((new ItemTransfer())->setIdProductAbstract($productAbstractTransfer->getIdProductAbstract()));

        // Act
        $this->tester->getFacade()->expandItemTransfersWithUrls($cartChangeTransfer);

        // Assert
        $this->assertSame($productUrl->getUrl(), $cartChangeTransfer->getItems()->offsetGet(0)->getUrl());
    }

    /**
     * @return void
     */
    public function testExpandItemTransfersWithUrlsForEmptyCart(): void
    {
        // Arrange
        $cartChangeTransfer = new CartChangeTransfer();

        // Act
        $this->tester->getFacade()->expandItemTransfersWithUrls($cartChangeTransfer);

        // Assert
        $this->assertCount(0, $cartChangeTransfer->getItems());
    }
}
