/* ============================================================
   FUTURETHEME - THEME JS
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  /* ------------------------------------------------------------
     ACORDEÓN MUSEO
     ------------------------------------------------------------ */

  const accordionButtons = document.querySelectorAll(".accordion-hd");

  accordionButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      const item = button.closest(".accordion-item");

      if (!item) {
        return;
      }

      const isOpen = item.classList.contains("is-open");

      /*
       * Cerrar otros acordeones del mismo grupo.
       * Si deseas permitir varios abiertos a la vez,
       * elimina este bloque.
       */
      const accordionGroup = item.closest(".museo-accordion");

      if (accordionGroup) {
        const openItems = accordionGroup.querySelectorAll(
          ".accordion-item.is-open",
        );

        openItems.forEach(function (openItem) {
          if (openItem !== item) {
            openItem.classList.remove("is-open");

            const openButton = openItem.querySelector(".accordion-hd");

            if (openButton) {
              openButton.setAttribute("aria-expanded", "false");
            }
          }
        });
      }

      if (isOpen) {
        item.classList.remove("is-open");
        button.setAttribute("aria-expanded", "false");
      } else {
        item.classList.add("is-open");
        button.setAttribute("aria-expanded", "true");
      }
    });
  });
});

/* ============================================================
   HEADER - MENÚ RESPONSIVE HAMBURGUESA
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  const navToggle = document.querySelector(".nav-toggle");
  const navMenu = document.querySelector("#primary-menu");

  if (!navToggle || !navMenu) {
    return;
  }

  navToggle.addEventListener("click", function () {
    const isOpen = document.body.classList.toggle("nav-is-open");

    navToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    navToggle.setAttribute(
      "aria-label",
      isOpen ? "Cerrar menú principal" : "Abrir menú principal",
    );
  });

  navMenu.addEventListener("click", function (event) {
    const link = event.target.closest("a");

    if (!link) {
      return;
    }

    document.body.classList.remove("nav-is-open");
    navToggle.setAttribute("aria-expanded", "false");
    navToggle.setAttribute("aria-label", "Abrir menú principal");
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      document.body.classList.remove("nav-is-open");
      navToggle.setAttribute("aria-expanded", "false");
      navToggle.setAttribute("aria-label", "Abrir menú principal");
    }
  });
});
