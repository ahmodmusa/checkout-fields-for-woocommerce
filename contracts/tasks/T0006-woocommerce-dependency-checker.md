# Task Contract

## Task ID

T0006

---

## Task Name

WooCommerce Dependency Checker

---

## Objective

Create a reusable service responsible for checking whether WooCommerce is available.

This task creates the service only.

It must NOT be used yet.

Integration will happen in a future task.

---

## Required Reading

Read every file inside

contracts/core/

before implementation.

---

## Allowed Files

src/Services/WooCommerceChecker.php

composer.json (ONLY if autoload update is required)

---

## Forbidden Files

checkout-fields-for-woocommerce.php

src/Plugin.php

assets/

languages/

tests/

docs/

contracts/

Any other file.

---

## Requirements

Create

src/Services/WooCommerceChecker.php

Namespace

SCFW\Services

Class

WooCommerceChecker

Class must be

final

Create exactly one public static method

isAvailable(): bool

The method must ONLY determine whether WooCommerce is active.

Use WordPress/WooCommerce best practices.

Do not show notices.

Do not deactivate the plugin.

Do not load WooCommerce.

Do not register hooks.

Do not perform redirects.

Do not cache results.

Do not throw exceptions.

Return only

true

or

false

---

## Acceptance Criteria

✓ One class created

✓ One public static method

✓ Returns bool

✓ No side effects

✓ No hooks

✓ No notices

✓ No plugin integration

✓ WPCS compliant

✓ PSR-12 compliant

---

## Deliverables

src/Services/WooCommerceChecker.php

---

## Commit Message

feat: add WooCommerce dependency checker

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