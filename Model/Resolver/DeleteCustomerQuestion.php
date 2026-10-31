<?php

/**
 * Copyright © Ecommet Solutions. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ecommet\ProductQuestionsGraphQl\Model\Resolver;

use Ecommet\ProductQuestions\Api\QuestionManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Mutation.deleteCustomerProductQuestion
 */
class DeleteCustomerQuestion implements ResolverInterface
{
    /**
     * @var QuestionManagementInterface
     */
    private QuestionManagementInterface $questionManagement;

    /**
     * @param QuestionManagementInterface $questionManagement
     */
    public function __construct(QuestionManagementInterface $questionManagement)
    {
        $this->questionManagement = $questionManagement;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        if (!$context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }
        try {
            return $this->questionManagement->deleteCustomerQuestion((int)$context->getUserId(), (int)$args['id']);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }
    }
}
