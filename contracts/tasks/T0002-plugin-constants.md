# Task Contract

## Task ID

T0002

---

## Task Name

Plugin Constants

---

## Objective

Define the global plugin constants.

No other functionality.

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

Define the constants only if they are not already defined.

Use defensive programming.

Create exactly these constants:

SCFW_VERSION

SCFW_FILE

SCFW_PATH

SCFW_URL

Values:

SCFW_VERSION → plugin version

SCFW_FILE → __FILE__

SCFW_PATH → plugin_dir_path( __FILE__ )

SCFW_URL → plugin_dir_url( __FILE__ )

---

## Rules

Do not

Create functions.

Create classes.

Load Composer.

Register hooks.

Create services.

Create activation hooks.

Create deactivation hooks.

Create globals.

Create namespaces.

---

## Acceptance Criteria

✓ Constants defined

✓ No duplicate definitions

✓ Plugin activates

✓ No fatal errors

✓ One file modified

---

## Commit Message

feat: define plugin constants

END TASK