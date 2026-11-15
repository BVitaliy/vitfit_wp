// Product cards (≥1200, any pointer — touch desktops too): on hover the moment video / photo replaces the
// packshot, opening from the side the cursor came in (--cx/--cy). The video is fetched when
// the card nears the screen (no delay on the first hover) and plays only while hovered.
// The packshot is gone then — a small bottle shows only while the cursor moves and trails
// it (--bx/--by, .is-moving), and fades out once the cursor rests (manager).
(function () {
  var fine = window.matchMedia("(min-width: 1200px)");
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  document.querySelectorAll(".js-product").forEach(function (card) {
    var media = card.querySelector(".product__media");
    var video = card.querySelector(".js-product-video");
    var raf = 0;
    var rest = 0;

    // the hover video is fetched before the hover: when the card comes near the screen
    // (no wait for the network on the first hover — manager)
    function load() {
      if (!video || video.getAttribute("src")) return;
      video.preload = "auto";
      video.src = video.getAttribute("data-src");
      video.load();
    }
    if (video && fine.matches && !reduce && "IntersectionObserver" in window) {
      var io = new IntersectionObserver(function (entries) {
        if (entries[0].isIntersecting) { load(); io.disconnect(); }
      }, { rootMargin: "600px 0px" });
      io.observe(card);
    }

    function pos(e) {
      var r = media.getBoundingClientRect();
      var x = Math.min(1, Math.max(0, (e.clientX - r.left) / r.width));
      var y = Math.min(1, Math.max(0, (e.clientY - r.top) / r.height));
      // % for the reveal origin, px from the centre for the bottle (a translate — no layout)
      return { x: x * 100, y: y * 100, dx: (x - 0.5) * r.width, dy: (y - 0.5) * r.height };
    }
    function follow(p) {
      media.style.setProperty("--bx", p.dx.toFixed(1) + "px");
      media.style.setProperty("--by", p.dy.toFixed(1) + "px");
    }

    card.addEventListener("pointerenter", function (e) {
      if (!fine.matches) return;
      var p = pos(e);
      media.style.setProperty("--cx", p.x.toFixed(0) + "%");
      media.style.setProperty("--cy", p.y.toFixed(0) + "%");
      // the small bottle starts where the cursor is, not flying in from the centre
      follow(p);
      if (video && !reduce) {
        load();
        var pl = video.play();
        if (pl && pl.catch) pl.catch(function () {});
      }
    });

    media.addEventListener("pointermove", function (e) {
      if (!fine.matches || raf) return;
      raf = requestAnimationFrame(function () {
        raf = 0;
        follow(pos(e));
        media.classList.add("is-moving");
        clearTimeout(rest);
        rest = setTimeout(function () { media.classList.remove("is-moving"); }, 650);
      });
    });

    card.addEventListener("pointerleave", function () {
      clearTimeout(rest);
      media.classList.remove("is-moving");
      media.style.removeProperty("--bx");
      media.style.removeProperty("--by");
      if (video) video.pause();
    });
  });
})();
