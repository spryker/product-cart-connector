<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductCartConnector;

use Codeception\Actor;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\LocalizedAttributesTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery;

/**
 * Inherited Methods
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
class ProductCartConnectorCommunicationTester extends Actor
{
    use _generated\ProductCartConnectorCommunicationTesterActions;

    protected const string PRODUCT_CONCRETE_NAME = 'Concrete product name';

    public function getCurrentLocale(): LocaleTransfer
    {
        return $this->getLocator()->locale()->facade()->getCurrentLocale();
    }

    public function findLocaleOtherThan(LocaleTransfer $localeTransfer): ?LocaleTransfer
    {
        $localeFacade = $this->getLocator()->locale()->facade();

        foreach ($localeFacade->getAvailableLocales() as $localeName) {
            if ($localeName !== $localeTransfer->getLocaleName()) {
                return $localeFacade->getLocale($localeName);
            }
        }

        return null;
    }

    /**
     * The concrete product gets a name in the current locale: the product cart plugin finds only concrete products with localized attributes.
     *
     * @param array<string, mixed> $productAbstractAttributes
     * @param array<string, mixed> $localizedAttributes
     */
    public function haveProductWithAbstractLocalizedAttributes(
        string $abstractName,
        LocaleTransfer $localeTransfer,
        array $productAbstractAttributes = [],
        array $localizedAttributes = []
    ): ProductConcreteTransfer {
        return $this->haveProduct([
            ProductConcreteTransfer::LOCALIZED_ATTRIBUTES => [[
                LocalizedAttributesTransfer::NAME => static::PRODUCT_CONCRETE_NAME,
                LocalizedAttributesTransfer::LOCALE => $this->getCurrentLocale()->toArray(),
            ]],
        ], [
            ProductAbstractTransfer::ATTRIBUTES => $productAbstractAttributes,
            ProductAbstractTransfer::LOCALIZED_ATTRIBUTES => [[
                LocalizedAttributesTransfer::NAME => $abstractName,
                LocalizedAttributesTransfer::LOCALE => $localeTransfer->toArray(),
                LocalizedAttributesTransfer::ATTRIBUTES => $localizedAttributes,
            ]],
        ]);
    }

    /**
     * @param array<string, mixed> $productAbstractLocalizedAttributes
     * @param array<string, mixed> $productConcreteAttributes
     * @param array<string, mixed> $productConcreteLocalizedAttributes
     */
    public function haveProductWithConcreteAttributes(
        string $abstractName,
        LocaleTransfer $localeTransfer,
        array $productAbstractLocalizedAttributes,
        array $productConcreteAttributes,
        array $productConcreteLocalizedAttributes = []
    ): ProductConcreteTransfer {
        return $this->haveProduct([
            ProductConcreteTransfer::ATTRIBUTES => $productConcreteAttributes,
            ProductConcreteTransfer::LOCALIZED_ATTRIBUTES => [[
                LocalizedAttributesTransfer::NAME => $abstractName,
                LocalizedAttributesTransfer::LOCALE => $localeTransfer->toArray(),
                LocalizedAttributesTransfer::ATTRIBUTES => $productConcreteLocalizedAttributes,
            ]],
        ], [
            ProductAbstractTransfer::LOCALIZED_ATTRIBUTES => [[
                LocalizedAttributesTransfer::NAME => $abstractName,
                LocalizedAttributesTransfer::LOCALE => $localeTransfer->toArray(),
                LocalizedAttributesTransfer::ATTRIBUTES => $productAbstractLocalizedAttributes,
            ]],
        ]);
    }

    /**
     * The item as the cart receives it before ProductCartPlugin expands it: with the concrete product SKU only.
     */
    public function createCartChangeTransferWithItemOfSku(string $sku): CartChangeTransfer
    {
        return (new CartChangeTransfer())->addItem((new ItemTransfer())->setSku($sku));
    }

    public function haveAttributeKey(string $attributeKey, bool $isSuper): void
    {
        SpyProductAttributeKeyQuery::create()
            ->filterByKey($attributeKey)
            ->findOneOrCreate()
            ->setIsSuper($isSuper)
            ->save();
    }
}
