# Feature Specification: Opaque Header on Scroll

## User Stories

### User Story 1 - Opaque Navigation on Scroll
As a visitor, I want the navigation bar to become opaque when I scroll down the page, so that the menu remains readable and stands out from the content behind it.

**Acceptance Scenarios**:

1. **Given** the user is at the top of the page, **When** the page is in its initial state, **Then** the background of the navigation lists (`.header__nav-list--main` and `.header__nav-list--secondary`) should be semi-transparent.
2. **Given** the user scrolls down more than 50 pixels, **When** the scroll event is triggered, **Then** the background of these navigation lists should become opaque (e.g., background-color opacity 1.0).
3. **Given** the user scrolls back to the top (less than 50 pixels), **When** the scroll event is triggered, **Then** these backgrounds should return to their initial semi-transparent state.

---

## Requirements

- The header container background should NOT change (it stays transparent or as defined).
- Only the "pills" containing the links should change opacity.
- The transition must be smooth.

## Success Criteria

- Scrolling down 50px on `index.html`, `fiche_oeuvre.html`, and other pages makes the header background dark and opaque.
- Scrolling back up restores transparency.
- The transition is smooth (0.3s as per existing SCSS).
- The implementation works correctly on pages using Locomotive Scroll.
