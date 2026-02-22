<section class="panel about-guide">
  <h1>About this project</h1>
  <p>
    This site showcases astrophotography sessions with transparent metadata so viewers can learn complete capture and processing workflows,
    from first polar-alignment adjustments to final color grading.
  </p>
  <p><strong>Licensing:</strong> All gallery images are published under a Creative Commons license so reuse terms remain clear across the site.</p>

  <h2>Complete astrophotography field guide</h2>
  <p>
    Use this as a practical, end-to-end checklist. The examples below reference a typical deep-sky setup profile
    (GoTo equatorial mount + refractor/reflector + cooled astronomy camera + guide scope + filter workflow),
    matching the metadata fields you store in this app.
  </p>

  <h3>1) Telescope and mount setup</h3>
  <ol>
    <li><strong>Tripod and mount foundation:</strong> Set tripod legs on firm ground, level the base, and orient the mount roughly north/south as appropriate for your hemisphere.</li>
    <li><strong>Balance the payload:</strong> With clutch released, balance RA and DEC axes with your full imaging train attached (camera, filter wheel/drawer, dew control, cables).</li>
    <li><strong>Cable discipline:</strong> Route cables with strain relief to avoid snags and minimize drag-induced guiding errors during meridian flips.</li>
    <li><strong>Polar alignment:</strong> Use your mount tool, plate-solving routine, or software-assisted routine until your error is acceptably low for your focal length.</li>
    <li><strong>Focus:</strong> Achieve critical focus with a Bahtinov mask or autofocus routine, then re-check after large temperature changes.</li>
  </ol>

  <h3>2) Planning your target and framing</h3>
  <ol>
    <li><strong>Choose by season and altitude:</strong> Prioritize targets that transit high in the sky for improved seeing and reduced atmospheric extinction.</li>
    <li><strong>Pick a focal-length match:</strong> Wide-field nebulae fit shorter focal lengths; galaxies/planetary nebulae generally benefit from longer focal lengths.</li>
    <li><strong>Check moon phase and light pollution:</strong> Prefer narrowband on bright moon nights and broadband/LRGB under darker skies.</li>
    <li><strong>Frame with plate solving:</strong> Save framing templates so repeat sessions stay consistent for multi-night integration.</li>
    <li><strong>Plan total integration time:</strong> Typical goals are 2-4h (quick result), 6-12h (strong detail), or 15h+ (faint structures and smoother noise).</li>
  </ol>

  <h3>3) Image acquisition workflow</h3>
  <ol>
    <li><strong>Calibrate and sync:</strong> Perform GoTo alignment and plate solve to center your target.</li>
    <li><strong>Guiding setup:</strong> Calibrate guider after target framing and verify aggressive settings are not causing oscillation.</li>
    <li><strong>Exposure strategy:</strong> Start with test exposures and histogram checks, then lock in gain/offset/ISO and sub-exposure length.</li>
    <li><strong>Dithering:</strong> Dither every 1-3 frames to reduce walking noise and improve stacked results.</li>
    <li><strong>Capture calibration frames:</strong> Record darks, flats, and bias (or dark flats) for cleaner master integration.</li>
  </ol>

  <h3>4) Guiding accuracy targets (rule-of-thumb levels)</h3>
  <ul>
    <li><strong>Excellent:</strong> 0.30"-0.60" RMS total error — ideal for longer focal lengths and fine pixel scales.</li>
    <li><strong>Very good:</strong> 0.60"-0.90" RMS — usually delivers tight stars for most refractor setups.</li>
    <li><strong>Usable:</strong> 0.90"-1.50" RMS — often acceptable for wider image scales, but inspect stars near frame edges.</li>
    <li><strong>Needs tuning:</strong> &gt;1.50" RMS — revisit balance, polar alignment, backlash compensation, seeing conditions, and cable drag.</li>
  </ul>
  <p>
    Practical interpretation: keep total RMS below your image scale when possible (arcsec/pixel), and remember seeing can dominate performance even with perfect mechanics.
  </p>

  <h3>5) Processing pipeline (from raw data to final image)</h3>
  <ol>
    <li><strong>Pre-processing:</strong> Calibrate lights with darks/flats/bias, then register and integrate into a master stack.</li>
    <li><strong>Gradient and background control:</strong> Remove light pollution gradients and neutralize background.</li>
    <li><strong>Color calibration:</strong> Use photometric or reference-based color workflows for natural star/nebula balance.</li>
    <li><strong>Noise reduction and deconvolution:</strong> Apply carefully with masks to preserve small-scale detail.</li>
    <li><strong>Stretching:</strong> Move from linear to nonlinear data progressively to avoid clipped highlights and harsh contrast transitions.</li>
    <li><strong>Star and structure balancing:</strong> Use selective star reduction and local contrast enhancements without overprocessing.</li>
    <li><strong>Final polish:</strong> Crop/rotate, annotate metadata, and export web-friendly JPEG plus archival full-resolution masters.</li>
  </ol>

  <h3>6) Recommended reference material</h3>
  <ul>
    <li><a href="https://openphdguiding.org/" target="_blank" rel="noopener noreferrer">PHD2 Guiding documentation</a> for guiding setup and troubleshooting.</li>
    <li><a href="https://nighttime-imaging.eu/docs/master/site/" target="_blank" rel="noopener noreferrer">N.I.N.A. documentation</a> for acquisition sequencing, autofocus, and automation.</li>
    <li><a href="https://siril.readthedocs.io/en/stable/" target="_blank" rel="noopener noreferrer">Siril documentation</a> for calibration, stacking, and post-processing.</li>
    <li><a href="https://pixinsight.com/doc/docs/" target="_blank" rel="noopener noreferrer">PixInsight reference documentation</a> for advanced processing workflows.</li>
    <li><a href="https://astronomy.tools/calculators/field_of_view/" target="_blank" rel="noopener noreferrer">Astronomy Tools field-of-view calculator</a> for planning framing with your optics and camera.</li>
    <li><a href="https://clearoutside.com/" target="_blank" rel="noopener noreferrer">Clear Outside</a> and <a href="https://www.meteoblue.com/en/weather/outdoorsports/seeing" target="_blank" rel="noopener noreferrer">Meteoblue Seeing</a> for weather and seeing forecasts.</li>
  </ul>
</section>
