// Custom dropdowns over native <select>s (.field__select > select): the select stays in the
// form (value, required, validation, change events — catalog sort), a button + listbox are
// drawn over it. Keyboard: ↑ ↓ / Enter / Space / Esc / Home / End; outside click closes;
// opens upwards when there is no room below. WP / CF7: the same markup works on any select.
(function () {
  "use strict";

  var openOne = null;

  function close(box) {
    if (!box) return;
    box.classList.remove("is-open", "is-up");
    box.querySelector(".cselect__btn").setAttribute("aria-expanded", "false");
    if (openOne === box) openOne = null;
  }

  document.querySelectorAll(".field__select > select").forEach(function (select, n) {
    var box = select.parentNode;
    box.classList.add("cselect");
    select.tabIndex = -1;
    select.setAttribute("aria-hidden", "true");
    select.inert = true; // still submitted and validated, but never focused (iOS opens its picker)

    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "field__input cselect__btn";
    btn.setAttribute("aria-haspopup", "listbox");
    btn.setAttribute("aria-expanded", "false");
    var label = box.closest("label");
    var labelText = label && label.querySelector(".field__label");
    if (labelText) btn.setAttribute("aria-label", labelText.textContent.replace("*", "").trim());
    var value = document.createElement("span");
    value.className = "cselect__value";
    btn.appendChild(value);

    var list = document.createElement("ul");
    list.className = "cselect__list";
    list.id = "cselect-" + n;
    list.setAttribute("role", "listbox");
    list.setAttribute("data-lenis-prevent", "");
    btn.setAttribute("aria-controls", list.id);

    var opts = [];
    Array.prototype.forEach.call(select.options, function (o) {
      if (o.disabled && !o.value) return; // the placeholder is only the empty state
      var li = document.createElement("li");
      li.className = "cselect__opt";
      li.setAttribute("role", "option");
      li.dataset.value = o.value;
      li.textContent = o.textContent;
      list.appendChild(li);
      opts.push(li);
    });

    box.appendChild(btn);
    box.appendChild(list);

    // the <label> around the field would pass a tap on an option / its own text to the hidden
    // select (phones then open the system picker): the button is the control here
    if (label) {
      label.addEventListener("click", function (e) {
        if (btn.contains(e.target)) return;
        e.preventDefault();
        if (!list.contains(e.target)) btn.focus();
      });
    }

    var active = -1;
    function sync() {
      var o = select.options[select.selectedIndex];
      var empty = !o || (o.disabled && !o.value);
      value.textContent = o ? o.textContent : "";
      box.classList.toggle("is-empty", empty);
      opts.forEach(function (li) {
        li.setAttribute("aria-selected", !empty && li.dataset.value === select.value ? "true" : "false");
      });
    }
    function highlight(i) {
      active = Math.max(0, Math.min(opts.length - 1, i));
      opts.forEach(function (li, k) { li.classList.toggle("is-active", k === active); });
      var li = opts[active];
      if (li) li.scrollIntoView({ block: "nearest" });
    }
    function open() {
      if (openOne && openOne !== box) close(openOne);
      box.classList.add("is-open");
      btn.setAttribute("aria-expanded", "true");
      openOne = box;
      // no room below → open upwards
      var r = btn.getBoundingClientRect();
      var need = Math.min(list.scrollHeight, 280) + 12;
      box.classList.toggle("is-up", window.innerHeight - r.bottom < need && r.top > window.innerHeight - r.bottom);
      var cur = opts.findIndex(function (li) { return li.getAttribute("aria-selected") === "true"; });
      highlight(cur < 0 ? 0 : cur);
    }
    function choose(i) {
      var li = opts[i];
      if (!li) return;
      select.value = li.dataset.value;
      select.dispatchEvent(new Event("change", { bubbles: true }));
      sync();
      close(box);
      btn.focus();
    }

    btn.addEventListener("click", function () {
      if (box.classList.contains("is-open")) close(box);
      else open();
    });
    opts.forEach(function (li, i) {
      li.addEventListener("click", function () { choose(i); });
      li.addEventListener("mousemove", function () { if (active !== i) highlight(i); });
    });
    btn.addEventListener("keydown", function (e) {
      var isOpen = box.classList.contains("is-open");
      if (e.key === "ArrowDown" || e.key === "ArrowUp") {
        e.preventDefault();
        if (!isOpen) open();
        else highlight(active + (e.key === "ArrowDown" ? 1 : -1));
      } else if (e.key === "Home" && isOpen) {
        e.preventDefault(); highlight(0);
      } else if (e.key === "End" && isOpen) {
        e.preventDefault(); highlight(opts.length - 1);
      } else if ((e.key === "Enter" || e.key === " ") && isOpen) {
        e.preventDefault(); choose(active);
      } else if (e.key === "Escape" && isOpen) {
        e.preventDefault(); e.stopPropagation(); close(box);
      } else if (e.key === "Tab") {
        close(box);
      }
    });

    select.addEventListener("change", sync);
    var form = select.form;
    if (form) form.addEventListener("reset", function () { setTimeout(sync); });
    sync();
  });

  document.addEventListener("click", function (e) {
    if (openOne && !openOne.contains(e.target)) close(openOne);
  });
  window.addEventListener("resize", function () { close(openOne); });
})();
