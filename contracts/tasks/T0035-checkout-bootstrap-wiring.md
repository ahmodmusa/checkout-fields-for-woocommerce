# Milestone Contract

## Milestone ID

T0035

---

## Milestone Name

Checkout Bootstrap Wiring

---

## Objective

Connect the Classic Checkout integration to the plugin lifecycle.

No business logic.

No checkout implementation.

No rendering changes.

No validation changes.

Only bootstrap wiring.

---

# Allowed Files

src/Plugin.php

src/Core/HookRegistrar.php

---

# Forbidden Files

Everything else.

---

# Requirements

1.

Register

CheckoutBootstrap

during plugin boot.

2.

The Frontend bootstrap must register all Classic Checkout hooks.

3.

Do NOT move business logic.

4.

Do NOT duplicate hook registration.

5.

Do NOT modify Core Field, Validation, Rendering, or Admin.

---

# Acceptance Criteria

✓ CheckoutBootstrap::register() executes

✓ WooCommerce hooks registered

✓ No duplicate hooks

✓ Plugin activates

✓ PHP lint

✓ PHPCS

✓ Live WooCommerce verification

---

Commit Message

feat: wire classic checkout bootstrap