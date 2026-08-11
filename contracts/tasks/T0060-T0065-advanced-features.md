# Phase 6 — Advanced Features

Version: 1.0.0

Status: Proposed Planning Contract

---

# Objective

Provide advanced workflow capabilities for conditional field visibility while
preserving the frozen Phase 1–5 architecture.

Phase 6 uses six tasks. The roadmap's Dynamic Visibility milestone is the
visibility capability produced by T0060. Cart Rules and AJAX Refresh are
combined in T0065 because cart-dependent rules require refresh behavior.

---

# Frozen Baseline

Phase 6 depends on the completed Phase 1–5 baseline and must preserve:

- FieldRepository ordering, enabled filtering, and global enable behavior.
- Core validation and rendering contracts.
- Classic Checkout and Block Checkout adapters.
- `_scfw_` order metadata and HPOS storage.
- Admin Order Display and its historical-data policy.
- Customer Emails, Thank You, My Account, and REST response integrations.
- Import/export compatibility and the `checkout-fields-config-v1` format.
- Namespace-specific duplicate-rendering suppression.

No Phase 6 task may create a second field configuration system, metadata
convention, storage layer, authentication system, or REST endpoint.

---

# Shared Architecture

Conditional evaluation belongs in an approved reusable Service. It must not
be implemented inside FieldRepository, Classic Checkout, Block Checkout,
templates, or rendering callbacks.

The existing global and per-field `enabled` gates remain authoritative. A
field that is globally disabled or individually disabled is never made visible
by a rule.

The existing field configuration remains in `scfw_options`. Phase 6 may add a
backward-compatible `rules` property to a field entry only if the schema and
import/export behavior are approved before implementation.

Old configurations without `rules` mean that no conditional rules apply.
Malformed or unknown rules must fail closed for visibility evaluation without
breaking checkout or invalidating the entire existing configuration.

---

## T0060 — Conditional Logic and Dynamic Visibility Foundation

### Objective

Define the shared conditional-rule representation, evaluator, and visibility
decision used by all later rule types and both checkout adapters.

### Scope

- Define the approved rule schema and condition/operator model.
- Define evaluation inputs and the visibility output.
- Define multiple-rule behavior and explicit AND/OR semantics.
- Define behavior when no rules exist.
- Define malformed and unknown-rule handling.
- Provide a reusable Service-layer evaluator.
- Establish the Classic and Block integration boundary without duplicating
  evaluation logic.

### Dependencies

- Completed Phase 1–5 baseline.
- Approved Phase 6 rule schema.

### Architecture

- Rule evaluation belongs in Services.
- FieldRepository remains the source of field identity, order, and enabled state.
- Checkout adapters receive shared visibility decisions but do not own rule logic.
- Existing rendering and validation contracts remain unchanged unless a later
  approved task requires a thin integration adapter.

### Rule/Configuration Representation

The contract must define a backward-compatible field-level `rules` property
inside the existing `scfw_options['fields']` configuration. No second option
or rule table is permitted.

### Acceptance Criteria

- A field with no rules behaves exactly as before.
- Global and per-field enabled gates override all rule results.
- The evaluator produces a deterministic visible/hidden decision.
- AND/OR behavior is explicit and tested.
- Malformed and unknown rules fail safely.
- No rule evaluation occurs in templates or FieldRepository.

### Allowed Files

- One approved Service-layer conditional evaluator.
- Approved configuration/schema and import/export integration files only if
  required by the approved schema decision.
- Minimal Classic/Block adapter integration files only if required to consume
  the shared decision and explicitly approved.
- Targeted tests and documentation.

### Forbidden Files

- Direct changes to FieldRepository behavior.
- Duplicate validators or renderers.
- New options, tables, metadata keys, or REST endpoints.
- Unapproved changes to Phase 1–5 integrations.

### Security Requirements

- Malformed rules fail closed.
- Rule input is validated and sanitized through centralized logic.
- Rules cannot bypass authorization or required validation.

### HPOS Requirements

- No order persistence changes.
- No direct database access.

### Classic Checkout Requirements

- Existing rendering, validation, and saving remain unchanged when no rules
  are configured.
- Visibility decisions must use the shared evaluator.

### Block Checkout Requirements

- Existing registration, validation, saving, and native API integration remain
  unchanged when no rules are configured.
- Visibility decisions must use the shared evaluator.

### Import/Export Requirements

- Old `checkout-fields-config-v1` exports remain importable.
- New rules, if approved in the schema, round-trip without loss.
- Malformed rules cannot corrupt valid existing fields.

### Regression Requirements

- Phase 1–5 behavior passes with no rules configured.
- Field ordering and enable gates remain unchanged.

