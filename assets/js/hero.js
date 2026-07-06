(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var hero = document.querySelector(".futuretheme-hero");

    if (!hero) {
      return;
    }

    var track = document.getElementById("futurethemeHeroTrack");
    var dotsWrap = document.getElementById("futurethemeHeroDots");
    var dataTag = document.getElementById("futurethemeHeroData");

    var titleEl = document.getElementById("futurethemeHeroTitle");
    var subEl = document.getElementById("futurethemeHeroSub");
    var tagEl = document.getElementById("futurethemeHeroTag");
    var buttonEl = document.getElementById("futurethemeHeroButton");

    var prevBtn = hero.querySelector("[data-hero-prev]");
    var nextBtn = hero.querySelector("[data-hero-next]");
    var slides = hero.querySelectorAll(".hero-slide");

    if (!track || !dotsWrap || !dataTag || !slides.length) {
      return;
    }

    var heroData = [];

    try {
      heroData = JSON.parse(dataTag.textContent);
    } catch (error) {
      heroData = [];
    }

    if (!heroData.length) {
      return;
    }

    var currentIndex = 0;
    var timer = null;
    var intervalTime = 5500;

    function buildDots() {
      dotsWrap.innerHTML = "";

      heroData.forEach(function (_, index) {
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
      var item = heroData[index];

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
      }
    }

    function goToSlide(index) {
      currentIndex =
        ((index % heroData.length) + heroData.length) % heroData.length;

      track.style.transform = "translateX(-" + currentIndex * 100 + "%)";

      slides.forEach(function (slide, slideIndex) {
        slide.classList.toggle("active", slideIndex === currentIndex);
      });

      dotsWrap.querySelectorAll(".hdot").forEach(function (dot, dotIndex) {
        dot.classList.toggle("on", dotIndex === currentIndex);
      });

      updateContent(currentIndex);
    }

    function moveSlide(direction) {
      goToSlide(currentIndex + direction);
    }

    function startAuto() {
      stopAuto();

      if (heroData.length <= 1) {
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

    hero.addEventListener("mouseenter", stopAuto);
    hero.addEventListener("mouseleave", startAuto);

    buildDots();
    goToSlide(0);
    startAuto();
  });
})();
