# Checkout Fields for WooCommerce

This repository is the production-quality foundation for the Checkout Fields for WooCommerce plugin.

## Project Status

This branch implements the plugin bootstrap, the admin menu framework, a WordPress Settings API settings foundation, and the internal core foundations for the field engine, validation, and rendering. The plugin loads and its admin menu remains available whether or not WooCommerce is active. WooCommerce feature compatibility (custom order tables / HPOS) is declared through the WooCommerce FeaturesUtil API when WooCommerce is present.

Implemented foundations:

- **Bootstrap** – plugin entry point, defensive Composer autoload loading, and the plugin kernel.
- **Admin Foundation** – admin menu and WordPress Settings API settings foundation.
- **Field Foundation** – immutable `Field` value object, `FieldType` enum, validating `FieldFactory`, and the default fields provider.
- **Validation Foundation** – WordPress-independent `Validator` with per-type rules and `ValidationResult`/`ValidationError` value objects.
- **Rendering Foundation** – the `Renderer` contract shared by Classic and Block Checkout, `RenderContext`/`RenderResult` value objects, and the renderer factory.

No WooCommerce checkout hooks, checkout field persistence, or renderer implementations are wired in yet.

## Requirements

- WordPress 6.7+
- WooCommerce 9+
- PHP 8.2+

## Repository Layout

- `src/Admin/` – admin menu and settings classes
- `src/Core/` – core hook registrar and application foundations
- `src/Core/Field/` – field engine foundation
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

Contributions should remain focused on repository foundation quality only until the project moves beyond initialization.

## License

This repository is licensed under the GNU General Public License v2 or later.
