# Astrophotography Website Build Plan

This task plan is designed for a Linux + Apache + PHP environment and optimized for large, high-detail astrophotography images (~2MB each).

## 1) Product vision and UX direction

- [x] Define brand and visual direction (dark sky palette, starfield accents, modern typography).
- [x] Choose homepage storytelling approach (hero image + mission statement + rotating featured/spotlight capture with nightly highlight facts).
- [ ] Create moodboard (NASA/APOD-inspired visual references, typography, spacing, card styles).
- [x] Decide wow-factor interactions (subtle parallax stars, animated constellation lines, smooth image reveal transitions).

## 2) Information architecture and content model

- [ ] Define public pages:
  - Home / Gallery (thumbnail grid)
  - Image detail page (full image + metadata + equipment used)
  - About (your story, location, skies, processing philosophy)
  - Contact / social links
- [ ] Define image metadata schema:
  - Title
  - Description/story behind capture
  - Capture date/time
  - Object name (e.g., Orion Nebula)
  - Equipment: camera, telescope/lens, mount, filters, guiding setup
  - Exposure details (total integration, sub exposure, ISO/gain, number of frames)
  - Processing software/workflow
  - Tags (galaxy, nebula, moon, planet, widefield)
  - [x] Wikipedia link + cache fields (`wikipediaUrl`, `wikiTitle`, `wikiExtract`, `wikiThumbnail`, `wikiFetchedAt`, `wikiStatus`)
- [ ] Decide whether to store metadata in flat files (JSON) or database (SQLite/MySQL); for quick start, use SQLite or JSON.

## 3) Technical architecture (PHP + Apache)

- [ ] Pick stack style:
  - Lightweight PHP app (no framework) for speed, or
  - Laravel/Slim for maintainability if future growth expected.
- [ ] Set up folder structure:
  - `/public` for web root
  - `/storage/images/original`
  - `/storage/images/thumbs`
  - `/storage/uploads/tmp`
- [ ] Add secure config strategy:
  - Environment variables for admin credentials and secret keys
  - Keep uploads and sensitive files outside public web root where possible
- [x] Persist PHP session files under writable `storage/sessions` to keep admin auth + CSRF state stable across host environments.

## Metadata enrichment integrations

- [x] Add backend Wikipedia metadata service with trusted-host URL validation and normalized response mapping (`title`, `summary`, `thumbnail`, `canonicalUrl`, `licenseText`, `lastFetchedAt`).
- [x] Add REST summary fetch with MediaWiki API fallback and structured error responses (`invalid_url`, `page_not_found`, `upstream_failure`).

## 4) Core feature #1: Public gallery with thumbnails

- [x] Build responsive gallery grid:
  - 1 column mobile, 2–3 tablet, 4+ desktop
  - Lazy loading images
  - Keyboard-accessible cards
- [x] Generate and serve optimized thumbnails on upload:
  - Create 400px and 800px variants
  - Use WebP where supported
  - Keep original full-res image for detail page
- [x] Build image detail page with:
  - Full view (optimized display size)
  - Metadata panel (equipment + exposure + notes)
  - Optional Wikipedia reference panel (extract, thumbnail, attribution, fallback state)
  - “Related captures” by tag or target

## 5) Core feature #2: Admin backdoor for uploads

- [x] Implement secure admin login (session-based auth + strong password hashing).
- [x] Add optional remember-me admin login with revocable, rotated device tokens.
- [x] Redirect already-authenticated admins away from the login screen to the admin portal to keep active sessions feeling persistent.
- [x] Add authenticated admin password rotation form (current password verification + minimum length checks).
- [ ] Restrict admin route by:
  - Obscure route path (not security by itself)
  - Real authentication and CSRF protection
  - Rate limiting / lockout after repeated failed logins
- [x] Build upload form with fields:
  - Image file
  - Title
  - Description
  - Equipment fields (camera, telescope/lens, mount, etc.)
  - Exposure details
  - Optional Wikipedia URL + preview fetch
  - Tags
- [x] Validate uploads:
  - Allowed MIME types (JPEG/PNG/WebP/TIFF if needed)
  - Max upload size (default now 150MB via `MAX_UPLOAD_BYTES`; keep PHP limits aligned)
  - Server-side image verification and sanitization
- [x] Surface actionable admin errors when request body exceeds PHP/app upload limits (e.g., `post_max_size`, `upload_max_filesize`, `MAX_UPLOAD_BYTES`).
- [x] On successful upload:
  - Store original image
  - Generate thumbnails
  - Save metadata
  - Show admin preview and success/failure status

## 6) Core feature #3: Equipment and capture details

- [ ] Create structured equipment sections (camera, optics, mount, guiding, filters).
- [x] Add optional reusable “equipment presets” in admin to avoid repetitive typing. (implemented as reusable setup-preset pills for scope type/object type/telescope/mount/camera/filter wheel/filters/filter set/processing software/tags, with append-style pill clicks for multi-value fields like tags and processing)
- [ ] Display equipment metadata cleanly on detail pages with badges/icons.
- [x] Add searchable/filterable fields (homepage toolbar supports object type, tag, capture date range, text search, and shareable sort/filter query params).

## 7) “Wow factor” enhancements

