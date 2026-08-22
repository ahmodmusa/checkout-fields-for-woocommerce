# Phase 5 — Order Integration

Version: 1.0.0

Status: Planning Contract

---

# Objective

Extend the frozen checkout-field implementation across the remaining official
Order Integration surfaces:

- Customer Emails
- Thank You Page
- My Account
- REST API Support

The Phase 3 and Phase 4 order persistence and Admin Order Display work must be
preserved. Phase 5 is an integration and presentation milestone, not a new
field-engine or checkout-engine milestone.

---

# Planning Conflicts

The repository contains two task-numbering inconsistencies that must not be
resolved silently:

- `T0040–T0044` is used by both the Block Checkout contract and the Phase 4
  Administration contract.
- The roadmap lists REST API Support in Phase 5, but the dependency map stops
  at `T0054 My Account`.

This contract preserves the existing reserved Phase 5 range and assigns
`T0055 REST API Support` as the next unused Phase 5 identifier. The existing
`T0050 Save Order Meta` and `T0051 Admin Order Display` entries are documented
as baseline reconciliation tasks because their capabilities are already
implemented and frozen by Phase 3/4. They must not trigger reimplementation.

If task numbering or REST placement must differ, an explicit architecture and
planning decision is required before implementation.

---

# Current Baseline

Phase 4 is frozen by the `phase4-complete` tag.

The baseline provides:

- Settings-driven field configuration in the existing `scfw_options` option.
- `FieldRepository` as the shared ordered and enabled field source.
- Immutable Core `Field` definitions and the existing `FieldType` taxonomy.
- Shared Core validation and rendering architecture.
- Classic Checkout rendering, validation, and saving.
- Block Checkout registration, validation, and saving through WooCommerce APIs.
- `_scfw_` order metadata written through WooCommerce order objects.
- HPOS-compatible order persistence.
- Capability-protected Admin Order Display with historical-value fallback.
- Protected JSON import/export using the stable
  `checkout-fields-config-v1` format.

Phase 5 may consume these capabilities. It must not replace or duplicate them.

---

# Scope

In scope:

- Reconcile and verify the already-frozen order-meta and Admin Order Display
  baseline (`T0050` and `T0051`) without changing its implementation.
- Make saved `_scfw_` values available in WooCommerce Customer Emails.
- Make saved `_scfw_` values available on the WooCommerce Thank You Page.
- Make saved `_scfw_` values available in the WooCommerce My Account order
  views.
- Define a supported REST representation for checkout-field order data, if
  the required architecture decision approves REST exposure.
- Read existing order data through WooCommerce order APIs.
- Preserve configured field order and labels where the target surface supports
  them.
- Preserve the existing `_scfw_` metadata convention and import/export format.

---

# Out of Scope

The following are not Phase 5 work:

- New checkout field types.
- Changes to `Field`, `FieldType`, `FieldFactory`, or `FieldRepository`.
- Changes to Core validation or rendering rules.
- Changes to Classic Checkout behavior.
- Changes to Block Checkout behavior.
- Changes to the Settings API, Field Builder, ordering, import, or export.
- Conditional logic, country rules, product rules, category rules, user-role
  rules, cart rules, AJAX refresh, or dynamic visibility.
- New custom database tables or a second options/configuration system.
- Rewriting or migrating existing order metadata.
- Re-saving order data from presentation surfaces.
- Public developer hooks, filters, or PHP APIs from Phase 7.
- PHPUnit, PHPStan, PHPCS cleanup, accessibility, or other Phase 8 work unless
  required by the task's quality gate.
- WordPress.org packaging or release work from Phase 9.

---

# Dependencies

Phase 5 depends on:

- The frozen `phase4-complete` baseline.
- Completed Phase 2 Field, Validation, and Rendering foundations.
- Completed Phase 3 Classic and Block Checkout integrations.
- Completed Admin Order Display and HPOS verification.
- The existing `_scfw_` meta convention.
- The existing `FieldRepository` configuration source.
- Native WooCommerce email, order confirmation, account-order, and REST
  extension points where available.

