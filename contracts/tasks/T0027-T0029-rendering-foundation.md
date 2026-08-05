# Milestone Contract

## Milestone ID

T0027–T0029

---

## Milestone Name

Rendering Foundation

---

## Objective

Implement the rendering foundation for the Field Engine.

This milestone establishes the rendering contracts that will be shared by Classic Checkout and Block Checkout.

No WooCommerce integration.

No HTML rendering.

No checkout hooks.

No saving.

No validation.

---

# Required Reading

Read every file inside

contracts/core/

Read every file inside

docs/

Read all completed milestone contracts.

---

# Allowed Files

src/Core/Rendering/Renderer.php

src/Core/Rendering/RenderContext.php

src/Core/Rendering/RenderResult.php

src/Core/Rendering/RendererFactory.php

---

# Forbidden Files

Everything else.

---

# Deliverables

Create

src/Core/Rendering/

Renderer.php

RenderContext.php

RenderResult.php

RendererFactory.php

---

# Requirements

## Part 1

Create

Renderer

as an interface.

Method

render(
    Field $field,
    RenderContext $context
): RenderResult

No implementation.

---

## Part 2

Create

RenderContext

Immutable object.

Properties

- values
- attributes

Constructor injection only.

Getters only.

No setters.

---

## Part 3

Create

RenderResult

Immutable object.

Properties

- html
- assets

Constructor injection only.

Getters only.

No setters.

---

## Part 4

Create

RendererFactory

Responsibilities

Return the proper renderer implementation.

At this milestone

DO NOT instantiate renderers.

DO NOT depend on WordPress.

DO NOT depend on WooCommerce.

The factory must throw

LogicException

because renderer implementations do not yet exist.

This is intentional.

---

# Rules

Do NOT

Render HTML

Use WordPress APIs

Use WooCommerce APIs

Create checkout integration

Register hooks

Create admin UI

Create services

Create repositories

Create AJAX

Create REST API

Modify existing files

---

# Acceptance Criteria

✓ Renderer interface

✓ RenderContext

✓ RenderResult

✓ RendererFactory

✓ Immutable objects

✓ PHP 8.2

✓ Strict Types

✓ PSR-12

✓ WPCS

✓ SOLID

✓ No WordPress dependency

✓ No WooCommerce dependency

✓ No dead code

---

# Commit Message

feat: implement rendering foundation

---

# Mandatory Review

Review

- Scope
- Architecture
- SOLID
- Performance
- Maintainability
- Future Extensibility
- Overall

PASS / FAIL

PHP lint

PHPCS

---

If implementation requires architectural change

STOP

Output exactly

ARCHITECT DECISION REQUIRED

END MILESTONE