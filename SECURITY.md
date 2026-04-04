# Security Policy

## Supported versions

Security fixes are applied to the latest published release line of Spectre
Icons.

| Version | Supported |
| ------- | --------- |
| 1.x     | Yes       |
| < 1.0   | No        |

Use the most recent plugin release whenever possible. Older releases may not
receive security fixes.

## Reporting a vulnerability

Do not open public issues for suspected security vulnerabilities.

Use GitHub Security Advisories to report vulnerabilities privately:

- [Report a vulnerability](https://github.com/phcdevworks/spectre-icons/security/advisories/new)

If GitHub Security Advisories are unavailable for your report, contact the
maintainers privately through GitHub rather than posting details publicly.

## What to include

Please include as much of the following as you can:

- a clear description of the issue and potential impact
- affected versions, if known
- reproduction steps or a proof of concept
- any relevant environment details such as WordPress, PHP, and Elementor
  versions
- suggested mitigations, if you have them

## What to expect

- acknowledgment within a reasonable time after receipt
- review and triage of the report
- follow-up questions if reproduction details are needed
- coordinated disclosure once a fix or mitigation is ready

## Scope

This policy covers security issues in:

- the Spectre Icons plugin code
- builder integration code maintained in this repository
- manifest loading, registration, and rendering logic
- SVG sanitization and related output handling

This policy does not cover:

- WordPress core vulnerabilities
- Elementor vulnerabilities outside this repository
- server, hosting, or deployment misconfiguration
- vulnerabilities in third-party services or projects that are not maintained in
  this repository

## Security expectations for contributors

When contributing to Spectre Icons:

- sanitize input and escape output consistently
- keep manifest and path handling defensive
- preserve or improve SVG sanitization behavior
- avoid introducing brittle builder hooks or unsafe rendering shortcuts
- report security concerns privately before discussing them publicly
