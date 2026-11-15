// WP-only glue that the markup did not need.
(function () {
  "use strict";

  // Editor links to "#contact" / "#partner" open those popups (e.g. a link in
  // the SEO text or an article) — the markup used [data-modal-open] buttons.
  document.addEventListener("click", function (event) {
    var link = event.target.closest && event.target.closest('a[href$="#contact"], a[href$="#partner"]');
    if (!link || link.hasAttribute("data-modal-open") || !window.VitModal) return;
    event.preventDefault();
    window.VitModal.open(link.getAttribute("href").split("#").pop());
  });
})();
