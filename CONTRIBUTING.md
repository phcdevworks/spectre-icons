# Contributing to Spectre Icons

Thanks for helping improve Spectre Icons! This WordPress plugin provides curated icon libraries for Elementor with manifest-driven performance and seamless color integration.

## Development Philosophy

This plugin follows a **WordPress and PHP-first** approach:

### 1. WordPress Plugin Architecture

**Purpose**: Integration with WordPress core and Elementor page builder

**Key Components**:

- Plugin bootstrap and activation hooks
- Admin settings panel for library management
- SVG sanitization and security

**Rules**:

- Follow WordPress Coding Standards (WPCS)
- Use proper WordPress hooks and filters
- Maintain backward compatibility with WordPress 6.0+
- Never execute code without proper capability checks

**Status**: v1.1.0 with full Elementor integration

### 2. Icon Library System

**Purpose**: Manifest-driven icon loading for optimal performance

**Ships**:

- JSON manifests for fast icon lookups
- SVG files organized by library
- Admin UI for toggling libraries

**Rules**:

- Always update manifests when adding icons
- Ensure GPL-compatible licensing
- Keep SVG files optimized and clean

**Status**: Lucide and Font Awesome Free fully integrated

### 3. Elementor Integration

**Purpose**: Seamless icon injection into Elementor's icon controls

**Key mechanism**:

- Custom icon libraries registered via Elementor's API
- Live preview injection in editor
- Color inheritance and branding support

**Rules**:

- Test both editor and frontend rendering
- Ensure compatibility with all icon widgets
- Follow Elementor's icon library structure

**Status**: Full support for Elementor 3.x+

### Golden Rule (Non-Negotiable)

**WordPress standards. Security first. Performance always.**

- All code follows WordPress Coding Standards
- All output is escaped, all input is sanitized
- SVGs are validated and sanitized before rendering
- Manifest loading is optimized for performance

## Development Setup

1. Clone the repository:

```bash
git clone https://github.com/phcdevworks/spectre-icons.git
cd spectre-icons
```

2. Install development dependencies:

```bash
composer install
```

3. Verify PHP syntax and coding standards:

```bash
bin/lint-php.sh
vendor/bin/phpcs --standard=WordPress spectre-icons.php includes/
```

## Project Structure

```
spectre-icons/
├── spectre-icons.php            # Plugin bootstrap
├── includes/
│   ├── class-spectre-icons-svg-sanitizer.php
│   └── elementor/
│       ├── class-spectre-icons-elementor-library-manager.php
│       ├── class-spectre-icons-elementor-manifest-renderer.php
│       ├── class-spectre-icons-elementor-settings.php
│       ├── icon-libraries.php
│       └── integration-hooks.php
├── assets/
│   ├── iconpacks/               # Raw SVG files
│   ├── manifests/               # JSON manifests
│   ├── js/                      # Client-side scripts
│   └── css/                     # Styles
└── .wordpress-org/              # WordPress.org assets
```

**Responsibilities**:

- **Plugin developers**: Edit PHP files and WordPress integration
- **Icon maintainers**: Update icon packs and manifests
- **Frontend developers**: Update JavaScript and CSS assets

## Contribution Guidelines

### Icon & Manifest Development

1. **Source SVGs**: Place icon files in `assets/iconpacks/<library-name>/`
2. **Update manifests**: Regenerate JSON manifests after adding icons
3. **Licensing**: Ensure GPL-compatible licenses for all icon packs
4. **Testing**: Verify icons render correctly in Elementor

### PHP & WordPress Development

- Follow WordPress Coding Standards for PHP
- Use proper escaping and sanitization
- Add comments for complex logic
- Test with PHP 7.4+ and 8.x
- Ensure WordPress 6.0+ compatibility

### Code Quality

- Run `bin/lint-php.sh` before committing
- Use `phpcs` to check WordPress coding standards
- Test in both Elementor editor and frontend
- Verify no JavaScript console errors
- Check color inheritance and branding features

### Documentation

- Update README.md when adding features
- Include code examples for new features
- Document breaking changes in commit messages
- Keep inline comments clear and concise

## Pull Request Process

1. **Branch from `main`**
2. **Make your changes** and test locally:
   - Run `bin/lint-php.sh` for syntax checks
   - Test in WordPress + Elementor environment
   - Verify icon rendering and color controls
3. **Update documentation** (README.md, readme.txt) as needed
4. **Open a PR** describing:
   - The motivation for the change
   - What was changed
   - Testing notes (WordPress version, Elementor version tested)
5. **Respond to feedback** and make requested changes

## Known Gaps (Not Done Yet)

- Gutenberg (Block Editor) integration
- Beaver Builder support
- Additional icon library integrations
- Advanced icon animation options
- Icon search and filtering improvements
- Custom icon upload functionality

## Questions or Issues?

Please open an issue or discussion on GitHub if you're unsure about the best approach for a change. Coordinating early avoids conflicts with:

- WordPress plugin architecture
- Elementor compatibility
- Icon library structure

## Code of Conduct

This project adheres to the [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code. Please report unacceptable behavior to the project maintainers.

## License

By contributing, you agree that your contributions will be licensed under the GPL-2.0-or-later License.
