# Class Diagram

Version: 1.0.0

Status: Architecture Freeze

---

# Purpose

This document defines the permanent class architecture of the project.

Responsibilities, ownership and dependencies are defined here.

Implementation may evolve.

Architecture must remain stable.

---

# High Level Overview

```
Plugin
    │
    ▼
Core
    │
    ├─────────────┐
    ▼             ▼
Admin        Checkout
                  │
        ┌─────────┴─────────┐
        ▼                   ▼
 Classic Checkout     Block Checkout
        │                   │
        └─────────┬─────────┘
                  ▼
              Validation
                  │
                  ▼
               Services
                  │
                  ▼
                Fields
```

---

# Plugin

Purpose

Application entry point.

Responsibilities

- Bootstrap application
- Start lifecycle
- Coordinate startup

Must NOT

- Render UI
- Validate data
- Save data

---

# Core

Purpose

Internal application coordination.

Responsibilities

- Bootstrap modules
- Coordinate services
- Manage application lifecycle

Must NOT

- Render checkout
- Contain business rules

---

# Admin

Purpose

Administration interface.

Responsibilities

- Settings pages
- Field management
- Admin requests

Delegates to

Services

Must NOT

- Save business data directly
- Validate checkout

---

# Checkout

Purpose

WooCommerce integration.

Contains

Classic Checkout

Block Checkout

Responsibilities

- Render fields
- Collect input
- Forward data

Must NOT

- Validate business rules
- Save order data directly

---

# Validation

Purpose

Central validation engine.

Responsibilities

- Required validation
- Format validation
- Shared validation rules

Used by

- Admin
- Classic Checkout
- Block Checkout

---

# Services

Purpose

Reusable business services.

Examples

- WooCommerceChecker
- SettingsService
- OrderMetaService

Responsibilities

- Business logic
- Shared operations

Must remain reusable.

---

# Fields

Purpose

Represent checkout field definitions.

Responsibilities

- Field metadata
- Field configuration
- Field defaults

Must NOT

- Render UI
- Save data

---

# Dependency Rules

Allowed

```
Plugin

↓

Core

↓

Admin / Checkout

↓

Validation

↓

Services

↓

Fields
```

Forbidden

```
Fields

↓

Checkout

↓

Plugin
```

No circular dependencies.

---

# Communication Rules

Admin

↓

Services

Checkout

↓

Validation

Validation

↓

Services

Services

↓

Fields

Fields never call Services.

Core coordinates everything.

---

# Lifecycle

```
Plugin::boot()

↓

Core

↓

Admin

↓

Checkout

↓

Validation

↓

Services
```

---

# Design Rules

Every class must have one responsibility.

Business logic belongs only inside Services.

Validation belongs only inside Validation.

Checkout classes communicate only with WooCommerce.

Core coordinates.

Plugin starts.

---

# Public API

Public API must remain minimal.

Only intentional extension points may become public.

Internal classes must not become public APIs accidentally.

---

# Future Expansion

Possible future modules

- REST
- CLI
- Integrations

They must integrate through Core.

They must never bypass Services.

---

END OF DOCUMENT