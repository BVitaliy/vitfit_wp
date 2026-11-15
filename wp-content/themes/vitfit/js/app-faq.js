/* global $ */
// FAQ accordion (one open at a time).
$(function () {
  $(document).on("click", ".js-faq-head", function () {
    var $item = $(this).closest(".faq__item");
    var open = !$item.hasClass("is-open");
    $item.siblings(".is-open").removeClass("is-open").find(".js-faq-head").attr("aria-expanded", "false");
    $item.toggleClass("is-open", open);
    $(this).attr("aria-expanded", open ? "true" : "false");
  });
});
