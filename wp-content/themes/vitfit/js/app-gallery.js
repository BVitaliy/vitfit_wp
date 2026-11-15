/* global Fancybox */
// Galleries that open in Fancybox ([data-fancybox], production page).
(function () {
  "use strict";

  if (!window.Fancybox) return;
  Fancybox.bind("[data-fancybox]", {
    Thumbs: false,
    on: {
      init: function () {
        if (window.VitScrollLock) window.VitScrollLock.lock("fancybox");
      },
      destroy: function () {
        if (window.VitScrollLock) window.VitScrollLock.unlock("fancybox");
      },
    },
  });
})();