### Live Verification

- Multiple rules and AND/OR behavior.
- No-rule behavior.
- Malformed-rule behavior.
- Classic and Block visibility parity.

### Definition of Done

- Rule schema and evaluator architecture approved.
- Shared evaluator implemented and tested.
- Classic/Block integration boundary verified.
- Import/export compatibility verified.
- Quality and security review pass.

---

## T0061 — Country Rules

### Objective

Apply conditional visibility using the active WooCommerce checkout country.

### Scope

- Country context from WooCommerce APIs.
- Supported, unsupported, and unset country behavior.
- Shared Classic and Block evaluation.

### Dependencies

- T0060.

### Architecture

- Country context is an evaluator input, not field business logic.
- WooCommerce country APIs are authoritative.

### Rule/Configuration Representation

Country conditions use the T0060 approved rule schema and existing
`scfw_options` field configuration.

### Acceptance Criteria

- Matching countries produce the approved visibility result.
- Unset and unsupported countries have deterministic behavior.
- Country changes do not create duplicate validation or persistence paths.

### Allowed Files

- T0060 evaluator/context Service files.
- Minimal approved checkout context adapters.
- Targeted tests and documentation.

### Forbidden Files

- Frozen FieldRepository behavior.
- Duplicated country lists or checkout validation.
- New storage or endpoints.

### Security Requirements

- Country input is sanitized and validated by centralized logic.
- Country rules cannot expose order or customer data.

### HPOS Requirements

- No order storage changes.

### Classic Checkout Requirements

- Existing Classic behavior remains unchanged without country rules.

### Block Checkout Requirements

- Existing Block behavior remains unchanged without country rules.

### Import/Export Requirements

- Country rules round-trip through the approved existing configuration format.

### Regression Requirements

- Required validation, field order, enable gates, and Phase 5 surfaces remain
  unchanged.

### Live Verification

- Matching, non-matching, unset, and unsupported country scenarios in both
  checkout adapters.

### Definition of Done

- Country rule tests and Classic/Block parity verification pass.

---

## T0062 — Product Rules

### Objective

Apply conditional visibility using the current WooCommerce cart/order product
context.

### Scope

- Product context from WooCommerce cart/product APIs.
- Empty-cart behavior.
- Unavailable or deleted product behavior.

### Dependencies

- T0060.

### Architecture

- Product context is supplied to the shared evaluator.
- No product lookup logic belongs in field renderers.

### Rule/Configuration Representation

Product conditions use the T0060 schema in existing `scfw_options` data.

### Acceptance Criteria

- Matching products produce the approved visibility result.
- Empty carts have deterministic behavior.
- Missing products fail safely.

### Allowed Files

- Approved evaluator/context Services.
- Minimal approved checkout context adapters.
- Targeted tests and documentation.

### Forbidden Files

- Direct SQL or product-table access.
- New product metadata or order metadata.
- Frozen checkout persistence code.

### Security Requirements

- Product context cannot bypass checkout authorization or validation.

### HPOS Requirements

- No order storage changes.

### Classic Checkout Requirements

- Existing Classic behavior remains unchanged without product rules.

### Block Checkout Requirements

- Existing Block behavior remains unchanged without product rules.

### Import/Export Requirements

- Product rules round-trip through the approved existing configuration format.

### Regression Requirements

- Cart/order persistence, field ordering, and Phase 5 responses remain intact.

### Live Verification

- Matching product, non-matching product, empty cart, and unavailable-product
  scenarios in both checkout adapters.

### Definition of Done

- Product rule behavior and Classic/Block parity pass review.

---

## T0063 — Category Rules

### Objective

Apply conditional visibility using WooCommerce product category context.

### Scope

- Category lookup through WooCommerce product/category APIs.
- Products with multiple categories.
- No matching category behavior.

### Dependencies

- T0060.

### Architecture

- Category context is an evaluator input.
- Category evaluation is shared by Classic and Block.

### Rule/Configuration Representation

Category conditions use the T0060 schema in existing `scfw_options` data.

### Acceptance Criteria

- Any approved matching-category semantics are deterministic.
- Multiple categories are handled consistently.
- No-match behavior is explicit.

### Allowed Files

- Approved evaluator/context Services.
- Minimal approved checkout context adapters.
- Targeted tests and documentation.

### Forbidden Files

- Direct SQL or taxonomy-table access.
- New category storage.
- Frozen checkout persistence changes.

### Security Requirements

- Category conditions cannot expose order/customer data.

### HPOS Requirements

- No order storage changes.

### Classic Checkout Requirements

- Existing behavior remains unchanged without category rules.

### Block Checkout Requirements

- Existing behavior remains unchanged without category rules.

