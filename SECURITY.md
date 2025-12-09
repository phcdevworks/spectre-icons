# Security Policy

## Supported Versions

We aim to support the latest published version of Spectre Icons. Older releases may not receive security fixes.

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

If you discover a security issue, please **do not** open a public issue. Instead, email the maintainers at **security@phcdevworks.com** or report via GitHub's private security advisory feature.

Provide as much detail as possible to help us reproduce and assess impact:

- A description of the vulnerability and potential impact
- Steps to reproduce or proof-of-concept
- Affected versions (if known)
- Any suggested fixes or mitigations

We will acknowledge receipt within **48 hours** and keep you informed of the fix status. Responsible disclosure is appreciated.

## Security Best Practices

When using Spectre Icons:

- Always use the latest version to receive security patches
- Review the [changelog](readme.txt) for security-related updates
- Follow the security advisories in this repository
- Report any XSS, SVG injection, or privilege escalation concerns immediately
- Ensure proper WordPress user capabilities are enforced (plugin only allows `manage_options` for settings)
- Verify icon manifest files are from trusted sources before deploying

## WordPress-Specific Security

This plugin follows WordPress security best practices:

- All user input is sanitized and validated
- All output is properly escaped
- Settings require `manage_options` capability
- No direct file execution (ABSPATH checks)
- No eval() or unserialize() usage
- SVG content is served from static manifests (not user-generated)

Thank you for helping keep Spectre Icons secure!
