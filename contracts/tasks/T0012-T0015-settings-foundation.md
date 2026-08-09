# Milestone Contract

## Milestone ID

T0012–T0015

---

## Milestone Name

Settings Foundation

---

## Objective

Implement the complete WordPress Settings API foundation for the plugin.

After this milestone the plugin must have a fully working administration page capable of storing one real setting using the native WordPress Settings API.

No checkout functionality is implemented.

No WooCommerce business logic is implemented.

---

# Required Reading

Read every file inside

contracts/core/

Read every file inside

docs/

Read all completed task contracts.

---

# Allowed Files

src/Admin/AdminMenu.php

src/Admin/Settings.php (new)

src/Core/HookRegistrar.php

---

# Forbidden Files

checkout-fields-for-woocommerce.php

src/Plugin.php

composer.json

tests/

assets/

languages/

docs/

contracts/

Any other file.

---

# Deliverables

Create

src/Admin/Settings.php

Modify

src/Admin/AdminMenu.php

Modify

src/Core/HookRegistrar.php

---

# Implementation Requirements

## Part 1

Create

SCFW\Admin\Settings

Final class.

---

## Part 2

Register one WordPress settings group.

Group

scfw

Option

scfw_options

Arguments

type => array

default => array()

show_in_rest => false

autoload => true

---

## Part 3

Register exactly one settings section.

ID

scfw_general

Title

General Settings

---

## Part 4

Register exactly one settings field.

ID

enabled

Label

Enable Plugin

Store

scfw_options['enabled']

---

## Part 5

Render the field.

Checkbox only.

Checked value

1

Unchecked

0

Escape all output.

No JavaScript.

No CSS.

---

## Part 6

Update AdminMenu::render()

The page must display

settings_errors()

settings_fields()

do_settings_sections()

submit_button()

inside a normal WordPress form.

Method

POST

action="options.php"

No additional UI.

---

## Part 7

Hook Registration

Register the Settings API using

admin_init

inside

HookRegistrar

The callback must call

Settings::register()

No additional hooks.

---

# Prohibited

Do NOT

Create checkout fields

Create WooCommerce logic

Create business logic

Create services

Create repositories

Create containers

Create assets

Create AJAX

Create REST API

Create validation beyond WordPress Settings API

---

# Acceptance Criteria

✓ Admin page opens correctly

✓ Settings API registered

✓ One settings section

✓ One checkbox field

✓ Setting persists

✓ WordPress nonce handled through Settings API

✓ No PHP warnings

✓ WPCS compliant

✓ PSR-12 compliant

✓ PHP 8.2 compatible

---

# Architecture Validation

Must respect

Project

Architecture

Directory Structure

Class Diagram

Request Lifecycle

Data Model

Development Workflow

No architectural changes allowed.

---

# Deliverables

src/Admin/Settings.php

src/Admin/AdminMenu.php

src/Core/HookRegistrar.php

---

# Commit Message

feat: implement settings foundation

---

# Mandatory Review

Review

Scope

Architecture

WordPress Standards

Security

Performance

Translation

Maintainability

Overall

PASS / FAIL

PHP lint

PHPCS

---

If any architectural change becomes necessary

STOP

Output exactly

ARCHITECT DECISION REQUIRED

---

END OF MILESTONE