### Import/Export Requirements

- Category rules round-trip through the approved configuration format.

### Regression Requirements

- Field ordering, validation, enable gates, and Phase 5 integrations remain
  unchanged.

### Live Verification

- Single-category, multi-category, and no-match scenarios in Classic and Block.

### Definition of Done

- Category rule behavior and parity verification pass.

---

## T0064 — User Role Rules

### Objective

Apply conditional visibility using WordPress user-role context.

### Scope

- Authenticated user roles.
- Guest behavior.
- Administrator and shop-manager behavior.
- WordPress user APIs.

### Dependencies

- T0060.

### Architecture

- Role context is obtained through WordPress APIs.
- No custom authentication or authorization system is introduced.

### Rule/Configuration Representation

Role conditions use the T0060 schema in existing `scfw_options` data.

### Acceptance Criteria

- Authenticated role behavior is deterministic.
- Guest behavior is explicit and safe.
- Administrator/shop-manager behavior is explicitly tested.

### Allowed Files

- Approved evaluator/context Services.
- Minimal approved checkout context adapters.
- Targeted tests and documentation.

### Forbidden Files

- Custom authentication.
- Direct user-table queries.
- Changes to REST authorization or order ownership.

### Security Requirements

- Role rules cannot grant access to order data.
- Role input uses WordPress user APIs only.

### HPOS Requirements

- No order storage changes.

### Classic Checkout Requirements

- Existing guest and authenticated checkout behavior remains unchanged without
  role rules.

### Block Checkout Requirements

- Existing guest and authenticated checkout behavior remains unchanged without
  role rules.

### Import/Export Requirements

- Role rules round-trip through the approved configuration format.

### Regression Requirements

- Customer/order REST authorization and T0054 My Account authorization remain
  unchanged.

### Live Verification

- Customer, administrator, shop-manager, and guest scenarios.

### Definition of Done

- Role behavior, security, and Classic/Block parity pass review.

---

## T0065 — Cart Rules and AJAX Refresh

### Objective

Apply cart-dependent rules and refresh visibility when cart state changes.

### Scope

- Cart-dependent conditions.
- Product/quantity/cart-total changes.
- Visibility refresh.
- Validation synchronization.
- Hidden required fields must not block checkout.

### Dependencies

- T0060, T0061, T0062, T0063, and T0064.

### Architecture

- Use native WooCommerce refresh mechanisms where possible.
- Refresh is an adapter concern; rule evaluation remains in Services.
- No custom REST endpoint is created merely for refresh.

### Rule/Configuration Representation

Cart conditions use the T0060 schema in existing `scfw_options` data.

### Acceptance Criteria

- Cart changes recalculate visibility.
- Visible required fields validate normally.
- Hidden required fields do not block checkout.
- Classic and Block decisions remain consistent.
- Refresh does not save or rewrite order metadata.

### Allowed Files

- Approved evaluator/context Services.
- Minimal approved Classic/Block refresh integration files.
- Existing asset registration files only if required and approved.
- Targeted tests and documentation.

### Forbidden Files

- New REST or AJAX endpoints merely for refresh.
- New tables, options, or metadata keys.
- Duplicate validation or rule evaluation.
- Changes to Phase 5 order integrations.

### Security Requirements

- Refresh requests use native WooCommerce security mechanisms.
- Client-side visibility never replaces server-side validation.
- Hidden fields cannot bypass required-field rules when they become visible.

### HPOS Requirements

- No order storage changes.

### Classic Checkout Requirements

- Native Classic refresh behavior remains intact.
- Required validation stays synchronized with visibility.

### Block Checkout Requirements

- Native Block Store API refresh behavior remains intact.
- Required validation stays synchronized with visibility.

### Import/Export Requirements

- Cart rules round-trip through the approved configuration format.

### Regression Requirements

- Classic, Block, Phase 5 integrations, HPOS, and enable gates remain intact.

### Live Verification

- Cart add/remove, quantity, total, empty-cart, visible-required, and
  hidden-required scenarios in both checkout adapters.

### Definition of Done

- Cart rules and refresh behavior pass end-to-end verification and architecture
  review.

---

# Phase 6 Completion Criteria

- T0060 through T0065 are complete and individually reviewed.
- Existing behavior is unchanged when no rules are configured.
- Classic and Block use shared rule decisions.
- Validation remains correct for visible and hidden fields.
- Configuration and import/export remain backward compatible.
- HPOS and `_scfw_` storage remain unchanged.
- T0050 through T0055 regression tests pass.
- Security, performance, lint, PHPCS, PHPStan, and architecture reviews pass.
- No unapproved source modules, endpoints, tables, options, or metadata keys
  are introduced.

---