No task may begin until its preceding task and required review are complete.

---

# Architecture Rules

- `FieldRepository` remains the single source of configured field definitions,
  order, enabled state, labels, and IDs.
- Core `Field`, `FieldType`, validation, and rendering contracts remain
  authoritative.
- WooCommerce integrations remain thin adapters.
- Reusable order-field lookup or formatting logic belongs in an approved
  reusable service, not in email, account, thank-you, or REST presentation
  callbacks.
- Admin remains an administration/presentation layer and must not gain order
  persistence logic.
- Classic and Block Checkout adapters remain unchanged unless a compatibility
  defect is proven and separately approved.
- Order data must be read and written only through WooCommerce order APIs.
- HPOS and legacy order storage must work without direct `wp_postmeta` access.
- No custom order tables are permitted.
- No duplicate options or configuration systems are permitted.
- No new directory may be introduced without architectural approval.
- Public APIs and extension points must not be created accidentally.
- Any architecture change requires the exact stop condition:
  `ARCHITECT DECISION REQUIRED`

---

# Data Model / Schema Changes

No data model change is required or approved by this contract.

Existing order metadata remains authoritative:

- Prefix: `_scfw_`
- Field key: `_scfw_{field_id}`
- Access: WooCommerce order object APIs

The `scfw_options` schema remains unchanged. The
`checkout-fields-config-v1` import/export format remains unchanged.

REST response fields, authentication, permissions, and visibility are not
approved by this contract and require the architecture decision described under
`T0055` before implementation.

---

# Task Breakdown

## T0050 — Save Order Meta Baseline Reconciliation

### Objective

Verify that the frozen Classic and Block Checkout integrations continue to save
configured field values under `_scfw_{field_id}` through HPOS-safe WooCommerce
order APIs. This task is verification only; it must not reimplement saving.

### Allowed Files

- `contracts/tasks/T0050-T0055-order-integration.md`
- `docs/08-task-dependency-map.md` only for approved dependency-map
  reconciliation.
- Verification scripts or reports outside the production repository.

### Forbidden Files

- All PHP source files.
- All existing Phase 3/4 implementation files.
- `scfw_options` schema and import/export format.
- New order persistence classes, tables, or metadata keys.
- All unrelated documentation and contracts.

### Dependencies

- Phase 4 freeze tag `phase4-complete`.
- Completed Phase 3 Classic and Block Checkout integration.

### Acceptance Criteria

- Existing Classic saving remains functional.
- Existing Block saving remains functional.
- `_scfw_{field_id}` remains the only SCFW order-meta convention.
- HPOS and legacy-compatible WooCommerce APIs remain the only storage access.
- No source implementation changes are required.

### Definition of Done

- Live verification passes for both checkout adapters.
- Existing source files remain unchanged.
- Review confirms this task did not duplicate or move persistence logic.

---

## T0051 — Admin Order Display Baseline Reconciliation

### Objective

Verify that the frozen Admin Order Display continues to show saved checkout
values, including historical values, without changing its implementation.

### Allowed Files

- `contracts/tasks/T0050-T0055-order-integration.md`
- `docs/08-task-dependency-map.md` only for approved dependency-map
  reconciliation.
- Verification scripts or reports outside the production repository.

### Forbidden Files

- All PHP source files.
- `src/Admin/OrderDisplay.php`.
- All existing Phase 3/4 implementation files.
- Direct database reads, new hooks, new order tables, or new metadata keys.

### Dependencies

- `T0050`.
- Completed `T0037 Admin Order Display`.

### Acceptance Criteria

- Saved values remain visible to authorized order-management users.
- Empty values remain suppressed.
- Disabled or removed fields retain historical-value visibility.
- Output remains escaped and capability protected.
- HPOS remains supported.

