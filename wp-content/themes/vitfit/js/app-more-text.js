// Expandable text (.js-more-text): "Читати більше" / "Згорнути" — class toggle
// only; height and labels are handled in CSS (style/_more-text.scss). As reatiss.
(function () {
  "use strict";

  document.querySelectorAll(".js-more-text").forEach(function (block) {
    var toggle = block.querySelector(".js-more-text-toggle");
    if (!toggle) return;

    toggle.addEventListener("click", function () {
      var open = block.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
  });
})();
