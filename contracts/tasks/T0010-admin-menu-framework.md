# Task Contract

## Task ID

T0010

---

## Task Name

Admin Menu Framework

---

## Objective

Create the plugin's first WordPress Admin page.

This task establishes the administration entry point for the plugin.

The page is intentionally minimal.

No settings, forms, fields, saving, or business logic are implemented.

---

## Required Reading

Read every file inside:

contracts/core/

Read every file inside:

docs/

Read all previously completed task contracts.

---

## Allowed Files

src/Admin/AdminMenu.php

src/Core/HookRegistrar.php

---

## Forbidden Files

checkout-fields-for-woocommerce.php

src/Plugin.php

composer.json

docs/

contracts/

tests/

assets/

languages/

Any other file.

---

## Requirements

Create:

src/Admin/AdminMenu.php

Namespace

SCFW\Admin

Create one final class:

AdminMenu

Create exactly two public static methods:

register(): void

render(): void

Responsibilities

register()

- Register one admin menu using WordPress:

add_menu_page()

Requirements

Menu Title

Checkout Fields

Page Title

Checkout Fields

Capability

manage_options

Menu Slug

scfw

Callback

render()

Icon

dashicons-feedback

Position

56

render()

Output exactly:

<div class="wrap">
    <h1>Checkout Fields</h1>
</div>

No other HTML.

No settings.

No forms.

No CSS.

No JavaScript.

No translation functions.

---

Modify

src/Core/HookRegistrar.php

Register exactly one hook:

admin_menu

Callback

SCFW\Admin\AdminMenu::register

Do NOT

- register additional hooks
- create settings
- create options
- enqueue assets
- implement forms
- save data
- call WooCommerce
- create services
- modify bootstrap

---

## Acceptance Criteria

✓ One new Admin class

✓ One admin_menu hook

✓ One menu page

✓ Page renders correctly

✓ No settings

✓ No business logic

✓ WPCS compliant

✓ PSR-12 compliant

---

## Deliverables

src/Admin/AdminMenu.php

src/Core/HookRegistrar.php

---

## Commit Message

feat: add admin menu framework

---

## Review Report

Include

- Scope
- Architecture
- Coding Standards
- Security
- Performance
- WordPress Best Practices
- Overall

PASS / FAIL

---

If implementation requires architectural change

STOP

Output exactly

ARCHITECT DECISION REQUIRED

END TASK