// WP: live suggestions in the header search overlay (replaces the markup's demo
// filter in app-header.js). Typing → wp_ajax_vtf_live_search (debounced 300 ms)
// → the `.search__result` rows; Enter submits the form to the search page.
(function () {
  "use strict";

  var input = document.querySelector(".js-search-input");
  var results = document.querySelector(".js-search-results");
  var list = document.querySelector(".js-search-list");
  var empty = document.querySelector(".js-search-empty");
  if (!input || !results || !list || !window.vitfit) return;

  var timer = 0;
  var request = 0;

  function show(html, term) {
    list.innerHTML = html;
    results.hidden = !term;
    if (empty) empty.hidden = !term || html.trim() !== "";
  }

  function run() {
    var term = input.value.trim();
    var id = ++request;

    if (term.length < 2) {
      show("", "");
      return;
    }

    fetch(window.vitfit.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      body: new URLSearchParams({ action: "vtf_live_search", nonce: window.vitfit.nonce, s: term }),
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (id !== request || !data || !data.success) return;
        show(data.data.html || "", term);
      })
      .catch(function () {});
  }

  input.addEventListener("input", function () {
    clearTimeout(timer);
    timer = setTimeout(run, 300);
  });
})();
