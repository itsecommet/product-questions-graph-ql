<?php

/**
 * Copyright © Ecommet Solutions. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ecommet\ProductQuestionsGraphQl\Model\Resolver;

use Ecommet\ProductQuestions\Api\QuestionManagementInterface;
use Ecommet\ProductQuestionsGraphQl\Model\Formatter;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Customer.product_questions
 */
class CustomerQuestions implements ResolverInterface
{
    /**
     * @var QuestionManagementInterface
     */
    private QuestionManagementInterface $questionManagement;

    /**
     * @var Formatter
     */
    private Formatter $formatter;

    /**
     * @param QuestionManagementInterface $questionManagement
     * @param Formatter $formatter
     */
    public function __construct(QuestionManagementInterface $questionManagement, Formatter $formatter)
    {
        $this->questionManagement = $questionManagement;
        $this->formatter = $formatter;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        if (!$context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }
        return array_map(
            [$this->formatter, 'formatCustomer'],
            $this->questionManagement->getCustomerQuestions((int)$context->getUserId())
        );
    }
}
