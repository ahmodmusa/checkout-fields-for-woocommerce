# Task Contract

## Task ID

T0009

---

## Task Name

Connect Hook Registration

---

## Objective

Connect the plugin bootstrap to the central Hook Registrar.

This task establishes the application's first internal execution path.

No WordPress hooks are registered in this milestone.

No business logic is implemented.

---

## Required Reading

Read every file inside:

contracts/core/

Read every file inside:

docs/

before implementation.

---

## Allowed Files

checkout-fields-for-woocommerce.php

src/Plugin.php

---

## Forbidden Files

src/Core/HookRegistrar.php

composer.json

assets/

languages/

tests/

docs/

contracts/

Any other file.

---

## Requirements

Modify only:

checkout-fields-for-woocommerce.php

src/Plugin.php

Requirements

Plugin::boot()

must call

SCFW\Core\HookRegistrar::register()

The bootstrap file must remain responsible only for

- loading Composer
- defining constants
- WooCommerce compatibility check
- calling Plugin::boot()

Do NOT

- call HookRegistrar directly from the bootstrap file
- register add_action()
- register add_filter()
- create services
- instantiate objects
- add business logic
- modify HookRegistrar

Maintain existing coding style.

---

## Acceptance Criteria

✓ Plugin::boot() calls HookRegistrar::register()

✓ Bootstrap continues calling only Plugin::boot()

✓ No WordPress hooks registered

✓ No business logic

✓ No side effects

✓ WPCS compliant

✓ PSR-12 compliant

---

## Deliverables

checkout-fields-for-woocommerce.php

src/Plugin.php

---

## Commit Message

feat: connect hook registrar

---

## Review Report

Include

- Scope
- Architecture
- Coding Standards
- Security
- Performance
- Overall

PASS / FAIL

---

If implementation requires architectural change

STOP

Output exactly

ARCHITECT DECISION REQUIRED

END TASK