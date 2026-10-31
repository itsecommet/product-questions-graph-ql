<?php

/**
 * Copyright © Ecommet Solutions. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ecommet\ProductQuestionsGraphQl\Model\Resolver;

use Ecommet\ProductQuestions\Api\VoteManagementInterface;
use Ecommet\ProductQuestionsGraphQl\Model\Formatter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Mutation.voteProductQuestion
 */
class Vote implements ResolverInterface
{
    /**
     * @var VoteManagementInterface
     */
    private VoteManagementInterface $voteManagement;

    /**
     * @var Formatter
     */
    private Formatter $formatter;

    /**
     * @param VoteManagementInterface $voteManagement
     * @param Formatter $formatter
     */
    public function __construct(VoteManagementInterface $voteManagement, Formatter $formatter)
    {
        $this->voteManagement = $voteManagement;
        $this->formatter = $formatter;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $input = $args['input'] ?? [];
        $customerId = $context->getExtensionAttributes()->getIsCustomer() ? (int)$context->getUserId() : null;
        try {
            $question = $this->voteManagement->vote(
                (int)($input['question_id'] ?? 0),
                (bool)($input['helpful'] ?? false),
                $customerId,
                $customerId ? null : (isset($input['guest_token']) ? (string)$input['guest_token'] : null)
            );
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }
        return $this->formatter->formatPublic($question);
    }
}
