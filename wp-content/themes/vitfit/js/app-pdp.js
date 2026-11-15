/* global Swiper */
// Product gallery (.js-pdp): main Swiper + thumbs + Fancybox (a click on a slide). The video
// slide never autoplays (client): the play button starts it with native controls; leaving
// the slide pauses it.
(function () {
  "use strict";

  var root = document.querySelector(".js-pdp");
  if (!root || typeof Swiper === "undefined") return;

  var thumbs = new Swiper(root.querySelector(".js-pdp-thumbs"), {
    slidesPerView: "auto",
    spaceBetween: 10,
    watchSlidesProgress: true,
    slideToClickedSlide: true,
  });

  var main = new Swiper(root.querySelector(".js-pdp-main"), {
    speed: 600,
    spaceBetween: 0,
    grabCursor: true,
    navigation: { prevEl: root.querySelector(".js-pdp-prev"), nextEl: root.querySelector(".js-pdp-next") },
    thumbs: { swiper: thumbs },
    keyboard: { enabled: true, onlyInViewport: true },
  });

  var slide = root.querySelector(".js-pdp-video-slide");
  var video = slide && slide.querySelector(".js-pdp-video");

  // ── Fancybox: a click (not a drag) on a slide opens the gallery full size, in sync ──
  if (window.Fancybox) {
    var slides = root.querySelectorAll(".js-pdp-main .pdp__slide");
    var items = [].map.call(slides, function (el) {
      var src = el.getAttribute("data-zoom");
      if (src) return { src: src, type: el.getAttribute("data-zoom-type") || "image" };
      return { src: '<div class="pdp-zoom">' + el.innerHTML + "</div>", type: "html" };
    });

    main.on("click", function (sw, e) {
      if (e.target.closest(".js-pdp-play")) return;
      if (slide && slide.classList.contains("is-playing") && e.target.closest("video")) return; // native controls
      if (video && !video.paused) video.pause();
      window.Fancybox.show(items, {
        startIndex: sw.activeIndex,
        Thumbs: false,
        on: {
          "Carousel.change": function (fb, carousel, to) {
            main.slideTo(to, 0);
          },
          init: function () {
            main.keyboard.disable(); // the arrows belong to Fancybox while it is open
            if (window.VitScrollLock) window.VitScrollLock.lock("fancybox");
          },
          destroy: function () {
            main.keyboard.enable();
            if (window.VitScrollLock) window.VitScrollLock.unlock("fancybox");
          },
        },
      });
    });
  }

  if (!video) return;

  root.querySelector(".js-pdp-play").addEventListener("click", function () {
    video.controls = true;
    slide.classList.add("is-playing");
    video.play();
  });

  // swiping over the native controls must not fight the slider
  video.addEventListener("pointerdown", function (e) {
    if (video.controls) e.stopPropagation();
  });

  main.on("slideChange", function () {
    if (!video.paused) video.pause();
  });

  video.addEventListener("ended", function () {
    slide.classList.remove("is-playing");
    video.controls = false;
  });
})();
