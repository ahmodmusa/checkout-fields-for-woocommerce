# Milestone Contract

## Milestone ID

T0024–T0026

---

## Milestone Name

Validation Foundation

---

## Objective

Implement the reusable validation layer for the Field Engine.

This milestone establishes field validation independent of WordPress, WooCommerce, rendering, checkout, or persistence.

Validation must operate only on the immutable Field model.

---

# Required Reading

Read every file inside

contracts/core/

Read every file inside

docs/

Read all completed milestone contracts.

---

# Allowed Files

src/Core/Validation/ValidationResult.php

src/Core/Validation/Validator.php

src/Core/Validation/ValidationError.php

---

# Forbidden Files

Everything else.

---

# Deliverables

Create

src/Core/Validation/

ValidationResult.php

Validator.php

ValidationError.php

---

# Requirements

## Part 1

Create

ValidationError

Immutable value object.

Properties

- code
- message

Constructor injection only.

Getters only.

No setters.

---

## Part 2

Create

ValidationResult

Immutable object.

Properties

- valid
- errors

Methods

- isValid(): bool
- getErrors(): array

No mutable state.

---

## Part 3

Create

Validator

Responsibilities

Validate a Field object together with a submitted value.

Method

validate(
    Field $field,
    mixed $value
): ValidationResult

Validation Rules

- required
- text
- textarea
- email
- tel
- number
- date
- checkbox
- radio
- select

Supported Rules

Required

Email format

Numeric values

Checkbox boolean

Radio option exists

Select option exists

Text values must be scalar

Textarea values must be scalar

Date must be parseable

Return ValidationResult.

Never throw exceptions for user input.

Throw InvalidArgumentException only for programmer errors.

---

# Rules

Do NOT

Use WordPress APIs

Use WooCommerce APIs

Render HTML

Register hooks

Save data

Create admin UI

Create checkout integration

Create AJAX

Create REST API

Create services

Modify existing files

---

# Acceptance Criteria

✓ ValidationResult

✓ ValidationError

✓ Validator

✓ Immutable objects

✓ No WordPress dependency

✓ No WooCommerce dependency

✓ PHP 8.2

✓ Strict Types

✓ PSR-12

✓ WPCS

✓ SOLID

✓ No dead code

✓ No placeholder code

---

# Commit Message

feat: implement validation foundation

---

# Mandatory Review

Review

- Scope
- Architecture
- SOLID
- Validation Logic
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