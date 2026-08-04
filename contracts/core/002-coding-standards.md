# Coding Standards Contract
## Contract ID: 002
## Version: 1.0.0

---

# Purpose

This contract defines the mandatory coding standards for the entire project.

Every PHP, JavaScript, CSS and Markdown file must follow these rules.

These rules are permanent unless updated by the Lead Architect.

---

# General Principles

Write code for humans first.

Readable code is better than clever code.

Small classes.

Small methods.

Small commits.

Single responsibility.

Consistency over personal preference.

---

# PHP Version

Minimum PHP

8.2

Use modern PHP features where appropriate.

Do not write code for legacy PHP versions.

---

# Strict Types

Every PHP file must begin with

declare(strict_types=1);

No exceptions.

---

# Coding Standards

Follow

WordPress Coding Standards

PSR-12

WooCommerce Best Practices

---

# Naming Rules

Use meaningful names.

Avoid abbreviations.

Avoid cryptic variable names.

Good

$order_meta

$field_definition

$field_manager

Bad

$tmp

$data1

$x

$obj2

---

# Class Naming

PascalCase

Examples

FieldManager

CheckoutProvider

ValidationService

Never

fieldmanager

field_manager

FIELDMANAGER

---

# Interface Naming

Suffix

Interface

Examples

CheckoutProviderInterface

StorageInterface

ValidationInterface

---

# Trait Naming

Suffix

Trait

Example

LoggerTrait

---

# Abstract Classes

Prefix

Abstract

Example

AbstractProvider

---

# Method Naming

camelCase

Examples

registerHooks()

saveOrderMeta()

validateField()

Never

RegisterHooks()

save_order_meta()

SAVE()

---

# Variables

camelCase

Examples

$orderId

$fieldKey

$fieldValue

Never

$order_id

$field_key

$tmp

---

# Constants

UPPER_CASE

Example

PLUGIN_VERSION

OPTION_NAME

---

# File Naming

One class per file.

File name must match class name.

FieldManager.php

ValidationService.php

Never

functions.php

helpers.php

misc.php

common.php

---

# Function Length

Target

Maximum 40 lines

If longer

Extract methods.

---

# Class Length

Prefer

One responsibility.

Avoid God Classes.

---

# Comments

Explain WHY

Not WHAT

Bad

Increment counter.

Good

Prevent duplicate validation during checkout refresh.

---

# PHPDoc

Public methods

Required.

Protected methods

Recommended.

Private methods

When necessary.

---

# Arrays

Prefer associative arrays

when data has meaning.

Avoid numeric magic indexes.

---

# Strings

Use single quotes

unless interpolation is required.

---

# Early Return

Prefer

Early return

Avoid nested if statements.

---

# Dependency Injection

Prefer constructor injection.

Avoid hidden dependencies.

Avoid global access.

---

# WordPress APIs

Always use

WordPress APIs

before writing custom solutions.

---

# Security

Always

sanitize input

escape output

verify nonces

check capabilities

validate data

Never trust request data.

---

# Escaping Matrix

HTML

esc_html()

Attribute

esc_attr()

URL

esc_url()

Textarea

esc_textarea()

JavaScript

wp_json_encode()

---

# Sanitization Matrix

Text

sanitize_text_field()

Textarea

sanitize_textarea_field()

Email

sanitize_email()

URL

esc_url_raw()

Integer

absint()

Boolean

rest_sanitize_boolean()

---

# Translation

Every user-facing string

must be translatable.

Use

__()

esc_html__()

esc_attr__()

Never hardcode UI strings.

---

# Logging

No debug output.

No var_dump().

No print_r().

No die().

No exit().

Use proper logging services only.

---

# Error Handling

Fail gracefully.

Never expose stack traces.

Never expose sensitive data.

---

# Performance

Avoid duplicate queries.

Avoid duplicate loops.

Avoid unnecessary allocations.

Cache expensive operations where appropriate.

---

# Dead Code

Forbidden.

Unused methods must not exist.

Unused variables must not exist.

Unused imports must not exist.

---

# Code Duplication

Duplicate logic is forbidden.

Extract reusable services.

---

# AI Rules

Never rewrite unrelated files.

Never reformat the whole project.

Modify only requested files.

---

# Self Review Checklist

Before completing a task verify

□ strict_types enabled

□ WPCS followed

□ PSR-12 followed

□ PHPDoc added

□ Translation ready

□ Sanitization complete

□ Escaping complete

□ No duplicate code

□ No dead code

□ No debug code

□ No architecture violations

---

END OF CONTRACT