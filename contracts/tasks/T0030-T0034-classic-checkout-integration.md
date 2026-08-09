# Milestone Contract

## Milestone ID

T0030–T0034

---

## Milestone Name

Classic Checkout Integration

---

## Objective

Implement the complete Classic WooCommerce Checkout integration.

This milestone connects the existing Foundation (Field, Validation, Rendering)
to the WooCommerce Classic Checkout lifecycle.

No Block Checkout.

No Admin Builder.

No Conditional Logic.

No AJAX.

No REST API.

---

# Required Reading

Read every file inside

docs/

contracts/core/

Read all completed milestone contracts.

Read all implemented source code.

---

# Allowed Files

src/Frontend/ClassicCheckout.php

src/Frontend/ClassicRenderer.php

src/Frontend/ClassicValidator.php

src/Frontend/ClassicSaver.php

src/Frontend/CheckoutBootstrap.php

---

# Forbidden Files

Everything else.

---

# Deliverables

Create

src/Frontend/

ClassicCheckout.php

ClassicRenderer.php

ClassicValidator.php

ClassicSaver.php

CheckoutBootstrap.php

---

# Responsibilities

## CheckoutBootstrap

Register every WooCommerce Classic Checkout hook.

No business logic.

No rendering.

No validation.

Only hook registration.

---

## ClassicRenderer

Render fields during Classic Checkout.

Must use

Renderer

RenderContext

RenderResult

No duplicated rendering logic.

---

## ClassicValidator

Use the shared

Validator

ValidationResult

No validation duplication.

---

## ClassicSaver

Save validated values into WooCommerce order meta.

Use

Meta Prefix

_scfw_

No custom tables.

---

## ClassicCheckout

Coordinator.

Wire together

Renderer

Validator

Saver

No duplicated business logic.

---

# WooCommerce Hooks

Register only the required hooks.

Render

Validate

Save

Nothing else.

---

# Rules

Do NOT

Implement Block Checkout

Create admin UI

Create field builder

Create AJAX

Create REST API

Create settings

Create services

Modify Core

Modify Validation

Modify Rendering

Modify Field Engine

---

# Acceptance Criteria

✓ Classic Checkout fields render

✓ Validation works

✓ Values save

✓ Order meta created

✓ Shared Validator reused

✓ Shared Renderer reused

✓ No duplicated logic

✓ PHP 8.2

✓ Strict Types

✓ PSR-12

✓ WPCS

✓ SOLID

✓ WooCommerce Classic only

---

# Commit Message

feat: implement classic checkout integration

---

# Mandatory Review

Review

- Scope

- Architecture

- WooCommerce Integration

- SOLID

- Performance

- Security

- Maintainability

- Future Extensibility

- Overall

PASS / FAIL

PHP lint

PHPCS

Live WooCommerce verification required.

---

If implementation requires architecture changes

STOP

Output exactly

ARCHITECT DECISION REQUIRED

END MILESTONE