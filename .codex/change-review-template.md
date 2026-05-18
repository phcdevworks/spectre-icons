# Codex Change Review Template

Use this when reviewing a Claude Code change set, pull request, or release
candidate.

## Scope

- Change owner:
- Requested outcome:
- Files changed:
- Product area:
- Agent boundary touched:

## Findings

List release-blocking or production-risk findings first.

```markdown
- [Severity] `path/to/file.php:123` - What can break, why it matters, and the
  smallest safe fix.
```

Severity guide:

- `Blocker`: breaks activation, rendering, saved icons, protected slugs, release
  packaging, or security expectations.
- `High`: likely regression in supported builder, settings, registry, sanitizer,
  or frontend/editor rendering.
- `Medium`: maintainability, compatibility, or documentation issue that should
  be fixed before release.
- `Low`: polish or follow-up that should not block a patch release.

## Invariant Check

- [ ] Bundled SVG assets untouched.
- [ ] Locked slugs and prefixes preserved.
- [ ] Manifest filenames preserved or migration explicitly documented.
- [ ] Existing saved icon class values remain compatible.

## Validation Notes

- Commands run:
- Manual checks:
- Checks skipped:
- Residual risk:

## Documentation Notes

- Docs updated:
- Docs still needed:
- Changelog needed:
- Release note needed:
- Config cleanup needed:

## Recommendation

Choose one:

- Ready for Bradley review.
- Ready after listed non-blocking cleanup.
- Blocked until findings are fixed.
