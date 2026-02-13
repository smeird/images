# Codex Parallel Task Plan (Conflict-Minimized)

This plan decomposes the website build into **independent Codex tasks** with explicit file ownership to reduce merge conflicts.

## Rules for all tasks

- Each task should touch only its assigned paths.
- Shared contracts (schema, routes, style tokens) must be treated as read-only unless a task explicitly owns them.
- If a task needs a contract change, create a **follow-up task** instead of editing another task’s owned files.

## Task list

### Task 01 — Project skeleton and bootstrap

**Goal:** Create initial app structure and bootstrap entrypoint.

**Owns paths:**
- `public/index.php`
- `src/bootstrap.php`
- `src/Http/Router.php`
- `src/Http/Request.php`
- `src/Http/Response.php`

**Must not touch:**
- `src/Domain/**`
- `templates/**`
- `public/assets/**`

---

### Task 02 — Environment/config loader

**Goal:** Centralize environment-based configuration.

**Owns paths:**
- `src/Config/config.php`
- `src/Config/env.php`
- `.env.example`

**Must not touch:**
- `public/**`
- `templates/**`
- admin/auth code

---

### Task 03 — Domain model: image metadata schema

**Goal:** Define canonical image metadata model + validation rules (framework-agnostic).

**Owns paths:**
- `src/Domain/Image/ImageMetadata.php`
- `src/Domain/Image/ImageMetadataValidator.php`
- `docs/contracts/image-metadata.md`

**Must not touch:**
- persistence/database adapters
- controllers/routes

---

### Task 04 — Persistence adapter (JSON repository)

**Goal:** Implement metadata persistence in JSON files for MVP.

**Owns paths:**
- `src/Infrastructure/Persistence/JsonImageRepository.php`
- `storage/metadata/.gitkeep`

**Must not touch:**
- domain schema files
- controllers/views

---

### Task 05 — Persistence adapter (SQLite repository)

**Goal:** Implement optional SQLite repository and migrations.

**Owns paths:**
- `src/Infrastructure/Persistence/SqliteImageRepository.php`
- `database/migrations/001_create_images.sql`
- `database/migrations/002_create_tags.sql`

**Must not touch:**
- public templates
- upload pipeline

---

### Task 06 — Image processing service (thumbnails)

**Goal:** Build thumbnail generation service (400px/800px + WebP variants).

**Owns paths:**
- `src/Infrastructure/Image/ThumbnailGenerator.php`
- `src/Infrastructure/Image/ImageVariant.php`

**Must not touch:**
- controller/view logic
- auth logic

---

### Task 07 — Upload pipeline service

**Goal:** Build secure upload orchestration service (validation + storage + thumbnail trigger).

**Owns paths:**
- `src/Application/Upload/UploadService.php`
- `src/Application/Upload/UploadRequest.php`
- `src/Application/Upload/UploadResult.php`

**Must not touch:**
- auth/session code
- gallery rendering templates

---

### Task 08 — Authentication core

**Goal:** Session-based auth primitives + password hashing helpers.

**Owns paths:**
- `src/Security/AuthService.php`
- `src/Security/PasswordHasher.php`
- `src/Security/SessionGuard.php`

**Must not touch:**
- upload form templates
- gallery templates

---

### Task 09 — CSRF + rate limiting middleware

**Goal:** Implement CSRF token manager and login throttling middleware.

**Owns paths:**
- `src/Security/CsrfTokenManager.php`
- `src/Security/LoginRateLimiter.php`
- `src/Http/Middleware/CsrfMiddleware.php`

**Must not touch:**
- metadata schema
- thumbnail service

---

### Task 10 — Public gallery page UI

**Goal:** Build gallery grid template with lazy-loading and accessible cards.

**Owns paths:**
- `templates/gallery/index.php`
- `public/assets/css/gallery.css`

**Must not touch:**
- admin templates
- image processing services

---

### Task 11 — Image detail page UI

**Goal:** Build detail page layout for full image + metadata panel + related captures slot.

**Owns paths:**
- `templates/gallery/detail.php`
- `public/assets/css/detail.css`

**Must not touch:**
- upload/auth logic
- shared token file

---

### Task 12 — Admin login UI + controller wiring

**Goal:** Implement admin login route/controller/template using auth service.

**Owns paths:**
- `src/Controller/Admin/LoginController.php`
- `templates/admin/login.php`
- `public/assets/css/admin-login.css`

**Must not touch:**
- upload controller
- gallery templates

---

### Task 13 — Admin upload UI + controller wiring

