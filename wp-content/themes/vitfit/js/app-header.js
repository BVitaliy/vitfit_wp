/* global $ */
// Header states (.is-scrolled glass, .is-hidden while scrolling down) · mobile menu ·
// search overlay (a field with suggestions while typing, Enter → the search page).
$(function () {
  var $html = $("html");
  var $header = $(".js-header");
  var $menu = $(".js-mobile-menu");
  var $burger = $(".js-burger");
  var $search = $(".js-search");

  // ── scroll states ──
  var lastY = 0;
  var ticking = false;
  function onScroll() {
    var y = window.lenis ? window.lenis.scroll : window.scrollY;
    $header.toggleClass("is-scrolled", y > 30);
    // hide going down past the first screen, show going up
    if (y > window.innerHeight * 0.6 && y > lastY + 4) $header.addClass("is-hidden");
    else if (y < lastY - 4 || y < 200) $header.removeClass("is-hidden");
    // sticky blocks outside the header follow it (article nav)
    $html.toggleClass("is-header-hidden", $header.hasClass("is-hidden"));
    lastY = y;
    ticking = false;
  }
  function requestTick() {
    if (!ticking) {
      ticking = true;
      requestAnimationFrame(onScroll);
    }
  }
  if (window.lenis) window.lenis.on("scroll", requestTick);
  window.addEventListener("scroll", requestTick, { passive: true });
  onScroll();

  // ── mobile menu ──
  function setMenu(open) {
    $menu.toggleClass("is-open", open);
    $burger.toggleClass("is-open", open).attr("aria-expanded", open ? "true" : "false");
    $header.toggleClass("is-menu-open", open);
    if (window.VitScrollLock) window.VitScrollLock[open ? "lock" : "unlock"]("menu");
  }
  $burger.on("click", function () {
    setMenu(!$menu.hasClass("is-open"));
  });
  $menu.on("click", "a", function () {
    setMenu(false);
  });
  $menu.on("click", ".js-mm-toggle", function () {
    $(this).toggleClass("is-open").next(".mobile-menu__sub").toggleClass("is-open");
  });
  window.matchMedia("(min-width: 1200px)").addEventListener("change", function (e) {
    if (e.matches) setMenu(false);
  });

  // ── search ──
  var $input = $(".js-search-input");

  function openSearch() {
    setMenu(false);
    $search.addClass("is-open").attr("aria-hidden", "false");
    if (window.VitScrollLock) window.VitScrollLock.lock("search");
    setTimeout(function () {
      $input.trigger("focus");
    }, 300);
  }
  function closeSearch() {
    if (!$search.hasClass("is-open")) return;
    $search.removeClass("is-open").attr("aria-hidden", "true");
    setTimeout(function () {
      if (window.VitScrollLock) window.VitScrollLock.unlock("search");
    }, 500);
  }
  // WP: suggestions while typing come from js/app-search.js (AJAX) — the markup's
  // demo filter over hardcoded rows is removed.

  $(".js-search-clear").on("click", function (e) {
    e.preventDefault();
    $input.val("").trigger("input").trigger("focus");
  });

  $(".js-search-open").on("click", openSearch);
  $search.on("click", ".js-search-close", closeSearch);
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      closeSearch();
      setMenu(false);
    }
  });
  $(document).on("vit:overlay-open", function () {
    closeSearch();
    setMenu(false);
  });
});
