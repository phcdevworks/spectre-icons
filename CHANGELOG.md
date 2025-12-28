# Changelog

All notable changes to this project will be documented here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the versioning reflects plugin releases for WordPress.

## [Unreleased]

## [1.0.0] - 2025-12-28

### Added

- Add GitHub Actions workflow for building WP ZIP ([6a2dcf1]).
- Add manifest file fallback logic for icon libraries ([30edd13]).
- Add style class to icons based on library slug ([f092992]).

### Changed

- Refactor build workflow and migrate readme to Markdown ([b6becf3]).
- Refactor WP.org ZIP build to use rsync and improve cleanup ([6fdcb6a]).
- Simplify WP zip build workflow and file copying ([ad3c5e9]).
- Refactor Elementor integration and manifest handling ([9b0a18a]).
- Refactor plugin structure and update metadata ([6d37aca]).
- Refactor SVG sanitizer for stricter, simpler sanitization ([938c02b]).
- Refactor Elementor integration hooks for Spectre Icons ([422479a]).
- Refactor icon library registration for Elementor ([28ae2db]).
- Refactor Elementor settings class for icon libraries ([f11f03d]).
- Refactor Elementor icon manifest renderer ([a94e186]).
- Refactor Spectre icon library manager for clarity and validation ([e99a137]).
- Chore updates to release ZIP ([73b06ae], [70321e0], [372eff9], [b425bf1], [f41ae0e], [8ee974d]).

### Fixed

- Fix icon prefix handling and improve style overrides ([0565550]).
- Fix zip build to avoid nested plugin folder ([2ac6f00]).

### Documentation

- Revise and expand README for Spectre Icons plugin ([c8ae77a]).

## [0.0.1] - 2025-12-10

### Added

- Add SECURITY.md with security policy ([2d91e81]).
- Add CONTRIBUTING.md with contribution guidelines ([ab64109]).
- Add project Code of Conduct ([8860a13]).
- Add GitHub templates and funding info ([53ef16e]).
- Add SVG sanitization and update icon rendering ([005a97e]).
- Add Composer and PHPCS configuration for tooling ([fde4dd2]).
- Add VS Code workspace configuration file ([5ad63c6]).
- Add WordPress.org assets and update licensing info ([21d1ddd]).
- Add PHP lint script and update README ([6141ce6]).
- Add initial Spectre Icons translation template ([c19d63d]).
- Add readme and translation support ([8300b3f]).
- Add dynamic asset versioning for cache busting ([8fe2dc3]).
- Add scoped refresh loops for icon rendering ([6b3cca2]).
- Add README and enhance icon preview observer ([0f5222d]).
- Add SVG injector for Elementor icon picker ([caca0b0]).
- Add debug tools and improve icon library manager ([36a0cab]).
- Add debug tools and improve icon picker initialization ([6b00f34]).
- Add FontAwesome brand SVG icon pack ([9202650]).
- Add Lucide icon pack and admin assets ([020ae4f]).
- Add Lucide icon library integration ([fc359e2]).
- Add Lucide icon SVGs to assets ([e9a8ba4]).

### Changed

- Refactor codebase for consistent coding standards ([12b4132]).
- Refactor code style and update plugin version ([b7a8cea]).
- Revise README with improved docs and update icon theme ([8be8e31]).
- Improve social icon color handling in admin CSS ([1024588]).
- Improve code comments and formatting for Elementor integration ([d0f0f30]).
- Refine Elementor social icon color handling ([e81c0bb]).
- Improve Elementor icon rendering and styling ([85204d1]).
- Improve Elementor social icon styling and sizing ([3c316d7]).
- Improve Elementor icon color handling in admin CSS ([e95a29a]).
- Improve icon rendering and debug logging ([5d6149b]).
- Standardize array syntax and spacing in Elementor integration ([9316091]).
- Update asset paths for admin and Elementor scripts/styles ([c3ed31c]).
- Refactor plugin to support multiple builders ([ac31af7]).
- Update README and improve icon CSS overrides ([bf8a82a]).
- Prevent redundant SVG rendering in icon admin JS ([b9bfb1c]).
- Update icon manifests and renderer logic ([2233f56]).
- Remove development stubs and debug scripts ([ae3e698]).
- Refactor icon picker SVG injection for Elementor ([6d8b992]).
- Remove debug and testing files, update manifests ([dbca981]).
- Switch to native Elementor icon rendering ([d966ed5]).
- Refactor icon admin JS and clean up PHP manifest ([36bcef9]).
- Improve icon style and picker modal handling ([8002ed8]).
- Update Lucide icon manifest and class ([925d0be]).
- Update icon manifests for FontAwesome and Lucide ([011a84a]).
- Refactor icon rendering and caching logic ([8262d35]).
- Refactor icon styles registration and add rendered class ([73bec0a]).

