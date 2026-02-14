# images

Astronomy image showcase website with a public gallery and a secure admin upload backdoor, implemented with PHP + JSON storage for quick deployment on Apache.

## Project status

**Current maturity:** MVP+ visual polish implemented and runnable locally.

Implemented now:
- public gallery and image detail pages
- cinematic dark-sky visual treatment with starfield texture, rotating spotlight hero card rules (latest/featured/daily deterministic date hash), and glassmorphism-style panels
- ambient micro-interactions (hover lift/glow, keyboard-accessible card metadata overlays, mouse-position tilt/parallax on gallery cards, and subtle hero twinkle/gradient drift that respects reduced-motion settings)
- detail viewer now has explicit split responsive experiences: widescreen desktop layout (expanded media canvas + widened side metadata/Wikipedia column for denser scanning, with reduced heading/body/chip typography and overflow-safe wiki divider sizing) and a stacked long-form mobile layout tuned for narrow screens.
- image detail fullscreen pill is anchored at the top-right of the image for quicker access before scrolling metadata
- Repository intentionally does not include bundled `.jpg` sample images; upload your own media through the admin flow.
- metadata display (capture, object type, structured equipment setup incl. scope type/telescope/mount/camera/filter chain, exposure, processing, tags)
- homepage now prioritizes an immersive, denser image wall (wider canvas + larger thumbnail coverage) with subtle scroll-linked spectral parallax accents (H-alpha/OIII-inspired gradients) that respect reduced-motion settings.
- filters now default to a low-prominence chip summary under the hero, while full controls live behind a Refine toggle (object type/tag/date-range/text search + sort) and still sync via shareable query-parameter state.
- secure admin route with session auth, CSRF protection, basic login rate limiting, task-based admin portal pages (upload/setup presets/media/security), in-session password change controls, and authenticated image deletion
- redesigned admin control center UX with a dedicated side navigation rail, top-of-workspace guided help cards, and wider content panels so uploads/presets/library/security actions are easier to discover and use on desktop screens.
- admin media library now supports spotlight selection plus navigation into a dedicated edit page for full metadata + SEO updates (with preset pills available while editing).
- image upload pipeline with MIME/size validation, thumbnail generation, and admin-side storage-capacity visibility
- admin setup-preset management for one-click upload/edit pills across observatory gear + metadata (scope type/object type/telescope/mount/camera/filter wheel/filters/filter set/processing software/tags)
- admin setup-preset changes now persist correctly to `setup_presets.json` for all categories, eliminating PHP notices during preset saves.
- graceful oversize-upload handling that reports when server (`post_max_size` / `upload_max_filesize`) or app (`MAX_UPLOAD_BYTES`) limits reject a request before PHP can parse form fields
- graceful storage-write error handling in admin actions (setup presets across all categories and uploads) when `storage/data` is not writable, avoiding PHP warnings exposed to users
- setup-preset validation errors are now category-aware (not hard-coded to scope type), so invalid/empty entries report the selected preset type.
- Wikipedia URL normalization uses PHP 7.4-compatible string checks (no PHP 8-only helpers) to avoid runtime fatals on older deployments.
- social preview tags on detail pages now point to the generated 800x500 JPEG thumbnail (instead of full original) to improve WhatsApp/Facebook card rendering reliability.

Planned next:
- continue production hardening (filter/search + client-side sorting now available on homepage with shareable query-parameter URLs).

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
    SetEnv MAX_UPLOAD_BYTES 157286400

    # Keep Apache/PHP body limits aligned for large uploads
    php_value upload_max_filesize 150M
    php_value post_max_size 150M

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
- After logging in, use the **Security** task page to rotate credentials.

You can override route and limits via env vars:
- `ADMIN_ROUTE` (default `/hidden-admin`)
- `SITE_NAME` (default `Night Sky Atlas`)
- `MAX_UPLOAD_BYTES` (default `157286400`, i.e., 150MB)
- `upload_max_filesize` and `post_max_size` (PHP ini/virtual-host values; should be >= `MAX_UPLOAD_BYTES`)

## Security notes (admin/backdoor)

