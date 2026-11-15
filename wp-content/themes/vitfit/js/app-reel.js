// Fullscreen video block (one screen high, static): the loop loads near the
// viewport and plays only in view. [data-video-open="src"] plays a video in
// the "video" popup.
(function () {
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  document.querySelectorAll(".js-reel").forEach(function (reel) {
    var video = reel.querySelector(".js-reel-video");
    if (video && window.innerWidth < 768 && video.dataset.posterMob) video.poster = video.dataset.posterMob;

    if (video && "IntersectionObserver" in window) {
      new IntersectionObserver(function (entries) {
        var on = entries[0].isIntersecting;
        if (on && !video.getAttribute("src")) {
          var mob = window.innerWidth < 768;
          if (mob && video.dataset.posterMob) video.poster = video.dataset.posterMob;
          video.src = mob && video.dataset.srcMob ? video.dataset.srcMob : video.dataset.src;
        }
        if (on && !reduce) {
          var p = video.play();
          if (p && p.catch) p.catch(function () {});
        } else video.pause();
      }, { rootMargin: "200px 0px" }).observe(reel);
    }

  });

  // popup player
  var player = document.querySelector(".js-modal-video");
  document.addEventListener("click", function (e) {
    var btn = e.target.closest("[data-video-open]");
    if (!btn || !player || !window.VitModal) return;
    e.preventDefault();
    player.src = btn.getAttribute("data-video-open");
    window.VitModal.open("video");
    var p = player.play();
    if (p && p.catch) p.catch(function () {});
  });
  // app-modals.js fires it through jQuery (not a native event)
  if (window.jQuery) {
    window.jQuery(document).on("vit:modal-closed", function () {
      if (player && player.getAttribute("src")) {
        player.pause();
        player.removeAttribute("src");
        player.load();
      }
    });
  }
})();
