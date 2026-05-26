# Codex Release Readiness Checklist

Use this checklist before Bradley Potts reviews a release candidate.

## ⛔ Hard Gates — Release Is BLOCKED Until All Pass

These are not optional. Every release, every time, no exceptions.

- [ ] `npm run check` passes (PHP unit tests + PHPCS).
- [ ] `npm run test:e2e:smoke` passes against a running wp-env.
- [ ] `npm run test:e2e:elementor` passes — verifies icons render on the **published frontend**, not only in the editor.
- [ ] `npm run test:e2e:my-icons` passes — verifies My Icons (spectre-user) uploaded SVGs render as inline SVG on the published frontend.

> **Why these are hard gates:** PHPUnit cannot catch silent frontend render failures.
> `render_icon` returns empty string with no exception when the manifest registry
> is not populated. Only a real browser visiting a published page can confirm that
> icons actually appear. This was the cause of the 1.4.0 production breakage on
> 300+ sites. It will not ship broken again.

To run the full gate locally:

```bash
npm run test:e2e:setup   # start wp-env + install Elementor (once per session)
npm run check:full       # PHP tests + lint + all three E2E suites
```

CI runs all of this automatically on every PR and push to main via the
`e2e-tests` job in `.github/workflows/tests.yml`. A PR cannot merge if the
E2E job is red.

---

## Version Consistency

- [ ] `SPECTRE_ICONS_VERSION` in `spectre-icons.php` matches the release version.
- [ ] Plugin header `Version:` in `spectre-icons.php` matches.
- [ ] Plugin header `Tested up to:` in `spectre-icons.php` matches the latest
      verified WordPress version.
- [ ] `package.json` `version` matches.
- [ ] `package-lock.json` root package version matches.
- [ ] `readme.txt` `Stable tag:` matches.
- [ ] `readme.txt` `Tested up to:` matches the latest verified WordPress version.
- [ ] `CHANGELOG.md` has an entry for the release.
- [ ] `readme.txt` changelog section includes the release when appropriate.
- [ ] `CHANGELOG.md` comparison links are current.
- [ ] README and WordPress.org docs state the verified Elementor support range,
      including Elementor 4.x when tested.

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
- [ ] `spectre-user` slug remains unchanged.
- [ ] `spectre-user-` class prefix remains unchanged.

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

- [ ] **`npm run check:full` passes** (PHP tests + lint + all E2E suites). This is the single command that proves the release is safe.
- [ ] `bin/lint-php.sh` passes.
- [ ] `vendor/bin/phpcs --standard=WordPress spectre-icons.php includes/` passes, or any deviation is explained.
- [ ] Manual WordPress activation check completed when release packaging or bootstrap behavior changes.

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
- [ ] npm run check:full (PHP + lint + E2E smoke + elementor + my-icons)
- [ ] Manual activation check
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
