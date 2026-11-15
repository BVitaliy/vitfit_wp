/* global gsap, ScrollTrigger, SplitText */
// Section titles (.h2 in <main>, not the hero) rise line by line on scroll-in.
// Lines are masked (.split-line); split after the fonts load so lines match.
(function () {
  if (typeof gsap === "undefined" || typeof SplitText === "undefined") return;
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
  gsap.registerPlugin(ScrollTrigger, SplitText);

  var titles = Array.prototype.filter.call(document.querySelectorAll("main .h2"), function (el) {
    return !el.closest(".hero") && !el.closest('[data-title-anim="off"]');
  });
  titles.forEach(function (t) {
    t.style.visibility = "hidden";
  });

  var ready = document.fonts && document.fonts.ready ? document.fonts.ready : Promise.resolve();
  ready.then(function () {
    titles.forEach(function (t) {
      var split = new SplitText(t, { type: "lines", linesClass: "split-line-inner", mask: "lines", aria: "auto" });
      t.style.visibility = "";
      gsap.set(split.lines, { yPercent: 110, rotate: 3, transformOrigin: "0 100%" });
      ScrollTrigger.create({
        trigger: t,
        start: "top 88%",
        once: true,
        onEnter: function () {
          gsap.to(split.lines, { yPercent: 0, rotate: 0, duration: 1.1, stagger: 0.1, ease: "expo.out" });
        },
      });
    });
    ScrollTrigger.refresh();
  });
})();
