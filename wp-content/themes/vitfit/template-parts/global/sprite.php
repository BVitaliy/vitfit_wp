<?php
/**
 * SVG sprite: packs (#bottle, #can, #glass) and the icons (#i-*) — markup inc/_sprite.php as delivered.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
  <defs>
    <clipPath id="bottle-clip">
      <path d="M68 52H132V76C132 100 190 110 190 150V436C190 455 177 468 158 468H42C23 468 10 455 10 436V150C10 110 68 100 68 76Z" />
    </clipPath>
    <linearGradient id="bottle-shade" x1="0" x2="1" y1="0" y2="0">
      <stop offset="0" stop-color="#000" stop-opacity=".34" />
      <stop offset=".14" stop-color="#fff" stop-opacity=".16" />
      <stop offset=".26" stop-color="#fff" stop-opacity=".05" />
      <stop offset=".62" stop-color="#000" stop-opacity="0" />
      <stop offset="1" stop-color="#000" stop-opacity=".38" />
    </linearGradient>
    <linearGradient id="bottle-depth" x1="0" x2="0" y1="0" y2="1">
      <stop offset="0" stop-color="#fff" stop-opacity=".18" />
      <stop offset=".5" stop-color="#000" stop-opacity="0" />
      <stop offset="1" stop-color="#000" stop-opacity=".28" />
    </linearGradient>
    <linearGradient id="can-metal" x1="0" x2="1" y1="0" y2="0">
      <stop offset="0" stop-color="#000" stop-opacity=".25" />
      <stop offset=".22" stop-color="#fff" stop-opacity=".55" />
      <stop offset=".4" stop-color="#fff" stop-opacity=".05" />
      <stop offset=".8" stop-color="#000" stop-opacity=".1" />
      <stop offset="1" stop-color="#000" stop-opacity=".35" />
    </linearGradient>
    <linearGradient id="cap-shade" x1="0" x2="1" y1="0" y2="0">
      <stop offset="0" stop-color="#fff" stop-opacity=".05" />
      <stop offset=".3" stop-color="#fff" stop-opacity=".22" />
      <stop offset=".45" stop-color="#fff" stop-opacity="0" />
      <stop offset="1" stop-color="#000" stop-opacity=".4" />
    </linearGradient>
  </defs>

  <symbol id="bottle" viewBox="0 0 200 470">
    <!-- glass -->
    <path d="M68 52H132V76C132 100 190 110 190 150V436C190 455 177 468 158 468H42C23 468 10 455 10 436V150C10 110 68 100 68 76Z" style="fill:var(--b-glass,rgba(255,255,255,.35))" />
    <!-- liquid -->
    <g clip-path="url(#bottle-clip)">
      <rect x="0" y="92" width="200" height="380" style="fill:var(--b-liquid,#c3173d)" />
      <rect x="0" y="92" width="200" height="380" fill="url(#bottle-depth)" />
      <ellipse cx="100" cy="92" rx="56" ry="5" fill="#fff" opacity=".35" />
      <!-- label -->
      <rect x="0" y="182" width="200" height="228" style="fill:var(--b-label,#ffd3de)" />
      <rect x="22" y="196" width="108" height="66" rx="3" fill="#16110e" />
      <text x="29" y="226" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="23.5" letter-spacing="-.6">KOMBU</text>
      <text x="29" y="253" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="23.5" letter-spacing="-.6">CHA</text>
      <text x="136" y="212" fill="#16110e" font-family="Ermilov, Arial Black, sans-serif" font-size="9">ЧАЙНИЙ</text>
      <text x="136" y="223" fill="#16110e" font-family="Ermilov, Arial Black, sans-serif" font-size="9">ГРИБ</text>
      <rect x="22" y="268" width="156" height="22" rx="2" fill="#fff" />
      <text x="22" y="304" fill="#16110e" font-family="Arial, sans-serif" font-size="8" font-weight="700">live probiotic</text>
      <!-- fruit -->
      <path d="M54 326 C58 316 66 312 76 311" fill="none" stroke="#16110e" stroke-width="1.6" />
      <circle cx="48" cy="344" r="14" style="fill:var(--b-fl,#f15f86)" stroke="#16110e" stroke-width="1.6" />
      <circle cx="75" cy="338" r="12" style="fill:var(--b-fl,#f15f86)" stroke="#16110e" stroke-width="1.6" />
      <circle cx="43" cy="339" r="4" fill="#fff" opacity=".6" />
      <!-- leaf: flavour half + green half -->
      <path d="M118 404 C100 372 104 318 168 296 C182 348 160 392 118 404Z" style="fill:var(--b-leaf,#63b648)" stroke="#16110e" stroke-width="1.8" />
      <path d="M118 404 C100 372 104 318 168 296 C140 330 126 366 118 404Z" style="fill:var(--b-fl,#f15f86)" />
      <path d="M118 404 C100 372 104 318 168 296 C182 348 160 392 118 404Z M118 404 C132 360 146 330 168 296" fill="none" stroke="#16110e" stroke-width="1.8" />
      <!-- logo -->
      <rect x="22" y="356" width="44" height="44" rx="3" fill="#16110e" />
      <text x="44" y="374" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="12" text-anchor="middle">VIT-</text>
      <text x="44" y="390" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="12" text-anchor="middle">FIT</text>
      <text x="176" y="402" fill="#16110e" font-family="Arial, sans-serif" font-size="8" text-anchor="end">0,5 л</text>
      <!-- cylinder shading over everything -->
      <rect x="0" y="0" width="200" height="470" fill="url(#bottle-shade)" />
      <rect x="24" y="120" width="9" height="300" rx="4.5" fill="#fff" opacity=".32" />
      <rect x="168" y="150" width="4" height="240" rx="2" fill="#fff" opacity=".18" />
    </g>
    <!-- cap -->
    <rect x="60" y="44" width="80" height="10" rx="3" fill="#16110e" />
    <rect x="63" y="2" width="74" height="46" rx="7" fill="#16110e" />
    <g stroke="#fff" stroke-opacity=".09" stroke-width="2">
      <path d="M72 8V44M80 8V44M88 8V44M96 8V44M104 8V44M112 8V44M120 8V44M128 8V44" />
    </g>
    <rect x="63" y="2" width="74" height="46" rx="7" fill="url(#cap-shade)" />
  </symbol>


  <?php // 0.33 l can — same label system and name strip height as the bottle ?>
  <symbol id="can" viewBox="0 0 200 470">
    <defs>
      <clipPath id="can-clip"><path d="M22 40C22 30 34 24 46 24H154C166 24 178 30 178 40V432C178 444 166 452 154 452H46C34 452 22 444 22 432Z" /></clipPath>
    </defs>
    <!-- lid + neck (aluminium) -->
    <path d="M40 10H160C168 10 172 16 172 22V30H28V22C28 16 32 10 40 10Z" fill="#c9cdd2" />
    <path d="M40 10H160C168 10 172 16 172 22V30H28V22C28 16 32 10 40 10Z" fill="url(#can-metal)" />
    <g clip-path="url(#can-clip)">
      <rect x="0" y="0" width="200" height="470" style="fill:var(--b-label,#ffd3de)" />
      <rect x="34" y="196" width="108" height="66" rx="3" fill="#16110e" />
      <text x="41" y="226" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="23.5" letter-spacing="-.6">KOMBU</text>
      <text x="41" y="253" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="23.5" letter-spacing="-.6">CHA</text>
      <rect x="34" y="268" width="132" height="22" rx="2" fill="#fff" />
      <text x="34" y="304" fill="#16110e" font-family="Arial, sans-serif" font-size="8" font-weight="700">live probiotic</text>
      <circle cx="58" cy="344" r="14" style="fill:var(--b-fl,#f15f86)" stroke="#16110e" stroke-width="1.6" />
      <circle cx="84" cy="338" r="12" style="fill:var(--b-fl,#f15f86)" stroke="#16110e" stroke-width="1.6" />
      <path d="M64 326 C68 316 76 312 86 311" fill="none" stroke="#16110e" stroke-width="1.6" />
      <path d="M118 410 C100 378 104 322 168 300 C182 352 160 398 118 410Z" style="fill:var(--b-leaf,#63b648)" />
      <path d="M118 410 C100 378 104 322 168 300 C140 334 126 372 118 410Z" style="fill:var(--b-fl,#f15f86)" />
      <path d="M118 410 C100 378 104 322 168 300 C182 352 160 398 118 410Z M118 410 C132 366 146 334 168 300" fill="none" stroke="#16110e" stroke-width="1.8" />
      <rect x="34" y="362" width="44" height="44" rx="3" fill="#16110e" />
      <text x="56" y="380" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="12" text-anchor="middle">VIT-</text>
      <text x="56" y="396" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="12" text-anchor="middle">FIT</text>
      <text x="166" y="438" fill="#16110e" font-family="Arial, sans-serif" font-size="8" text-anchor="end">0,33 л</text>
      <rect x="0" y="0" width="200" height="470" fill="url(#bottle-shade)" />
      <rect x="36" y="60" width="9" height="360" rx="4.5" fill="#fff" opacity=".35" />
    </g>
    <!-- bottom rim -->
    <path d="M30 446H170C170 456 160 464 148 464H52C40 464 30 456 30 446Z" fill="#b8bcc2" />
    <path d="M30 446H170C170 456 160 464 148 464H52C40 464 30 456 30 446Z" fill="url(#can-metal)" />
  </symbol>

  <?php // 0.33 l glass bottle — slim body, long neck, metal crown cap ?>
  <symbol id="glass" viewBox="0 0 200 470">
    <defs>
      <clipPath id="glass-clip"><path d="M84 46H116V120C116 168 152 182 152 230V444C152 458 142 466 130 466H70C58 466 48 458 48 444V230C48 182 84 168 84 120Z" /></clipPath>
    </defs>
    <path d="M84 46H116V120C116 168 152 182 152 230V444C152 458 142 466 130 466H70C58 466 48 458 48 444V230C48 182 84 168 84 120Z" style="fill:var(--b-glass,rgba(255,255,255,.35))" />
    <g clip-path="url(#glass-clip)">
      <rect x="0" y="96" width="200" height="380" style="fill:var(--b-liquid,#c3173d)" />
      <rect x="0" y="96" width="200" height="380" fill="url(#bottle-depth)" />
      <rect x="0" y="190" width="200" height="226" style="fill:var(--b-label,#ffd3de)" />
      <rect x="56" y="196" width="88" height="66" rx="3" fill="#16110e" />
      <text x="61" y="226" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="19" letter-spacing="-.6">KOMBU</text>
      <text x="61" y="253" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="19" letter-spacing="-.6">CHA</text>
      <rect x="56" y="268" width="88" height="22" rx="2" fill="#fff" />
      <text x="56" y="304" fill="#16110e" font-family="Arial, sans-serif" font-size="7" font-weight="700">live probiotic</text>
      <circle cx="72" cy="340" r="11" style="fill:var(--b-fl,#f15f86)" stroke="#16110e" stroke-width="1.4" />
      <circle cx="92" cy="335" r="9" style="fill:var(--b-fl,#f15f86)" stroke="#16110e" stroke-width="1.4" />
      <path d="M112 404 C102 380 104 344 142 328 C150 362 138 394 112 404Z" style="fill:var(--b-leaf,#63b648)" />
      <path d="M112 404 C102 380 104 344 142 328 C126 352 118 378 112 404Z" style="fill:var(--b-fl,#f15f86)" />
      <path d="M112 404 C102 380 104 344 142 328 C150 362 138 394 112 404Z M112 404 C120 372 128 352 142 328" fill="none" stroke="#16110e" stroke-width="1.6" />
      <rect x="56" y="364" width="34" height="34" rx="3" fill="#16110e" />
      <text x="73" y="379" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="9.5" text-anchor="middle">VIT-</text>
      <text x="73" y="392" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="9.5" text-anchor="middle">FIT</text>
      <rect x="0" y="0" width="200" height="470" fill="url(#bottle-shade)" />
      <rect x="58" y="230" width="7" height="200" rx="3.5" fill="#fff" opacity=".3" />
      <rect x="88" y="60" width="5" height="100" rx="2.5" fill="#fff" opacity=".3" />
    </g>
    <!-- crown cap -->
    <path d="M80 22H120L122 46H78Z" fill="#c9a24a" />
    <path d="M80 22H120L122 46H78Z" fill="url(#can-metal)" />
    <g stroke="#16110e" stroke-opacity=".25" stroke-width="1.2"><path d="M84 24V45M90 24V45M96 24V45M102 24V45M108 24V45M114 24V45" /></g>
  </symbol>
  <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M4 12h15m-6-6 6 6-6 6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" /></symbol>
  <symbol id="i-chevron" viewBox="0 0 12 8"><path d="m1 1.5 5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></symbol>
  <symbol id="i-close" viewBox="0 0 24 24"><path d="M5 5l14 14M19 5 5 19" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" /></symbol>
  <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 4v16M4 12h16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" /></symbol>
  <symbol id="i-search" viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="7" fill="none" stroke="currentColor" stroke-width="2" /><path d="m16 16 5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" /></symbol>
  <symbol id="i-globe" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9" /><path d="M3 12h18M12 3c2.6 2.6 3.8 5.6 3.8 9S14.6 18.4 12 21c-2.6-2.6-3.8-5.6-3.8-9S9.4 5.6 12 3Z" /></g></symbol>
  <symbol id="i-instagram" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="3" width="18" height="18" rx="5" /><circle cx="12" cy="12" r="4.2" /></g><circle cx="17.4" cy="6.6" r="1.2" fill="currentColor" /></symbol>
  <symbol id="i-tiktok" viewBox="0 0 24 24"><path d="M14.5 3c.3 2.4 1.8 4 4.5 4.3v3c-1.7 0-3.2-.5-4.4-1.4v6.4a5.7 5.7 0 1 1-5.7-5.7c.3 0 .6 0 .9.1v3.1a2.7 2.7 0 1 0 1.8 2.5V3h2.9Z" fill="currentColor" /></symbol>
  <symbol id="i-facebook" viewBox="0 0 24 24"><path d="M13.6 21v-7.6h2.6l.4-3h-3V8.5c0-.9.3-1.5 1.5-1.5h1.6V4.3A21 21 0 0 0 14.4 4c-2.3 0-3.9 1.4-3.9 4v2.3H8v3h2.5V21h3.1Z" fill="currentColor" /></symbol>
  <symbol id="i-link" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10 14a4.5 4.5 0 0 0 6.4 0l3-3a4.5 4.5 0 0 0-6.4-6.4l-1 1" /><path d="M14 10a4.5 4.5 0 0 0-6.4 0l-3 3a4.5 4.5 0 0 0 6.4 6.4l1-1" /></g></symbol>
  <symbol id="i-viber" viewBox="0 0 24 24"><path d="M12 3c-4.6 0-8 1.6-8 7.4 0 3.2 1 5.2 3 6.3V20l3-2.4c.6.1 1.3.1 2 .1 4.6 0 8-1.6 8-7.3S16.6 3 12 3Zm3.6 10.4c-.3.5-1 1-1.6.9-2.4-.4-4.6-2.6-5.2-5-.2-.7.3-1.4.8-1.7.3-.2.7-.1.9.2l.8 1.2c.2.3.1.7-.2.9l-.4.3c.4 1.2 1.4 2.2 2.6 2.6l.3-.4c.2-.3.6-.4.9-.2l1.2.8c.3.1.4.5.2.8Z" fill="currentColor" /></symbol>
  <symbol id="i-pin" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Z" /><circle cx="12" cy="9.5" r="2.6" /></g></symbol>
  <symbol id="i-phone" viewBox="0 0 24 24"><path d="M6.6 3.5 9 3l1.6 4.2-1.9 1.4a11 11 0 0 0 6.7 6.7l1.4-1.9L21 15l-.5 2.4A2.5 2.5 0 0 1 18 19.5C10.5 19.5 4.5 13.5 4.5 6a2.5 2.5 0 0 1 2.1-2.5Z" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round" /></symbol>
  <symbol id="i-mail" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="3" /><path d="m4 7 8 6 8-6" /></g></symbol>
  <symbol id="i-paperclip" viewBox="0 0 24 24"><path d="m20 11.5-8.2 8.2a5 5 0 0 1-7-7L13 4.4a3.4 3.4 0 0 1 4.8 4.8L9.6 17.4a1.7 1.7 0 0 1-2.4-2.4l7.6-7.6" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" /></symbol>
  <symbol id="i-telegram" viewBox="0 0 24 24"><path d="M21 4 3 11.2l5.6 1.9L18 7l-7.3 7.4.3 5.6 3-3.6 4 3L21 4Z" fill="currentColor" /></symbol>
  <symbol id="i-play" viewBox="0 0 24 24"><path d="M8 5.5v13l11-6.5-11-6.5Z" fill="currentColor" /></symbol>
  <symbol id="i-star" viewBox="0 0 24 24"><path d="M12 2c.6 5.4 4.6 9.4 10 10-5.4.6-9.4 4.6-10 10-.6-5.4-4.6-9.4-10-10 5.4-.6 9.4-4.6 10-10Z" fill="currentColor" /></symbol>
  <symbol id="i-leaf" viewBox="0 0 32 32"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 26C4 14 12 5 27 5c0 15-9 23-21 21Z" /><path d="M6 26 18 14" /></g></symbol>
  <symbol id="i-bubbles" viewBox="0 0 32 32"><g fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="20" r="6" /><circle cx="22" cy="11" r="4" /><circle cx="23" cy="23" r="2.5" /><circle cx="12" cy="7" r="2" /></g></symbol>
  <symbol id="i-tea" viewBox="0 0 32 32"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h18v6a8 8 0 0 1-8 8h-2a8 8 0 0 1-8-8v-6Z" /><path d="M23 14h2a3 3 0 0 1 0 6h-2.5M10 4c-1 2 1 3 0 5M15 4c-1 2 1 3 0 5" /></g></symbol>
  <symbol id="i-heart" viewBox="0 0 32 32"><path d="M16 27S4 20 4 11.5A6.5 6.5 0 0 1 16 8a6.5 6.5 0 0 1 12 3.5C28 20 16 27 16 27Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" /></symbol>
  <symbol id="i-pack" viewBox="0 0 32 32"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M10 4h4v4l2 3v17h-8V11l2-3V4ZM20 12h6v16h-6z" /><path d="M20 16h6" /></g></symbol>
  <symbol id="i-trend" viewBox="0 0 32 32"><path d="M4 23 12 15l5 5 11-11m0 0h-7m7 0v7" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" /></symbol>
  <symbol id="i-truck" viewBox="0 0 32 32"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 8h16v14H3zM19 13h6l4 5v4H19z" /><circle cx="9" cy="24" r="2.5" fill="var(--bg,#fff)" /><circle cx="23" cy="24" r="2.5" fill="var(--bg,#fff)" /></g></symbol>
  <symbol id="i-megaphone" viewBox="0 0 32 32"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M5 13v6h4l11 6V7L9 13H5Z" /><path d="M9 19l2 7h3l-1.5-6M25 12c1.5 1 1.5 7 0 8" stroke-linecap="round" /></g></symbol>
  <symbol id="i-drop" viewBox="0 0 32 32"><path d="M16 4c5 7 9 11.5 9 16a9 9 0 0 1-18 0c0-4.5 4-9 9-16Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" /></symbol>
  <symbol id="i-clock" viewBox="0 0 32 32"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="16" cy="16" r="11" /><path d="M16 9v7l5 3" /></g></symbol>
  <symbol id="i-snow" viewBox="0 0 32 32"><path d="M16 4v24M5.6 10l20.8 12M5.6 22l20.8-12M12 6l4 3 4-3M12 26l4-3 4 3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></symbol>
  <symbol id="i-rate" viewBox="0 0 24 24"><path d="M12 1.6l3.1 6.4 7 .9-5.1 4.9 1.3 6.9L12 17.4l-6.3 3.3 1.3-6.9-5.1-4.9 7-.9z" fill="currentColor" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" /></symbol>
  <symbol id="i-sparkle" viewBox="0 0 24 24"><path d="M10 3c.5 4 2.5 6 6.5 6.5-4 .5-6 2.5-6.5 6.5-.5-4-2.5-6-6.5-6.5 4-.5 6-2.5 6.5-6.5ZM18.5 14c.25 2 1.25 3 3.25 3.25-2 .25-3 1.25-3.25 3.25-.25-2-1.25-3-3.25-3.25 2-.25 3-1.25 3.25-3.25Z" fill="currentColor" /></symbol>
  <symbol id="i-filter" viewBox="0 0 24 24"><path d="M4 6h16M7 12h10M10 18h4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" /></symbol>
  <symbol id="i-reset" viewBox="0 0 24 24"><path d="M4.5 12a7.5 7.5 0 1 0 2.2-5.3M4.5 4.5v4h4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" /></symbol>
  <symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" /></symbol>
</svg>
