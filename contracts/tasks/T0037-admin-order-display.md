# Milestone Contract

## Milestone ID

T0037

---

## Milestone Name

Admin Order Display

---

## Objective

Display saved SCFW checkout field values on the WooCommerce Admin Order Edit screen.

This milestone documents the Admin Order Display implementation that already exists.

No new implementation.

No additional features.

---

## Scope

- WooCommerce Admin Order Edit screen
- HPOS-compatible order access
- `_scfw_` checkout field meta
- Checkout Fields section
- Capability protection
- Escaped output
- Suppression when there are no saved values

---

## Current Implementation

Documentation of the existing implementation:

- src/Admin/OrderDisplay.php

- src/Core/HookRegistrar.php

---

### OrderDisplay

- Fires on

woocommerce_admin_order_data_after_order_details

- Reads saved checkout field values from WooCommerce order meta

- Uses the shared DefaultFields provider for field definitions

- Maps each field to its meta key using the shared prefix

_scfw_

- Renders a

Checkout Fields

section on the Admin Order Edit screen

- Displays only fields that have a saved value

- Contains no save, validation, or rendering duplication

---

## Order Data Access

The display reads order data through the WooCommerce order object and

get_meta()

so the same code works with HPOS and the legacy post storage.

This implementation does NOT use

wp_postmeta

and does NOT require the legacy post tables.

---

## Hook Registration

- src/Core/HookRegistrar.php

registers

OrderDisplay::register()

- Registration only happens when WooCommerce is active

- Hook registration stays centralized through HookRegistrar

- The implementation supports both order storage modes

---

## Security

The existing implementation checks the capability:

edit_shop_orders

The section only renders when the current user can manage orders.

Every dynamic value is escaped before output.

Output escaping used

- esc_html_e()

- esc_html()

No raw values are printed.

---

## Empty Values

When no SCFW checkout field has a saved value:

- Fields with an empty value are omitted

- The Checkout Fields section is not displayed

An order without saved checkout field values shows no empty section.

---

## HPOS

The implementation must work with WooCommerce HPOS:

- Reads meta through the WooCommerce order object

- Uses the custom order tables when enabled

- Does not touch legacy wp_postmeta

- Does not bypass WooCommerce order APIs

---

## Acceptance Criteria

✓ Saved checkout field appears in Admin Order Edit

✓ _scfw_ meta is read through WooCommerce order APIs

✓ HPOS works

✓ Capability protection exists

✓ Output is escaped

✓ Empty orders do not show an empty section

✓ No checkout save logic is duplicated

✓ No Block Checkout logic

✓ No AJAX

✓ No REST

---

## Verification

Live verification has already been completed:

- WordPress 7.0.2
- WooCommerce 11.0.0
- HPOS enabled
- Real order tested
- Delivery Instructions displayed
- Test value displayed
- Empty order suppressed

---

## Architecture

- Admin presentation remains in src/Admin/

- Hook registration remains centralized through HookRegistrar

- No new architecture is introduced

- No new layers

- No new directories

- No new services

---

## Commit Message

docs: document admin order display

---

END OF CONTRACT