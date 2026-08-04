# Architecture

## Principles
- SOLID
- WordPress Coding Standards
- PSR-12
- Strict Types
- Service-based architecture
- Provider Pattern

## Structure

src/
- Admin
- Core
- Frontend
- Helpers
- Services
- Contracts
- Views

## Checkout Providers

CheckoutManager
├── ClassicCheckoutProvider
└── BlocksCheckoutProvider

Business logic lives only inside Core/Services.

Classic and Blocks only communicate with WooCommerce APIs.

## Data Storage

- scfw_options
- _scfw_* order meta

No custom database tables in v1.
