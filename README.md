# Ecommet Product Questions GraphQL

GraphQL API for [Ecommet Product Questions & Answers](https://ecommet.in): read answered questions
on products, list and delete a customer's questions, ask questions and vote on answers. For PWA
Studio, headless and mobile storefronts.

| | |
| --- | --- |
| Composer package | `ecommet/module-product-questions-graph-ql` |
| Requires | `ecommet/module-product-questions` 2.0+ |
| Magento | Open Source / Adobe Commerce 2.4.7 – 2.4.9 |
| PHP | 8.2 – 8.5 |

## Installation

```bash
composer require ecommet/module-product-questions-graph-ql
bin/magento module:enable Ecommet_ProductQuestionsGraphQl
bin/magento setup:upgrade
bin/magento cache:flush
```

## Queries

Answered questions of a product, in the store view of the `Store` header. Results are cached and
refreshed when the product's questions change.

```graphql
{
  productQuestions(sku: "24-MB01", pageSize: 5, currentPage: 1, search: "strap") {
    total_count
    page_info { current_page total_pages }
    items { id question answer author_name answered_by created_at answered_at helpful_yes helpful_no }
  }
}
```

The same list is available on every product:

```graphql
{
  products(filter: {sku: {eq: "24-MB01"}}) {
    items { sku questions(pageSize: 3) { total_count items { question answer } } }
  }
}
```

The signed-in customer's own questions (customer token required):

```graphql
{
  customer {
    product_questions { id product_sku question answer status created_at answered_at }
  }
}
```

`status` is `PENDING` or `ANSWERED`.

## Mutations

```graphql
# Customer token required
mutation {
  askProductQuestion(input: {sku: "24-MB01", question: "Does it fit a 15 inch laptop?", phone: null}) {
    question { id status }
  }
}

# Customers are identified by their token; guests send a random 16-128 character token they keep
mutation {
  voteProductQuestion(input: {question_id: 1, helpful: true, guest_token: "k3j4h5g6f7d8s9a0q1w2"}) {
    id helpful_yes helpful_no
  }
}

# Customer token required
mutation {
  deleteCustomerProductQuestion(id: 12)
}
```

Guests cannot ask questions through GraphQL; the storefront form, with its spam checks, is the guest path.
The store's settings (enabled, voting, guest voting, length, link blocking, hourly limit) apply.

## Support

- Email: [ecommet.solutions@gmail.com](mailto:ecommet.solutions@gmail.com)
- Website: [https://ecommet.in](https://ecommet.in)

## License

Ecommet Free Use License; see [LICENSE.txt](LICENSE.txt).
