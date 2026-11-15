/* global gsap, ScrollTrigger */
// Scroll reveals, drift, count-up, footer wordmark. Reduced motion: nothing hidden.
(function () {
  if (typeof gsap === "undefined") return;
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
  gsap.registerPlugin(ScrollTrigger);

  // One-shot reveal: hidden state now, the tween starts from onEnter.
  // (An animation attached to a `once` ScrollTrigger is killed together with
  // it when the end is passed at once — the sticky footer froze mid-way.)
  function reveal(targets, from, to, start, trigger) {
    gsap.set(targets, from);
    ScrollTrigger.create({
      trigger: trigger || targets,
      start: start || "top 92%",
      once: true,
      onEnter: function () {
        gsap.to(targets, to);
      },
    });
  }

  // ── cards: one after another (up + fade) ──
  var CARDS = ".benefit, .product, .post, .shop, .faq__item, .about__stats li, .b2b__perks li, .flavors__head";
  var cards = gsap.utils.toArray(CARDS).filter(function (el) {
    // catalog cards fade in by themselves (app-catalog.js): hidden ones have no box,
    // a scroll trigger would never show them after a filter change
    return !el.closest(".hero") && !el.closest(".js-cat-grid");
  });
  // reveals animate the `translate` property: `transform` belongs to the
  // cards' own hover transitions and the scroll drift (data-speed) — sharing it
  // made them flicker and clearProps reset the drift
  gsap.set(cards, { opacity: 0, translate: "0px 60px" });
  ScrollTrigger.batch(cards, {
    start: "top 90%",
    once: true,
    onEnter: function (batch) {
      gsap.to(batch, {
        opacity: 1,
        translate: "0px 0px",
        duration: 1,
        stagger: 0.08,
        ease: "expo.out",
        clearProps: "translate,opacity",
      });
    },
  });

  // ── Instagram posts: a plain fade, one after another (manager: the rise looked odd) ──
  var posts = gsap.utils.toArray(".insta__item");
  gsap.set(posts, { opacity: 0 });
  ScrollTrigger.batch(posts, {
    start: "top 92%",
    once: true,
    onEnter: function (batch) {
      gsap.to(batch, { opacity: 1, duration: 0.8, stagger: 0.05, ease: "power1.out", clearProps: "opacity" });
    },
  });

  // ── small parts: rise ──
  var RISE = ".about__row, main .chip:not(.hero__chip):not(.post__cat), .section-head__text, .section-head .link-arrow, .section-head > .btn, .story__lead, .about__body > *:not(.about__stats):not(.chip), .b2b__body > *:not(.b2b__perks):not(.b2b__title):not(.chip), .faq-block__aside > *:not(.h2):not(.chip), .seo-block__box";
  gsap.utils.toArray(RISE).forEach(function (el) {
    reveal(el, { opacity: 0, translate: "0px 30px" }, {
      opacity: 1,
      translate: "0px 0px",
      duration: 1,
      ease: "expo.out",
      clearProps: "translate,opacity",
    });
  });

  // ── big rounded blocks scale in ──
  gsap.utils.toArray(".b2b__box, .about__media").forEach(function (el) {
    reveal(el, { scale: 0.92 }, { scale: 1, duration: 1.4, ease: "expo.out", clearProps: "transform" }, "top 85%");
  });

  // ── drift ──
  gsap.utils.toArray("[data-parallax]").forEach(function (box) {
    var img = box.querySelector("img");
    if (!img) return;
    gsap.fromTo(img, { yPercent: -6 }, {
      yPercent: 6,
      ease: "none",
      scrollTrigger: { trigger: box, start: "top bottom", end: "bottom top", scrub: true },
    });
  });
  if (window.matchMedia("(min-width: 992px)").matches) {
    gsap.utils.toArray("[data-speed]").forEach(function (el) {
      var s = parseFloat(el.getAttribute("data-speed")) || 0;
      gsap.fromTo(el, { yPercent: s * -300 }, {
        yPercent: s * 300,
        ease: "none",
        scrollTrigger: { trigger: el, start: "top bottom", end: "bottom top", scrub: true },
      });
    });
  }

  // ── count-up ──
  gsap.utils.toArray(".js-stats [data-count]").forEach(function (el) {
    var to = parseFloat(el.getAttribute("data-count"));
    var o = { v: 0 };
    el.textContent = "0";
    ScrollTrigger.create({
      trigger: el,
      start: "top 90%",
      once: true,
      onEnter: function () {
        gsap.to(o, {
          v: to,
          duration: 1.6,
          ease: "power3.out",
          onUpdate: function () {
            el.textContent = Math.round(o.v);
          },
        });
      },
    });
  });

  // ── footer wordmark letters jump in ──
  // ≥1200 the footer is sticky under <main> and is uncovered as the page
  // slides up, so its own position says nothing: start when the bottom of
  // <main> has uncovered a quarter of the word (it shows bottom-up). Below 1200
  // the footer scrolls normally.
  var footerEl = document.querySelector(".footer");
  function footerFits() {
    if (!footerEl) return;
    document.documentElement.classList.toggle("footer-fits", footerEl.offsetHeight <= window.innerHeight);
  }
  footerFits();
  window.addEventListener("resize", footerFits);
  // measured again once fonts / images have their final size
  window.addEventListener("load", footerFits);
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(footerFits);

  var word = document.querySelector(".js-footer-word");
  if (word) {
    var letters = word.children;
    var footer = word.closest(".footer");
    var main = document.querySelector("main");
    var play = function () {
      gsap.to(letters, {
        yPercent: 0,
        rotate: 0,
        duration: 1.1,
        stagger: 0.06,
        ease: "back.out(1.6)",
        // all: GSAP also writes translate / rotate: none, which would block the CSS hover
        clearProps: "all",
      });
    };
    gsap.set(letters, { yPercent: 100, rotate: 8 });
    var sticky = window.matchMedia("(min-width: 1200px)").matches && getComputedStyle(footer).position === "sticky";
    ScrollTrigger.create(
      sticky
        ? {
            trigger: main,
            // viewport y where the word starts once the footer is fully shown
            start: function () {
              // (a footer taller than the screen sits with its bottom on the screen bottom)
              var wordY = window.innerHeight - footer.offsetHeight + word.offsetTop;
              return "bottom " + Math.round(wordY + word.offsetHeight * 0.75) + "px"; // uncovered bottom-up: 25% of the word shown
            },
            once: true,
            invalidateOnRefresh: true,
            onEnter: play,
          }
        : { trigger: word, start: "top 85%", once: true, onEnter: play }
    );
  }
})();
