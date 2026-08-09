# Milestone Contract

## Milestone ID

T0016–T0019

---

## Milestone Name

Admin Foundation Completion

---

## Objective

Complete the administration foundation before starting the Field Engine.

After this milestone the administration layer must be considered complete for Phase 1.

The plugin must expose a production-quality WordPress administration foundation.

No checkout functionality.

No field engine.

No rendering engine.

No order meta.

---

# Required Reading

Read every file inside

contracts/core/

Read every file inside

docs/

Read every completed task contract.

---

# Allowed Files

src/Admin/AdminMenu.php

src/Admin/Settings.php

src/Core/HookRegistrar.php

README.md

---

# Forbidden Files

checkout-fields-for-woocommerce.php

src/Plugin.php

src/Services/

src/Fields/

composer.json

contracts/

docs/

tests/

assets/

languages/

Any other file.

---

# Deliverables

Modify only the allowed files.

No new files.

---

# Requirements

## Part 1

Improve the Settings page layout.

Use standard WordPress admin markup.

No custom CSS.

No custom JavaScript.

---

## Part 2

Display settings errors correctly.

Use

settings_errors()

following WordPress best practices.

---

## Part 3

Review capabilities.

Every admin action must require

manage_options

No capability duplication.

---

## Part 4

Review hook registration.

Register only required admin hooks.

No duplicate hooks.

No unnecessary callbacks.

---

## Part 5

Review Settings API.

One settings group.

One section.

One field.

No duplicate registrations.

No dead code.

---

## Part 6

Documentation.

Update README only if current implementation differs.

No architectural documentation changes.

---

# Prohibited

Do NOT

Create checkout fields

Create rendering engine

Create validation

Create saving engine

Create WooCommerce logic

Create AJAX

Create REST API

Create assets

Create JavaScript

Create CSS

Create database tables

Create new classes

Create new files

Change architecture

---

# Acceptance Criteria

✓ Clean admin foundation

✓ No duplicate hooks

✓ One settings group

✓ One settings section

✓ One settings field

✓ Proper capability checks

✓ WordPress Settings API best practices

✓ WPCS

✓ PSR-12

✓ PHP 8.2

✓ No dead code

✓ No placeholder code

---

# Commit Message

feat: finalize admin foundation

---

# Mandatory Review

Review

Scope

Architecture

WordPress Standards

Security

Performance

Maintainability

Documentation

Overall

PASS / FAIL

PHP lint

PHPCS

---

If implementation requires architectural change

STOP

Output exactly

ARCHITECT DECISION REQUIRED

END MILESTONE