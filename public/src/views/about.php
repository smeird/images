<section class="panel about-guide">
  <h1>About this project</h1>
  <p>
    This project started as a place to publish finished astrophotography images, but it has grown into a practical learning notebook for the entire craft.
    Instead of showing only final images, each capture includes the context that usually gets lost: how the target was chosen, how the mount was tuned,
    how guiding behaved during the session, and what decisions were made in processing. The goal is that a beginner can read one page and understand what to do
    on their next clear night, while an experienced imager can compare workflow details and refine their own process.
  </p>
  <p>
    Every image on this site is shared under a Creative Commons license, and that licensing is intentionally visible across the experience.
    Clear licensing keeps educational reuse simple for clubs, classrooms, and hobby communities who want to discuss techniques using real capture examples.
  </p>

  <h2>A complete deep-sky imaging narrative</h2>
  <p>
    A successful astrophotography result is almost never the product of one setting or one piece of hardware. It is the cumulative result of many small decisions
    made in sequence: careful setup before dark, realistic target planning, disciplined acquisition, and restrained processing. The visual diagrams below summarize
    this journey and the relationships between gear components so you can orient yourself quickly before diving into details.
  </p>

  <figure class="about-diagram" role="img" aria-labelledby="session-flow-caption">
    <svg viewBox="0 0 920 280" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="flowGradient" x1="0" x2="1">
          <stop offset="0%" stop-color="#19345f"/>
          <stop offset="100%" stop-color="#355d99"/>
        </linearGradient>
      </defs>
      <rect x="20" y="70" width="160" height="110" rx="14" fill="url(#flowGradient)" stroke="#9ec1ff"/>
      <rect x="200" y="70" width="160" height="110" rx="14" fill="url(#flowGradient)" stroke="#9ec1ff"/>
      <rect x="380" y="70" width="160" height="110" rx="14" fill="url(#flowGradient)" stroke="#9ec1ff"/>
      <rect x="560" y="70" width="160" height="110" rx="14" fill="url(#flowGradient)" stroke="#9ec1ff"/>
      <rect x="740" y="70" width="160" height="110" rx="14" fill="url(#flowGradient)" stroke="#9ec1ff"/>
      <text x="100" y="110" text-anchor="middle" fill="#eaf2ff" font-size="19" font-family="Inter, sans-serif">Setup</text>
      <text x="100" y="138" text-anchor="middle" fill="#eaf2ff" font-size="14" font-family="Inter, sans-serif">level, balance,</text>
      <text x="100" y="157" text-anchor="middle" fill="#eaf2ff" font-size="14" font-family="Inter, sans-serif">polar align</text>

      <text x="280" y="110" text-anchor="middle" fill="#eaf2ff" font-size="19" font-family="Inter, sans-serif">Plan</text>
      <text x="280" y="138" text-anchor="middle" fill="#eaf2ff" font-size="14" font-family="Inter, sans-serif">target altitude,</text>
      <text x="280" y="157" text-anchor="middle" fill="#eaf2ff" font-size="14" font-family="Inter, sans-serif">moon, framing</text>

      <text x="460" y="110" text-anchor="middle" fill="#eaf2ff" font-size="19" font-family="Inter, sans-serif">Capture</text>
      <text x="460" y="138" text-anchor="middle" fill="#eaf2ff" font-size="14" font-family="Inter, sans-serif">guide, dither,</text>
      <text x="460" y="157" text-anchor="middle" fill="#eaf2ff" font-size="14" font-family="Inter, sans-serif">calibration frames</text>

      <text x="640" y="110" text-anchor="middle" fill="#eaf2ff" font-size="19" font-family="Inter, sans-serif">Process</text>
      <text x="640" y="138" text-anchor="middle" fill="#eaf2ff" font-size="14" font-family="Inter, sans-serif">stack, gradient</text>
      <text x="640" y="157" text-anchor="middle" fill="#eaf2ff" font-size="14" font-family="Inter, sans-serif">control, stretch</text>

      <text x="820" y="110" text-anchor="middle" fill="#eaf2ff" font-size="19" font-family="Inter, sans-serif">Publish</text>
      <text x="820" y="138" text-anchor="middle" fill="#eaf2ff" font-size="14" font-family="Inter, sans-serif">metadata,</text>
      <text x="820" y="157" text-anchor="middle" fill="#eaf2ff" font-size="14" font-family="Inter, sans-serif">story + settings</text>

      <path d="M181 125 H198" stroke="#a6c8ff" stroke-width="3" marker-end="url(#arrow)"/>
      <path d="M361 125 H378" stroke="#a6c8ff" stroke-width="3" marker-end="url(#arrow)"/>
      <path d="M541 125 H558" stroke="#a6c8ff" stroke-width="3" marker-end="url(#arrow)"/>
      <path d="M721 125 H738" stroke="#a6c8ff" stroke-width="3" marker-end="url(#arrow)"/>
      <defs>
        <marker id="arrow" markerWidth="8" markerHeight="8" refX="6" refY="4" orient="auto">
          <polygon points="0 0, 8 4, 0 8" fill="#a6c8ff"/>
        </marker>
      </defs>
    </svg>
    <figcaption id="session-flow-caption">Diagram: the nightly deep-sky workflow from first setup to published result.</figcaption>
  </figure>

  <h3>How to set up for repeatable results</h3>
  <p>
    Begin with mechanical stability before thinking about exposure settings. Put your tripod on firm ground, level the head, and make sure every leg clamp is tight.
    After the telescope and camera train are attached, balance both RA and DEC so the mount is not fighting gravity on either axis. Good cable routing matters more
    than many people expect: a single snag can imitate periodic error and ruin otherwise excellent guiding.
  </p>
  <p>
    Polar alignment should be treated as a quality gate. If your alignment is poor, every downstream decision becomes harder. Once aligned, perform focus with either
    a Bahtinov mask or autofocus routine, then re-check focus whenever temperature changes noticeably. These habits are not glamorous, but they are what make
    multi-hour integrations consistent.
  </p>

  <figure class="about-diagram" role="img" aria-labelledby="train-map-caption">
    <svg viewBox="0 0 900 250" xmlns="http://www.w3.org/2000/svg">
      <rect x="20" y="95" width="140" height="70" rx="12" fill="#1d335c" stroke="#a6c8ff"/>
      <rect x="200" y="95" width="140" height="70" rx="12" fill="#1d335c" stroke="#a6c8ff"/>
      <rect x="380" y="95" width="140" height="70" rx="12" fill="#1d335c" stroke="#a6c8ff"/>
      <rect x="560" y="95" width="140" height="70" rx="12" fill="#1d335c" stroke="#a6c8ff"/>
      <rect x="740" y="95" width="140" height="70" rx="12" fill="#1d335c" stroke="#a6c8ff"/>

      <text x="90" y="136" fill="#eff6ff" font-size="17" text-anchor="middle">Mount</text>
      <text x="270" y="136" fill="#eff6ff" font-size="17" text-anchor="middle">Scope</text>
      <text x="450" y="136" fill="#eff6ff" font-size="17" text-anchor="middle">Flattener</text>
      <text x="630" y="136" fill="#eff6ff" font-size="17" text-anchor="middle">Filter Wheel</text>
      <text x="810" y="136" fill="#eff6ff" font-size="17" text-anchor="middle">Camera</text>

      <path d="M160 130 H200 M340 130 H380 M520 130 H560 M700 130 H740" stroke="#a6c8ff" stroke-width="3"/>
      <text x="450" y="42" fill="#c8ddff" font-size="16" text-anchor="middle">Typical imaging train layout (left to right)</text>
      <text x="450" y="220" fill="#9eb8e5" font-size="14" text-anchor="middle">Guide scope + guide camera run in parallel and feed correction pulses to the mount.</text>
    </svg>
    <figcaption id="train-map-caption">Diagram: a common deep-sky imaging train and where each component contributes.</figcaption>
  </figure>

  <h3>Planning and acquisition in plain language</h3>
  <p>
    Choose targets that will spend meaningful time high above the horizon. A dim nebula at a low altitude can look bright in planning software but still produce
    weak signal through heavy atmosphere. Match your focal length to the target scale, then decide whether the night favors narrowband work (moonlit conditions)
    or broadband color (darker skies). When framing is locked in, save it as a reusable template so your next session can add cleanly to the same project.
  </p>
  <p>
    During capture, think in terms of consistency rather than hero exposures. Use a sub-exposure length that keeps stars controlled and the histogram safely away
    from clipping, guide calmly, and dither every few frames. Calibration frames are not optional overhead; they are part of the signal chain. Darks, flats,
    and bias (or dark flats) make the final integration cleaner and reduce time spent fighting artifacts later.
  </p>

  <h3>Interpreting guiding without guesswork</h3>
  <p>
    Guiding RMS is easiest to interpret relative to image scale. If your total RMS is lower than your arcseconds-per-pixel value, your stars are often in a healthy
    range for deep-sky work. As a rough benchmark, 0.30" to 0.60" is excellent, 0.60" to 0.90" is very good, and values above 1.50" usually indicate something
    that should be corrected, such as polar alignment drift, balance bias, backlash, wind, or cable drag. Seeing still sets the upper limit, so avoid over-tuning
    for temporary atmospheric spikes.
  </p>

  <h3>Processing as a sequence of small, reversible decisions</h3>
  <p>
    Start by calibrating and integrating your lights into a clean master. Remove gradients before major stretching so background correction does not fight an
    already nonlinear image. Perform color calibration with a reference method, then apply noise reduction and deconvolution with masks so details remain natural.
    Stretch gradually, checking star cores and background neutrality at each step. Final polish should focus on balance and readability, not maximal contrast.
    A restrained finish usually ages better than an aggressively sharpened one.
  </p>

  <h2>Example sky references and inspiration</h2>
  <p>
    These public-domain astronomy images are included here as visual references for composition, star color contrast, and scale. They are not local captures,
    but they are helpful for understanding what different classes of targets can look like when processed with care.
  </p>

  <div class="about-photo-grid">
    <figure>
      <img src="https://upload.wikimedia.org/wikipedia/commons/5/57/M31bobo.jpg" alt="Andromeda Galaxy wide-field reference image">
      <figcaption>
        <strong>Andromeda Galaxy (M31).</strong> A classic broadband target where framing choices determine whether you emphasize the bright core,
        surrounding dust lanes, or the neighboring satellite galaxies.
      </figcaption>
    </figure>
    <figure>
      <img src="https://upload.wikimedia.org/wikipedia/commons/d/d3/Orion_Nebula_-_Hubble_2006_mosaic_18000.jpg" alt="Orion Nebula Hubble mosaic reference image">
      <figcaption>
        <strong>Orion Nebula (M42).</strong> A dynamic object with extreme brightness range, useful for practicing exposure blending and careful highlight management.
      </figcaption>
    </figure>
  </div>

  <h3>Recommended references for deeper study</h3>
  <p>
    If you want to go deeper, the PHD2, N.I.N.A., Siril, and PixInsight documentation sets remain some of the most practical sources for troubleshooting and workflow
    refinement. For planning, field-of-view calculators and seeing forecasts are essential companions to clear-sky reports.
  </p>
  <p class="about-reference-links">
    <a href="https://openphdguiding.org/" target="_blank" rel="noopener noreferrer">PHD2 Guiding</a> ·
    <a href="https://nighttime-imaging.eu/docs/master/site/" target="_blank" rel="noopener noreferrer">N.I.N.A.</a> ·
    <a href="https://siril.readthedocs.io/en/stable/" target="_blank" rel="noopener noreferrer">Siril</a> ·
    <a href="https://pixinsight.com/doc/docs/" target="_blank" rel="noopener noreferrer">PixInsight</a> ·
    <a href="https://astronomy.tools/calculators/field_of_view/" target="_blank" rel="noopener noreferrer">Astronomy Tools FOV Calculator</a> ·
    <a href="https://clearoutside.com/" target="_blank" rel="noopener noreferrer">Clear Outside</a> ·
    <a href="https://www.meteoblue.com/en/weather/outdoorsports/seeing" target="_blank" rel="noopener noreferrer">Meteoblue Seeing</a>
  </p>

  <h2>Astrophotography learning and tool directory</h2>
  <p>
    The resource list below is organized by purpose so you can quickly jump to practical beginner guides, active communities, planning tools, capture/processing software,
    and inspiration platforms. Every link opens in a new tab.
  </p>

  <div class="about-resource-grid">
    <section class="about-resource-card">
      <h3>General guides and tutorials</h3>
      <ul>
        <li>
          <a href="https://www.galactic-hunter.com/post/starting-astrophotography-a-complete-guide" target="_blank" rel="noopener noreferrer">Beginner Astrophotography: A Complete Guide</a>
          <span>Comprehensive beginner-to-intermediate guide covering gear, planning, light pollution, tracking and processing.</span>
        </li>
        <li>
          <a href="https://astrobackyard.com/resources" target="_blank" rel="noopener noreferrer">Astrophotography Resources (AstroBackyard)</a>
          <span>Curated list of software tools, apps and general resources to support imaging workflows.</span>
        </li>
        <li>
          <a href="https://www.reddit.com/r/AskAstrophotography/comments/e609qd/astrophotography_guide_for_beginners" target="_blank" rel="noopener noreferrer">Astrophotography Guide for Beginners (Reddit)</a>
          <span>Community-driven advice oriented around deep sky imaging fundamentals.</span>
        </li>
      </ul>
    </section>

    <section class="about-resource-card">
      <h3>Forums and community sites</h3>
      <ul>
        <li>
          <a href="https://www.cloudynights.com/" target="_blank" rel="noopener noreferrer">Cloudy Nights</a>
          <span>One of the most comprehensive online communities for techniques, equipment decisions, processing, and troubleshooting.</span>
        </li>
        <li>
          <a href="https://stargazerslounge.com/" target="_blank" rel="noopener noreferrer">Stargazers Lounge</a>
          <span>Active UK-centric community for practical help, shared experiences and inspiration.</span>
        </li>
      </ul>
    </section>

    <section class="about-resource-card">
      <h3>Planning and sky simulation tools</h3>
      <ul>
        <li>
          <a href="https://stellarium.org/" target="_blank" rel="noopener noreferrer">Stellarium</a>
          <span>Free open-source planetarium software for visualizing the sky, planning sessions, and identifying targets.</span>
        </li>
        <li>
          <a href="https://telescopius.com/" target="_blank" rel="noopener noreferrer">Telescopius</a>
          <span>Online planner with target lists, visibility charts, and search features tailored for astrophotography.</span>
        </li>
        <li>
          <a href="https://astronomy.tools/" target="_blank" rel="noopener noreferrer">Astronomy-Tools.com</a>
          <span>Calculators for field of view planning and telescope/camera match-ups.</span>
        </li>
      </ul>
    </section>

    <section class="about-resource-card">
      <h3>Software for capture, processing and analysis</h3>
      <ul>
        <li>
          <a href="https://edu.kde.org/kstars/" target="_blank" rel="noopener noreferrer">KStars</a>
          <span>Free planetarium and capture suite with autofocus, guiding, and sequence management via Ekos.</span>
        </li>
        <li>
          <a href="https://www.siril.org/" target="_blank" rel="noopener noreferrer">Siril</a>
          <span>Cross-platform astrophotography processing suite for calibration, alignment, stacking and enhancement.</span>
        </li>
        <li>
          <a href="http://www.astronomie.be/registax" target="_blank" rel="noopener noreferrer">RegiStax</a>
          <span>Freeware stacking and processing software widely used for planetary imaging.</span>
        </li>
      </ul>
    </section>

    <section class="about-resource-card">
      <h3>Planning and auxiliary tools</h3>
      <ul>
        <li>
          <a href="https://www.cloudynights.com/forums/topic/872107-deep-sky-planning-software/" target="_blank" rel="noopener noreferrer">DeepSkyPlanner</a>
          <span>Community-referenced software for creating multi-target observation lists and session plans.</span>
        </li>
        <li>
          <a href="https://www.lightpollutionmap.info/" target="_blank" rel="noopener noreferrer">Light Pollution Map</a>
          <span>Global satellite-based dark-sky maps for selecting imaging locations.</span>
        </li>
        <li>
          <a href="http://astrometry.net/" target="_blank" rel="noopener noreferrer">Astrometry.net</a>
          <span>Free plate-solving service to identify celestial coordinates in your images.</span>
        </li>
      </ul>
    </section>

    <section class="about-resource-card">
      <h3>Image hosting and inspiration</h3>
      <ul>
        <li>
          <a href="https://www.astrobin.com/" target="_blank" rel="noopener noreferrer">AstroBin</a>
          <span>Dedicated astrophotography image platform with tagging, exposure metadata, and community feedback.</span>
        </li>
      </ul>
      <h3>Journal and broader astronomy coverage</h3>
      <ul>
        <li>
          <a href="https://skyandtelescope.org/" target="_blank" rel="noopener noreferrer">Sky &amp; Telescope</a>
          <span>Long-standing astronomy magazine with observing guides, gear reviews and imaging features.</span>
        </li>
      </ul>
      <h3>Apps and mobile tools</h3>
      <ul>
        <li>
          <a href="https://picastroapp.com/post/best-astrophotography-apps-for-use-in-astrophotography" target="_blank" rel="noopener noreferrer">Astrophotography Apps Guide (Picastro)</a>
          <span>Curated list of mobile tools useful for field planning, imaging, and reference.</span>
        </li>
      </ul>
      <h3>Supplementary resource index</h3>
      <ul>
        <li>
          <a href="https://3sistersastronomy.com/resources/astro-sites-2/" target="_blank" rel="noopener noreferrer">Astro Sites</a>
          <span>Comprehensive astronomy and astrophotography link directory assembled by enthusiasts.</span>
        </li>
      </ul>
    </section>
  </div>
