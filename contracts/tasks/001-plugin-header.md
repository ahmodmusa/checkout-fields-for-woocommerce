# Task Contract

## Task ID

001

---

## Task Name

Plugin Header

---

## Objective

Create only the production-ready WordPress plugin header.

---

## Required Reading

contracts/core/000-project-contract.md

contracts/core/001-architecture-contract.md

contracts/core/002-coding-standards.md

contracts/core/003-project-conventions.md

contracts/core/004-review-rules.md

contracts/core/005-task-template.md

---

## Allowed Files

checkout-fields-for-woocommerce.php

---

## Forbidden Files

Everything else.

---

## Requirements

Only update the plugin entry file.

The file must contain only:

- declare(strict_types=1);
- Plugin Header
- ABSPATH protection

Nothing else.

Do not define constants.

Do not load Composer.

Do not register hooks.

Do not create classes.

Do not create services.

Do not create functions.

Do not create activation hooks.

Do not create deactivation hooks.

Do not instantiate anything.

---

## Acceptance Criteria

Plugin appears in WordPress Plugins page.

Plugin activates.

No fatal errors.

Only one file modified.

---

## Commit Message

feat: add plugin header

---

END TASK