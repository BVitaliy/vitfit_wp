/* global $, Swiper, gsap */
// Home hero slider: fade between two flavour banners, autoplay with progress
// bars, the section takes the colours of the active slide, the bottle and
// bubbles follow the cursor; on scroll the content lifts and the bottle drifts.
$(function () {
  var hero = document.querySelector(".js-hero");
  if (!hero || typeof Swiper === "undefined") return;

  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var DELAY = 7000;
  var dots = hero.querySelectorAll(".hero__dot");
  var bars = hero.querySelectorAll(".hero__dot i");
  var flClass = "";

  function setFlavor(slide) {
    var fl = slide.getAttribute("data-flavor");
    if (flClass) hero.classList.remove(flClass);
    flClass = "fl--" + fl;
    hero.classList.add(flClass);
  }

  // background videos of slides (optional, CMS)
  function playVideo(slide) {
    hero.querySelectorAll(".js-hero-video").forEach(function (v) {
      if (!slide.contains(v)) return v.pause();
      if (!v.src) v.src = window.innerWidth < 768 && v.dataset.srcMob ? v.dataset.srcMob : v.dataset.src;
      var p = v.play();
      if (p && p.catch) p.catch(function () {});
    });
  }

  var swiper = new Swiper(hero.querySelector(".js-hero-slider"), {
    effect: "fade",
    fadeEffect: { crossFade: true },
    speed: 900,
    // rewind, not loop: with 2 slides loop re-orders them in the DOM on every
    // change — the slide jumps for a frame and its entrance replays (blink)
    rewind: true,
    allowTouchMove: true,
    autoplay: reduce ? false : { delay: DELAY, disableOnInteraction: false, pauseOnMouseEnter: false },
    on: {
      init: function () {
        setFlavor(this.slides[this.activeIndex]);
      },
      slideChange: function () {
        var s = this;
        var slide = this.slides[this.activeIndex];
        setFlavor(slide);
        playVideo(slide);
        dots.forEach(function (d, i) {
          d.classList.toggle("is-active", i === s.realIndex);
        });
        bars.forEach(function (b) {
          b.style.setProperty("--p", 0);
        });
      },
      autoplayTimeLeft: function (s, time, progress) {
        var bar = bars[s.realIndex];
        if (bar) bar.style.setProperty("--p", 1 - progress);
      },
    },
  });

  dots.forEach(function (d, i) {
    d.addEventListener("click", function () {
      swiper.slideTo(i);
    });
  });

  if ("IntersectionObserver" in window) {
    new IntersectionObserver(function (entries) {
      hero.classList.toggle("is-offscreen", !entries[0].isIntersecting);
    }).observe(hero);
  }

  if (reduce || typeof gsap === "undefined") return;

  // cursor parallax (desktop): GSAP eases only --mx / --my on the section;
  // each layer turns them into its own `transform` by --depth (_hero.scss).
  // Never GSAP x/y on the layers — GSAP would also write translate/rotate/scale
  // inline and kill their CSS entrance (the flicker).
  if (window.matchMedia("(min-width: 1200px)").matches) {
    var toX = gsap.quickTo(hero, "--mx", { duration: 1.2, ease: "power3" });
    var toY = gsap.quickTo(hero, "--my", { duration: 1.2, ease: "power3" });
    hero.addEventListener("pointermove", function (e) {
      toX(e.clientX / window.innerWidth - 0.5);
      toY(e.clientY / window.innerHeight - 0.5);
    });
  }

  // scroll: content lifts, the visual sinks slower
  gsap.to(hero.querySelectorAll(".hero__content"), {
    yPercent: -18,
    ease: "none",
    scrollTrigger: { trigger: hero, start: "top top", end: "bottom top", scrub: true },
  });
  gsap.to(hero.querySelectorAll(".hero__visual"), {
    yPercent: 12,
    ease: "none",
    scrollTrigger: { trigger: hero, start: "top top", end: "bottom top", scrub: true },
  });
});
