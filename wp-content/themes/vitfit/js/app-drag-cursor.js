// Drag cursor over sliders (manager): ≥1200, a round "‹ ›" badge follows the pointer over any
// Swiper that can be dragged (hero, product gallery, reviews, related, article photos…),
// shrinks while pressed. Over links / buttons / product cards inside a slider it steps
// aside — those keep their own cursor and hover. One element for the whole page.
(function () {
  "use strict";

  var wide = window.matchMedia("(min-width: 1200px)");
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  var dot = document.createElement("div");
  dot.className = "drag-cursor";
  dot.setAttribute("aria-hidden", "true");
  dot.innerHTML = '<svg viewBox="0 0 24 24"><path d="M9 6 3 12l6 6M15 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>';
  document.body.appendChild(dot);

  var x = 0, y = 0, cx = 0, cy = 0, raf = 0, on = false;
  var SKIP = "a, button, input, select, textarea, label, .js-product, .swiper-pagination, .pdp__play";

  function draggable(el) {
    var sw = el.closest(".swiper");
    if (!sw || !sw.swiper || sw.swiper.destroyed) return null;
    var s = sw.swiper;
    if (!s.params.allowTouchMove || s.isLocked) return null;
    return sw;
  }

  function tick() {
    cx += (x - cx) * 0.25;
    cy += (y - cy) * 0.25;
    dot.style.transform = "translate(" + cx.toFixed(1) + "px," + cy.toFixed(1) + "px)";
    raf = Math.abs(x - cx) + Math.abs(y - cy) > 0.3 ? requestAnimationFrame(tick) : 0;
  }

  document.addEventListener("pointermove", function (e) {
    if (!wide.matches || e.pointerType === "touch") return;
    x = e.clientX;
    y = e.clientY;
    var show = !!draggable(e.target) && !e.target.closest(SKIP);
    if (show !== on) {
      on = show;
      if (show) { cx = x; cy = y; } // appear at the pointer, don't fly in
      dot.classList.toggle("is-on", show);
      document.documentElement.classList.toggle("is-drag-cursor", show);
    }
    if (!raf) raf = requestAnimationFrame(tick);
  }, { passive: true });

  document.addEventListener("pointerdown", function () { dot.classList.add("is-down"); });
  document.addEventListener("pointerup", function () { dot.classList.remove("is-down"); });
  document.addEventListener("pointerleave", function () {
    on = false;
    dot.classList.remove("is-on");
    document.documentElement.classList.remove("is-drag-cursor");
  });
})();
