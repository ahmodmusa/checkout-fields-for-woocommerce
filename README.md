# Checkout Fields for WooCommerce

This repository is the production-quality foundation for the Checkout Fields for WooCommerce plugin.

## Project Status

This branch provides the plugin bootstrap, the admin menu framework, and a WordPress Settings API settings foundation. The plugin loads and its admin menu remains available whether or not WooCommerce is active. No business logic, WooCommerce checkout hooks, or checkout fields are implemented yet.

## Requirements

- WordPress 6.7+
- WooCommerce 9+
- PHP 8.2+

## Repository Layout

- `src/Admin/` – admin menu and settings classes
- `src/Core/` – central hook registrar
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
