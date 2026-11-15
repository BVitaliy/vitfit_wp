/* global $, Swiper */
// .js-grid-slider — a grid on desktop that becomes a Swiper below
// data-slider-below (px, default 1200). Slide widths come from CSS (slidesPerView auto).
$(function () {
  if (typeof Swiper === "undefined") return;

  $(".js-grid-slider").each(function () {
    var el = this;
    var below = parseInt(el.getAttribute("data-slider-below"), 10) || 1200;
    var mq = window.matchMedia("(max-width: " + (below - 0.02) + "px)");
    var swiper = null;

    function toggle() {
      if (mq.matches && !swiper) {
        swiper = new Swiper(el, {
          slidesPerView: "auto",
          spaceBetween: 10,
          speed: 600,
          grabCursor: true,
          watchOverflow: true,
          pagination: { el: el.querySelector(".grid-slider__pagination"), clickable: true },
        });
      } else if (!mq.matches && swiper) {
        swiper.destroy(true, true);
        swiper = null;
      }
    }

    mq.addEventListener("change", toggle);
    toggle();
  });
});

// .js-slider — a Swiper on every width (product reviews, "Спробуйте також"). Slide widths
// from CSS; arrows .js-slider-prev / -next in the block's section head (≥1200), dots below.
// The gap is 1rem from 1200 (scales with the root, as rem(16) in the CSS), 10px below.
$(function () {
  if (typeof Swiper === "undefined") return;

  function gap() {
    return window.innerWidth >= 1200 ? parseFloat(getComputedStyle(document.documentElement).fontSize) : 10;
  }

  $(".js-slider").each(function () {
    var el = this;
    var block = el.closest("section") || el.parentNode;
    var swiper = new Swiper(el, {
      slidesPerView: "auto",
      spaceBetween: gap(),
      speed: 600,
      grabCursor: true,
      watchOverflow: true,
      navigation: { prevEl: block.querySelector(".js-slider-prev"), nextEl: block.querySelector(".js-slider-next") },
      pagination: { el: el.querySelector(".grid-slider__pagination"), clickable: true },
    });

    window.addEventListener("resize", function () {
      var g = gap();
      if (swiper.params.spaceBetween !== g) {
        swiper.params.spaceBetween = g;
        swiper.update();
      }
    });
  });
});