</section>

<section class="panel">
  <h2>Comprehensive astrophotography guide</h2>
  <p>This guide is written as a practical, start-to-finish workflow you can follow in the field and then refine in post-processing. Equipment examples use the rig values already used throughout this project: <strong>Esprit 120 telescope</strong>, <strong>EQ6-R Pro mount</strong>, <strong>ASI2600MM camera</strong>, <strong>ZWO 7x2&quot; EFW</strong>, and <strong>Ha/OIII/SII narrowband filters</strong>.</p>

  <h3>1) Telescope setup and site preparation</h3>
  <ol>
    <li><strong>Tripod and mount leveling:</strong> Set up the tripod on stable ground, spread legs fully, and level the mount base before adding payload. Good mechanical alignment here reduces later correction load.</li>
    <li><strong>Balance both axes:</strong> With the Esprit 120 + camera + filter wheel installed, balance RA and DEC carefully in the exact imaging configuration (all cables attached). Slight east-heavy RA bias can help some mounts maintain gear mesh.</li>
    <li><strong>Cable management:</strong> Route power/USB/dew-heater cables so the mount can slew meridian-to-horizon without snagging. Leave smooth service loops and test full-range slews in daylight.</li>
    <li><strong>Polar alignment:</strong> Use your preferred method (polar scope, software-assisted plate-solving, or dedicated polar-align routines). For long focal-length work, aim for polar error under 1-2 arcminutes, and ideally below 1 arcminute when practical.</li>
    <li><strong>Focus and thermal stability:</strong> Reach rough focus at dusk, then fine-focus once stars are visible. Recheck focus as temperature drops, especially on refractors where focus drift is common.</li>
  </ol>

  <h3>2) Image planning (before darkness and during the night)</h3>
  <ol>
    <li><strong>Choose target by altitude and season:</strong> Prioritize objects that culminate high to reduce atmospheric distortion and improve signal quality.</li>
    <li><strong>Frame planning:</strong> Use a field-of-view calculator with your Esprit 120 + ASI2600MM combination to preview object scale and orientation before session start.</li>
    <li><strong>Moon and sky condition strategy:</strong> Shoot narrowband (Ha/OIII/SII) in bright Moon phases and reserve broadband or RGB-heavy work for darker windows.</li>
    <li><strong>Exposure strategy:</strong> Plan total integration first (for example 6-20+ hours depending on target), then divide into subexposures suitable for your sky brightness, guiding quality, and mount behavior.</li>
    <li><strong>Sequence planning:</strong> Prepare a capture sequence that includes autofocus triggers, dithering cadence, filter changes, meridian flip handling, and calibration frames.</li>
  </ol>

  <h3>3) Image acquisition (capture night workflow)</h3>
  <ol>
    <li><strong>Plate-solve and center:</strong> Slew, solve, center, and rotate framing to match your plan.</li>
    <li><strong>Guiding setup and expected accuracy:</strong>
      <ul>
        <li><strong>Excellent:</strong> ~0.3"-0.6" total RMS (rare, premium seeing/mechanics).</li>
        <li><strong>Very good:</strong> ~0.6"-0.9" total RMS (commonly enough for small stars at moderate focal lengths).</li>
        <li><strong>Usable:</strong> ~0.9"-1.4" total RMS (often acceptable depending on image scale and seeing).</li>
        <li><strong>Troubleshooting zone:</strong> &gt;1.4" RMS sustained; inspect balance, cable drag, backlash, aggressiveness, exposure time, and wind shake.</li>
      </ul>
      Accuracy targets should be interpreted relative to your image scale (arcsec/pixel). As a rule of thumb, keeping RMS around or below your image scale typically yields cleaner star profiles.
    </li>
    <li><strong>Dither between frames:</strong> Dither every 1-3 subframes to suppress fixed-pattern noise and walking noise in stacked integrations.</li>
    <li><strong>Monitor data quality live:</strong> Watch FWHM/HFR trends, star eccentricity, histogram placement, and cloud/smoke intrusion. Abort poor subs early to protect total integration efficiency.</li>
    <li><strong>Capture calibration data:</strong> Acquire darks, flats, and dark-flats/bias according to your camera workflow. Keep flats matched to optical train state (focus, rotation, filter, reducer spacing).</li>
  </ol>

  <h3>4) Processing workflow (from raw frames to final image)</h3>
  <ol>
    <li><strong>Pre-processing and calibration:</strong> Calibrate lights with dark/flat data, perform registration, quality-weighted integration, and rejection of poor frames.</li>
    <li><strong>Background and gradient correction:</strong> Remove vignetting remnants and sky gradients before aggressive stretching.</li>
    <li><strong>Linear-stage noise management:</strong> Apply careful denoise while preserving structure (especially in faint nebula dust).</li>
    <li><strong>Channel combination:</strong> For your Ha/OIII/SII workflow, combine channels (SHO, HOO, or custom blends), then neutralize stars/background before non-linear stretch.</li>
    <li><strong>Non-linear stretch and contrast shaping:</strong> Stretch gradually to preserve dynamic range and avoid clipping bright cores.</li>
    <li><strong>Star control and color refinement:</strong> Use star reduction or star-separate workflows only as needed; maintain natural star sizes and color variety.</li>
    <li><strong>Final polish:</strong> Micro-contrast, saturation tuning, local structure enhancement, crop/rotation cleanup, and export master + web-ready versions.</li>
  </ol>

  <h3>5) Quality checklist before publishing</h3>
  <ul>
    <li>Stars are round across most of the field (no strong tilt or corner elongation).</li>
    <li>Background is neutral/smooth without blotchy chroma noise.</li>
    <li>No clipped black point and no blown-out bright cores unless intentionally styled.</li>
    <li>Metadata is complete (target, date, exposure totals, telescope, mount, camera, filters, processing notes).</li>
  </ul>

  <h3>Helpful reference material</h3>
  <ul>
    <li><a href="https://nighttime-imaging.eu/docs/master/site/" target="_blank" rel="noreferrer noopener">N.I.N.A. Documentation (sequencing, autofocus, plate-solving)</a></li>
    <li><a href="https://openphdguiding.org/documentation/" target="_blank" rel="noreferrer noopener">PHD2 Guiding Documentation</a></li>
    <li><a href="https://siril.readthedocs.io/en/stable/" target="_blank" rel="noreferrer noopener">Siril Official Documentation</a></li>
    <li><a href="https://pixinsight.com/" target="_blank" rel="noreferrer noopener">PixInsight (official site + documentation entrypoint)</a></li>
    <li><a href="https://clarkvision.com/articles/astrophotography.image.processing.basics/" target="_blank" rel="noreferrer noopener">Astrophotography Image Processing Basics (Clarkvision)</a></li>
    <li><a href="https://www.astropy.org/" target="_blank" rel="noreferrer noopener">Astropy Project (advanced scientific tooling and references)</a></li>
  </ul>
</section>
