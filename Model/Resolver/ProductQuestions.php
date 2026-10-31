<?php

/**
 * Copyright © Ecommet Solutions. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ecommet\ProductQuestionsGraphQl\Model\Resolver;

use Ecommet\ProductQuestions\Api\PublicQuestionListInterface;
use Ecommet\ProductQuestionsGraphQl\Model\Formatter;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Query.productQuestions(sku) and ProductInterface.questions.
 */
class ProductQuestions implements ResolverInterface
{
    /**
     * @var PublicQuestionListInterface
     */
    private PublicQuestionListInterface $publicQuestionList;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var Formatter
     */
    private Formatter $formatter;

    /**
     * @param PublicQuestionListInterface $publicQuestionList
     * @param ProductRepositoryInterface $productRepository
     * @param Formatter $formatter
     */
    public function __construct(
        PublicQuestionListInterface $publicQuestionList,
        ProductRepositoryInterface $productRepository,
        Formatter $formatter
    ) {
        $this->publicQuestionList = $publicQuestionList;
        $this->productRepository = $productRepository;
        $this->formatter = $formatter;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $pageSize = (int)($args['pageSize'] ?? 10);
        $currentPage = (int)($args['currentPage'] ?? 1);
        if ($pageSize < 1 || $currentPage < 1) {
            throw new GraphQlInputException(__('pageSize and currentPage must be greater than 0.'));
        }

        if (isset($value['model'])) {
            $productId = (int)$value['model']->getId();
        } else {
            try {
                $productId = (int)$this->productRepository->get((string)$args['sku'])->getId();
            } catch (NoSuchEntityException $e) {
                throw new GraphQlNoSuchEntityException(__('The product "%1" does not exist.', $args['sku']), $e);
            }
        }

        $search = isset($args['search']) ? trim((string)$args['search']) : null;
        $results = $this->publicQuestionList->getByProductId(
            $productId,
            $pageSize,
            $currentPage,
            $search === '' ? null : $search
        );
        return $this->formatter->formatPage($results, $productId);
    }
}
