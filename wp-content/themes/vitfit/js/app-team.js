// Team popup: a card (.js-team-open, data-modal-open="team") copies its <template> into the
// shared panel — on the card itself, so it runs before the delegated opener in app-modals.js.
(function () {
  "use strict";

  var body = document.querySelector(".js-team-modal-body");
  if (!body) return;

  document.querySelectorAll(".js-team-open").forEach(function (card) {
    var tpl = card.parentElement.querySelector(".js-team-bio");
    if (!tpl) return;
    card.addEventListener("click", function () {
      body.innerHTML = "";
      body.appendChild(tpl.content.cloneNode(true));
    });
  });
})();
