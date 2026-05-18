# Codex Release Readiness Checklist

Use this checklist before Bradley Potts reviews a release candidate.

## Version Consistency

- [ ] `SPECTRE_ICONS_VERSION` in `spectre-icons.php` matches the release version.
- [ ] Plugin header `Version:` in `spectre-icons.php` matches.
- [ ] `package.json` `version` matches.
- [ ] `package-lock.json` root package version matches.
- [ ] `readme.txt` `Stable tag:` matches.
- [ ] `CHANGELOG.md` has an entry for the release.
- [ ] `readme.txt` changelog section includes the release when appropriate.
- [ ] `CHANGELOG.md` comparison links are current.

## Package Metadata And Contracts

- [ ] `package.json` name, description, license, repository, bugs, homepage, and
      scripts are accurate.
- [ ] Public WordPress plugin behavior, hooks, settings, and saved icon class
      contracts match documentation.
- [ ] Public exports or generated type declarations are checked if this plugin
      later gains a build/export surface.
- [ ] Build output is current if build artifacts are part of the release.
- [ ] Generated files are not stale and are clearly separated from source files.
- [ ] Breaking changes are clearly marked, or the release is confirmed
      backward-compatible.

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

- [ ] `npm run check` passes.
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
- [ ] `AGENTS.md`, `CLAUDE.md`, `CODEX.md`, and
      `.github/copilot-instructions.md` remain aligned on agent roles and
      repository rules.
- [ ] `.codex/*` checklists remain current for release, review, documentation,
      and repo-hygiene workflows.

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

### Release Notes
- ...

### Compatibility Notes
- ...

### Blockers
- None / ...
```