- [x] Cinematic dark theme with starfield background and subtle motion (respect reduced-motion accessibility setting).
- [x] Lightbox/fullscreen viewer with smooth zoom transitions. (implemented fullscreen mode on image detail page; control pill positioned at top-right of media)
- [ ] Before/after slider (stacked vs processed image), optional for advanced showcase.
- [x] Spotlight section on homepage with rotating selection rules (latest, featured override, daily deterministic pick).
- [ ] Constellation-style timeline view by capture date.
- [x] Ambient micro-interactions (hover/focus metadata fade-ins, elegant loading skeletons, and pointer-based card tilt with reduced-motion fallback).
- [x] Reimagined landing page as an image-first experience (wider gallery utilization, compact filter footprint, and reduced gallery text emphasis for stronger visual impact).
- [x] Added subtle spectral parallax accents (H-alpha reds + OIII cyans) on the landing hero and shifted homepage filtering to chip-summary-first with full controls behind a Refine toggle.
- [x] Mobile ergonomics polish for 360–430px widths (sticky utility row, larger 44px touch targets, tighter typography/spacing, overflow-safe title + metadata chip handling).
- [x] Define clearly distinct detail-view experiences by breakpoint: widescreen desktop layout that uses horizontal space with overflow-safe metadata/Wikipedia divider behavior, and long-thin stacked mobile layout for narrow screens.

## 8) Performance and image delivery

- [ ] Use responsive image `srcset` and lazy loading.
- [ ] Enable Apache compression and long-lived cache headers for generated thumbnails.
- [ ] Add CDN option later (CloudFront) if traffic grows.
- [ ] Create background job/CLI script to regenerate thumbnails for older uploads.
- [x] Add lazy refresh on detail reads for stale Wikipedia cache entries (>7 days) while serving cached data immediately.
- [x] Parse and store infobox-derived key facts (e.g., size/shape/distance fields) from Wikipedia links for detail-page context.
- [x] Invalidate and refresh cached Wikipedia summary/facts when an admin changes the Wikipedia URL on an image.

## 9) Security hardening

- [ ] Enforce HTTPS (AWS cert + redirect HTTP→HTTPS).
- [ ] Set secure headers (CSP, HSTS, X-Content-Type-Options, Referrer-Policy).
- [ ] Lock down upload execution (never execute uploaded files).
- [ ] Validate/sanitize all metadata inputs to prevent XSS.
- [ ] Add periodic backup plan for images + metadata.

## 10) SEO and discoverability

- [ ] Add semantic page titles, meta descriptions, Open Graph tags.
  - [x] Implement image detail canonical URL + Open Graph/Twitter image/title metadata for rich social link previews.
  - [x] Serve social preview cards from the generated 800x500 JPEG thumbnail and include Open Graph image type/dimension tags for better WhatsApp/Facebook compatibility.
- [ ] Include alt text strategy for each image.
- [ ] Auto-generate XML sitemap.
- [ ] Add schema.org metadata for images/creative works.

## 11) Operations and maintainability

- [x] Add admin tools:
  - [x] Task-based admin portal pages (Upload image / Setup presets / Manage images / Dedicated edit page / Security) with persistent sidebar navigation for faster admin-only task switching.
  - [x] Edit metadata after upload on a dedicated page (including SEO meta tag fields and preset pills)
  - [x] Delete/unpublish image
  - [x] Mark featured images (homepage spotlight selector)
- [ ] Add logging and error monitoring.
- [ ] Create deployment checklist for Apache/PHP config updates.
- [ ] Document recovery steps (restore from backup).

## 12) Phased execution plan

### Phase 1 — MVP (1–2 weeks)
- [x] Build public gallery grid with thumbnails.
- [x] Build secure admin login + upload form.
- [x] Save image metadata including equipment details.
- [x] Launch with core styling and mobile responsiveness.

### Phase 2 — Polish (1 week)
- [x] Add image detail page enhancements and filtering. (homepage filtering + sorting delivered)
- [x] Improve visual design and transitions.
- [x] Add homepage featured section (implemented as rotating hero spotlight + Tonight's Highlight caption block).

### Phase 3 — Advanced (later)
- [ ] Add before/after comparisons.
- [ ] Add advanced tag search and timeline view.
- [ ] Add analytics and performance tuning.

## 13) Suggested immediate next actions (first 48 hours)

- [x] Confirm stack choice: plain PHP + JSON (implemented MVP).
- [x] Set up project structure and Apache-friendly front controller routing.
- [x] Maintain PHP 7.4+ compatibility for upload and thumbnail helpers (avoid PHP 8-only syntax in runtime paths).
- [x] Remove PHP 8-only `str_starts_with` usage from Wikipedia URL normalization path to prevent fatal errors on PHP 7.4 deployments.
- [x] Implement admin authentication and secure upload pipeline.
- [x] Build thumbnail generation and gallery listing.
- [ ] Populate with 10 sample images + metadata. (repository now ships without bundled .jpg assets)
- [x] Review and finalize visual style with 2–3 homepage design variants.

## Codex parallelization plan

- See `CODEX_PARALLEL_TASKS.md` for a conflict-minimized task decomposition suitable for parallel PR development.
