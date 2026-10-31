# Changelog

All notable changes to this module are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the module uses
[Semantic Versioning](https://semver.org/).

## [1.0.0] - 2026-10-31

### Added
- `productQuestions(sku, pageSize, currentPage, search)` query and `ProductInterface.questions` field,
  cached with the product question cache tag.
- `Customer.product_questions`.
- `askProductQuestion`, `voteProductQuestion` and `deleteCustomerProductQuestion` mutations.
