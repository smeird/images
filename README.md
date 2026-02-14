# images

Astronomy image showcase website with a public gallery and a secure admin upload backdoor, implemented with PHP + JSON storage for quick deployment on Apache.

## Project status

**Current maturity:** MVP+ visual polish implemented and runnable locally.

Implemented now:
- public gallery and image detail pages
- cinematic dark-sky visual treatment with starfield texture, spotlight hero card, and glassmorphism-style panels
- ambient micro-interactions (hover lift/glow, metadata chips, richer card transitions)
- Repository intentionally does not include bundled `.jpg` sample images; upload your own media through the admin flow.
- metadata display (capture, equipment, exposure, processing, tags)
- secure admin route with session auth, CSRF protection, basic login rate limiting, and in-session password change controls
- image upload pipeline with MIME/size validation and thumbnail generation
- graceful oversize-upload handling that reports when server (`post_max_size` / `upload_max_filesize`) or app (`MAX_UPLOAD_BYTES`) limits reject a request before PHP can parse form fields
- Wikipedia URL normalization uses PHP 7.4-compatible string checks (no PHP 8-only helpers) to avoid runtime fatals on older deployments.

Planned next:
- richer filtering/search, editing/deleting uploads, and stronger production hardening.

## Runtime/build assumptions

- Linux environment
- PHP 7.4+ with GD enabled
- Apache (`mod_rewrite`) or PHP built-in dev server
- Writable `storage/` directory (the app now persists PHP sessions in `storage/sessions` to keep CSRF/session state stable across environments)

## Local development

```bash
cd /workspace/images
php -S 0.0.0.0:8080 -t public public/index.php
```

Then open `http://localhost:8080`.

## Apache configuration (recommended)

Use `public/` as the Apache document root so that `storage/` is never directly web-accessible.

```apache
<VirtualHost *:80>
    ServerName images.local
    DocumentRoot /var/www/images/public

    <Directory /var/www/images/public>
        Options -Indexes +FollowSymLinks
        AllowOverride None
        Require all granted

        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^ index.php [QSA,L]
    </Directory>

    # Optional runtime overrides
    SetEnv ADMIN_ROUTE /hidden-admin
    SetEnv SITE_NAME "Night Sky Atlas"
    SetEnv MAX_UPLOAD_BYTES 33554432

    # Keep Apache/PHP body limits aligned for large uploads
    php_value upload_max_filesize 32M
    php_value post_max_size 32M

    ErrorLog ${APACHE_LOG_DIR}/images-error.log
    CustomLog ${APACHE_LOG_DIR}/images-access.log combined
</VirtualHost>
```

Enable required modules/sites and reload Apache:

```bash
sudo a2enmod rewrite
sudo a2ensite images.conf
sudo systemctl reload apache2
```

If you deploy behind HTTPS, keep the same `DocumentRoot` and route all HTTP traffic to HTTPS at the Apache or load-balancer layer.

### Default admin access (change immediately)

- Route: `/hidden-admin/login`
- Username: `admin`
- Password: `change-me-now`
- After logging in, use the **Change admin password** form on the upload page to rotate credentials.

You can override route and limits via env vars:
- `ADMIN_ROUTE` (default `/hidden-admin`)
- `SITE_NAME` (default `Night Sky Atlas`)
- `MAX_UPLOAD_BYTES` (default `10485760`)
- `upload_max_filesize` and `post_max_size` (PHP ini/virtual-host values; should be >= `MAX_UPLOAD_BYTES`)

## Security notes (admin/backdoor)

