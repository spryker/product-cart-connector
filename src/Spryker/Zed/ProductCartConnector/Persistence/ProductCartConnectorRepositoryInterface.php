<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Persistence;

interface ProductCartConnectorRepositoryInterface
{
    /**
     * @param list<int> $productAbstractIds
     *
     * @return array<int, \Generated\Shared\Transfer\ProductAbstractTransfer>
     */
    public function getProductAbstractsWithLocalizedAttributesIndexedByIdProductAbstract(array $productAbstractIds, int $idLocale): array;

    /**
     * Returns the given attribute keys that are super attributes.
     *
     * @param list<string> $attributeKeys
     *
     * @return list<string>
     */
    public function getSuperAttributeKeys(array $attributeKeys): array;
}
