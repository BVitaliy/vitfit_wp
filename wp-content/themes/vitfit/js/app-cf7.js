// Contact Form 7 → design integration (WP; replaces the markup's demo submit in
// app-form.js, which only handles `.js-form`).
//
// CF7 forms (`form.vtf-form`) are sent by CF7; this file maps its lifecycle
// onto the design: the field error state (.is-error on inputs / the rating
// fieldset), «Fill in all the required fields» above the button (.form__error),
// a busy button while sending, and the "thanks" popup on `wpcf7mailsent`
// (team checklist: DOM events, loader, error highlight + text).
(function () {
  "use strict";

  var FORM = "form.vtf-form";
  var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  var MESSAGE = (window.vitfit && window.vitfit.i18n && window.vitfit.i18n.formError) || "Please fill in all the required fields";

  function required(input) {
    return input.classList.contains("wpcf7-validates-as-required") || input.hasAttribute("aria-required");
  }

  function fieldError(input) {
    if (input.type === "checkbox") return required(input) && !input.checked;
    var value = String(input.value || "").trim();
    if (required(input) && !value) return true;
    if (input.type === "email" && value && !EMAIL.test(value)) return true;
    if (input.classList.contains("js-phone") && value && window.jQuery && window.jQuery.fn.inputmask && !window.jQuery(input).inputmask("isComplete")) return true;
    return false;
  }

  function mark(input, bad) {
    input.classList.toggle("is-error", bad);
    input.setAttribute("aria-invalid", bad ? "true" : "false");
  }

  function controls(form) {
    return form.querySelectorAll(".field__input:not(.cselect__btn), .check input");
  }

  function note(form, show) {
    var error = form.querySelector(".form__error");
    if (!error && show) {
      var submit = form.querySelector("[type=submit]");
      error = document.createElement("p");
      error.className = "form__error";
      error.setAttribute("role", "alert");
      error.textContent = MESSAGE;
      if (submit) submit.parentNode.insertBefore(error, submit);
    }
    if (error) error.hidden = !show;
  }

  function focus(input) {
    // a custom dropdown (app-select.js): its button takes the focus, not the hidden select
    var btn = input.tagName === "SELECT" && input.parentNode.querySelector(".cselect__btn");
    (btn || input).focus();
  }

  // Capture phase: runs before CF7's own submit listener, so an invalid form
  // never reaches the server.
  document.addEventListener("submit", function (event) {
    var form = event.target.closest && event.target.closest(FORM);
    if (!form) return;

    var first = null;
    controls(form).forEach(function (input) {
      var bad = fieldError(input);
      mark(input, bad);
      if (bad && !first) first = input;
    });
    note(form, !!first);

    if (first) {
      event.preventDefault();
      event.stopImmediatePropagation();
      focus(first);
      return;
    }

    var btn = form.querySelector("[type=submit]");
    if (btn) btn.classList.add("is-loading");
  }, true);

  // Clear a field's error as soon as it becomes valid.
  ["input", "change"].forEach(function (type) {
    document.addEventListener(type, function (event) {
      var input = event.target;
      var form = input.closest && input.closest(FORM);
      if (!form || !input.classList.contains("is-error")) return;
      if (!fieldError(input)) mark(input, false);
      if (!form.querySelector(".is-error")) note(form, false);
    });
  });

  ["wpcf7submit", "wpcf7invalid", "wpcf7spam", "wpcf7mailfailed", "wpcf7mailsent"].forEach(function (name) {
    document.addEventListener(name, function (event) {
      var btn = event.target.querySelector("[type=submit]");
      if (btn) btn.classList.remove("is-loading");
    });
  });

  // Server-side validation failed: show the design's errors on those fields.
  document.addEventListener("wpcf7invalid", function (event) {
    var form = event.target;
    var invalid = (event.detail && event.detail.apiResponse && event.detail.apiResponse.invalid_fields) || [];
    var first = null;

    invalid.forEach(function (item) {
      var wrap = form.querySelector('[data-name="' + item.field + '"]');
      var input = wrap && wrap.querySelector(".field__input:not(.cselect__btn), input");
      if (!input) return;
      mark(input, true);
      if (!first) first = input;
    });

    note(form, true);
    if (first) focus(first);
  });

  document.addEventListener("wpcf7mailsent", function (event) {
    controls(event.target).forEach(function (input) {
      mark(input, false);
    });
    note(event.target, false);
    if (window.VitModal) window.VitModal.open("thanks");
  });

  // A failed send is not a validation problem — announce the message CF7 shows.
  ["wpcf7mailfailed", "wpcf7spam"].forEach(function (name) {
    document.addEventListener(name, function (event) {
      var output = event.target.querySelector(".wpcf7-response-output");
      if (output) output.setAttribute("role", "alert");
    });
  });
})();
