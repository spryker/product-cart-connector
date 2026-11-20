<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Business\Resolver;

interface ItemSelectedVariantAttributesResolverInterface
{
    /**
     * @param array<int|string, mixed> $concreteAttributes
     * @param array<string, string> $superAttributeKeys
     *
     * @return array<int|string, string>
     */
    public function resolveSelectedVariantAttributes(array $concreteAttributes, array $superAttributeKeys): array;
}