- Admin route is hidden but also protected with real authentication.
- Passwords are stored as `password_hash` values (bcrypt) and can be rotated from the authenticated admin area.
- Admin login supports an optional 30-day remember-me device cookie; tokens are stored server-side as SHA-256 hashes, rotated after auto-login, and revoked on logout/password change.
- Visiting the admin login URL while already authenticated now redirects directly to the admin upload page, avoiding accidental “logged out” confusion when opening `/hidden-admin/login` in an existing session.
- CSRF token required on login, upload, delete, and password-change forms, backed by file-based PHP sessions in `storage/sessions` to avoid token mismatches when default system session paths are unavailable.
- Basic per-IP login throttling is enforced.
- Uploads accept only JPEG/PNG/WebP and enforce max-size limit; effective limit is the minimum of `MAX_UPLOAD_BYTES`, `upload_max_filesize`, and `post_max_size`.
- Wikipedia URLs are restricted to `wikipedia.org/wiki/...` article links and fetched server-side for preview + public detail enrichment.
- Wikipedia panel includes attribution/license note, optional infobox-derived key facts (size/shape/distance-style fields), and graceful fallback when external fetch is unavailable.
- Uploaded files are stored outside the public web root and served through `media.php`.
- Wikipedia metadata fetches only allow trusted Wikipedia hosts (`en.wikipedia.org` plus optional language subdomains) and return structured error codes for UI-safe fallbacks.
- Social preview URLs are generated from request host/scheme headers, so production deployments should keep trusted proxy/host header handling correctly configured.
- Open Graph image metadata now includes type + dimensions so social crawlers can parse previews more consistently.
- Admin metadata editor validates required fields and Wikipedia URLs before persisting updates; if the Wikipedia URL changes, cached wiki summary/facts are reset and refreshed so references stay in sync.

## Folder/file map

- `public/index.php` — front controller/router for public + admin routes.
- `public/src/bootstrap.php` — shared helpers, auth, upload + thumbnail logic.
- `public/src/views/` — HTML view templates.
- `public/src/views/home.php` embeds homepage image JSON payload for client-side filtering/sorting and now renders an image-first landing layout with spectral parallax accents plus chip-summary-first filtering with a Refine toggle for advanced controls.
- `public/src/services/wikipedia.php` — Wikipedia URL validation + metadata normalization helper service.
- `public/assets/style.css` — cinematic dark UI styling and interaction polish.
- `storage/data/images.json` — image metadata records (including Wikipedia cache fields, spotlight flag, and editable SEO meta tags).
- `storage/sessions/` — file-backed PHP session storage used for admin auth + CSRF continuity.
- `storage/logs/app.log` — background/lazy refresh failure logs for non-fatal runtime issues.
- `storage/data/users.json` — admin credential hashes.
- `storage/data/setup_presets.json` — reusable setup preset pills (scope type, object type, telescope, mount, camera, filter wheel, filters, filter set, processing software, tags) for admin upload/edit workflows.
- `storage/data/scope_types.json` — legacy scope-type preset store still read for backward compatibility.
- `WEBSITE_TASKS.md` — implementation tracker.
- `CODEX_PARALLEL_TASKS.md` — parallel work planning.

## User-facing flow

```mermaid
flowchart TD
  Visitor_lands_on_homepage --> See_cinematic_hero_rotating_spotlight_and_subtle_spectral_parallax
  See_cinematic_hero_rotating_spotlight_and_subtle_spectral_parallax --> Read_Tonights_Highlight_facts_and_open_details_CTA
  Read_Tonights_Highlight_facts_and_open_details_CTA --> Review_active_filter_chip_summary
  Review_active_filter_chip_summary -->|optional| Open_Refine_panel_for_full_filter_controls
  Open_Refine_panel_for_full_filter_controls --> Browse_thumbnail_gallery
  Review_active_filter_chip_summary --> Browse_thumbnail_gallery
  Browse_thumbnail_gallery --> Open_image_detail
  Open_image_detail --> Review_metadata_object_equipment_exposure_and_tags
  Review_metadata_object_equipment_exposure_and_tags --> Copy_image_specific_share_link
  Copy_image_specific_share_link --> Paste_in_Facebook_WhatsApp_or_iMessage
  Paste_in_Facebook_WhatsApp_or_iMessage --> Preview_card_shows_image_and_title
  Preview_card_shows_image_and_title --> Continue_browsing_gallery
  Open_image_detail --> Wikipedia_data_available
  Wikipedia_data_available -->|yes| Show_extract_thumbnail_read_more_link_and_attribution_note
  Wikipedia_data_available -->|no_or_fetch_failed| Show_fallback_no_external_reference_yet
```

## Admin upload flow

