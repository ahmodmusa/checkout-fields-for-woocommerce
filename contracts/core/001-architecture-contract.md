# Architecture Contract
## Contract ID: 001
## Version: 1.0.0

---

# Purpose

This document defines the permanent architecture of the project.

The architecture defined here MUST remain consistent throughout the lifetime of the project.

Implementation may evolve.

Architecture must remain stable.

---

# Authority

Only the Lead Architect may modify this document.

Implementation AI must never change this architecture.

If implementation conflicts with this architecture,

STOP.

Output:

ARCHITECT DECISION REQUIRED

---

# Architecture Principles

The project follows:

- SOLID
- KISS
- DRY
- YAGNI
- Composition over Inheritance
- Dependency Injection where appropriate
- Single Responsibility Principle

---

# Plugin Architecture

The plugin uses a layered architecture.

Presentation Layer

↓

Application Layer

↓

Domain Layer

↓

Infrastructure Layer

Business logic must never exist inside presentation classes.

---

# Directory Structure

The structure below is permanent.

src/

Admin/

Core/

Frontend/

Helpers/

Services/

Contracts/

Support/

Views/

Future folders require architectural approval.

---

# Checkout Architecture

Classic Checkout

and

Block Checkout

are first-class citizens.

Neither implementation has priority over the other.

Both must expose identical capabilities whenever technically possible.

---

# Checkout Providers

Checkout rendering must be provider-based.

Never duplicate business logic.

Provider classes communicate only with WooCommerce.

Business rules belong elsewhere.

---

# Business Logic

Business logic must never exist inside

Admin

Classic Checkout

Block Checkout

Views

Templates

Business logic belongs only in Core and Services.

---

# Services

Services must be reusable.

Services must not know

Admin

Classic

Blocks

Views

Services expose reusable functionality only.

---

# Helpers

Helpers are utility classes only.

Helpers must never contain business rules.

Helpers must remain stateless.

---

# Views

Views contain presentation only.

No validation.

No saving.

No queries.

No business logic.

---

# Contracts

Interfaces belong only inside

src/Contracts

Never place interfaces elsewhere.

---

# Admin

Admin classes

Render admin UI

Handle requests

Call Services

Nothing more.

---

# Frontend

Frontend classes

Render checkout

Collect input

Delegate business logic to Services.

---

# Data Storage

Persistent data strategy

WordPress Options

Order Meta

WordPress APIs

No custom database tables

unless explicitly approved.

---

# Dependencies

Minimize dependencies.

Prefer native WordPress APIs.

Do not introduce libraries without approval.

---

# Autoloading

PSR-4 only.

Manual includes are forbidden except the bootstrap.

---

# Global State

Avoid globals.

Avoid static mutable state.

Dependency injection is preferred.

---

# Hooks

Hooks belong only where appropriate.

Never scatter hooks randomly.

Register hooks through dedicated bootstrap classes.

---

# Assets

Load CSS and JS only when required.

Never load admin assets on frontend.

Never load frontend assets in admin.

---

# Security

Every layer must validate its own inputs.

Never trust data from another layer.

---

# Performance

Avoid duplicate queries.

Avoid unnecessary object creation.

Avoid unnecessary hook registrations.

Lazy-load where appropriate.

---

# Extensibility

Every public extension point

must be intentional.

Never expose internals accidentally.

---

# Lite / Pro

Architecture must support Lite and Pro.

Lite must not depend on Pro.

Pro may extend Lite.

Lite must never reference Pro classes.

---

# Testing

Architecture should remain testable.

Prefer constructor injection.

Avoid hidden dependencies.

---

# Forbidden

Never

Move folders

Rename namespaces

Rename services

Rename providers

Merge layers

Duplicate logic

Create circular dependencies

---

# Architecture Review

Before completing implementation verify

□ Layer boundaries respected

□ No business logic in UI

□ No duplicated logic

□ Services reusable

□ Helpers stateless

□ Views clean

□ No architecture violations

---

END OF CONTRACT