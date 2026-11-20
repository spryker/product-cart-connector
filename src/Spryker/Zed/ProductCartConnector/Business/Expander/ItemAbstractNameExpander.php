<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Business\Expander;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;

class ItemAbstractNameExpander implements ItemAbstractNameExpanderInterface
{
    /**
     * @param array<int, \Generated\Shared\Transfer\ProductAbstractTransfer> $productAbstractTransfers
     */
    public function expandItems(CartChangeTransfer $cartChangeTransfer, array $productAbstractTransfers): CartChangeTransfer
    {
        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            $productAbstractTransfer = $productAbstractTransfers[$itemTransfer->getIdProductAbstract()] ?? null;
            $itemTransfer->setAbstractName($this->findAbstractName($productAbstractTransfer));
        }

        return $cartChangeTransfer;
    }

    protected function findAbstractName(?ProductAbstractTransfer $productAbstractTransfer): ?string
    {
        // The reader adds the localized attributes of the current locale only.
        foreach ($productAbstractTransfer?->getLocalizedAttributes() ?? [] as $localizedAttributesTransfer) {
            if ($localizedAttributesTransfer->getName()) {
                return $localizedAttributesTransfer->getName();
            }
        }

        return null;
    }
}
