/* global $ */
// .js-form: required + email + checkbox validation; a valid form opens "thanks".
// WP: Contact Form 7 → open the same popup on wpcf7mailsent.
$(function () {
  if ($.fn.inputmask) $(".js-phone").inputmask("+38 (999) 999-99-99", { showMaskOnHover: false });

  var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function check(el) {
    var ok = true;
    // a required radio group (review rating): one of the name checked, error on its fieldset
    if (el.type === "radio") {
      if (!el.required) return true;
      ok = $(el.form).find('input[name="' + el.name + '"]:checked').length > 0;
      $(el).closest("fieldset").toggleClass("is-error", !ok);
      return ok;
    }
    if (el.type === "checkbox") ok = !el.required || el.checked;
    else if (el.required && !$.trim(el.value)) ok = false;
    else if (el.type === "email" && el.value && !EMAIL.test(el.value)) ok = false;
    else if ($(el).hasClass("js-phone") && el.required && $.fn.inputmask && !$(el).inputmask("isComplete")) ok = false;
    $(el).toggleClass("is-error", !ok);
    return ok;
  }

  $(document).on("submit", ".js-form", function (e) {
    e.preventDefault();
    var valid = true;
    $(this)
      .find("input, select, textarea")
      .each(function () {
        if (!check(this)) valid = false;
      });
    // "fill in the required fields" above the submit button (manager)
    var $err = $(this).find(".form__error");
    if (!$err.length) $err = $('<p class="form__error" role="alert" hidden>Заповніть, будь ласка, усі обов’язкові поля</p>').insertBefore($(this).find('[type="submit"]').last());
    $err.prop("hidden", valid);
    if (!valid) {
      var $first = $(this).find(".is-error").first().find("input").addBack("input, textarea, select").first();
      // a custom dropdown (app-select.js): its button takes the focus, not the hidden select
      var $cbtn = $first.siblings(".cselect__btn");
      ($cbtn.length ? $cbtn : $first).trigger("focus");
      return;
    }
    this.reset();
    if (window.VitModal) window.VitModal.open("thanks");
  });

  $(document).on("input change", ".js-form .is-error", function () {
    check(this);
    var $form = $(this).closest(".js-form");
    if (!$form.find(".is-error").length) $form.find(".form__error").prop("hidden", true);
  });

  $(document).on("change", ".js-form fieldset.is-error input", function () {
    check($(this).closest("fieldset").find("input[required]")[0]);
  });
});
