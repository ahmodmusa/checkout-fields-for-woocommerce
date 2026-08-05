# Milestone Contract

## Milestone ID

T0020–T0023

---

## Milestone Name

Field Engine Foundation

---

## Objective

Implement the complete domain model for checkout fields.

This milestone establishes the core Field Engine used by every future component of the plugin.

After this milestone the project must have a reusable, immutable field model independent of WordPress and WooCommerce.

No rendering.

No validation.

No persistence.

No checkout integration.

No admin UI.

---

# Required Reading

Read every file inside

contracts/core/

Read every file inside

docs/

Read all completed milestone contracts.

---

# Allowed Files

src/Core/Field/Field.php

src/Core/Field/FieldType.php

src/Core/Field/FieldFactory.php

src/Core/Field/DefaultFields.php

---

# Forbidden Files

Everything else.

---

# Deliverables

Create

src/Core/Field/

Field.php

FieldType.php

FieldFactory.php

DefaultFields.php

---

# Requirements

## Part 1

Create

enum FieldType : string

Supported types

- text
- textarea
- email
- tel
- number
- date
- checkbox
- radio
- select

No helper methods.

---

## Part 2

Create

Field

as an immutable value object.

Properties

- id
- label
- type
- required
- placeholder
- description
- options
- defaultValue

All properties must be private.

Constructor injection only.

Getters only.

No setters.

No mutable state.

---

## Part 3

Create

FieldFactory

Responsibilities

- create Field objects
- validate constructor arguments
- throw InvalidArgumentException when invalid

The factory must not depend on WordPress.

The factory must not depend on WooCommerce.

---

## Part 4

Create

DefaultFields

Responsibilities

Return

array<Field>

Initially

return an empty array.

No placeholder methods.

No TODO.

---

# Prohibited

Do NOT

- render HTML
- use WordPress APIs
- use WooCommerce APIs
- register hooks
- save data
- validate checkout input
- sanitize checkout input
- create services
- create repositories
- create AJAX
- create REST API

---

# Acceptance Criteria

✓ Immutable Field object

✓ FieldType enum

✓ Factory

✓ DefaultFields provider

✓ PHP 8.2

✓ Strict Types

✓ PSR-12

✓ WPCS

✓ SOLID

✓ No WordPress dependency

✓ No WooCommerce dependency

✓ No dead code

✓ No placeholder code

---

# Commit Message

feat: implement field engine foundation

---

# Mandatory Review

Review

- Scope
- Architecture
- SOLID
- Immutability
- Performance
- Maintainability
- Future Extensibility
- Overall

PASS / FAIL

PHP lint

PHPCS

---

If implementation requires architectural change

STOP

Output exactly

ARCHITECT DECISION REQUIRED

END MILESTONE