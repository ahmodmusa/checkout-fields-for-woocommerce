# Changelog

All notable changes to this project will be documented in this file.

## [0.0.1] - 2026-08-04

### Added
- Repository foundation scaffold for WordPress.org production readiness.
- WordPress plugin bootstrap entry file with defensive autoload loading.
- Composer, PHPCS, and PHPStan configuration.
- WordPress.org plugin readme and GitHub README foundation.
- WooCommerce dependency guard service.
- Central hook registrar and plugin kernel wiring.
- Admin menu framework and WordPress Settings API settings foundation.

### Changed
- Moved the WooCommerce availability check from plugin-include time into the WordPress plugin lifecycle.
- Tightened repository metadata and package configuration without altering the project identity.

### Security
- Hardened the bootstrap so a missing Composer autoloader cannot white-screen the site.
- Added a sanitize callback for the registered settings option.
- Kept the bootstrap file minimal and ABSPATH protected.
