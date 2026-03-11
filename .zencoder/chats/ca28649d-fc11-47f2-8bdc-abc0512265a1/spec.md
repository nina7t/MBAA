# Technical Specification: Opaque Header on Scroll

## Technical Context
- **Language**: JavaScript, SCSS
- **Dependencies**: Locomotive Scroll (v4.1.3), jQuery (available but maybe not needed here)
- **Current implementation**: `js/menu.js` handles native scroll events to toggle `.header--scrolled` class.

## Technical Implementation Brief

1.  **SCSS Update**: Update `.header--scrolled` in `css/layout/_entete.scss` to change the background color of `.header__nav-list--main` and `.header__nav-list--secondary` to be fully opaque. Adjust their initial opacity if necessary to make the transition visible.
2.  **JavaScript Centralization**:
    - Create a new script `js/locomotive-init.js` (or similar) to centralize Locomotive Scroll initialization.
    - This script will also handle the toggling of the `.header--scrolled` class when Locomotive Scroll is used.
    - It should expose the Locomotive Scroll instance globally (e.g., `window.locoScroll`).
3.  **HTML Update**: Update HTML files to use the centralized script and ensure `data-scroll-container` is present where needed.
4.  **Compatibility**: Ensure `js/menu.js` doesn't conflict or works in tandem with the Locomotive Scroll handler.

## Source Code Structure
- `css/layout/_entete.scss`: Header styles.
- `js/locomotive-init.js`: (New) Centralized scroll management.
- `index.html`, `fiche_oeuvre.html`, etc.: Updated to use centralized JS.

## Contracts
- Class `.header--scrolled`: Added to `.header` element when scroll > 50px.
- Event `scroll`: Listened on both `window` and Locomotive Scroll instance.

## Delivery Phases
1.  **Phase 1: Styles Update**: Update SCSS to implement the visual opaque state.
2.  **Phase 2: Centralized Scroll Script**: Create `js/locomotive-init.js` and update one page (e.g., `index.html`) to use it and verify it works with Locomotive Scroll.
3.  **Phase 3: Global Rollout**: Update all other HTML files to use the centralized script.

## Verification Strategy
- **Manual Verification**: Scroll the page and check if the header background changes.
- **Console Verification**: Check if `header--scrolled` class is correctly toggled in the DOM.
- **Cross-page Verification**: Ensure it works on both pages with and without Locomotive Scroll (though the goal is to have it everywhere).
