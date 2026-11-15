/* global gsap, ScrollTrigger, Swiper */
// Nine flavours. ≥1200: the block pins and the track scrolls sideways with
// the page; the card nearest the centre is active and the section takes its
// light colour. Below 1200: a Swiper, colour per active slide.
(function () {
  var section = document.querySelector(".js-flavors");
  if (!section) return;

  var track = section.querySelector(".js-flavors-track");
  var cards = Array.prototype.slice.call(section.querySelectorAll(".flavor"));
  var counter = section.querySelector(".js-flavors-current");
  var bar = section.querySelector(".js-flavors-bar");
  var current = -1;

  function activate(i) {
    if (i === current) return;
    current = i;
    cards.forEach(function (c, n) {
      c.classList.toggle("is-active", n === i);
    });
    var col = getComputedStyle(cards[i]).getPropertyValue("--fl-l").trim();
    section.style.setProperty("--flv-bg", col);
    counter.textContent = ("0" + (i + 1)).slice(-2);
  }
  activate(0);

  function nearestToCentre() {
    var mid = window.innerWidth / 2;
    var best = 0;
    var dist = Infinity;
    cards.forEach(function (c, n) {
      var r = c.getBoundingClientRect();
      var d = Math.abs(r.left + r.width / 2 - mid);
      if (d < dist) {
        dist = d;
        best = n;
      }
    });
    return best;
  }

  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var swiper = null;
  var mqMobile = window.matchMedia("(max-width: 1199.98px)");

  function mobile() {
    if (mqMobile.matches && !swiper && typeof Swiper !== "undefined") {
      swiper = new Swiper(section.querySelector(".js-flavors-slider"), {
        slidesPerView: "auto",
        spaceBetween: 12,
        speed: 700,
        grabCursor: true,
        on: {
          slideChange: function () {
            activate(this.activeIndex);
            bar.style.setProperty("--p", (this.activeIndex + 1) / cards.length);
          },
        },
      });
      bar.style.setProperty("--p", 1 / cards.length);
    } else if (!mqMobile.matches && swiper) {
      swiper.destroy(true, true);
      swiper = null;
    }
  }
  mqMobile.addEventListener("change", mobile);
  mobile();

  if (typeof gsap === "undefined") return;
  gsap.registerPlugin(ScrollTrigger);
  var mm = gsap.matchMedia();

  mm.add("(min-width: 1200px)", function () {
    var pin = section.querySelector(".flavors__pin");

    // the disc is centred on its stage; on low screens it is taller than the
    // stage and would reach into the title gap — push the cards down by that
    // overflow (pushing them shrinks the stage by as much: extra = disc − stage)
    var stage = section.querySelector(".flavor__stage");
    var ring = section.querySelector(".flavor__ring");
    // ≥1200 sizes, measured (app-flavors.js owns them, CSS has the fallbacks):
    // --flv-body   every card's text block = the tallest one (formats / names on one line)
    // --flv-stage  the stage: at most half the screen, less when title + cards + bar
    //              would not fit the pinned screen
    // --flv-ring   the disc: 70% of the card (≤ 12% past a low stage), drawn 20% bigger
    // --flv-bottle the bottle, sized from the disc before the +20%
    // --flv-extra  the disc's overflow above its stage → the cards move down by it, so
    //              the title → disc gap stays --sp-head
    var bodies = section.querySelectorAll(".flavor__body");
    var head = section.querySelector(".flavors__head");
    var viewport = section.querySelector(".flavors__viewport");
    var set = function (k, v) { pin.style.setProperty(k, v + "px"); };
    function fitGap() {
      ["--flv-body", "--flv-stage", "--flv-ring", "--flv-bottle", "--flv-extra"].forEach(function (k) {
        pin.style.removeProperty(k);
      });
      var tallest = 0;
      bodies.forEach(function (b) { tallest = Math.max(tallest, b.offsetHeight); });
      set("--flv-body", tallest);

      var wide = ring.offsetWidth; // 70% of the card
      var cs = getComputedStyle(pin);
      var room = pin.clientHeight - parseFloat(cs.paddingTop) - parseFloat(cs.paddingBottom);
      // the stage is never taller than the disc (the gap is measured to the disc)
      var h = Math.min(window.innerHeight * 0.5, wide * 1.2);
      var base, disc, extra;
      for (var i = 0; i < 8; i++) {
        base = Math.min(wide, h + wide * 0.12);
        disc = base * 1.2;
        h = Math.min(h, disc);
        extra = Math.max(0, (disc - h) / 2);
        set("--flv-stage", h);
        set("--flv-ring", disc);
        set("--flv-extra", extra);
        // layout sizes (offset*), not boxes — the title may still be mid reveal
        var used = head.offsetHeight + parseFloat(getComputedStyle(head).marginBottom) + parseFloat(cs.rowGap) + viewport.offsetHeight;
        var over = used - room;
        if (over < 1) break;
        h = Math.max(60, h - over * 0.6);
      }
      set("--flv-bottle", Math.min(window.innerHeight * 0.6, Math.max(h * 1.12, base * 0.95), base * 1.15));
    }
    fitGap();
    ScrollTrigger.addEventListener("refreshInit", fitGap);
    var slider = section.querySelector(".js-flavors-slider");
    var distance = function () {
      var pad = parseFloat(getComputedStyle(slider).paddingRight) || 0;
      return Math.max(0, track.scrollWidth - slider.clientWidth + pad * 2);
    };

    var tween = gsap.to(track, {
      x: function () {
        return -distance();
      },
      ease: "none",
      scrollTrigger: {
        trigger: section,
        pin: pin,
        start: "top top",
        end: function () {
          return "+=" + distance();
        },
        scrub: reduce ? true : 0.8,
        invalidateOnRefresh: true,
        onUpdate: function (self) {
          bar.style.setProperty("--p", 0.11 + self.progress * 0.89);
          activate(nearestToCentre());
        },
      },
    });

    return function () {
      ScrollTrigger.removeEventListener("refreshInit", fitGap);
      ["--flv-body", "--flv-stage", "--flv-ring", "--flv-bottle", "--flv-extra"].forEach(function (k) {
        pin.style.removeProperty(k);
      });
      tween.scrollTrigger && tween.scrollTrigger.kill();
      tween.kill();
      gsap.set(track, { clearProps: "transform" });
    };
  });
})();
