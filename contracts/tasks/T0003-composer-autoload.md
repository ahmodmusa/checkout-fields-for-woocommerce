# Task Contract

## Task ID

T0003

---

## Task Name

Composer Autoload

---

## Objective

Support Composer autoloading.

Nothing else.

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

Support Composer autoloading.

If

vendor/autoload.php

exists

require it.

Otherwise

continue safely.

Never produce fatal errors.

Do not instantiate classes.

Do not register hooks.

Do not load services.

Do not create Plugin class.

Do not create Loader.

Do not create functions.

---

## Acceptance Criteria

✓ Composer autoload supported

✓ No fatal error if vendor does not exist

✓ One file modified

---

## Commit Message

feat: add composer autoload support

END TASK