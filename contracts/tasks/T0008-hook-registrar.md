# Task Contract

## Task ID

T0008

---

## Task Name

Hook Registrar

---

## Objective

Create the application's central hook registrar.

This class will become the single place responsible for registering WordPress hooks.

No hooks are registered in this milestone.

This task creates only the architectural entry point.

---

## Required Reading

Read every file inside:

contracts/core/

Read every file inside:

docs/

before implementation.

---

## Allowed Files

src/Core/HookRegistrar.php

---

## Forbidden Files

checkout-fields-for-woocommerce.php

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

Create

src/Core/HookRegistrar.php

Namespace

SCFW\Core

Class

HookRegistrar

Class must be

final

Create exactly one public static method

register(): void

Method body must remain empty.

Do NOT

- call add_action()
- call add_filter()
- instantiate classes
- register services
- reference WooCommerce
- reference Admin
- reference Checkout
- create properties
- create constructors
- create helper methods

Use

declare(strict_types=1);

Include proper PHPDoc.

---

## Acceptance Criteria

✓ One class created

✓ Namespace correct

✓ Final class

✓ One public static register(): void

✓ Empty implementation

✓ No WordPress hooks

✓ No side effects

✓ WPCS compliant

✓ PSR-12 compliant

---

## Deliverables

src/Core/HookRegistrar.php

---

## Commit Message

feat: add hook registrar

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