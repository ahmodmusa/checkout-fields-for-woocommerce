# Data Model

Version: 1.0.0

Status: Architecture Freeze

---

# Purpose

This document defines the permanent data model of the project.

It specifies what data exists, where it is stored, and how it flows through the system.

Implementation details may evolve.

The data model must remain stable.

---

# Design Principles

The project follows:

- WordPress First
- WooCommerce First
- No Custom Database Tables (Version 1)
- Native WordPress APIs
- Predictable Data Structures
- Backward Compatibility

---

# Data Categories

The plugin stores four categories of data.

1. Plugin Configuration

2. Field Definitions

3. Order Data

4. Runtime Data

---

# Plugin Configuration

Storage

WordPress Options API

Option Name

scfw_options

Purpose

Store plugin-wide settings.

Examples

- General settings
- Feature toggles
- Display preferences
- Defaults

---

# Field Definitions

Purpose

Describe checkout fields.

Storage

WordPress Options API

Future versions may support custom export/import without changing storage.

A field definition may contain:

- ID
- Type
- Label
- Placeholder
- Description
- Required
- Default Value
- Options
- Validation Rules
- Visibility Rules
- Priority

Field definitions are configuration.

They are NOT customer data.

---

# Order Data

Storage

WooCommerce Order Meta

Meta Prefix

_scfw_

Purpose

Store validated customer input.

Examples

_scfw_company

_scfw_tax_number

_scfw_delivery_note

Only validated values may be stored.

---

# Runtime Data

Purpose

Temporary execution data.

Storage

Memory only.

Examples

- Validation state
- Render context
- Processing flags

Runtime data must never be persisted.

---

# Data Ownership

Plugin Configuration

↓

Field Definitions

↓

Checkout

↓

Validation

↓

Order Meta

Configuration controls behavior.

Order Meta stores customer data.

---

# Data Flow

Configuration

↓

Field Definition

↓

Render Field

↓

Customer Input

↓

Sanitize

↓

Validate

↓

Normalize

↓

Save Order Meta

↓

Display

---

# Data Lifetime

Plugin Configuration

Persistent

Field Definitions

Persistent

Order Meta

Persistent

Runtime Data

Temporary

---

# Data Integrity

All stored values must be

Sanitized

↓

Validated

↓

Normalized

↓

Persisted

Raw customer input must never be stored.

---

# Data Access Rules

Admin

Read

Write

Plugin Configuration

Checkout

Read

Field Definitions

Validation

Read

Field Definitions

Order

Read

Write

Order Meta

Display

Read

Order Meta only.

---

# Naming Conventions

Option

scfw_options

Order Meta

_scfw_

Transient

scfw_

Constants

SCFW_

Namespace

SCFW

---

# Future Expansion

Future versions may introduce

- Import / Export
- REST API
- CLI
- Analytics

These features must reuse the existing data model.

Breaking changes are not permitted.

---

# Migration Strategy

Future versions may

Add new fields

Add new options

Add new meta keys

They must never require destructive migrations.

Backward compatibility is mandatory.

---

# Performance

Configuration should be loaded once.

Order Meta should be queried only when required.

Duplicate reads should be avoided.

No unnecessary database writes.

---

# Security

Every value must be

Sanitized

Validated

Escaped

Capability checked where applicable.

Sensitive information must never be exposed accidentally.

---

# Architecture Review Checklist

Before implementation verify

□ Configuration stored in WordPress Options

□ Customer data stored in Order Meta

□ Runtime data never persisted

□ No duplicate storage

□ No custom tables

□ Naming conventions respected

□ Backward compatibility preserved

---

END OF DOCUMENT