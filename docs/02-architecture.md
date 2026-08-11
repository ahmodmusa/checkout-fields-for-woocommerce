# Architecture

Version: 1.0.0

Status: Architecture Freeze

---

# Purpose

This document defines the permanent technical architecture of the project.

Implementation may evolve.

Architecture must remain stable.

Every implementation task must follow this document.

---

# Architecture Principles

The project follows:

- SOLID
- KISS
- DRY
- YAGNI
- Composition over Inheritance
- WordPress Coding Standards
- PSR-12
- Strict Types
- WordPress First
- WooCommerce First

---

# Layered Architecture

The project uses a layered architecture.

Presentation Layer

↓

Application Layer

↓

Domain Layer

↓

Infrastructure Layer

Responsibilities must remain separated.

Business logic must never exist inside presentation classes.

---

# Directory Structure

The project is organized into the following modules.

```
src/
├── Admin/
├── Assets/
├── Checkout/
│   ├── Blocks/
│   └── Classic/
├── Core/
├── Contracts/
├── Fields/
├── REST/
├── Services/
├── Support/
└── Validation/
```

Additional directories require architectural approval.

---

# Bootstrap Flow

Application startup sequence:

Plugin Header

↓

ABSPATH Protection

↓

Composer Autoload

↓

Plugin Constants

↓

WooCommerce Compatibility Check

↓

Plugin::boot()

No additional startup logic may be added without architectural approval.

---

# Checkout Architecture

Classic Checkout and Block Checkout are first-class citizens.

Neither implementation has priority.

Both must expose identical capabilities whenever technically possible.

Checkout implementations communicate only with WooCommerce APIs.

Business logic must never exist inside checkout implementations.

---

# REST Integration

REST integrations are thin WooCommerce response adapters.

REST integrations must:

- Use existing WooCommerce REST extension points unless a new endpoint is explicitly approved.
- Use WooCommerce order APIs and existing Services.
- Preserve native WooCommerce authorization and response behavior.
- Never expose generic order metadata or create a second data model.

---

# Adapter Type Mapping

The Core field type and the UI control type may differ between checkout
adapters when the target platform does not support the Core field type.

This is an adapter-level compatibility mapping, not a Core architecture
change. The Core Field definition and the FieldType enum are never modified
for an adapter.

For example, WooCommerce Blocks supports only the additional checkout field
types text, select, and checkbox. The Block Checkout adapter maps the Core
TEXTAREA type to the Blocks text type while the Classic Checkout adapter
continues to render the Core TEXTAREA type unchanged.

---

# Business Logic Rules

Business logic belongs only inside:

- Core
- Services

Business logic must never exist inside:

- Admin
- Checkout
- Views
- Assets

---

# Services

Services provide reusable functionality.

Services must:

- Be reusable
- Be independent
- Avoid presentation concerns

Services must never know about:

- Admin UI
- Checkout rendering
- View templates

---

# Validation

Validation must be centralized.

Validation rules must never be duplicated.

Both Classic and Block Checkout must use the same validation logic.

---

# Data Storage Strategy

Configuration

- WordPress Options API

Order Data

- WooCommerce Order Meta

Transient Data

- WordPress Transients (only when required)

Custom Database Tables

Not permitted in Version 1.

---

# Dependency Policy

Prefer native WordPress APIs.

Prefer WooCommerce APIs.

Minimize third-party dependencies.

New dependencies require architectural approval.

---

# Asset Loading

Assets must load only when required.

Admin assets must never load on the frontend.

Frontend assets must never load inside wp-admin.

Block assets must load only when Block Checkout is active.

---

# Performance Policy

The project should:

- Avoid duplicate queries
- Avoid duplicate hook registrations
- Avoid unnecessary object creation
- Lazy-load expensive components where appropriate
- Minimize memory usage

---

# Security Policy

All external input must be:

- Sanitized
- Validated
- Escaped

Nonces must protect every state-changing request.

Capabilities must be verified before privileged actions.

---

# Extensibility

Every public extension point must be intentional.

Internal classes must not become public APIs accidentally.

Future extensions should not require architectural changes.

---

# Lite / Pro Strategy

Lite is the foundation.

Pro extends Lite.

Lite must never reference Pro code.

Shared architecture must remain identical between editions.

---

# Compatibility Targets

WordPress

- 6.7+

WooCommerce

- 9+

PHP

- 8.2+

---

# Architecture Review Checklist

Before every implementation verify:

- Layer boundaries respected
- No business logic in UI
- No duplicated validation
- Services remain reusable
- Checkout implementations remain thin
- Performance unaffected
- Security maintained
- Architecture unchanged

---

END OF DOCUMENT
