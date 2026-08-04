# Project Conventions
## Contract ID: 003
## Version: 1.0.0

---

# Purpose

This document defines the permanent naming conventions used throughout the project.

These conventions are mandatory.

No AI may change them.

Only the Lead Architect may modify this document.

---

# Project Identity

Plugin Name

Checkout Fields for WooCommerce

Repository

https://github.com/ahmodmusa/wc-checkout-fields

Author

Ahmod Musa

Author URI

https://ahmodmusa.com

GitHub Username

ahmodmusa

License

GPL-2.0-or-later

---

# Text Domain

checkout-fields-for-woocommerce

Never change.

---

# Namespace

SCFW

Never change.

All project classes must belong to this namespace.

Example

SCFW\Admin

SCFW\Core

SCFW\Services

---

# PHP Namespace Rules

Every PHP class must use namespaces.

Never use the global namespace.

One namespace per file.

Never mix namespaces.

---

# Prefix

scfw_

Used for

Functions

Constants

Actions

Filters

Transient Keys

Cache Keys

Internal identifiers

---

# Order Meta Prefix

_scfw_

Every order meta key must begin with

_scfw_

Examples

_scfw_delivery_date

_scfw_tax_number

Never create order meta without this prefix.

---

# Option Name

scfw_options

Global plugin settings

Only one root option should exist unless explicitly approved.

---

# Constant Prefix

SCFW_

Examples

SCFW_VERSION

SCFW_FILE

SCFW_PATH

SCFW_URL

SCFW_MIN_PHP

Never introduce different prefixes.

---

# Hook Prefix

Actions

scfw_

Filters

scfw_

Examples

scfw_before_render

scfw_after_save

scfw_validate_field

Never use generic hook names.

---

# CSS Prefix

scfw-

Examples

scfw-field

scfw-label

scfw-wrapper

Never use generic CSS class names.

---

# JavaScript Prefix

scfw

Examples

window.scfw

scfwSettings

scfwAdmin

---

# Function Naming

Use verbs.

Examples

registerHooks()

renderFields()

saveOrderMeta()

validateFields()

Never use

process()

run()

execute()

handle()

unless absolutely necessary.

---

# Service Naming

Suffix

Service

Examples

ValidationService

OrderMetaService

AssetService

---

# Provider Naming

Suffix

Provider

Examples

ClassicCheckoutProvider

BlocksCheckoutProvider

---

# Interface Naming

Suffix

Interface

Examples

StorageInterface

CheckoutProviderInterface

---

# Trait Naming

Suffix

Trait

---

# Abstract Classes

Prefix

Abstract

---

# Event Names

Always begin with

scfw_

---

# Nonce Names

Always begin with

scfw_

---

# Cache Keys

Always begin with

scfw_

---

# Transients

Always begin with

scfw_

---

# REST Namespace

scfw/v1

Future versions

scfw/v2

scfw/v3

---

# Database

No custom tables in Lite.

Persistent storage

WordPress Options

Order Meta

WordPress APIs

---

# Translation Domain

checkout-fields-for-woocommerce

Never hardcode another domain.

---

# File Organization

One class

One file

One responsibility

---

# Commit Messages

Use Conventional Commits.

Examples

feat:

fix:

refactor:

docs:

test:

perf:

style:

chore:

build:

ci:

Never invent custom commit prefixes.

---

# Version Strategy

0.0.1

Repository Foundation

0.1.0

Bootstrap

0.2.0

Core

0.5.0

Feature Complete

0.9.0

Beta

1.0.0

First WordPress.org Release

---

# AI Rules

Never modify this document.

Never invent new prefixes.

Never invent new namespaces.

Never rename anything.

If naming changes are required

Output

ARCHITECT DECISION REQUIRED

---

# Self Review

Before completing any task verify

□ Namespace correct

□ Prefix correct

□ Meta prefix correct

□ Hook prefix correct

□ Text Domain correct

□ REST namespace correct

□ Constant prefix correct

□ CSS prefix correct

□ JavaScript prefix correct

□ Commit message correct

---

END OF CONTRACT