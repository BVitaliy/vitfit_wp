// Tabs that filter a block (.js-filter): buttons [data-filter] show the items [data-type]
// inside the same block ("all" = everything) — where to buy (shop types).
// Also the ✕ of search fields (.js-input-clear).
(function () {
  "use strict";

  document.querySelectorAll(".js-filter").forEach(function (root) {
    var tabs = root.querySelectorAll("[data-filter]");
    var items = root.querySelectorAll("[data-type]");
    var current = "all";

    function apply() {
      items.forEach(function (item) {
        item.hidden = !(current === "all" || item.getAttribute("data-type") === current);
      });
      if (window.ScrollTrigger) window.ScrollTrigger.refresh();
    }

    tabs.forEach(function (tab) {
      tab.addEventListener("click", function () {
        current = tab.getAttribute("data-filter");
        tabs.forEach(function (t) {
          var on = t === tab;
          t.classList.toggle("is-active", on);
          t.setAttribute("aria-pressed", on ? "true" : "false");
        });
        apply();
      });
    });
  });

  // ✕ in a search field: clear it, keep the focus, tell the listeners
  document.querySelectorAll(".js-input-clear").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var input = btn.parentNode.querySelector("input");
      if (!input) return;
      input.value = "";
      input.dispatchEvent(new Event("input", { bubbles: true }));
      input.focus();
    });
  });
})();
