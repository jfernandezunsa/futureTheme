/**
 * FutureTheme - Destacados de portada.
 *
 * Controla:
 * - Slider principal con elementos de categoría "hero".
 * - Preparado para futuras interacciones de tarjetas destacadas.
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    initFuturethemeSlider();
  });

  function initFuturethemeSlider() {
    var slider = document.querySelector(".futuretheme-slider");

    if (!slider) {
      return;
    }

    var track = document.getElementById("futurethemeSliderTrack");
    var dotsWrap = document.getElementById("futurethemeSliderDots");
    var dataTag = document.getElementById("futurethemeSliderData");

    var titleEl = document.getElementById("futurethemeSliderTitle");
    var subEl = document.getElementById("futurethemeSliderSub");
    var tagEl = document.getElementById("futurethemeSliderTag");
    var buttonEl = document.getElementById("futurethemeSliderButton");

    var prevBtn = slider.querySelector("[data-slider-prev]");
    var nextBtn = slider.querySelector("[data-slider-next]");
    var slides = slider.querySelectorAll(".hero-slide");

    if (!track || !dataTag || !slides.length) {
      return;
    }

    var sliderData = [];

    try {
      sliderData = JSON.parse(dataTag.textContent);
    } catch (error) {
      sliderData = [];
    }

    if (!sliderData.length) {
      return;
    }

    var currentIndex = 0;
    var timer = null;
    var intervalTime = 5500;

    function buildDots() {
      if (!dotsWrap || sliderData.length <= 1) {
        return;
      }

      dotsWrap.innerHTML = "";

      sliderData.forEach(function (_, index) {
        var dot = document.createElement("button");

        dot.type = "button";
        dot.className = "hdot" + (index === 0 ? " on" : "");
        dot.setAttribute("aria-label", "Ir al slide " + (index + 1));

        dot.addEventListener("click", function () {
          goToSlide(index);
          restartAuto();
        });

        dotsWrap.appendChild(dot);
      });
    }

    function updateContent(index) {
      var item = sliderData[index];

      if (!item) {
        return;
      }

      if (tagEl) {
        tagEl.textContent = item.tag || "";
      }

      if (titleEl) {
        titleEl.textContent = item.title || "";
      }

      if (subEl) {
        subEl.textContent = item.excerpt || "";
      }

      if (buttonEl) {
        buttonEl.textContent = item.button_text || "Más información";
        buttonEl.setAttribute("href", item.button_url || "#");

        if (item.new_tab) {
          buttonEl.setAttribute("target", "_blank");
          buttonEl.setAttribute("rel", "noopener noreferrer");
        } else {
          buttonEl.removeAttribute("target");
          buttonEl.removeAttribute("rel");
        }
      }
    }

    function goToSlide(index) {
      currentIndex =
        ((index % sliderData.length) + sliderData.length) % sliderData.length;

      track.style.transform = "translateX(-" + currentIndex * 100 + "%)";

      slides.forEach(function (slide, slideIndex) {
        slide.classList.toggle("active", slideIndex === currentIndex);
      });

      if (dotsWrap) {
        dotsWrap.querySelectorAll(".hdot").forEach(function (dot, dotIndex) {
          dot.classList.toggle("on", dotIndex === currentIndex);
        });
      }

      updateContent(currentIndex);
    }

    function moveSlide(direction) {
      goToSlide(currentIndex + direction);
    }

    function startAuto() {
      stopAuto();

      if (sliderData.length <= 1) {
        return;
      }

      timer = window.setInterval(function () {
        moveSlide(1);
      }, intervalTime);
    }

    function stopAuto() {
      if (timer) {
        window.clearInterval(timer);
        timer = null;
      }
    }

    function restartAuto() {
      stopAuto();
      startAuto();
    }

    if (prevBtn) {
      prevBtn.addEventListener("click", function () {
        moveSlide(-1);
        restartAuto();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener("click", function () {
        moveSlide(1);
        restartAuto();
      });
    }

    slider.addEventListener("mouseenter", stopAuto);
    slider.addEventListener("mouseleave", startAuto);

    buildDots();
    goToSlide(0);
    startAuto();
  }
})();
