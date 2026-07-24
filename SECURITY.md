# Security Policy

## Supported versions

Nexo Agenda is under active development. Security fixes are applied to the
`main` branch. If you self-host, keep your deployment up to date with `main`.

## Reporting a vulnerability

**Please do not open a public issue for security vulnerabilities.**

Report privately through one of these channels:

- **GitHub** — use [Security Advisories](https://github.com/nexo-tools/nexo-agenda/security/advisories/new)
  ("Report a vulnerability") on this repository.
- **Email** — nexoagenda@alvarocdev.com

Please include:

- A description of the vulnerability and its impact
- Steps to reproduce (proof of concept if possible)
- The affected version / commit
- Any suggested remediation

You'll get an acknowledgment within a few days. Once the issue is confirmed and
fixed, we'll credit you in the release notes unless you prefer to stay anonymous.

## Scope

This policy covers the Nexo Agenda application code in this repository. Issues in
third-party dependencies should be reported to the respective projects, though
we appreciate a heads-up so we can bump the dependency.

## Hardening a deployment

Follow [DEPLOYMENT.md](DEPLOYMENT.md). In particular, in production:

- `APP_DEBUG=false`
- `SESSION_SECURE_COOKIE=true` (HTTPS)
- A strong, unique `APP_KEY`
- Serve only `public/` as the web root
- Keep the security headers / CSP middleware enabled (default)