**Goal:** Implement upload form route/controller/template integrated with upload service.

**Owns paths:**
- `src/Controller/Admin/UploadController.php`
- `templates/admin/upload.php`
- `public/assets/css/admin-upload.css`

**Must not touch:**
- auth internals
- public gallery/detail templates

---

### Task 14 — Shared design tokens + base layout

**Goal:** Define global visual system (colors, spacing, typography) for dark-sky theme.

**Owns paths:**
- `public/assets/css/tokens.css`
- `templates/layout/base.php`

**Must not touch:**
- page-specific CSS files
- backend services

---

### Task 15 — Starfield/background visual enhancement

**Goal:** Add optional ambient starfield animation respecting reduced-motion settings.

**Owns paths:**
- `public/assets/css/effects-starfield.css`
- `public/assets/js/effects-starfield.js`

**Must not touch:**
- gallery/detail/admin templates
- tokens.css

---

### Task 16 — Lightbox viewer enhancement

**Goal:** Add fullscreen/lightbox interaction for detail images.

**Owns paths:**
- `public/assets/js/lightbox.js`
- `public/assets/css/lightbox.css`

**Must not touch:**
- thumbnail generation or upload pipeline
- auth code

---

### Task 17 — Filter/search query layer

**Goal:** Add reusable query object for filtering by tags/equipment/object.

**Owns paths:**
- `src/Application/Gallery/GalleryFilter.php`
- `src/Application/Gallery/GalleryQueryService.php`

**Must not touch:**
- templates CSS/JS
- upload/auth services

---

### Task 18 — SEO metadata helpers

**Goal:** Add page-level SEO/OG metadata builder and schema snippets.

**Owns paths:**
- `src/Presentation/Seo/MetaBuilder.php`
- `templates/partials/seo.php`
- `templates/partials/schema-image.php`

**Must not touch:**
- controllers for admin upload/auth
- image processing

---

### Task 19 — Security headers + Apache snippets

**Goal:** Add deployment-safe hardening snippets (CSP/HSTS/XCTO/Referrer-Policy).

**Owns paths:**
- `ops/apache/security-headers.conf`
- `docs/deployment/security-hardening.md`

**Must not touch:**
- PHP application logic
- templates

---

### Task 20 — Cache/compression Apache snippets

**Goal:** Add static asset caching and compression config.

**Owns paths:**
- `ops/apache/cache-and-compression.conf`
- `docs/deployment/performance.md`

**Must not touch:**
- PHP source
- templates

---

### Task 21 — XML sitemap generator

**Goal:** Build CLI script to generate sitemap from stored metadata.

**Owns paths:**
- `scripts/generate-sitemap.php`
- `public/sitemap.xml` (generated artifact)

**Must not touch:**
- controller logic
- admin views

---

### Task 22 — Thumbnail regeneration CLI

**Goal:** Create CLI tool to regenerate all missing/legacy thumbnails.

**Owns paths:**
- `scripts/regenerate-thumbnails.php`
- `docs/operations/thumbnail-regeneration.md`

**Must not touch:**
- gallery/admin templates
- auth services

---

### Task 23 — Logging + error monitoring plumbing

**Goal:** Add centralized logger and request error handler.

**Owns paths:**
- `src/Infrastructure/Observability/Logger.php`
- `src/Infrastructure/Observability/ErrorHandler.php`
- `storage/logs/.gitkeep`

**Must not touch:**
- CSS/JS/templates
- domain metadata schema

---

### Task 24 — Content seed pack (sample astrophotos metadata only)

**Goal:** Add 10 sample metadata entries to unblock UI development.

**Owns paths:**
- `storage/metadata/samples/*.json`
- `docs/content/sample-catalog.md`

**Must not touch:**
- source code, templates, scripts

---

## Merge-conflict prevention checklist for reviewers

- Prefer one task per PR.
- Reject PRs that modify files outside task ownership.
- Merge in this order for best stability:
  1. Tasks 01–03 (contracts/foundation)
  2. Tasks 04–09 (services/security)
  3. Tasks 10–16 (UI/enhancements)
  4. Tasks 17–24 (features/ops/content)

## Suggested parallel batches

- **Batch A (Backend core):** 01, 02, 04, 06, 07, 23
- **Batch B (Security/Admin):** 08, 09, 12, 13, 19
- **Batch C (Public UX):** 10, 11, 14, 15, 16, 18
- **Batch D (Data/Operations):** 05, 17, 20, 21, 22, 24

These batches are designed so each contributor owns distinct files, allowing high parallelization with minimal merge overlap.
