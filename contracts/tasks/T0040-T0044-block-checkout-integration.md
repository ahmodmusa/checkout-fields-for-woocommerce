# Milestone Contract

## Milestone ID

T0040–T0044

## Milestone Name

Block Checkout Integration

---

## Objective

Implement the complete WooCommerce Block Checkout integration using the
existing Field, Validation, and Rendering foundations.

The implementation must provide the same checkout-field behavior as
Classic Checkout wherever technically possible.

---

## Required Reading

Before implementation, read:

- docs/
- contracts/core/
- contracts/tasks/
- src/Core/Field/
- src/Core/Validation/
- src/Core/Rendering/
- src/Frontend/
- src/Admin/

Read the existing Classic Checkout implementation carefully.

---

## Scope

This milestone covers:

1. Block Checkout field registration
2. Block Checkout field rendering
3. Field value collection
4. Shared validation
5. Shared saving
6. HPOS-compatible order meta persistence
7. End-to-end live verification

---

## Architecture

Block Checkout must be implemented as a WooCommerce integration layer.

The existing Core layer remains unchanged.

Reuse:

- Field
- FieldType
- Validator
- ValidationResult
- Renderer
- RenderContext
- RenderResult

Do not duplicate business rules.

Classic Checkout and Block Checkout must use the same field definitions,
validation rules, and persistence conventions whenever technically possible.

---

## Required Behavior

The existing field:

ID:

delivery_instructions

Type:

textarea

Label:

Delivery Instructions

Required:

No

Meta key:

_scfw_delivery_instructions

must work on WooCommerce Block Checkout.

---

## Rules

Do NOT:

- modify the Field model
- modify FieldType
- modify Core Validation
- modify Core Rendering
- change the Classic Checkout pipeline
- create an Admin Field Builder
- create Conditional Logic
- create AJAX endpoints
- create custom REST endpoints
- create custom database tables
- change the `_scfw_` meta convention

---

## WooCommerce Compatibility

Use the official WooCommerce Blocks extension APIs.

Do not implement Block Checkout by pretending it is Classic Checkout.

Do not rely on:

- `woocommerce_after_order_notes`
- `woocommerce_after_checkout_validation`
- `woocommerce_checkout_create_order`

for the Block Checkout field.

The Block Checkout integration must use the appropriate WooCommerce Blocks
extension/checkout mechanisms.

---

## Acceptance Criteria

### Rendering

- Block Checkout displays Delivery Instructions.
- Field is usable by the customer.
- Field is part of the checkout form lifecycle.

### Submission

- Customer-entered value reaches the server correctly.

### Validation

- Shared Core Validator is reused.
- Validation behavior matches Classic Checkout.

### Saving

- Value is stored as:

_scfw_delivery_instructions

- Storage works with HPOS.
- No legacy `wp_postmeta` dependency.

### Compatibility

- Classic Checkout remains functional.
- Block Checkout works independently.
- No duplicate business logic.

### Quality

- PHP 8.2
- Strict types
- PSR-12
- WPCS
- PHP lint
- PHPCS

---

## Live Verification

Use the real LocalWP WordPress + WooCommerce installation.

Verify:

1. Open the Block Checkout page.
2. Confirm Delivery Instructions is visible.
3. Enter:

BLOCK CHECKOUT LIVE TEST

4. Complete checkout.
5. Confirm order creation.
6. Confirm:

_scfw_delivery_instructions

exists in the HPOS order meta.
7. Confirm:

$order->get_meta( '_scfw_delivery_instructions', true )

returns the submitted value.
8. Confirm Classic Checkout still works.
9. Confirm the Admin Order Display shows the value.

---

## Empty Value

A blank optional field must not cause checkout failure.

Admin display must continue suppressing the Checkout Fields section when
there are no saved SCFW values.

---

## Security

- Use WooCommerce's established checkout security mechanisms.
- Sanitize and validate submitted values.
- Escape displayed output.
- Do not expose checkout data publicly.
- Do not add unnecessary endpoints.

---

## Performance

Avoid unnecessary queries.

Do not introduce polling.

Do not introduce client-side loops.

Use WooCommerce's native Block Checkout extension lifecycle.

---

## Review Requirements

The implementation requires review for:

- Scope
- Architecture
- WooCommerce Blocks API usage
- Classic/Block parity
- Rendering
- Validation
- Saving
- HPOS
- Security
- Performance
- Maintainability

---

## Mandatory Live Test

Static analysis alone is insufficient.

The milestone is not complete until a real Block Checkout order is successfully
created and the submitted value is verified in HPOS order meta.

---

## Commit Message

feat: implement block checkout integration

---

If the official WooCommerce Blocks API required by this milestone is not
available in the installed WooCommerce version, STOP and report the exact
limitation.

If implementation requires changing frozen architecture:

STOP

Output exactly:

ARCHITECT DECISION REQUIRED

END MILESTONE