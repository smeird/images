<section class="panel">
  <h1>About this project</h1>
  <p>This site showcases astrophotography sessions with transparent metadata so viewers can learn the complete capture and processing workflow.</p>
  <p><strong>Licensing:</strong> All gallery images are published under a Creative Commons license so reuse terms remain clear across the site.</p>
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
