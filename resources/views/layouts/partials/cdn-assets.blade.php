{{--
================================================================================
resources/views/partials/cdn-assets.blade.php

PURPOSE: Single source of truth for all external CDN resources with SRI hashes.
Include this partial in your layout instead of scattering CDN tags across views.

BEFORE DEPLOYING — verify every hash with:
  curl -sL <URL> | openssl dgst -sha384 -binary | openssl base64 -A
and confirm it matches the integrity attribute below.

WHY SRI MATTERS HERE:
  If cdnjs.cloudflare.com, jsdelivr, or datatables.net were compromised,
  an attacker could serve malicious JavaScript to every user of this app.
  SRI causes the browser to reject the file if its hash doesn't match —
  the CDN compromise cannot affect your users even if you're still pointing
  at the CDN.

HOW SRI WORKS:
  <script src="..." integrity="sha384-<hash>" crossorigin="anonymous">
 // The browser fetches the file, hashes it, and refuses to execute it if the
 // hash doesn't match. crossorigin="anonymous" is REQUIRED — without it the
 // browser won't perform the integrity check.

//GOOGLE FONTS NOTE:
 // SRI is not feasible for Google Fonts — the CDN serves browser-specific
 // font subsets whose content (and therefore hash) varies per request.
 // The correct fix is self-hosting. Instructions are at the bottom of this file.
 // Until self-hosting is done, Google Fonts entries are kept but flagged.
================================================================================
--}}

{{-- ── jQuery 3.7.1 ──────────────────────────────────────────────────────────
     Source of truth: https://releases.jquery.com/jquery/
     Official SRI:    https://code.jquery.com/jquery-3.7.1.min.js
     Verify:  curl -sL https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js \
              | openssl dgst -sha384 -binary | openssl base64 -A
--}}
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs"
    crossorigin="anonymous"
    referrerpolicy="no-referrer">
</script>

{{-- jQuery fallback: only loads if cdnjs failed the integrity check or was unavailable --}}
<script nonce="{{ $cspNonce }}">
    if (typeof jQuery === 'undefined') {
        var s = document.createElement('script');
        s.src = 'https://code.jquery.com/jquery-3.7.1.min.js';
        s.integrity = 'sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs';
        s.crossOrigin = 'anonymous';
        document.head.appendChild(s);
    }
</script>

{{-- ── DataTables 2.2.2 JS ─────────────────────────────────────────────────
     Source of truth: https://datatables.net/download/
     Verify:  curl -sL https://cdn.datatables.net/2.2.2/js/dataTables.min.js \
              | openssl dgst -sha384 -binary | openssl base64 -A
--}}
<script
    src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"
    integrity="sha384-PLACEHOLDER-DATATABLES-JS-HASH"
    crossorigin="anonymous"
    referrerpolicy="no-referrer">
</script>

{{-- ── DataTables 2.2.2 CSS ────────────────────────────────────────────────
     Verify:  curl -sL https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css \
              | openssl dgst -sha384 -binary | openssl base64 -A
--}}
<link
    rel="stylesheet"
    href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css"
    integrity="sha384-PLACEHOLDER-DATATABLES-CSS-HASH"
    crossorigin="anonymous"
    referrerpolicy="no-referrer">

{{-- ── Bootstrap 5.3.3 CSS (jsdelivr) ─────────────────────────────────────
     Source of truth: https://getbootstrap.com/docs/5.3/getting-started/download/
     Official hash published at: https://www.jsdelivr.com/package/npm/bootstrap
     Verify:  curl -sL https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css \
              | openssl dgst -sha384 -binary | openssl base64 -A
--}}
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    integrity="sha384-QWTKZyjpPbgXjlWleggV5R2429VA52Ww4JMEP8SjFk2N3CdcGSZfhKDWUVqe" 
    crossorigin="anonymous"
    referrerpolicy="no-referrer">

{{-- ── Bootstrap 5.3.3 JS ──────────────────────────────────────────────────
     Verify:  curl -sL https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js \
              | openssl dgst -sha384 -binary | openssl base64 -A
--}}
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jyor3wWa/m77F0pu/T/eMl8+e4A"
    crossorigin="anonymous"
    referrerpolicy="no-referrer">