```mermaid
flowchart TD
  Admin_opens_hidden_route --> Already_authenticated
  Already_authenticated -->|yes| Admin_control_center_with_side_navigation_and_wide_workspace
  Already_authenticated -->|no| Login_form_with_CSRF_and_optional_remember_me
  Login_form_with_CSRF_and_optional_remember_me --> Credential_check_and_rate_limit
  Credential_check_and_rate_limit --> Admin_control_center_with_side_navigation_and_wide_workspace
  Admin_control_center_with_side_navigation_and_wide_workspace --> Upload_page
  Admin_control_center_with_side_navigation_and_wide_workspace --> Setup_presets_page
  Admin_control_center_with_side_navigation_and_wide_workspace --> Media_library_page
  Admin_control_center_with_side_navigation_and_wide_workspace --> Dedicated_edit_page
  Admin_control_center_with_side_navigation_and_wide_workspace --> Security_page
  Upload_page --> Review_storage_and_upload_limits
  Upload_page --> Use_setup_preset_pills_and_enter_capture_details
  Use_setup_preset_pills_and_enter_capture_details --> Multi_select_append_pills_for_tags_and_processing_software
  Upload_page --> Upload_image_and_metadata
  Upload_image_and_metadata --> Body_exceeds_effective_upload_limit
  Body_exceeds_effective_upload_limit -->|yes| Show_actionable_size_limit_error
  Body_exceeds_effective_upload_limit -->|no| MIME_and_size_validation
  MIME_and_size_validation --> Store_original_generate_thumbnail_and_write_metadata_JSON
  Store_original_generate_thumbnail_and_write_metadata_JSON --> Image_appears_in_public_gallery
  Setup_presets_page --> Add_or_delete_reusable_preset_pills_in_setup_presets_json
  Media_library_page --> Set_or_change_homepage_spotlight_capture_for_homepage_rotation
  Media_library_page --> Open_dedicated_edit_page_for_a_capture
  Dedicated_edit_page --> Edit_all_metadata_fields_preset_pills_and_SEO_tags
  Edit_all_metadata_fields_preset_pills_and_SEO_tags --> Multi_select_append_pills_for_tags_and_processing_software
  Edit_all_metadata_fields_preset_pills_and_SEO_tags --> If_Wikipedia_URL_changed_clear_old_cache_and_refresh_wiki_summary_and_facts
  Media_library_page --> Delete_image_with_CSRF_confirm
  Delete_image_with_CSRF_confirm --> Remove_JSON_record_and_media_files
  Security_page --> Verify_current_password_and_enforce_12_plus_chars
  Verify_current_password_and_enforce_12_plus_chars --> Write_updated_password_hash_to_users_JSON
```

## High-level architecture

```mermaid
graph LR
  Public_Browser --> PHP_Front_Controller
  Admin_Browser --> PHP_Front_Controller
  PHP_Front_Controller --> Template_Views
  Template_Views --> Cinematic_CSS_Theme_Layer_subtle_twinkle_gradient_drift_spectral_parallax_split_desktop_mobile_detail_viewer_shell_and_wide_admin_two_column_workspace
  Template_Views --> Canonical_and_Open_Graph_meta_tags
  PHP_Front_Controller --> Auth_CSRF_and_Rate_Limit
  PHP_Front_Controller --> JSON_metadata_users_wiki_cache_spotlight_and_SEO_fields
  PHP_Front_Controller --> Wikipedia_APIs_summary_and_parse_infobox
  PHP_Front_Controller --> storage_logs_app_log
  PHP_Front_Controller --> Originals_and_Thumbs_in_storage
  PHP_Front_Controller --> Wikipedia_REST_summary_fetch
```

## Wikipedia cache behavior

- `wikipediaUrl` is captured from admin upload (or seeded data in `images.json`).
- Detail pages always render cached Wikipedia fields first; page rendering never waits on live API calls.
- If `wikiFetchedAt` is older than 7 days (or missing), refresh is queued as a lazy background task at PHP shutdown.
- On fetch failure, existing cached title/extract/thumbnail/key-fact values are preserved, `wikiStatus` is set to `error`, and the failure is logged to `storage/logs/app.log`.
- When an admin changes a record's Wikipedia URL, cached wiki title/extract/thumbnail/key facts are immediately invalidated and a fresh fetch is attempted during the same save operation.

## Keeping docs in sync (required)

For every behavior change in this repository:
1. Update `README.md` in the same commit.
2. Update Mermaid diagrams if flow/architecture changed.
3. Update `WEBSITE_TASKS.md` status/notes as relevant.
4. Document new env vars, operational assumptions, and security behavior.

A behavior-changing code diff without matching docs updates is incomplete.
