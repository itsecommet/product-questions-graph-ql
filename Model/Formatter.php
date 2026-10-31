<?php

/**
 * Copyright © Ecommet Solutions. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ecommet\ProductQuestionsGraphQl\Model;

use Ecommet\ProductQuestions\Api\Data\PublicQuestionInterface;
use Ecommet\ProductQuestions\Api\Data\PublicQuestionSearchResultsInterface;
use Ecommet\ProductQuestions\Api\Data\QuestionInterface;

/**
 * Turns question objects into GraphQL result arrays.
 */
class Formatter
{
    /**
     * Format a page of public questions
     *
     * @param PublicQuestionSearchResultsInterface $results
     * @param int $productId Used for cache tags
     * @return array
     */
    public function formatPage(PublicQuestionSearchResultsInterface $results, int $productId): array
    {
        $pageSize = $results->getPageSize();
        return [
            'items' => array_map([$this, 'formatPublic'], $results->getItems()),
            'total_count' => $results->getTotalCount(),
            'page_info' => [
                'page_size' => $pageSize,
                'current_page' => $results->getCurrentPage(),
                'total_pages' => $pageSize ? (int)ceil($results->getTotalCount() / $pageSize) : 0,
            ],
            'product_id' => $productId,
        ];
    }

    /**
     * Format a public question
     *
     * @param PublicQuestionInterface $question
     * @return array
     */
    public function formatPublic(PublicQuestionInterface $question): array
    {
        return [
            'id' => $question->getId(),
            'question' => $question->getQuestion(),
            'answer' => $question->getAnswer(),
            'author_name' => $question->getAuthorName(),
            'answered_by' => $question->getAnsweredBy(),
            'created_at' => $question->getCreatedAt(),
            'answered_at' => $question->getAnsweredAt(),
            'helpful_yes' => $question->getHelpfulYes(),
            'helpful_no' => $question->getHelpfulNo(),
        ];
    }

    /**
     * Format a question for the customer who asked it; rejected questions show as pending
     *
     * @param QuestionInterface $question
     * @return array
     */
    public function formatCustomer(QuestionInterface $question): array
    {
        $answered = $question->getStatus() === QuestionInterface::STATUS_ANSWERED && $question->getAnswer();
        return [
            'id' => (int)$question->getId(),
            'product_sku' => (string)$question->getSku(),
            'question' => (string)$question->getQuestion(),
            'answer' => $answered ? $question->getAnswer() : null,
            'status' => $answered ? 'ANSWERED' : 'PENDING',
            'created_at' => (string)$question->getCreatedAt(),
            'answered_at' => $answered ? $question->getAnsweredAt() : null,
        ];
    }
}
