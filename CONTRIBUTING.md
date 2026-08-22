# Contributing to Checkout Fields for WooCommerce

Thank you for considering a contribution.

This project follows an architecture-first, task-driven workflow documented in `docs/09-development-workflow.md`. Please read that document before opening a pull request.

## Branch workflow

- `develop` is the integration branch. All work branches from `develop`.
- `main` is stable production. Never commit directly to `main`.

Typical flow:

1. `git checkout develop && git pull origin develop`
2. `git checkout -b feat/your-feature`
3. Implement one focused task
4. Run quality gates (below) and push the feature branch
5. Open a Pull Request with base `develop`

## Development setup

- PHP 8.2+, WordPress 6.7+, WooCommerce 9+
- Install dependencies: `composer install`

## Quality gates

Run these before committing:

```
composer phpcs
composer phpstan
composer test
# or directly
vendor/bin/phpcs src
vendor/bin/phpunit
```

For frontend JavaScript changes, also run:

```
node --check assets/frontend/block-checkout-date.js
node --check assets/admin/field-builder.js
```

Ensure `git diff --check` reports no whitespace errors.

## Pull request expectations

- One logical change per PR
- Include tests or manual verification notes when affecting checkout rendering, validation, or order storage (HPOS)
- Keep Admin and Frontend changes scoped to their respective assets; do not mix unrelated refactors
