/* global Lenis, gsap, ScrollTrigger */
// Smooth scroll (Lenis) driven by the GSAP ticker so ScrollTrigger pins /
// scrubs stay in sync with the smoothed position. Exposed as window.lenis
// for modals (stop/start) and anchor links.
(function () {
  "use strict";

  var hasGsap = typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined";
  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (hasGsap) {
    gsap.registerPlugin(ScrollTrigger);
    // mobile address bar resizes the viewport — not a reason to recalc pins
    ScrollTrigger.config({ ignoreMobileResize: true });
  }

  // ── Lenis ──────────────────────────────────────────────────────
  // On every device, phones included. QA on a touch device: ?nativeScroll=1 switches to
  // the browser's own scroll, ?nativeScroll=0 back to Lenis (remembered in localStorage).
  var lenis = null;
  var isTouchScroll = window.matchMedia("(hover: none), (pointer: coarse), (max-width: 767.98px)").matches;

  function preferNativeScroll() {
    if (!isTouchScroll) return false;
    try {
      var params = new URLSearchParams(window.location.search);
      if (params.has("nativeScroll")) {
        var raw = params.get("nativeScroll");
        var on = raw === "1" || raw === "true" || raw === "";
        localStorage.setItem("vitfit-native-scroll", on ? "1" : "0");
        return on;
      }
      var saved = localStorage.getItem("vitfit-native-scroll");
      if (saved !== null) return saved === "1";
    } catch (err) {}
    return false;
  }

  if (typeof Lenis !== "undefined" && !reduceMotion && !preferNativeScroll()) {
    // Touch tuning. Phones = the ykagro values (lerp 0.12, syncTouchLerp 0.18); tablets and
    // touch desktops keep theirs (lerp 0.14, syncTouchLerp 0.26).
    var isPhone = window.matchMedia("(max-width: 767.98px)").matches;
    lenis = new Lenis({
      lerp: isPhone ? 0.12 : isTouchScroll ? 0.14 : 0.1,
      smoothWheel: true,
      touchMultiplier: isTouchScroll ? 1.6 : 1,
      syncTouch: true,
      syncTouchLerp: isPhone ? 0.18 : isTouchScroll ? 0.26 : 0.075,
      touchInertiaExponent: isTouchScroll ? 1.45 : 1.7,
      // long text in a textarea scrolls itself, not the page
      prevent: function (node) {
        return node.nodeName === "TEXTAREA";
      },
    });
    window.lenis = lenis;

    // Sliders: Lenis must not read a touch there. It scrolled the page by the finger's small
    // vertical drift during a sideways swipe (the page moved up / down — manager); now the
    // browser locks the direction (Swiper's touch-action: pan-y): sideways = the slider,
    // up / down = the page. Touch only — the wheel over a slider stays smooth.
    if (isTouchScroll) {
      document.querySelectorAll(".swiper").forEach(function (el) {
        el.setAttribute("data-lenis-prevent-touch", "");
      });
    }

    if (hasGsap) {
      lenis.on("scroll", ScrollTrigger.update);
      gsap.ticker.add(function (time) {
        lenis.raf(time * 1000);
      });
      gsap.ticker.lagSmoothing(0);
    } else {
      var raf = function (t) {
        lenis.raf(t);
        requestAnimationFrame(raf);
      };
      requestAnimationFrame(raf);
    }
  }

  // ── Anchor links ───────────────────────────────────────────────
  document.addEventListener("click", function (e) {
    var link = e.target.closest('a[href^="#"]');
    if (!link) return;
    var id = link.getAttribute("href");
    if (id.length < 2) return;
    var target = document.querySelector(id);
    if (!target) return;
    e.preventDefault();
    var offset = -parseFloat(getComputedStyle(document.documentElement).getPropertyValue("--header-h")) || 0;
    if (lenis) lenis.scrollTo(target, { offset: offset, duration: 1.2 });
    else target.scrollIntoView({ behavior: "smooth" });
  });


  // ── ScrollTrigger refresh ──────────────────────────────────────
  // Trigger bounds are measured before fonts / media arrive and those change
  // the page height — re-measure on every real height change, not only load.
  if (hasGsap) {
    var refresh = function () {
      ScrollTrigger.refresh();
    };
    window.addEventListener("load", refresh);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(refresh);

    if ("ResizeObserver" in window) {
      var lastHeight = document.body.offsetHeight;
      var timer = null;
      new ResizeObserver(function () {
        var h = document.body.offsetHeight;
        if (h === lastHeight) return;
        lastHeight = h;
        clearTimeout(timer);
        timer = setTimeout(refresh, 150);
      }).observe(document.body);
    }
  }

  // While the page scrolls, nothing under a resting cursor switches its hover on (cards
  // opening their video / photo as they pass by made the scroll look jerky — manager).
  var scrollTimer = 0;
  var root = document.documentElement;
  window.addEventListener("scroll", function () {
    if (!root.classList.contains("is-scrolling")) root.classList.add("is-scrolling");
    clearTimeout(scrollTimer);
    scrollTimer = setTimeout(function () { root.classList.remove("is-scrolling"); }, 160);
  }, { passive: true });
})();
