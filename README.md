# Checkout Fields for WooCommerce

This repository is the production-quality foundation for the Checkout Fields for WooCommerce plugin.

## Project Status

This branch implements configurable checkout fields for WooCommerce Classic and Block Checkout, including administration, import/export, ordering, validation, and HPOS-compatible order display. The plugin loads and its admin menu remains available whether or not WooCommerce is active. WooCommerce feature compatibility (custom order tables / HPOS) is declared through the WooCommerce FeaturesUtil API when WooCommerce is present.

Implemented functionality:

- **Bootstrap** – plugin entry point, defensive Composer autoload loading, and the plugin kernel.
- **Administration** – admin menu, Settings API field builder, deterministic ordering, and protected JSON import/export.
- **Field Engine** – immutable `Field` value object, `FieldType` enum, validating `FieldFactory`, default fields, and the settings-backed `FieldRepository`.
- **Validation Foundation** – WordPress-independent `Validator` with per-type rules and `ValidationResult`/`ValidationError` value objects.
- **Rendering Foundation** – the `Renderer` contract shared by Classic and Block Checkout, `RenderContext`/`RenderResult` value objects, and the renderer factory.
- **Checkout Integration** – configurable Classic and Block Checkout adapters with shared validation and `_scfw_` order metadata.
- **Admin Order Display** – escaped, capability-protected current and historical checkout values through WooCommerce order APIs.
- **Customer Emails** – escaped checkout-field values in customer and administrative WooCommerce order emails.
- **Thank You Page** – canonical escaped checkout-field presentation on the WooCommerce order-received surface.
- **My Account** – authorized checkout-field presentation in the customer's WooCommerce order view.
- **REST Order Response** – additive `scfw_fields` data on the existing administrator/shop-manager WooCommerce order REST response, using HPOS-safe order APIs.

The global `scfw_options['enabled']` setting controls whether configured fields render, validate, register, or save. The administration page remains available when the feature is disabled.

## Requirements

- WordPress 6.7+
- WooCommerce 9+
- PHP 8.2+

## Repository Layout

- `src/Admin/` – administration, field builder, import/export, and order display classes
- `src/Core/` – core hook registrar and application foundations
- `src/Core/Field/` – field engine and settings-backed field repository
- `src/Core/Validation/` – validation foundation
- `src/Core/Rendering/` – rendering foundation
- `src/Services/` – reusable services
- `assets/` – static assets directory
- `languages/` – translation domain files
- `tests/` – test suite directory
- `docs/` – project documentation

## Development Quality Gates

- Composer for package management
- PHPCS for WordPress coding standards
- PHPStan for static analysis
- WordPress.org-ready metadata and packaging files

## Contributing

Contributions should preserve the documented Classic/Block adapter architecture and the existing WordPress/WooCommerce storage conventions.

## License

This repository is licensed under the GNU General Public License v2 or later.
