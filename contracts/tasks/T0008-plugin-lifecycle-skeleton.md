# Task Contract

## Task ID

T0008

---

## Task Name

Plugin Lifecycle Skeleton

---

## Objective

Create the permanent lifecycle skeleton for the Plugin kernel.

This task defines the application's startup sequence.

Do not implement any business logic.

Do not register hooks.

Do not register services.

Only create the lifecycle structure.

---

## Required Reading

Read every file inside

contracts/core/

before implementation.

---

## Allowed Files

src/Plugin.php

---

## Forbidden Files

checkout-fields-for-woocommerce.php

composer.json

assets/

languages/

tests/

docs/

contracts/

Any other file.

---

## Requirements

Modify

src/Plugin.php

Keep the class

final

Keep

boot()

public static

Inside boot()

call the following methods in this exact order

registerServices()

registerHooks()

bootAdmin()

bootFrontend()

bootBlocks()

Create exactly these five methods.

Each method must

be private static

return void

have an empty body

Do not add

constructors

properties

constants

services

hooks

WordPress APIs

WooCommerce APIs

business logic

comments inside methods

TODO markers

placeholder code

---

## Lifecycle Order

boot()

↓

registerServices()

↓

registerHooks()

↓

bootAdmin()

↓

bootFrontend()

↓

bootBlocks()

This order is permanent.

Future tasks may implement the methods.

They must never change the order.

---

## Acceptance Criteria

✓ Plugin class updated

✓ boot() calls five lifecycle methods

✓ Five private static methods exist

✓ Empty implementations

✓ No business logic

✓ No hooks

✓ No services

✓ WPCS compliant

✓ PSR-12 compliant

---

## Deliverables

src/Plugin.php

---

## Commit Message

feat: add plugin lifecycle skeleton

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

If any architectural uncertainty exists

STOP

Output

ARCHITECT DECISION REQUIRED

Do not continue.

END TASK