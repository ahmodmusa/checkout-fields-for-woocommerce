# Task Contract

## Task ID

T0011

---

## Task Name

Settings Page

---

## Objective

Implement the first plugin administration page.

This task replaces the temporary placeholder page created in T0010.

The page remains read-only.

No settings registration, saving, validation, or business logic are implemented.

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

---

## Forbidden Files

checkout-fields-for-woocommerce.php

src/Core/HookRegistrar.php

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

Modify only:

src/Admin/AdminMenu.php

Keep

register(): void

unchanged.

Modify

render(): void

The page must render exactly:

- WordPress wrapper

```
<div class="wrap">
```

- H1

```
Checkout Fields
```

- One description paragraph

```
Welcome to Checkout Fields for WooCommerce.
```

- One horizontal rule

```
<hr>
```

- One H2

```
Plugin Information
```

- One table using the WordPress

```
widefat
```

class.

The table must contain:

Plugin

Checkout Fields for WooCommerce

Version

SCFW_VERSION

PHP

PHP_VERSION

WordPress

get_bloginfo( 'version' )

WooCommerce

Active / Not Active

Determine WooCommerce status using:

SCFW\Services\WooCommerceChecker::isAvailable()

No forms.

No buttons.

No settings.

No saving.

No JavaScript.

No CSS.

No inline styles.

Escape all output.

Use translation functions for all user-facing strings.

---

## Acceptance Criteria

✓ Existing menu remains unchanged

✓ Settings page renders correctly

✓ Version information displayed

✓ WooCommerce status displayed

✓ No forms

✓ No persistence

✓ WPCS compliant

✓ PSR-12 compliant

---

## Deliverables

src/Admin/AdminMenu.php

---

## Commit Message

feat: add settings page

---

## Review Report

Include

- Scope
- Architecture
- Coding Standards
- Security
- Performance
- Translation
- WordPress Best Practices
- Overall

PASS / FAIL

---

If implementation requires architectural change

STOP

Output exactly

ARCHITECT DECISION REQUIRED

END TASK