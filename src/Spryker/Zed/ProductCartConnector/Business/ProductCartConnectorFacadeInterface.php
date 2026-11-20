<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Business;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface ProductCartConnectorFacadeInterface
{
    /**
     * Specification:
     * - Reads a persisted concrete product from database.
     * - Expands the items of the CartChangeTransfer with the concrete product's data.
     * - Sets `ItemTransfer.abstractName` to the name of the abstract product in the current locale; null when it has none there.
     * - Sets `ItemTransfer.brand` to the `brand` attribute in the order of the product storage: concrete localized over concrete
     *   over abstract localized over abstract, the localized ones in the current locale; null when none has a non-empty scalar one.
     * - Sets `ItemTransfer.selectedVariantAttributes` to the super attributes among the concrete attributes, the localized values of the
     *   current locale winning, attribute key to value, ordered by attribute key as strings, e.g. ["color" => "Red", "storage_capacity" => "64 GB"];
     *   empty and non-scalar values are left out.
     * - Takes the localized concrete attributes from the concrete products it reads; reads the abstract products with their localized attributes and the super attribute keys with one database query each.
     * - Returns the expanded CartChangeTransfer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function expandItems(CartChangeTransfer $cartChangeTransfer);

    /**
     * Specification:
     * - Checks added to cart products on existing
     * - Returns pre-check transfer with error messages (in negative case)
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartPreCheckResponseTransfer
     */
    public function validateItems(CartChangeTransfer $cartChangeTransfer);

    /**
     * Specification:
     *  - Removes inactive items from quote.
     *  - Adds note to messages about removed items.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function filterInactiveItems(QuoteTransfer $quoteTransfer): QuoteTransfer;

    /**
     * Specification:
     * - Reads persisted URLs from database by abstract product IDs.
     * - Expands CartChangeTransfer items with abstract product URLs.
     * - Returns expanded CartChangeTransfer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function expandItemTransfersWithUrls(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer;

    /**
     * Specification:
     * - Validates the items from `QuoteTransfer.items` and the product bundles from `QuoteTransfer.bundleItems`.
     * - Deduplicates `QuoteTransfer.bundleItems` by `QuoteTransfer.bundleItem.groupKey`, so a bundle added several times is validated once.
     * - Does nothing if both `QuoteTransfer.items` and `QuoteTransfer.bundleItems` are empty.
     * - Requires either `QuoteTransfer.item.sku` or `QuoteTransfer.item.abstractSku` to be set.
     * - Validates if concrete products with sku `QuoteTransfer.item.sku` exist and active.
     * - If `QuoteTransfer.item.sku` is missing, validates if abstract products with sku `QuoteTransfer.item.abstractSku` exist.
     * - Adds corresponding errors to `CheckoutResponseTransfer` in case of failed validation.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function validateCheckoutQuoteItems(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): bool;

    /**
     * Specification:
     * - Requires `CartChangeTransfer.quote` to be set.
     * - Requires `CartChangeTransfer.quote.store` to be set.
     * - Filters out items from `CartChangeTransfer` that are not active.
     * - Adds a message for each item that is not active.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function filterOutInactiveCartChangeItems(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer;
}