### Definition of Done

- Live Admin Order verification passes.
- Existing source files remain unchanged.
- Review confirms this task did not add checkout or persistence logic.

---

## T0052 — Customer Emails

### Objective

Display saved checkout-field values in applicable WooCommerce customer and
administrative order emails using native WooCommerce email extension points.

### Allowed Files

- `src/Core/HookRegistrar.php` for centralized hook registration only.
- One explicitly approved reusable order-field presentation service under
  `src/Services/`, if required by the architecture review.
- One explicitly approved email adapter under an existing source module, if
  required by the architecture review.
- Documentation for the task contract and verified behavior only.

### Forbidden Files

- `src/Core/Field/`.
- `src/Core/Validation/`.
- `src/Core/Rendering/`.
- `src/Frontend/ClassicCheckout.php`.
- `src/Frontend/BlockCheckout.php`.
- `src/Admin/OrderDisplay.php`.
- `src/Admin/Settings.php`.
- `src/Admin/FieldBuilder.php`.
- `src/Admin/FieldTransfer.php`.
- The root plugin file and frozen bootstrap flow.
- New order tables, metadata keys, options, or REST endpoints.

### Dependencies

- `T0050` and `T0051`.
- Native WooCommerce email hooks available in the supported WooCommerce range.
- Architecture decision on the adapter/service placement before implementation.

### Acceptance Criteria

- Applicable emails display only saved SCFW values.
- Field order follows `FieldRepository` where the email surface supports order.
- Empty values are omitted.
- Values are escaped for email output.
- No order is saved or mutated while rendering an email.
- Existing email behavior remains unchanged when no SCFW values exist.

### Definition of Done

- Customer and administrative email scenarios are live-tested.
- HPOS and legacy-compatible order APIs are used.
- Security, performance, lint, PHPCS, and architecture review pass.
- One focused implementation commit is produced only after the architecture
  decision is approved.

---

## T0053 — Thank You Page

### Objective

Display saved checkout-field values on the WooCommerce order-received / Thank
You Page using the native order-received lifecycle.

### Allowed Files

- `src/Core/HookRegistrar.php` for centralized hook registration only.
- One explicitly approved reusable order-field presentation service under
  `src/Services/`, if required.
- One explicitly approved Thank You adapter under an existing source module,
  if required.
- Documentation for the task contract and verified behavior only.

### Forbidden Files

- All frozen Core Field, Validation, and Rendering files.
- `src/Frontend/ClassicCheckout.php`.
- `src/Frontend/BlockCheckout.php`.
- `src/Admin/OrderDisplay.php`.
- Settings, Field Builder, Import/Export, and configuration schema files.
- Direct `wp_postmeta` access, new tables, new metadata keys, or new options.
- Custom AJAX or REST endpoints.

### Dependencies

- `T0050` and `T0051`.
- Native WooCommerce order-received hooks.
- Architecture decision on adapter/service placement before implementation.

### Acceptance Criteria

- Values display only for the correct order.
- Values are read through the WooCommerce order object.
- Empty values do not produce empty rows or sections.
- Output is escaped.
- Unauthenticated users cannot use the surface to view another order.
- No checkout or order persistence behavior changes.

### Definition of Done

- Guest and logged-in order-received scenarios are live-tested.
- HPOS works.
- Security, performance, lint, PHPCS, and architecture review pass.
- One focused implementation commit is produced only after the architecture
  decision is approved.

---

## T0054 — My Account

### Objective

Display saved checkout-field values in the authenticated customer's applicable
WooCommerce My Account order views.

### Allowed Files

- `src/Core/HookRegistrar.php` for centralized hook registration only.
- One explicitly approved reusable order-field presentation service under
  `src/Services/`, if required.
- One explicitly approved My Account adapter under an existing source module,
  if required.
- Documentation for the task contract and verified behavior only.

### Forbidden Files

