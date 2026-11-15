/* global gsap, ScrollTrigger */
// Fermentation process (.js-ferment): the bottle rides the wave with the scroll; a step lights up
// (.is-on) when the bottle reaches it. ≥1200 along the SVG path, below down the vertical line.
// Reduced motion: no ride, all steps on.
(function () {
  "use strict";

  var root = document.querySelector(".js-ferment");
  if (!root) return;
  var steps = root.querySelectorAll(".js-ferment-step");
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  if (reduce || typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") {
    steps.forEach(function (s) { s.classList.add("is-on"); });
    return;
  }
  gsap.registerPlugin(ScrollTrigger);

  var scene = root.querySelector(".js-ferment-scene");
  var band = root.querySelector(".js-ferment-band");
  var path = root.querySelector(".js-ferment-path");
  var bottle = root.querySelector(".js-ferment-bottle");
  var wide = window.matchMedia("(min-width: 1200px)");
  var VIEW_W = 1000;
  var VIEW_H = 100;

  // the piece of the path over the container: x 0 → 1000 (the tails only run off the screen)
  var total = path.getTotalLength();
  var from = 0;
  var to = total;
  for (var l = 0; l <= total; l += total / 400) {
    var x = path.getPointAtLength(l).x;
    if (x <= 0) from = l;
    if (x <= VIEW_W) to = l;
  }

  function place(p) {
    var x, y, angle = 0, reached;
    if (wide.matches) {
      var w = band.offsetWidth;
      var h = band.offsetHeight;
      var len = from + (to - from) * p;
      var a = path.getPointAtLength(len);
      var b = path.getPointAtLength(Math.min(total, len + 2));
      x = (a.x / VIEW_W) * w;
      y = band.offsetTop + (a.y / VIEW_H) * h;
      // leans with the wave, gently
      angle = (Math.atan2(((b.y - a.y) / VIEW_H) * h, ((b.x - a.x) / VIEW_W) * w) * 180) / Math.PI / 3;
      reached = function (s) { return x >= s.offsetLeft + s.offsetWidth / 2 - 2; };
    } else {
      x = 0;
      y = p * scene.offsetHeight;
      reached = function (s) { return y >= s.offsetTop; };
    }
    bottle.style.transform = "translate(" + x.toFixed(1) + "px," + y.toFixed(1) + "px) translate(-50%,-50%) rotate(" + angle.toFixed(1) + "deg)";
    steps.forEach(function (s) { s.classList.toggle("is-on", reached(s)); });
  }

  var st = ScrollTrigger.create({
    trigger: scene,
    start: "top 65%",
    end: "bottom 70%",
    scrub: 0.6,
    onUpdate: function (self) { place(self.progress); },
    onRefresh: function (self) { place(self.progress); },
  });
  place(st.progress);
})();
