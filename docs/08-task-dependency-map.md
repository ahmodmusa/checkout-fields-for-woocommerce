# Task Dependency Map

Version: 1.0.0

Status: Architecture Freeze

---

# Purpose

This document defines the execution order of every implementation task.

Tasks must always be implemented according to this dependency map.

No task may be implemented before all of its dependencies are completed.

Every task represents exactly one reviewable Git commit.

---

# Development Rules

- One Task = One Commit
- One Commit = One Feature
- Every task requires review before merge.
- Future milestones must never be implemented early.
- Dependencies must never be bypassed.

---

# Phase 0 — Foundation

Status

Completed

```
T0001 Plugin Header
        ↓
T0002 Plugin Constants
        ↓
T0003 Composer Autoload
        ↓
T0004 Plugin Kernel
        ↓
T0005 Boot Plugin
        ↓
T0006 WooCommerce Compatibility
        ↓
T0007 Bootstrap Guard
```

---

# Phase 1 — Bootstrap Framework

```

T0008 Hook Registration
        ↓
T0009 Asset Registration
        ↓
T0010 Admin Bootstrap
        ↓
T0011 Checkout Bootstrap
        ↓
T0012 Block Checkout Bootstrap
        ↓
T0013 Settings Bootstrap
        ↓
T0014 Plugin Lifecycle Integration
```

Nothing from Phase 2 may begin until Phase 1 is complete.

---

# Phase 2 — Field Engine

```
T0020 Field Definition
        ↓
T0021 Field Types
        ↓
T0022 Field Defaults
        ↓
T0023 Field Configuration
        ↓
T0024 Validation Rules
        ↓
T0025 Validation Engine
        ↓
T0026 Sanitization
        ↓
T0027 Rendering Engine
        ↓
T0028 Saving Engine
        ↓
T0029 Display Engine
```

All checkout implementations depend on this phase.

---

# Phase 3 — Checkout Integration

```
T0030 Classic Checkout Rendering
        ↓
T0031 Classic Validation
        ↓
T0032 Classic Saving
```

↓

```
T0033 Block Registration
        ↓
T0034 Block Rendering
        ↓
T0035 Block Validation
        ↓
T0036 Block Saving
```

↓

```
T0037 Shared Checkout Integration
```

Classic and Block implementations must remain functionally identical whenever technically possible.

---

# Phase 4 — Administration

```
T0040 Settings Page
        ↓
T0041 Settings Sections
        ↓
T0042 Settings Fields
        ↓
T0043 Field Builder
        ↓
T0044 Drag & Drop Ordering
        ↓
T0045 Import
        ↓
T0046 Export
```

## Phase 4 Implementation Notes

Configuration remains in the existing `scfw_options` option. Each ordered
field entry contains:

- `id` — sanitized lowercase identifier using letters, numbers, underscores, or hyphens.
- `label` — non-empty sanitized display label.
- `type` — one of the Core `FieldType` values: `text`, `textarea`, `email`, `tel`, `number`, `date`, `checkbox`, `radio`, or `select`.
- `required` — boolean required state.
- `enabled` — boolean field state.

The entry order is the checkout order. The global `scfw_options['enabled']`
setting controls whether configured fields render, validate, register, or save;
the administration page remains available when it is disabled. A field with
`enabled` false is not active in either checkout adapter.

Import and export use the stable JSON format marker
`checkout-fields-config-v1` and the top-level keys `format`, `enabled`, and
`fields`. Imported files must contain a complete valid field schema. Exported
configuration can be imported without changing configuration content; only the
generated download filename contains a timestamp.

Block Checkout uses WooCommerce's additional checkout field API. Core
`textarea` fields map to the supported Block `text` control, while Classic
Checkout continues to render the Core textarea type.

Admin Order Display reads `_scfw_` values through the WooCommerce order API.
Values remain visible after a field is disabled or removed, using the current
field label when available and a readable identifier fallback for removed
fields.

---

# Phase 5 — Order Integration

Status

Completed at `bee9aabf0e09f2a9e87d412db6255336890be94e`.

```
T0050 Save Order Meta
        ↓
T0051 Admin Order Display
        ↓
T0052 Customer Emails
        ↓
T0053 Thank You Page
        ↓
T0054 My Account
        ↓
T0055 REST API Support
```

Phase 5 planning reconciliation:

- `T0050` and `T0051` are already satisfied by the frozen Phase 3/4 order
  persistence and Admin Order Display baseline. They require verification, not
  reimplementation.
- REST API Support is included in the official Phase 5 roadmap and is assigned
  the next unused identifier, `T0055`.
- The repository also contains a historical `T0040–T0044` identifier conflict
  between the Block Checkout and Administration contracts. This conflict is
  not silently renumbered by Phase 5.

- T0052 Customer Emails, T0053 Thank You Page, T0054 My Account, and T0055 REST
  API Support are implemented through thin WooCommerce integrations. T0055
  extends the existing `/wc/v3/orders/<id>` response with additive
  `scfw_fields` data for administrator and shop-manager consumers.

---

# Phase 6 — Advanced Features

```
T0060 Conditional Logic
        ↓
T0061 Country Rules
        ↓
T0062 Product Rules
        ↓
T0063 Category Rules
        ↓
T0064 User Role Rules
        ↓
T0065 AJAX Refresh
```

---

# Phase 7 — Developer Platform

```
T0070 Public Hooks
        ↓
T0071 Public Filters
        ↓
T0072 PHP API
        ↓
T0073 Extension Points
```

---

# Phase 8 — Quality

```
T0080 PHPUnit
        ↓
T0081 PHPStan
        ↓
T0082 PHPCS
        ↓
T0083 Performance Review
        ↓
T0084 Security Review
        ↓
T0085 Accessibility
        ↓
T0086 Internationalization
```

---

# Phase 9 — Release

```
T0090 Documentation Review
        ↓
T0091 WordPress.org Review
        ↓
T0092 Packaging
        ↓
T0093 Final QA
        ↓
T0094 Release Candidate
        ↓
T0095 Stable Release 1.0.0
```

---

# Dependency Rules

A task may only start when:

- All previous dependencies are complete.
- All required reviews have passed.
- All required commits have been merged.

Skipping dependencies is prohibited.

---

# Rollback Policy

If a task fails review:

- Fix the task.
- Repeat the review.
- Do not continue to the next task.

No later task may compensate for an earlier architectural defect.

---

# Architecture Protection

Tasks may implement functionality.

Tasks must never modify:

- Project Vision
- Architecture
- Directory Structure
- Data Model
- Request Lifecycle

Those documents are frozen.

---

# Completion Criteria

The project reaches Version 1.0.0 only when:

- Every task is complete.
- Every review has passed.
- All documentation is current.
- Coding standards pass.
- Static analysis passes.
- WordPress.org requirements pass.
- WooCommerce Classic Checkout is fully supported.
- WooCommerce Block Checkout is fully supported.

---

END OF DOCUMENT
