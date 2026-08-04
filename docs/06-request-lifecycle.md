# Request Lifecycle

Version: 1.0.0

Status: Architecture Freeze

---

# Purpose

This document defines the complete lifecycle of a checkout field request.

It describes how data flows through the system from rendering to persistence.

Implementation may evolve.

The lifecycle must remain stable.

---

# Overview

Every checkout field follows the same lifecycle regardless of checkout implementation.

Classic Checkout and Block Checkout must follow the same business flow.

Only the WooCommerce integration layer differs.

---

# High-Level Lifecycle

```
Field Definition

↓

Field Rendering

↓

Customer Input

↓

Sanitization

↓

Validation

↓

Normalization

↓

Order Save

↓

Order Display

↓

Customer Display

↓

Email Display
```

---

# Step 1 — Field Definition

Source

Field configuration.

Responsibilities

- Define field type
- Define label
- Define options
- Define validation rules
- Define visibility rules

Output

Field Definition Object

---

# Step 2 — Rendering

Responsibilities

- Render field
- Apply attributes
- Display default values
- Display descriptions

Performed by

Classic Checkout

or

Block Checkout

Business rules are NOT executed here.

---

# Step 3 — Customer Input

User submits checkout.

Responsibilities

Collect submitted values.

No validation.

No saving.

---

# Step 4 — Sanitization

Responsibilities

Normalize raw input.

Examples

- Trim spaces
- Convert empty strings
- Remove unsafe characters

Output

Clean Input

---

# Step 5 — Validation

Responsibilities

Required validation

Format validation

Business validation

Conditional validation

Output

Validation Result

Validation is shared between

Classic Checkout

and

Block Checkout.

---

# Step 6 — Normalization

Responsibilities

Convert validated data into a consistent internal format.

Examples

Dates

Checkbox values

Numbers

Arrays

---

# Step 7 — Order Save

Responsibilities

Persist data.

Storage

WooCommerce Order Meta

Only validated data may be stored.

---

# Step 8 — Order Display

Responsibilities

Display saved values

Admin Order

Customer Order

Thank You Page

---

# Step 9 — Customer Display

Responsibilities

My Account

Order Details

---

# Step 10 — Email Display

Responsibilities

Customer Emails

Admin Emails

---

# Error Flow

```
Customer Input

↓

Validation

↓

Validation Failed

↓

Display Errors

↓

Customer Corrects Input

↓

Validation

↓

Continue
```

---

# Rules

Rendering never validates.

Validation never renders.

Saving never validates.

Display never saves.

Each stage has one responsibility.

---

# Shared Pipeline

Classic Checkout

↓

Validation

↓

Saving

↓

Display

Block Checkout

↓

Validation

↓

Saving

↓

Display

Only rendering differs.

Everything else is shared.

---

# Security

All input must be

Sanitized

↓

Validated

↓

Escaped

↓

Stored

Never trust customer input.

---

# Performance

Validation must execute once.

Saving must execute once.

Duplicate processing is prohibited.

---

# Architecture Review Checklist

Before implementation verify

□ Rendering contains no business logic

□ Validation is shared

□ Saving occurs only after validation

□ Order Meta contains only validated data

□ Display uses stored data only

□ Classic and Blocks follow identical lifecycle

---

END OF DOCUMENT