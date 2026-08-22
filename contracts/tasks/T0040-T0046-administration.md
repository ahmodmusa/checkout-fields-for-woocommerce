# Phase 4 — Administration

Version: 1.0.0

Status: Implementation Contract

---

# Purpose

Implement the complete administration layer for managing checkout fields.

The existing Admin Foundation and Settings API must be reused.

This phase introduces:

- Settings-driven field configuration
- Field management
- Field ordering
- Import
- Export

The administration layer must control the existing Core Field system without
moving business logic into the Admin layer.

---

# Required Reading

Before implementation, read:

- docs/
- contracts/core/
- contracts/tasks/
- src/Admin/
- src/Core/Field/
- src/Core/Validation/
- src/Core/Rendering/
- src/Frontend/
- CHANGELOG.md
- README.md

Also inspect the current implementation of:

- AdminMenu
- Settings
- DefaultFields
- Field
- FieldFactory
- Classic Checkout
- Block Checkout
- Admin Order Display

---

# Important Existing State

The following already exist and MUST NOT be recreated:

- Plugin admin menu
- Settings API foundation
- `scfw_options`
- `enabled` setting
- Core Field model
- FieldType
- FieldFactory
- DefaultFields
- Classic Checkout integration
- Block Checkout integration
- Admin Order Display

The implementation must extend these foundations.

---

# Phase 4 Tasks

T0040 Settings Page
T0041 Settings Sections
T0042 Settings Fields
T0043 Field Builder
T0044 Drag & Drop Ordering
T0045 Import
T0046 Export

---

# Architecture

Admin is a presentation/configuration layer.

Admin code MUST NOT contain:

- checkout business rules
- validation rules
- rendering rules
- persistence logic for checkout orders
- WooCommerce checkout hooks

Admin configuration must produce field definitions/configuration that the
existing Core and Frontend layers can consume.

---

# T0040 — Settings Page

Reuse the existing Settings page.

Do not create a second settings page.

The existing admin menu slug remains:

scfw

Capability:

manage_options

---

# T0041 — Settings Sections

Create the administration sections required for field management.

At minimum:

## General

Existing plugin-level settings.

## Checkout Fields

Configuration of checkout fields.

No duplicate Settings API registration.

---

# T0042 — Settings Fields

The administrator must be able to configure checkout fields.

Each field configuration must support at minimum:

- ID
- Label
- Type
- Required
- Enabled

The configuration must be stored through the existing WordPress Settings API.

Use the existing:

scfw_options

option.

Do not create a second options system.

---

# T0043 — Field Builder

Create an admin interface for managing checkout fields.

The administrator must be able to:

- view fields
- enable/disable fields
- edit field label
- select field type
- configure required state
- add supported fields
- remove configurable fields

The UI must use standard WordPress admin components where possible.

No unnecessary custom framework.

---

# Field Types

The builder must use the existing Core `FieldType` enum.

Do NOT create a second field-type taxonomy.

Supported types must correspond to the existing Core field types.

If a field type is not technically supported by the current checkout adapter,
do not silently invent support.

Document the limitation.

---

# T0044 — Drag & Drop Ordering

Allow administrators to reorder checkout fields.

The stored order must be deterministic.

The same configured order must be consumed by:

- Classic Checkout
- Block Checkout

Do not create separate ordering systems for Classic and Block.

---

# T0045 — Import

Provide an admin Import facility.

Import must:

- accept the defined field configuration format
- validate the imported structure
- sanitize imported values
- reject malformed configurations
- preserve the field configuration schema
- require appropriate administrator capability
- use WordPress nonce protection

Import must NOT directly execute arbitrary PHP or code.

---

# T0046 — Export

Provide an admin Export facility.

Export must:

- export the current field configuration
- use a stable documented format
- be deterministic
- require appropriate administrator capability
- use WordPress nonce protection
- escape/encode output correctly

The exported configuration must be importable again.

---

# Security

All admin actions must:

- require `manage_options`
- use WordPress nonces where applicable
- sanitize input
- validate structure
- escape output
- prevent arbitrary code execution
- prevent unauthorized modification

Do not trust imported configuration.

---

# Data Model

Reuse:

scfw_options

Do not create custom database tables.

Do not introduce a second settings option unless an explicit architecture
decision is required.

The field configuration schema must be documented.

---

# Checkout Integration

The administration layer must NOT duplicate checkout logic.

After configuration:

