# Milestone Contract

## Milestone ID

T0036

---

## Milestone Name

Classic Checkout First Field

---

## Objective

Implement the first fully working custom checkout field for WooCommerce Classic Checkout.

This milestone must demonstrate the complete end-to-end checkout pipeline:

Field Definition
→ Render
→ Validation
→ Save
→ Order Meta

The field is hardcoded for demonstration purposes.

No field builder.

No dynamic settings.

No Block Checkout.

---

# Required Reading

Read all files in:

- docs/
- contracts/core/
- contracts/tasks/

Read all previously completed milestones.

---

# Allowed Files

src/Core/Field/DefaultFields.php

src/Frontend/ClassicCheckout.php

src/Frontend/ClassicRenderer.php

src/Frontend/ClassicValidator.php

src/Frontend/ClassicSaver.php

---

# Forbidden Files

Everything else.

---

# Deliverables

Implement exactly ONE hardcoded field.

Field

ID:
delivery_instructions

Label:
Delivery Instructions

Type:
textarea

Required:
No

Meta key:
_scfw_delivery_instructions

---

# Requirements

## DefaultFields

Return one immutable Field object.

No settings.

No database.

No admin configuration.

---

## Renderer

Render the textarea on Classic Checkout.

Reuse the existing Core Rendering contracts.

---

## Validator

Reuse the shared Validator.

No duplicated validation logic.

---

## Saver

Save the submitted value into WooCommerce order meta.

Use

_scfw_delivery_instructions

---

## Checkout

Wire the complete pipeline.

---

# Rules

Do NOT

- implement Block Checkout
- create Admin Builder
- create dynamic fields
- create AJAX
- create REST
- modify Core Validation
- modify Core Rendering
- modify Field model
- create services

---

# Acceptance Criteria

✓ Checkout page displays one textarea

✓ Customer can type a value

✓ Validation passes

✓ Order stores

_scfw_delivery_instructions

✓ Order meta exists

✓ No duplicated logic

✓ PHP 8.2

✓ Strict Types

✓ PSR-12

✓ WPCS

✓ Live WooCommerce verification required

---

# Commit Message

feat: add first classic checkout field

---

# Mandatory Review

Review

- Scope
- Architecture
- WooCommerce Integration
- Validation
- Rendering
- Saving
- Security
- Performance
- Maintainability
- Overall

PASS / FAIL

PHP lint

PHPCS

Live WooCommerce verification required.

If architecture changes are required

STOP

Output exactly

ARCHITECT DECISION REQUIRED

END MILESTONE