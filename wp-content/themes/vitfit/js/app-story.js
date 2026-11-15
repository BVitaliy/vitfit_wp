/* global gsap, ScrollTrigger */
// "Від чаю до пляшки" — one timeline scrubbed by the steps column:
// 0 tea leaves fall, tea fills · 1 SCOBY settles · 2 fermentation (bubbles,
// tea turns into the flavour, days 1→14) · 3 bottling (cap, label, tilt).
// The section background moves through the step colours.
(function () {
  var story = document.querySelector(".js-story");
  if (!story) return;

  var steps = story.querySelectorAll(".story__step");

  // progress items scroll to their step (the timeline follows the scroll)
  story.querySelectorAll(".js-st-go").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var step = steps[+btn.getAttribute("data-step")];
      if (!step) return;
      var r = step.getBoundingClientRect();
      // step centre to the middle of the screen
      var y = window.scrollY + r.top + r.height / 2 - window.innerHeight / 2 + 2;
      if (window.lenis) window.lenis.scrollTo(y, { duration: 1.4 });
      else window.scrollTo({ top: y, behavior: "smooth" });
    });
  });
  var dots = story.querySelectorAll(".story__progress li");
  var q = function (s) {
    return story.querySelector(s);
  };
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function setActive(i) {
    steps.forEach(function (el, n) {
      el.classList.toggle("is-active", n === i);
    });
    dots.forEach(function (el, n) {
      el.classList.toggle("is-active", n === i);
      el.classList.toggle("is-past", n < i);
    });
  }

  if (typeof gsap === "undefined" || reduce) {
    // static final state
    steps.forEach(function (el) {
      el.classList.add("is-active");
    });
    [".js-st-label", ".js-st-cap", ".js-st-bubbles", ".js-st-drops"].forEach(function (s) {
      q(s).setAttribute("opacity", "1");
    });
    q(".js-st-liquid").setAttribute("transform", "translate(0 112)");
    q(".js-st-liq1").setAttribute("stop-color", "#c4203f");
    q(".js-st-liq2").setAttribute("stop-color", "#4a0613");
    q(".js-st-leaves").setAttribute("opacity", "0");
    return;
  }

  gsap.registerPlugin(ScrollTrigger);

  var liquid = q(".js-st-liquid");
  var liq1 = q(".js-st-liq1");
  var liq2 = q(".js-st-liq2");
  var leaves = story.querySelectorAll(".js-st-leaves path");
  var scoby = q(".js-st-scoby");
  var bubbles = q(".js-st-bubbles");
  var label = q(".js-st-label");
  var cap = q(".js-st-cap");
  var bottle = q(".js-story-bottle");
  var fact = q(".js-st-days");
  var daysNum = q(".js-st-days-num");
  var days = { v: 1 };
  var disc = q(".js-st-disc");
  var sparks = story.querySelectorAll(".js-st-sparks svg");
  var drops = q(".js-st-drops");

  gsap.set(liquid, { y: 470 });
  gsap.set(label, { opacity: 0, scaleX: 0.15, transformOrigin: "50% 50%" });
  gsap.set(cap, { opacity: 0, y: -150 });
  gsap.set(bottle, { transformOrigin: "50% 80%" });

  var tl = gsap.timeline({ defaults: { ease: "none" } });

  // disc behind the bottle: tea amber → SCOBY peach → live lime → cherry
  tl.fromTo(disc, { "--disc": "#e7b357", scale: 0.8 }, { "--disc": "#e7b357", scale: 0.9, duration: 1 }, 0)
    .to(disc, { "--disc": "#f3c48f", scale: 0.95, duration: 1 }, 1)
    .to(disc, { "--disc": "#b9e05a", scale: 1, duration: 1 }, 2)
    .to(disc, { "--disc": "#f15f86", scale: 1.06, duration: 1 }, 3)
    .to(story, { "--fizz": 1, duration: 0.3 }, 2.1)
    .to(story, { "--fizz": 0.5, duration: 0.4 }, 3.2)
    .fromTo(sparks, { scale: 0, rotation: -90 }, { scale: 1, rotation: 0, duration: 0.35, stagger: 0.06, ease: "back.out(3)" }, 3.6);

  // 0 — tea
  tl.to(story, { "--story-bg": "#f6e3bf", duration: 1 }, 0)
    .to(leaves, { y: "+=230", rotation: "+=160", opacity: 0, stagger: 0.1, duration: 0.7, ease: "power1.in" }, 0)
    .to(liquid, { y: 300, duration: 1, ease: "power1.inOut" }, 0)
    // 1 — SCOBY
    .to(story, { "--story-bg": "#f2e8d6", duration: 1 }, 1)
    .fromTo(scoby, { y: -300, opacity: 0 }, { y: 0, opacity: 1, duration: 0.7, ease: "power2.out" }, 1)
    .to(liquid, { y: 270, duration: 1 }, 1)
    // 2 — fermentation
    .to(story, { "--story-bg": "#e9f7b0", duration: 1 }, 2)
    .to(liquid, { y: 112, duration: 1, ease: "power1.inOut" }, 2)
    .to(liq1, { attr: { "stop-color": "#c4203f" }, duration: 1 }, 2)
    .to(liq2, { attr: { "stop-color": "#4a0613" }, duration: 1 }, 2)
    .to(bubbles, { opacity: 1, duration: 0.3 }, 2)
    .to(fact, { opacity: 1, y: 0, scale: 1, duration: 0.25 }, 2)
    .to(days, {
      v: 14,
      duration: 0.9,
      onUpdate: function () {
        daysNum.textContent = Math.round(days.v);
      },
    }, 2)
    .to(bottle, { rotation: 2, duration: 0.5, yoyo: true, repeat: 1, ease: "sine.inOut" }, 2)
    // 3 — bottling
    .to(story, { "--story-bg": "#ffc9d6", duration: 1 }, 3)
    .to(fact, { opacity: 0, y: -10, duration: 0.2 }, 3)
    .to(scoby, { y: -320, opacity: 0, duration: 0.5, ease: "power2.in" }, 3)
    .to(bubbles, { opacity: 0.45, duration: 0.4 }, 3)
    .to(cap, { opacity: 1, duration: 0.1 }, 3.3)
    .to(cap, { y: 0, duration: 0.45, ease: "back.out(1.4)" }, 3.3)
    // the label wraps on once the cap has landed (3.75) — quickly opaque, then unrolls
    .to(label, { opacity: 1, duration: 0.06 }, 3.72)
    .to(label, { scaleX: 1, duration: 0.4, ease: "power3.out" }, 3.72)
    .to(drops, { opacity: 1, duration: 0.3 }, 3.95)
    .to(bottle, { rotation: -6, scale: 1.04, duration: 0.5, ease: "power2.out" }, 3.72)
    .to({}, { duration: 0.2 });

  var last = -1;
  ScrollTrigger.create({
    trigger: story.querySelector(".story__steps"),
    start: "top 55%",
    end: "bottom 70%",
    scrub: 0.7,
    animation: tl,
    onUpdate: function (self) {
      var i = Math.min(steps.length - 1, Math.floor(self.progress * steps.length * 0.999));
      if (i !== last) {
        last = i;
        setActive(i);
      }
    },
  });
})();
