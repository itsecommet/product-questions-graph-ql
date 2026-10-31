<?php

/**
 * Copyright © Ecommet Solutions. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ecommet\ProductQuestionsGraphQl\Model\Resolver;

use Ecommet\ProductQuestions\Api\QuestionManagementInterface;
use Ecommet\ProductQuestionsGraphQl\Model\Formatter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Mutation.askProductQuestion (customers only, like the REST API)
 */
class AskQuestion implements ResolverInterface
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
            throw new GraphQlAuthorizationException(__('Please sign in to ask a question.'));
        }
        $input = $args['input'] ?? [];
        try {
            $question = $this->questionManagement->askAsCustomer(
                (int)$context->getUserId(),
                (string)($input['sku'] ?? ''),
                (string)($input['question'] ?? ''),
                isset($input['phone']) ? (string)$input['phone'] : null
            );
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }
        return ['question' => $this->formatter->formatCustomer($question)];
    }
}