- All frozen Core Field, Validation, and Rendering files.
- Classic or Block Checkout implementation files.
- Admin Order Display and Settings implementation files.
- Direct database access, new tables, new metadata keys, or new options.
- Public REST endpoints or AJAX endpoints created for convenience.

### Dependencies

- `T0050` and `T0051`.
- Native WooCommerce My Account order hooks.
- Architecture decision on adapter/service placement before implementation.

### Acceptance Criteria

- A customer sees values only for orders they are authorized to view.
- Empty values are omitted.
- Values are escaped.
- Guest and unauthorized order access cannot disclose SCFW metadata.
- Existing My Account order behavior remains intact.
- HPOS works through WooCommerce order APIs.

### Definition of Done

- Authenticated, guest, and unauthorized-access scenarios are live-tested.
- Security, performance, lint, PHPCS, and architecture review pass.
- One focused implementation commit is produced only after the architecture
  decision is approved.

---

## T0055 — REST API Support

### Objective

Determine and, only after explicit approval, expose the minimum necessary SCFW
order-field data through an appropriate existing WooCommerce/WordPress REST
extension point.

REST exposure is included by the official Phase 5 roadmap but is not specified
by the frozen architecture or data model. This task therefore begins with an
architecture decision and must not begin implementation automatically.

### Allowed Files

- `src/Core/HookRegistrar.php` for centralized hook registration only after
  approval.
- One explicitly approved REST integration file under an approved existing
  module, or a newly approved REST module.
- One approved reusable order-field presentation service under `src/Services/`
  if shared logic is required.
- Contract/documentation files required by the approved REST decision.

### Forbidden Files

- All frozen Core Field, Validation, and Rendering files.
- Classic Checkout, Block Checkout, Admin Order Display, and Phase 4 settings
  implementation files.
- New custom REST endpoints before approval.
- Unauthenticated order-data exposure.
- Direct database access, new order tables, new options, or new metadata keys.
- Changes to `checkout-fields-config-v1`.

### Dependencies

- `T0050` through `T0054`.
- A written architecture decision covering endpoint choice, response shape,
  authentication, authorization, and privacy.
- Confirmation that the supported WooCommerce/WordPress versions provide the
  required extension point.

### Acceptance Criteria

- REST behavior is approved before implementation.
- Only authorized consumers can access order-field values.
- Response data is limited to the required SCFW values.
- Existing WooCommerce REST behavior is not overridden or broken.
- HPOS works through WooCommerce order APIs.
- No custom endpoint is added merely for convenience when a native extension
  point is sufficient.

### Definition of Done

- Architecture decision is recorded and approved.
- Endpoint and authorization behavior are tested for authorized,
  unauthorized, missing-order, and empty-value cases.
- Security, performance, lint, PHPCS, and architecture review pass.
- One focused implementation commit is produced only after approval.

---

# Runtime Behavior

## Admin

Existing Phase 4 settings, Field Builder, Import/Export, and Admin Order
Display remain unchanged. Phase 5 presentation surfaces must not add settings
or persistence controls.

## Classic Checkout

No behavior change. Existing configured fields continue to render, validate, and
save through the frozen Classic Checkout adapter.

## Block Checkout

No behavior change. Existing configured fields continue to register, validate,
and save through the official Blocks API.

## Order Persistence

Phase 5 surfaces read existing `_scfw_{field_id}` values. They do not save,
rewrite, migrate, or normalize order metadata.

## HPOS

All order reads use WooCommerce order objects and supported APIs. No direct
legacy post-meta or custom-table access is permitted.

## Existing Configurations

Existing `scfw_options`, field IDs, labels, enabled states, configured order,
`_scfw_` metadata, and `checkout-fields-config-v1` exports remain compatible.
Removed or disabled fields may remain visible in historical order contexts
where the target surface has an authorized order view.

---

# Security Requirements

