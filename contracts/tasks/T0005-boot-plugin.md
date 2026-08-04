# Task Contract

## Task ID

T0005

---

## Task Name

Boot Plugin Kernel

---

## Objective

Boot the plugin kernel from the root plugin file.

This task only connects the bootstrap file with the Plugin kernel.

No additional functionality may be implemented.

---

## Required Reading

Read every contract inside

contracts/core/

before implementation.

---

## Allowed Files

checkout-fields-for-woocommerce.php

---

## Forbidden Files

src/Plugin.php

composer.json

assets/

languages/

tests/

docs/

contracts/

Any other file.

---

## Requirements

Import the Plugin class.

Use

use SCFW\Plugin;

After

- ABSPATH protection
- Composer autoload
- Plugin constants

Call

Plugin::boot();

Nothing else.

Do NOT

Instantiate the plugin.

Create services.

Register hooks.

Create activation hooks.

Create deactivation hooks.

Create helper functions.

Create global variables.

Load WooCommerce.

Add business logic.

Modify Plugin.php.

---

## Acceptance Criteria

✓ Plugin boots successfully

✓ Plugin::boot() is called

✓ No fatal errors

✓ No hooks registered

✓ No services loaded

✓ No business logic

✓ Only one file modified

✓ WPCS compliant

✓ PSR-12 compliant

---

## Deliverables

checkout-fields-for-woocommerce.php

---

## Commit Message

feat: boot plugin kernel

---

## Review Report

Include

Scope

Architecture

Coding Standards

Security

Performance

Overall

PASS / FAIL

---

If implementation requires any architectural decision

STOP

Output

ARCHITECT DECISION REQUIRED

Do not continue.

END TASK