<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCartConnector\Business\Resolver;

class ItemSelectedVariantAttributesResolver implements ItemSelectedVariantAttributesResolverInterface
{
    /**
     * @param array<int|string, mixed> $concreteAttributes
     * @param array<string, string> $superAttributeKeys
     *
     * @return array<int|string, string>
     */
    public function resolveSelectedVariantAttributes(array $concreteAttributes, array $superAttributeKeys): array
    {
        $selectedVariantAttributes = [];

        foreach ($concreteAttributes as $attributeKey => $value) {
            $isSuperAttribute = isset($superAttributeKeys[(string)$attributeKey]);
            $hasValue = is_scalar($value) && (string)$value !== '';

            if (!$isSuperAttribute || !$hasValue) {
                continue;
            }

            $selectedVariantAttributes[$attributeKey] = (string)$value;
        }

        // String order, so every concrete product of the abstract product lists its attributes in the same order: PHP turns a numeric attribute key into an integer.
        ksort($selectedVariantAttributes, SORT_STRING);

        return $selectedVariantAttributes;
    }
}
