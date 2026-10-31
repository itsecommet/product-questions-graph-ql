<?php

/**
 * Copyright © Ecommet Solutions. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ecommet\ProductQuestionsGraphQl\Model\Resolver\Identity;

use Ecommet\ProductQuestions\Model\Question;
use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

/**
 * Cache tag of the product's question list, cleaned when its questions change.
 */
class ProductQuestions implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public function getIdentities(array $resolvedData): array
    {
        return empty($resolvedData['product_id'])
            ? []
            : [Question::CACHE_TAG_PRODUCT . (int)$resolvedData['product_id']];
    }
}
