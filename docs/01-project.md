# Project Overview

# Checkout Fields for WooCommerce

Version: 1.0.0

Status: Architecture Freeze

Repository:
https://github.com/ahmodmusa/wc-checkout-fields

License:
GPL-2.0-or-later

Author:
Ahmod Musa

---

# Vision

Build the most modern, lightweight, extensible and developer-friendly checkout field plugin for WooCommerce.

The plugin must support both Classic Checkout and Block Checkout as first-class citizens from the first stable release.

The project prioritizes simplicity, performance, long-term maintainability and WordPress.org compliance over feature quantity.

---

# Mission

Deliver a production-quality WooCommerce plugin that follows modern PHP practices while remaining fully compatible with the WordPress ecosystem.

The Lite version must provide a complete checkout field solution.

The Pro version must extend Lite without modifying Lite architecture.

---

# Primary Goals

- WordPress.org first
- WooCommerce-first architecture
- Classic Checkout support
- Block Checkout support
- Shared Lite / Pro architecture
- PHP 8.2+
- WordPress 6.7+
- WooCommerce 9+
- Production-quality code
- Minimal dependencies
- High performance
- Long-term maintainability

---

# Non Goals

The project will NOT include:

- Page Builder integrations
- Form Builder replacement
- CRM functionality
- Marketing automation
- Analytics platform
- Payment Gateway
- Shipping solution

Those belong to independent plugins.

---

# Target Users

Primary

- WooCommerce store owners

Secondary

- WooCommerce agencies

- Freelance developers

- Enterprise WooCommerce developers

---

# Lite Features

Field Types

- Text
- Textarea
- Email
- Telephone
- Number
- Date
- Checkbox
- Radio
- Select

Core Features

- Required fields
- Placeholder
- Description
- Validation
- Order Meta
- Admin Order display
- Customer Email display
- My Account display

Checkout Support

- Classic Checkout
- Block Checkout

---

# Pro Features

- Conditional Logic
- Product Rules
- Category Rules
- User Role Rules
- Country Rules
- Cart Rules
- File Upload
- Advanced Validation
- Import / Export
- Analytics
- Field Groups
- Repeater Fields

---

# Technical Stack

Language

PHP 8.2+

Platform

WordPress 6.7+

Commerce

WooCommerce 9+

JavaScript

Modern ESNext

Build

Composer

Coding Standards

WordPress Coding Standards

PSR-12

Static Analysis

PHPStan

---

# Project Identity

Namespace

SCFW

Prefix

scfw_

Option Name

scfw_options

Meta Prefix

_scfw_

Text Domain

checkout-fields-for-woocommerce

Slug

checkout-fields-for-woocommerce

---

# Design Principles

The project follows

- SOLID
- KISS
- DRY
- YAGNI
- Composition over Inheritance
- WordPress First
- WooCommerce First

---

# Performance Goals

- Load assets only when necessary.
- No unnecessary database queries.
- No duplicate hooks.
- Lazy loading where appropriate.
- No external framework dependency.

---

# Compatibility Goals

Supported

- WooCommerce Classic Checkout
- WooCommerce Block Checkout

Browsers

- Chrome
- Firefox
- Edge
- Safari

---

# Release Strategy

Phase 1

WordPress.org Lite Release

Phase 2

Commercial Pro Release

Phase 3

Enterprise Features

---

# Success Criteria

The project is considered successful when:

- Passes WordPress.org review.
- Passes PHPCS.
- Passes PHPStan.
- Supports Classic Checkout.
- Supports Block Checkout.
- Provides identical field behavior across both checkout systems whenever technically possible.
- Maintains a stable architecture suitable for long-term development.

---

END OF DOCUMENT