Classic Checkout and Block Checkout must consume the same logical
configuration.

The existing:

- Field
- FieldFactory
- Validator
- Renderer

architecture remains authoritative.

---

# Compatibility

The existing hardcoded:

delivery_instructions

field is the initial configured field.

Do not break existing orders.

Do not change:

_scfw_delivery_instructions

Existing saved order metadata must remain readable.

---

# Backward Compatibility

Existing:

scfw_options

data must remain valid.

Existing `enabled` behavior must continue working.

Existing Classic Checkout behavior must remain functional.

Existing Block Checkout behavior must remain functional.

---

# UI Requirements

Use native WordPress admin UI patterns.

The interface should include:

- field list/table
- Add Field action
- Edit action
- Enable/disable control
- Delete/remove action where permitted
- field ordering
- Save Changes
- Import
- Export

Avoid introducing a frontend framework unless technically required.

---

# AJAX / REST

Do not introduce AJAX or REST merely for convenience.

Use normal WordPress admin form submission unless drag-and-drop ordering
technically requires a dedicated request mechanism.

If AJAX/REST becomes necessary, use WordPress nonce and capability checks.

---

# Validation

Admin configuration validation is separate from checkout value validation.

Do not mix:

Admin field configuration validation

with:

Customer checkout value validation.

The existing Core Validator remains responsible for customer checkout values.

---

# Acceptance Criteria

## Settings

✓ Existing Settings page reused

✓ No duplicate settings page

✓ General settings continue working

✓ Checkout Fields section exists

✓ Settings save correctly

---

## Field Builder

✓ Existing fields can be configured

✓ Label can be changed

✓ Type can be selected from FieldType

✓ Required state can be changed

✓ Enabled state can be changed

✓ Supported fields can be added

✓ Configured fields can be removed where allowed

---

## Ordering

✓ Fields can be reordered

✓ Order persists

✓ Classic Checkout uses the configured order

✓ Block Checkout uses the configured logical order where technically possible

---

## Import

✓ Valid configuration imports

✓ Invalid configuration is rejected

✓ Input is sanitized

✓ Capability protected

✓ Nonce protected

✓ No arbitrary code execution

---

## Export

✓ Configuration exports

✓ Export format is deterministic

✓ Export can be imported again

✓ Capability protected

✓ Nonce protected

---

## Compatibility

✓ Existing Delivery Instructions continues working

✓ Existing Classic Checkout continues working

✓ Existing Block Checkout continues working

✓ Existing order meta remains readable

---

# Quality Gates

Required:

- PHP 8.2
- strict types
- PSR-4
- PSR-12
- WPCS
- PHP lint
- PHPCS

PHPStan may be reported separately if external WordPress/WooCommerce stubs
are still unavailable.

---

# Live Verification

Use the real LocalWP WordPress + WooCommerce installation.

Verify:

1. Open Settings.
2. Existing settings still work.
3. Add/configure a checkout field.
4. Change its label.
5. Enable/disable it.
6. Change its order.
7. Save.
8. Open Classic Checkout.
9. Verify configuration is reflected.
10. Open Block Checkout.
11. Verify configuration is reflected where technically supported.
12. Export configuration.
13. Change configuration.
14. Import the previous export.
15. Verify configuration is restored.
16. Place a real order.
17. Verify the configured field is saved.
18. Open Admin Order.
19. Verify the saved value.

---

# Architecture Protection

Do NOT modify:

- Project Vision
- frozen architecture
- Core Field model
- FieldType taxonomy
- Core Validator
- Core Renderer contracts
- Classic Checkout business logic
- Block Checkout business logic

unless an actual architectural incompatibility is discovered.

If architecture must change:

STOP

Output exactly:

ARCHITECT DECISION REQUIRED

---

# Documentation

Update documentation only after implementation is verified.

Document:

- field configuration schema
- import/export format
- supported field types
- known Block Checkout type mappings
- admin configuration behavior

Do not document future/unimplemented features.

---

# Completion Criteria

Phase 4 is complete only when:

- T0040–T0046 requirements are implemented
- Settings work
- Field Builder works
- Ordering works
- Import works
- Export works
- Classic Checkout still works
- Block Checkout still works
- Admin Order Display still works
- Security review passes
- PHPCS passes
- PHP lint passes
- Live verification passes
- Documentation is current

---

# Suggested Commit

feat: implement administration field management

END PHASE 