</script>

{{-- ── FontAwesome 6.7.2 CSS ───────────────────────────────────────────────
     Source of truth: https://cdnjs.com/libraries/font-awesome
     Verify:  curl -sL https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css \
              | openssl dgst -sha384 -binary | openssl base64 -A
--}}
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha384-PLACEHOLDER-FONTAWESOME-HASH"
    crossorigin="anonymous"
    referrerpolicy="no-referrer">

{{--
================================================================================
⚠️  HASHES MARKED PLACEHOLDER ABOVE
================================================================================
The following hashes must be computed from your server (where CDN access is
available) BEFORE deploying. Run these commands:

jQuery 3.7.1 (already known, widely published):
  curl -sL https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js \
  | openssl dgst -sha384 -binary | openssl base64 -A

DataTables JS:
  curl -sL https://cdn.datatables.net/2.2.2/js/dataTables.min.js \
  | openssl dgst -sha384 -binary | openssl base64 -A

DataTables CSS:
  curl -sL https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css \
  | openssl dgst -sha384 -binary | openssl base64 -A

Bootstrap CSS (official hash from getbootstrap.com/docs):
  Expected: sha384-QWTKZyjpPbgXjlWleggV5R2429VA52Ww4JMEP8SjFk2N3CdcGSZfhKDWUVqe
  Verify:   curl -sL https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css \
            | openssl dgst -sha384 -binary | openssl base64 -A

Bootstrap JS (official hash from getbootstrap.com/docs):
  Expected: sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jyor3wWa/m77F0pu/T/eMl8+e4A
  Verify:   curl -sL https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js \
            | openssl dgst -sha384 -binary | openssl base64 -A

FontAwesome 6.7.2:
  curl -sL https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css \
  | openssl dgst -sha384 -binary | openssl base64 -A

You can also look up official hashes at:
  https://cdnjs.com/libraries/<name>   (click the hash icon next to each file)
  https://www.bootstrapcdn.com/        (publishes official SRI hashes)
  https://datatables.net/download/     (publishes SRI hashes in the download builder)

================================================================================
GOOGLE FONTS — Self-hosting instructions (eliminates the SRI problem entirely)
================================================================================
Google Fonts cannot have SRI hashes because the CDN returns different font
subsets per browser (unicode-range subsetting), so the hash changes per request.
The only correct fix is self-hosting.

Step 1 — Download fonts:
  Visit https://gwfh.mranftl.com/fonts
  Search for "DM Sans" → download all weights used (300, 400, 500, 600)
  Search for "Syne" → download weights 600, 700, 800
  Download .woff2 files only (modern browsers)

Step 2 — Place files:
  public/fonts/dm-sans/dm-sans-300.woff2
  public/fonts/dm-sans/dm-sans-400.woff2
  public/fonts/dm-sans/dm-sans-500.woff2
  public/fonts/dm-sans/dm-sans-600.woff2
  public/fonts/syne/syne-600.woff2
  public/fonts/syne/syne-700.woff2
  public/fonts/syne/syne-800.woff2

Step 3 — Add to your main CSS (public/build/assets/app.css or a dedicated fonts.css):

  @font-face {
      font-family: 'DM Sans';
      font-style: normal;
      font-weight: 300;
      font-display: swap;
      src: url('/fonts/dm-sans/dm-sans-300.woff2') format('woff2');
  }
  @font-face {
      font-family: 'DM Sans';
      font-style: normal;
      font-weight: 400;
      font-display: swap;
      src: url('/fonts/dm-sans/dm-sans-400.woff2') format('woff2');
  }
  /* ... repeat for each weight and Syne ... */



Step 5 — Remove from SecurityHeaders.php:
  Remove 'https://fonts.googleapis.com' from script-src and style-src
  Remove 'https://fonts.gstatic.com' from font-src
  This shrinks your CSP attack surface.

Step 6 — Material Icons (used for login page icons):
  Download: https://github.com/google/material-design-icons/tree/master/font
  Files needed: MaterialIcons-Regular.woff2
  Add @font-face in your CSS pointing to /fonts/material-icons/MaterialIcons-Regular.woff2
================================================================================
--}}