# Directory Structure

Version: 1.0.0

Status: Architecture Freeze

---

# Purpose

This document defines the permanent directory structure of the project.

Every implementation task must follow this structure.

New directories require architectural approval.

---

# Root Structure

```
checkout-fields-for-woocommerce/

├── assets/
├── docs/
├── languages/
├── src/
├── tests/

├── checkout-fields-for-woocommerce.php
├── composer.json
├── readme.txt
├── README.md
├── CHANGELOG.md
├── LICENSE
```

---

# Source Structure

```
src/

├── Admin/
├── Assets/
├── Checkout/
│   ├── Blocks/
│   └── Classic/
├── Contracts/
├── Core/
├── Fields/
├── REST/
├── Services/
├── Support/
└── Validation/
```

---

# Module Responsibilities

## Admin

Purpose

- Admin pages
- Settings UI
- Field management interface
- Admin notices

Must NOT contain

- Business logic
- Validation logic
- Order saving logic

May depend on

- Services
- Validation

---

## Assets

Purpose

- Asset registration
- CSS loading
- JavaScript loading
- Build asset manifests

Must NOT contain

- Business logic

---

## Checkout

Purpose

Integrate with WooCommerce checkout.

Subdirectories

Classic/

Blocks/

Responsibilities

- Render fields
- Collect user input
- Delegate processing

Must NOT contain

- Validation rules
- Business logic

---

## Contracts

Purpose

Project interfaces.

Examples

- Interfaces
- Public contracts

Must NOT contain

Implementations.

---

## Core

Purpose

Application bootstrap.

Responsibilities

- Plugin lifecycle
- Internal orchestration
- System coordination

Must NOT contain

Checkout rendering.

---

## Fields

Purpose

Field definitions.

Responsibilities

- Field metadata
- Field configuration
- Field factories (future)

Must NOT contain

Rendering logic.

---

## Services

Purpose

Reusable application services.

Examples

- WooCommerceChecker
- SettingsService
- OrderMetaService

Services must remain independent.

---

## REST

Purpose

- WooCommerce REST response integrations approved by the architecture.

Responsibilities

- Register approved native REST extension points.
- Adapt existing Service output to additive REST response data.

Must NOT contain

- Custom order storage.
- Direct database access.
- Custom authorization systems.
- Duplicate field lookup or configuration logic.

---

## Support

Purpose

Shared utilities.

Examples

- Collections
- Arrays
- Strings
- Paths

Must remain stateless.

Must NOT contain

Business rules.

---

## Validation

Purpose

Central validation.

Responsibilities

- Validation rules
- Sanitization
- Shared validation pipeline

Used by

- Classic Checkout
- Block Checkout
- Admin

---

# Dependency Rules

Allowed dependency direction

```
Admin
        │
Checkout
        │
        ▼
Services
        │
        ▼
Validation
        │
        ▼
Core
```

Modules must never create circular dependencies.

---

# Folder Creation Rules

New folders require architectural approval.

Existing folders must not be renamed.

Existing folders must not change responsibilities.

---

# File Naming

One public class per file.

Filename must match class name.

Example

WooCommerceChecker.php

↓

class WooCommerceChecker

---

# Namespace Rules

Namespace root

SCFW

Examples

SCFW\Admin

SCFW\Core

SCFW\Services

SCFW\Validation

---

# Future Expansion

Future modules may include

- Integrations
- REST
- CLI

Only after architectural approval.

---

END OF DOCUMENT
