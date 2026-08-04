# Task Contract

## Task ID

T0007

---

## Task Name

Bootstrap Guard

---

## Objective

Prevent the plugin from booting when WooCommerce is unavailable.

Do not display notices.

Do not deactivate the plugin.

Only stop the bootstrap process.

---

## Required Reading

Read every file inside

contracts/core/

before implementation.

---

## Allowed Files

checkout-fields-for-woocommerce.php

src/Plugin.php

src/Services/WooCommerceChecker.php

---

## Forbidden Files

Everything else.

---

## Requirements

Before calling

Plugin::boot()

verify WooCommerce availability.

Use

WooCommerceChecker::isAvailable()

If WooCommerce is unavailable

return immediately.

Do not call

Plugin::boot()

Do not show admin notices.

Do not deactivate the plugin.

Do not redirect.

Do not die().

Do not exit().

---

## Acceptance Criteria

✓ Plugin boots only when WooCommerce exists

✓ Plugin exits gracefully

✓ No notices

✓ No deactivation

✓ No fatal errors

✓ Only allowed files modified

---

## Commit Message

feat: guard plugin bootstrap

END TASK