document.addEventListener("DOMContentLoaded", function () {
  if (typeof Flickity === "undefined") {
    console.error("Flickity non chargé");
    return;
  }

  const carouselList = document.querySelector(".carousel__list");
  const filterItems = document.querySelectorAll(".filtre__list-item[data-filter]");
  const prevButton = document.querySelector(".carousel__arrow--prev");
  const nextButton = document.querySelector(".carousel__arrow--next");

  if (!carouselList) {
    console.log("Carousel non trouvé");
    return;
  }

  // Modifiez ces options dans votre initialisation Flickity
let flkty = new Flickity(carouselList, {
  cellAlign: "center", // IMPORTANT : l'image active est au milieu
  contain: false,      // Permet de déborder à gauche et à droite
  wrapAround: true,
  prevNextButtons: false,
  pageDots: false,
  // ... le reste de vos options
});

function updateBlur() {
  const all = carouselList.querySelectorAll(".carousel__item__container");
  const total = all.length;
  const i = flkty.selectedIndex;

  all.forEach(el => {
    // Par défaut : flou et sombre (les images qui débordent)
    el.style.opacity = "0.4";
    el.style.filter = "blur(4px)";
    el.style.transform = "scale(0.9)"; // Optionnel : petit effet de recul
  });

  // Les 3 du milieu : celle de gauche (-1), la centrale (0), celle de droite (1)
  [-1, 0, 1].forEach(offset => {
    // Calcul de l'index avec modulo pour le mode wrapAround
    const idx = (i + offset + total) % total;
    if (all[idx]) {
      all[idx].style.opacity = "1";
      all[idx].style.filter = "none";
      all[idx].style.transform = "scale(1)";
    }
  });
}

flkty.on("select", updateBlur);
flkty.on("settle", updateBlur); // Sécurité pour recalculer après le mouvement
updateBlur();

  // ── Boutons navigation ──
  if (prevButton) prevButton.addEventListener("click", () => flkty.previous());
  if (nextButton) nextButton.addEventListener("click", () => flkty.next());

  // ── Clavier ──
  document.addEventListener("keydown", function (e) {
    if (e.key === "ArrowLeft") flkty.previous();
    if (e.key === "ArrowRight") flkty.next();
  });

  // ── Filtres ──
  filterItems.forEach(function (item) {
    item.addEventListener("click", function (e) {
      e.preventDefault();

      const filterValue = this.dataset.filter;

      // Active state
      filterItems.forEach(f => f.classList.remove("filtre__list-item--active"));
      this.classList.add("filtre__list-item--active");

      // Afficher / masquer les cartes
      const allContainers = carouselList.querySelectorAll(".carousel__item__container");
      allContainers.forEach(function (container) {
        const match =
          filterValue === "tous" ||
          (container.dataset.filter && container.dataset.filter.includes(filterValue));

        container.style.display = match ? "" : "none";
      });

      // Recalculer Flickity
      flkty.reloadItems();
      flkty.resize();
      flkty.select(0);
    });
  });

  console.log("✓ Carousel initialisé");
});