- Use WooCommerce's native authorization and lifecycle checks.
- Verify the current user may view the specific order before displaying data.
- Never expose order metadata by order ID alone.
- Escape HTML/email output and encode REST output through the native API.
- Do not add state-changing requests for read-only presentation.
- REST, if approved, must define a permission callback and privacy boundary.
- Do not expose field configuration or arbitrary order metadata.
- Do not introduce new nonces where no state-changing request exists; use native
  WooCommerce mechanisms.

---

# Performance Requirements

- Avoid duplicate order loads and metadata queries.
- Reuse one resolved field list per rendered surface where appropriate.
- Omit empty values without unnecessary work.
- Do not introduce polling, AJAX refresh, or client-side loops.
- Do not affect checkout request performance for users who do not view a Phase
  5 surface.
- REST responses must be bounded and avoid loading unrelated orders or fields.

---

# Backward Compatibility

- Existing Classic Checkout behavior must remain functional.
- Existing Block Checkout behavior must remain functional.
- Existing HPOS and legacy-compatible order APIs must remain supported.
- Existing Admin Order Display behavior must remain functional.
- Existing `_scfw_` metadata must remain readable.
- Existing `scfw_options` data must remain valid.
- Existing import/export files using `checkout-fields-config-v1` must remain
  importable without migration.
- No existing field ID may be renamed or remapped.

---

# Testing Requirements

Every implementation task must include:

- PHP 8.2 compatibility.
- Strict types and PSR-12/WPCS compliance.
- PHP lint and PHPCS.
- Live WordPress/WooCommerce verification.
- HPOS verification.
- Empty-value verification.
- Existing configuration compatibility verification.

Additional surface tests:

- Customer Emails: customer and administrative email output, empty values,
  escaped values, and no order mutation.
- Thank You Page: correct-order display, guest access boundaries, empty values,
  escaped output, and HPOS.
- My Account: owner access, unauthorized access, guest access, empty values,
  escaped output, and HPOS.
- REST: only after approval, authorized/unauthorized access, missing orders,
  response shape, privacy, and HPOS.

Baseline tasks `T0050` and `T0051` are verification-only and must not modify
source files.

---

# Documentation Requirements

Before each implementation task is marked complete, document only verified
behavior:

- Supported surface and hook/API used.
- Which field values are displayed.
- Authorization and privacy behavior.
- HPOS access behavior.
- Empty-value behavior.
- Any WooCommerce-version limitation.

Do not update README or CHANGELOG for planning work. Do not document an
unimplemented REST API or other future behavior as completed.

---

# Freeze Criteria

Phase 5 may be frozen only when:

- The `T0050`/`T0051` baseline reconciliation passes without source changes.
- Customer Emails, Thank You Page, and My Account behavior is implemented,
  reviewed, and live-verified.
- REST API Support is either implemented under an approved architecture
  decision and verified, or explicitly deferred with a recorded decision.
- Classic Checkout and Block Checkout behavior is unchanged and verified.
- Existing Admin Order Display and HPOS behavior is unchanged and verified.
- Existing import/export compatibility is verified.
- Security and privacy review passes.
- PHP lint, PHPCS, and required static-analysis reporting pass.
- Documentation describes only implemented behavior.
- Each implementation task has one focused reviewed commit.

---

# Commit Strategy

Follow the repository rule:

- One task = one commit.
- One commit = one feature.
- Use concise conventional commit messages.
- Do not combine email, Thank You, My Account, and REST implementation in one
  commit.
- Baseline reconciliation tasks must not create source implementation commits.
- Do not commit until the task review and required live verification pass.

Suggested messages:

- `docs: reconcile order integration baseline`
- `feat: add checkout fields to customer emails`
- `feat: add checkout fields to thank you page`
- `feat: add checkout fields to my account orders`
- `feat: expose checkout fields through approved rest integration`

---

If implementation requires changing frozen architecture, output exactly:

ARCHITECT DECISION REQUIRED

---

END PHASE
