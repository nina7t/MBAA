document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.querySelector(".header__menu-toggle");
  const nav = document.querySelector(".header__nav");
  if (!toggle || !nav) return;

  const links = nav.querySelectorAll("a");

  const setMenuState = (isOpen) => {
    toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    nav.setAttribute("aria-hidden", isOpen ? "false" : "true");
    document.body.classList.toggle("is-nav-open", isOpen);
  };

  const syncWithViewport = () => {
    if (window.innerWidth >= 860) {
      // Menu toujours visible sur desktop
      nav.setAttribute("aria-hidden", "false");
      toggle.setAttribute("aria-expanded", "false");
      document.body.classList.remove("is-nav-open");
    } else {
      // On referme par défaut sur mobile
      setMenuState(false);
    }
  };

  toggle.addEventListener("click", () => {
    const isOpen = toggle.getAttribute("aria-expanded") === "true";
    setMenuState(!isOpen);
  });

  links.forEach((link) => {
    link.addEventListener("click", () => {
      if (window.innerWidth < 860) {
        setMenuState(false);
      }
    });
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && toggle.getAttribute("aria-expanded") === "true") {
      setMenuState(false);
    }
  });

  window.addEventListener("resize", syncWithViewport);
  syncWithViewport();
});

