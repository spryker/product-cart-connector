<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class ProductCartConnectorConfig extends AbstractBundleConfig
{
    protected const string CHECKOUT_ERROR_TYPE = 'ProductUnavailable';

    protected const string BRAND_ATTRIBUTE_KEY = 'brand';

    /**
     * Specification:
     * - Returns the error type identifier used in CheckoutErrorTransfer for errors produced by this module.
     *
     * @api
     */
    public function getCheckoutErrorType(): string
    {
        return static::CHECKOUT_ERROR_TYPE;
    }

    /**
     * Specification:
     * - Returns the key of the product attribute that holds the brand of a product.
     * - The storefront reads the brand by `\Spryker\Client\ProductStorage\ProductStorageConfig::getBrandAttributeKey()`:
     *   a project that changes the key changes it in both, so the cart and the storefront report the same brand.
     *
     * @api
     */
    public function getBrandAttributeKey(): string
    {
        return static::BRAND_ATTRIBUTE_KEY;
    }
}