- Admin route is hidden but also protected with real authentication.
- Passwords are stored as `password_hash` values (bcrypt) and can be rotated from the authenticated admin area.
- CSRF token required on login and upload forms, backed by file-based PHP sessions in `storage/sessions` to avoid token mismatches when default system session paths are unavailable.
- Basic per-IP login throttling is enforced.
- Uploads accept only JPEG/PNG/WebP and enforce max-size limit; effective limit is the minimum of `MAX_UPLOAD_BYTES`, `upload_max_filesize`, and `post_max_size`.
- Wikipedia URLs are restricted to `wikipedia.org/wiki/...` article links and fetched server-side for preview + public detail enrichment.
- Wikipedia panel includes attribution/license note and gracefully falls back when external fetch is unavailable.
- Uploaded files are stored outside the public web root and served through `media.php`.
- Wikipedia metadata fetches only allow trusted Wikipedia hosts (`en.wikipedia.org` plus optional language subdomains) and return structured error codes for UI-safe fallbacks.

## Folder/file map

- `public/index.php` — front controller/router for public + admin routes.
- `public/src/bootstrap.php` — shared helpers, auth, upload + thumbnail logic.
- `public/src/views/` — HTML view templates.
- `public/src/services/wikipedia.php` — Wikipedia URL validation + metadata normalization helper service.
- `public/assets/style.css` — cinematic dark UI styling and interaction polish.
- `storage/data/images.json` — image metadata records (including Wikipedia cache fields).
- `storage/sessions/` — file-backed PHP session storage used for admin auth + CSRF continuity.
- `storage/logs/app.log` — background/lazy refresh failure logs for non-fatal runtime issues.
- `storage/data/users.json` — admin credential hashes.
- `WEBSITE_TASKS.md` — implementation tracker.
- `CODEX_PARALLEL_TASKS.md` — parallel work planning.

## User-facing flow

```mermaid
flowchart TD
  A[Visitor lands on homepage] --> B[See cinematic hero + spotlight capture]
  B --> C[Browse thumbnail gallery]
  C --> D[Open image detail]
  D --> E[Review metadata\nobject + equipment + exposure + tags]
  E --> F{Wikipedia data available?}
  F -- yes --> G[Show extract + thumbnail + read more link + attribution note]
  F -- no/fetch failed --> H[Show fallback: No external reference yet]
```

## Admin upload flow

```mermaid
flowchart TD
  A[Admin opens hidden route] --> B[Login form + CSRF]
  B --> C[Credential check + rate limit]
  C --> D[Upload image + enter metadata]
  D --> M{Body exceeds effective upload limit?}
  M -- yes --> N[Show actionable size-limit error]
  M -- no --> E[MIME/size validation]
  C --> J[Optional password change form]
  J --> K[Verify current password + enforce 12+ chars]
  K --> L[Write updated password_hash to users JSON]
  E --> F[Store original outside web root]
  F --> G[Generate thumbnail]
  G --> H[Write JSON metadata]
  H --> I[Image appears in public gallery]
```

## High-level architecture

```mermaid
graph LR
  U[Public Browser] --> APP[PHP Front Controller]
  A[Admin Browser] --> APP
  APP --> VIEWS[Template Views]
  VIEWS --> THEME[Cinematic CSS Theme Layer]
  APP --> SEC[Auth + CSRF + Rate Limit]
  APP --> DATA[(JSON metadata/users + wiki cache fields)]
  APP --> WIKI[Wikipedia REST summary API]
  APP --> LOGS[(storage/logs/app.log)]
  APP --> IMG[(Originals + Thumbs in storage/)]
  APP --> WIKI[Wikipedia REST summary fetch]
```

## Wikipedia cache behavior

- `wikipediaUrl` is captured from admin upload (or seeded data in `images.json`).
- Detail pages always render cached Wikipedia fields first; page rendering never waits on live API calls.
- If `wikiFetchedAt` is older than 7 days (or missing), refresh is queued as a lazy background task at PHP shutdown.
- On fetch failure, existing cached title/extract/thumbnail values are preserved, `wikiStatus` is set to `error`, and the failure is logged to `storage/logs/app.log`.

## Keeping docs in sync (required)

For every behavior change in this repository:
1. Update `README.md` in the same commit.
2. Update Mermaid diagrams if flow/architecture changed.
3. Update `WEBSITE_TASKS.md` status/notes as relevant.
4. Document new env vars, operational assumptions, and security behavior.

A behavior-changing code diff without matching docs updates is incomplete.
