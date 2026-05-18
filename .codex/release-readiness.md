# Codex Release Readiness Checklist

Use this checklist before Bradley Potts reviews a release candidate.

## Version Consistency

- [ ] `SPECTRE_ICONS_VERSION` in `spectre-icons.php` matches the release version.
- [ ] Plugin header `Version:` in `spectre-icons.php` matches.
- [ ] `package.json` `version` matches.
- [ ] `readme.txt` `Stable tag:` matches.
- [ ] `CHANGELOG.md` has an entry for the release.
- [ ] `readme.txt` changelog section includes the release when appropriate.
- [ ] `CHANGELOG.md` comparison links are current.

## Protected Asset And Slug Check

- [ ] No bundled SVG source files under `assets/iconpacks/` were edited,
      renamed, deleted, regenerated, optimized, or bulk-transformed.
- [ ] `spectre-lucide` slug remains unchanged.
- [ ] `spectre-lucide-` class prefix remains unchanged.
- [ ] `spectre-lucide.json` filename remains unchanged.
- [ ] `spectre-fontawesome` slug remains unchanged.
- [ ] `spectre-fa-` class prefix remains unchanged.
- [ ] `spectre-fontawesome.json` filename remains unchanged.

Suggested commands:

```bash
git diff --name-status
git diff -- assets/iconpacks/
git diff -- assets/manifests/spectre-lucide.json assets/manifests/spectre-fontawesome.json
```

## Product Scope Review

- [ ] Changes are focused on builder icon-library expansion, registration,
      rendering, admin UX, tests, documentation, or release tooling.
- [ ] No broad Spectre ecosystem, design-system, theme, or unrelated WordPress
      feature work slipped in.
- [ ] Builder-specific changes are contained in the relevant adapter or
      integration area.
- [ ] Future builder support remains additive rather than disruptive.

## Validation

- [ ] `bin/lint-php.sh` passes.
- [ ] `composer test` passes.
- [ ] `vendor/bin/phpcs --standard=WordPress spectre-icons.php includes/`
      passes, or any deviation is explained.
- [ ] `npm run test:e2e:smoke` passes when activation or settings behavior is
      touched.
- [ ] `npm run test:e2e:elementor` passes when Elementor picker, preview, or
      rendering behavior is touched.
- [ ] Manual WordPress activation check completed when release packaging or
      bootstrap behavior changes.
- [ ] Manual Elementor editor and frontend rendering check completed when icon
      behavior changes.

## Documentation

- [ ] `README.md` is current for user-facing behavior.
- [ ] `readme.txt` is current for WordPress.org-facing behavior.
- [ ] `CHANGELOG.md` summarizes user-visible changes and compatibility notes.
- [ ] `CONTRIBUTING.md` remains accurate for setup and validation.
- [ ] `AGENTS.md`, `CLAUDE.md`, and `CODEX.md` remain aligned on agent roles and
      repository rules.

## Release Note Template

```markdown
## Codex Release Readiness

Version: x.y.z
Status: Ready / Blocked

### Changed Areas
- ...

### Validation Completed
- ...

### Validation Not Run
- ... because ...

### Documentation
- ...

### Compatibility Notes
- ...

### Blockers
- None / ...
```
