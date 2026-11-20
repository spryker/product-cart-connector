<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Business\Reader;

use Generated\Shared\Transfer\CartChangeTransfer;

interface ProductAbstractReaderInterface
{
    /**
     * @return array<int, \Generated\Shared\Transfer\ProductAbstractTransfer>
     */
    public function getProductAbstractsWithCurrentLocaleAttributesIndexedByIdProductAbstract(CartChangeTransfer $cartChangeTransfer): array;
}
