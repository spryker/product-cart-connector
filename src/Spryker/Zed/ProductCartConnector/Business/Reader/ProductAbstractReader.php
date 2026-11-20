<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Business\Reader;

use Generated\Shared\Transfer\CartChangeTransfer;
use Spryker\Zed\ProductCartConnector\Dependency\Facade\ProductCartConnectorToLocaleInterface;
use Spryker\Zed\ProductCartConnector\Persistence\ProductCartConnectorRepositoryInterface;

class ProductAbstractReader implements ProductAbstractReaderInterface
{
    public function __construct(
        protected ProductCartConnectorToLocaleInterface $localeFacade,
        protected ProductCartConnectorRepositoryInterface $productCartConnectorRepository
    ) {
    }

    /**
     * @return array<int, \Generated\Shared\Transfer\ProductAbstractTransfer>
     */
    public function getProductAbstractsWithCurrentLocaleAttributesIndexedByIdProductAbstract(CartChangeTransfer $cartChangeTransfer): array
    {
        return $this->productCartConnectorRepository->getProductAbstractsWithLocalizedAttributesIndexedByIdProductAbstract(
            $this->getProductAbstractIds($cartChangeTransfer),
            $this->localeFacade->getCurrentLocale()->getIdLocaleOrFail(),
        );
    }

    /**
     * @return list<int>
     */
    protected function getProductAbstractIds(CartChangeTransfer $cartChangeTransfer): array
    {
        $productAbstractIds = [];

        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            $idProductAbstract = $itemTransfer->getIdProductAbstract();

            if (!$idProductAbstract) {
                continue;
            }

            $productAbstractIds[$idProductAbstract] = $idProductAbstract;
        }

        return array_values($productAbstractIds);
    }
}