### Documentation

- Release v1.0.0 and improve Elementor SVG color support ([02b6f1e]).

### Fixed

- Enable Intelephense diagnostics and add stubs path ([cb12589]).

[unreleased]: https://github.com/phcdevworks/spectre-icons/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/phcdevworks/spectre-icons/tree/v1.0.0
[0.0.1]: https://github.com/phcdevworks/spectre-icons/tree/v0.0.1
[f092992]: https://github.com/phcdevworks/spectre-icons/commit/f092992
[0565550]: https://github.com/phcdevworks/spectre-icons/commit/0565550
[73b06ae]: https://github.com/phcdevworks/spectre-icons/commit/73b06ae
[70321e0]: https://github.com/phcdevworks/spectre-icons/commit/70321e0
[9b0a18a]: https://github.com/phcdevworks/spectre-icons/commit/9b0a18a
[372eff9]: https://github.com/phcdevworks/spectre-icons/commit/372eff9
[30edd13]: https://github.com/phcdevworks/spectre-icons/commit/30edd13
[b425bf1]: https://github.com/phcdevworks/spectre-icons/commit/b425bf1
[2ac6f00]: https://github.com/phcdevworks/spectre-icons/commit/2ac6f00
[f41ae0e]: https://github.com/phcdevworks/spectre-icons/commit/f41ae0e
[ad3c5e9]: https://github.com/phcdevworks/spectre-icons/commit/ad3c5e9
[6fdcb6a]: https://github.com/phcdevworks/spectre-icons/commit/6fdcb6a
[8ee974d]: https://github.com/phcdevworks/spectre-icons/commit/8ee974d
[b6becf3]: https://github.com/phcdevworks/spectre-icons/commit/b6becf3
[6a2dcf1]: https://github.com/phcdevworks/spectre-icons/commit/6a2dcf1
[6d37aca]: https://github.com/phcdevworks/spectre-icons/commit/6d37aca
[938c02b]: https://github.com/phcdevworks/spectre-icons/commit/938c02b
[422479a]: https://github.com/phcdevworks/spectre-icons/commit/422479a
[28ae2db]: https://github.com/phcdevworks/spectre-icons/commit/28ae2db
[f11f03d]: https://github.com/phcdevworks/spectre-icons/commit/f11f03d
[a94e186]: https://github.com/phcdevworks/spectre-icons/commit/a94e186
[e99a137]: https://github.com/phcdevworks/spectre-icons/commit/e99a137
[c8ae77a]: https://github.com/phcdevworks/spectre-icons/commit/c8ae77a
[2d91e81]: https://github.com/phcdevworks/spectre-icons/commit/2d91e81
[ab64109]: https://github.com/phcdevworks/spectre-icons/commit/ab64109
[8860a13]: https://github.com/phcdevworks/spectre-icons/commit/8860a13
[53ef16e]: https://github.com/phcdevworks/spectre-icons/commit/53ef16e
[12b4132]: https://github.com/phcdevworks/spectre-icons/commit/12b4132
[b7a8cea]: https://github.com/phcdevworks/spectre-icons/commit/b7a8cea
[005a97e]: https://github.com/phcdevworks/spectre-icons/commit/005a97e
[8be8e31]: https://github.com/phcdevworks/spectre-icons/commit/8be8e31
[1024588]: https://github.com/phcdevworks/spectre-icons/commit/1024588
[d0f0f30]: https://github.com/phcdevworks/spectre-icons/commit/d0f0f30
[e81c0bb]: https://github.com/phcdevworks/spectre-icons/commit/e81c0bb
[85204d1]: https://github.com/phcdevworks/spectre-icons/commit/85204d1
[3c316d7]: https://github.com/phcdevworks/spectre-icons/commit/3c316d7
[e95a29a]: https://github.com/phcdevworks/spectre-icons/commit/e95a29a
[5d6149b]: https://github.com/phcdevworks/spectre-icons/commit/5d6149b
[9316091]: https://github.com/phcdevworks/spectre-icons/commit/9316091
[fde4dd2]: https://github.com/phcdevworks/spectre-icons/commit/fde4dd2
[02b6f1e]: https://github.com/phcdevworks/spectre-icons/commit/02b6f1e
[5ad63c6]: https://github.com/phcdevworks/spectre-icons/commit/5ad63c6
[21d1ddd]: https://github.com/phcdevworks/spectre-icons/commit/21d1ddd
[6141ce6]: https://github.com/phcdevworks/spectre-icons/commit/6141ce6
[c19d63d]: https://github.com/phcdevworks/spectre-icons/commit/c19d63d
[8300b3f]: https://github.com/phcdevworks/spectre-icons/commit/8300b3f
[c3ed31c]: https://github.com/phcdevworks/spectre-icons/commit/c3ed31c
[ac31af7]: https://github.com/phcdevworks/spectre-icons/commit/ac31af7
[bf8a82a]: https://github.com/phcdevworks/spectre-icons/commit/bf8a82a
[8fe2dc3]: https://github.com/phcdevworks/spectre-icons/commit/8fe2dc3
[6b3cca2]: https://github.com/phcdevworks/spectre-icons/commit/6b3cca2
[b9bfb1c]: https://github.com/phcdevworks/spectre-icons/commit/b9bfb1c
[2233f56]: https://github.com/phcdevworks/spectre-icons/commit/2233f56
[0f5222d]: https://github.com/phcdevworks/spectre-icons/commit/0f5222d
[ae3e698]: https://github.com/phcdevworks/spectre-icons/commit/ae3e698
[cb12589]: https://github.com/phcdevworks/spectre-icons/commit/cb12589
[6d8b992]: https://github.com/phcdevworks/spectre-icons/commit/6d8b992
[caca0b0]: https://github.com/phcdevworks/spectre-icons/commit/caca0b0
[dbca981]: https://github.com/phcdevworks/spectre-icons/commit/dbca981
[36a0cab]: https://github.com/phcdevworks/spectre-icons/commit/36a0cab
[d966ed5]: https://github.com/phcdevworks/spectre-icons/commit/d966ed5
[36bcef9]: https://github.com/phcdevworks/spectre-icons/commit/36bcef9
[6b00f34]: https://github.com/phcdevworks/spectre-icons/commit/6b00f34
[8002ed8]: https://github.com/phcdevworks/spectre-icons/commit/8002ed8
[925d0be]: https://github.com/phcdevworks/spectre-icons/commit/925d0be
[011a84a]: https://github.com/phcdevworks/spectre-icons/commit/011a84a
[8262d35]: https://github.com/phcdevworks/spectre-icons/commit/8262d35
[9202650]: https://github.com/phcdevworks/spectre-icons/commit/9202650
[73bec0a]: https://github.com/phcdevworks/spectre-icons/commit/73bec0a
[020ae4f]: https://github.com/phcdevworks/spectre-icons/commit/020ae4f
[fc359e2]: https://github.com/phcdevworks/spectre-icons/commit/fc359e2
[e9a8ba4]: https://github.com/phcdevworks/spectre-icons/commit/e9a8ba4
