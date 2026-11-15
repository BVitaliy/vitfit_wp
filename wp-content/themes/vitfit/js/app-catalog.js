// Catalog list: filters (pack / volume / flavour), sort, active chips, count,
// "show more" and the phone filter sheet. Demo filtering runs in the browser;
// in WP the same form goes to the server (GET / AJAX) and returns the same cards.
(function () {
  var root = document.querySelector(".js-catalog");
  if (!root) return;

  var form = root.querySelector(".js-filters-form");
  var grid = root.querySelector(".js-cat-grid");
  var items = Array.prototype.slice.call(grid.querySelectorAll(".js-cat-item"));
  var sortSel = root.querySelector(".js-cat-sort");
  var activeBox = root.querySelector(".js-cat-active");
  var empty = root.querySelector(".js-cat-empty");
  var moreBtn = root.querySelector(".js-cat-more");
  var moreWrap = moreBtn && moreBtn.closest(".catalog__more");
  var counts = root.querySelectorAll(".js-cat-count");
  var words = root.querySelectorAll(".js-cat-word");
  var badge = root.querySelector(".js-filters-badge");
  var sheet = root.querySelector(".js-filters");
  var PAGE = 12;
  var limit = PAGE;

  // 1 товар · 2–4 товари · 5+ товарів (11–14 → товарів).
  // WP: the three forms come from the page (data-words) — the site has UA and EN.
  var WORDS = ["товар", "товари", "товарів"];
  try {
    if (root.dataset.words) WORDS = JSON.parse(root.dataset.words);
  } catch (e) {}
  var REMOVE = root.dataset.removeLabel || "Прибрати фільтр:";

  function word(n) {
    var a = n % 10, b = n % 100;
    if (a === 1 && b !== 11) return WORDS[0];
    if (a >= 2 && a <= 4 && (b < 12 || b > 14)) return WORDS[1];
    return WORDS[2];
  }

  function state() {
    var fd = new FormData(form);
    return {
      pack: fd.get("pack") || "",
      volume: fd.getAll("volume"),
      flavor: fd.getAll("flavor"),
      sort: sortSel ? sortSel.value : "popular",
    };
  }

  function match(el, s) {
    if (s.pack && el.dataset.pack !== s.pack) return false;
    if (s.volume.length && s.volume.indexOf(el.dataset.volume) === -1) return false;
    if (s.flavor.length && s.flavor.indexOf(el.dataset.flavor) === -1) return false;
    return true;
  }

  function sorter(mode) {
    if (mode === "name") return function (a, b) { return a.dataset.name.localeCompare(b.dataset.name, "uk"); };
    if (mode === "new") return function (a, b) { return (b.dataset.new - a.dataset.new) || (a.dataset.rank - b.dataset.rank); };
    return function (a, b) { return a.dataset.rank - b.dataset.rank; };
  }

  function labelOf(input) {
    return input.closest("label").textContent.trim();
  }

  function renderChips(s) {
    activeBox.innerHTML = "";
    var chosen = Array.prototype.filter.call(form.querySelectorAll("input:checked"), function (i) { return i.value; });
    chosen.forEach(function (input) {
      var b = document.createElement("button");
      b.type = "button";
      b.className = "fl--" + (input.name === "flavor" ? input.value : "original");
      b.innerHTML = labelOf(input) + '<svg viewBox="0 0 24 24"><use href="#i-close" /></svg>';
      b.setAttribute("aria-label", REMOVE + " " + labelOf(input));
      b.addEventListener("click", function () {
        if (input.type === "radio") form.querySelector('input[name="' + input.name + '"][value=""]').checked = true;
        else input.checked = false;
        update();
      });
      activeBox.appendChild(b);
    });
    activeBox.hidden = !chosen.length;
    if (badge) {
      badge.textContent = chosen.length;
      badge.hidden = !chosen.length;
    }
  }

  var first = true;

  function update(keepLimit) {
    var s = state();
    if (!keepLimit) limit = PAGE;
    var list = items.filter(function (el) { return match(el, s); }).sort(sorter(s.sort));
    // order: the matched ones in sort order, the rest after (hidden)
    items.slice().sort(sorter(s.sort)).forEach(function (el) { grid.appendChild(el); });
    list.forEach(function (el) { grid.appendChild(el); });

    var shown = 0;
    items.forEach(function (el) {
      var on = list.indexOf(el) !== -1 && list.indexOf(el) < limit;
      var was = !el.hidden && !first;
      el.hidden = !on;
      if (on && !was) {
        el.style.setProperty("--i", shown);
        el.classList.remove("is-in");
        void el.offsetWidth; // restart the fade-in
        el.classList.add("is-in");
      }
      if (on) shown++;
    });

    first = false;
    var n = list.length;
    counts.forEach(function (c) { c.textContent = n; });
    words.forEach(function (w) { w.textContent = word(n); });
    empty.hidden = n > 0;
    if (moreWrap) moreWrap.hidden = n <= limit;
    renderChips(s);
  }

  form.addEventListener("change", function () { update(); });
  if (sortSel) sortSel.addEventListener("change", function () { update(); });
  if (moreBtn) moreBtn.addEventListener("click", function () { limit += PAGE; update(true); });

  root.querySelectorAll(".js-filters-reset").forEach(function (b) {
    b.addEventListener("click", function (e) {
      e.preventDefault();
      form.reset();
      update();
    });
  });

  // ── phone / tablet filter sheet ──
  function setSheet(open) {
    sheet.classList.toggle("is-open", open);
    if (window.VitScrollLock) window.VitScrollLock[open ? "lock" : "unlock"]("filters");
  }
  root.querySelectorAll(".js-filters-open").forEach(function (b) { b.addEventListener("click", function () { setSheet(true); }); });
  root.querySelectorAll(".js-filters-close").forEach(function (b) { b.addEventListener("click", function () { setSheet(false); }); });
  document.addEventListener("keydown", function (e) { if (e.key === "Escape" && sheet.classList.contains("is-open")) setSheet(false); });
  window.matchMedia("(min-width: 1200px)").addEventListener("change", function (e) { if (e.matches) setSheet(false); });

  // links like category.php?pack=can (footer) open the list pre-filtered — WP does it server-side
  try {
    new URLSearchParams(location.search).forEach(function (value, key) {
      form.querySelectorAll('input[name="' + key + '"]').forEach(function (input) {
        if (input.value === value) input.checked = true;
      });
    });
  } catch (e) {}

  update();
})();
