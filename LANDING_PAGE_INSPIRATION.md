# Astrophotography Landing Page Inspiration (Benchmark Notes)

## Research goal
Collect practical landing-page layout patterns commonly used by astrophotography websites so this project can choose a direction that fits the current PHP + JSON implementation.

> Note: this document captures pattern-level observations and implementation-ready options for this repo.

## Pattern signals seen across popular astro-image sites

Across astrophotography portfolio/gallery sites, the strongest home-page patterns are:
1. **Hero-first storytelling** (single cinematic image + short mission copy + 1–2 CTA buttons).
2. **Image-grid-first exploration** (dense masonry/grid above the fold with minimal introductory copy).
3. **Editorial spotlight + supporting rows** (a featured “story of the night” section, then categorized rows).
4. **Community/data hybrid** (search/filter controls and metadata surfaced immediately, often before long narrative copy).

## Option A — Cinematic Hero + Curated Highlights
Best when the goal is emotional impact and brand differentiation.

### Layout
- Full-width hero image with 1 headline, 1 supporting paragraph, and 2 CTAs (`Explore Gallery`, `Tonight's Highlight`).
- Beneath hero: three compact cards (`Latest Capture`, `Featured Object`, `This Month's Theme`).
- Follow with a 2–3 row gallery preview (`Latest`, `Nebulae`, `Galaxies`) and a final "View All" CTA.

### Why it works for this repo
- Matches the current cinematic visual language already in `style.css` and homepage spotlight behavior.
- Requires minimal data-model change; mostly template composition and section ordering.

### Implementation complexity
- **Low** (restructure existing home sections and tune typography/spacing).

## Option B — Gallery-First (Discovery in 1 Scroll)
Best when repeat visitors primarily come to browse many captures quickly.

### Layout
- Compact top bar with title, short one-line mission, and refine/search trigger.
- Immediate image wall (dense first viewport) with sticky mini-filter row.
- Side/inline rotating spotlight card that changes without pushing grid down.
- Metadata hover/focus overlays stay lightweight to preserve scan speed.

### Why it works for this repo
- Aligns with existing filter/sort/query-state implementation.
- Emphasizes asset quality and encourages longer session depth through continuous browsing.

### Implementation complexity
- **Low-to-medium** (mostly CSS/layout tuning and reducing hero vertical footprint).

## Option C — Magazine/Journal Narrative
Best when you want to position the site as both portfolio and educational destination.

### Layout
- Opening "issue cover" section (headline + featured image + short editor note).
- Alternating content rhythm:
  - feature story block (large image + narrative),
  - mini gallery strip,
  - technique/gear callout,
  - next feature.
- End with structured archive links (`By Object`, `By Season`, `By Equipment`).

### Why it works for this repo
- Complements the expanded educational/about content direction.
- Builds stronger context around each capture, not just image thumbnails.

### Implementation complexity
- **Medium** (requires curated content blocks and stronger editorial cadence in homepage data).

## Option D — Data-Rich Explorer (Power User)
Best when target users are astrophotographers who care about acquisition details.

### Layout
- Above-the-fold search + faceted controls (object type, focal length bin, integration time, capture date).
- Main area split: image grid + quick stats panel (`Top objects this month`, `Most-used filters`, `New uploads`).
- Optional compare module (`same object, different nights/setup`).

### Why it works for this repo
- Leverages existing metadata depth (equipment, filters, exposure, tags).
- Creates a distinct niche versus generic photo portfolio sites.

### Implementation complexity
- **Medium-to-high** (more client-side aggregation widgets and richer filter UX).

## Recommended shortlist for this project
If you want fast progress with visible impact:
1. **Primary recommendation: Option A** (best brand/story balance with least structural risk).
2. **Secondary recommendation: Option B** (best for pure browsing speed and return visits).
3. **Future evolution path: Layer Option D modules into A/B** after baseline conversion metrics are stable.

## Decision rubric (quick pick)
Use this when choosing:
- Pick **A** if your priority is "first impression and emotional connection."
- Pick **B** if your priority is "users view many images quickly."
- Pick **C** if your priority is "teach + tell stories, not just showcase."
- Pick **D** if your priority is "serious hobbyist tooling and metadata exploration."

## Suggested next implementation step
Create two homepage wireframe variants in this repo:
- Variant 1: Option A (Cinematic Hero + Curated Highlights)
- Variant 2: Option B (Gallery-First)

Then run a quick content-fit review (desktop + mobile) and pick one to productionize.
