<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Communication\Plugin;

use Generated\Shared\Transfer\CartChangeTransfer;
use Spryker\Zed\Cart\Dependency\ItemExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\ProductCartConnector\Business\ProductCartConnectorFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductCartConnector\Communication\ProductCartConnectorCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductCartConnector\ProductCartConnectorConfig getConfig()
 */
class ProductCartPlugin extends AbstractPlugin implements ItemExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Expands the items with the data of their concrete products.
     * - Sets `ItemTransfer.abstractName` to the name of the abstract product in the current locale; null when it has none there.
     * - Sets `ItemTransfer.brand` to the `brand` attribute in the order of the product storage: concrete localized over concrete
     *   over abstract localized over abstract, the localized ones in the current locale; null when none has a non-empty scalar one.
     * - Sets `ItemTransfer.selectedVariantAttributes` to the super attributes among the concrete attributes, the localized values of the
     *   current locale winning, attribute key to value, ordered by attribute key as strings, e.g. ["color" => "Red", "storage_capacity" => "64 GB"];
     *   empty and non-scalar values are left out.
     * - Takes the localized concrete attributes from the concrete products it reads; reads the abstract products with their localized attributes and the super attribute keys with one database query each.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function expandItems(CartChangeTransfer $cartChangeTransfer)
    {
        $this->getFacade()->expandItems($cartChangeTransfer);

        return $cartChangeTransfer;
    }
}
