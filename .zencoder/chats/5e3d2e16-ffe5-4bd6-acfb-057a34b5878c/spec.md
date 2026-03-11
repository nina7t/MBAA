# Technical Specification - Carousel Blur Fix

## Context
- **Theme**: WordPress Mbaa_theme
- **Library**: Flickity
- **Target**: Ensure 3 clear cards (center + neighbors) and 2 blurred/partial cards (edges).

## Implementation Approach

### 1. JavaScript (js/carousel-flickity.js)
Modify `createFlickity` to include a listener for the `select` and `settle` events. 
This listener will:
- Iterate through all cells.
- Identify the `selectedIndex`.
- Identify the `prevIndex` and `nextIndex` (handling wrap-around with modulo).
- Add an `is-clear` class to the current, previous, and next cells.
- Remove `is-clear` from all other cells.
- Initially run this on carousel initialization.

```javascript
function updateClearClasses(flickity) {
  const cells = flickity.cells;
  const numCells = cells.length;
  const selectedIndex = flickity.selectedIndex;
  
  // Clear all
  cells.forEach(cell => cell.element.classList.remove('is-clear'));
  
  // Add to selected and its neighbors
  const prevIndex = (selectedIndex - 1 + numCells) % numCells;
  const nextIndex = (selectedIndex + 1) % numCells;
  
  cells[selectedIndex].element.classList.add('is-clear');
  cells[prevIndex].element.classList.add('is-clear');
  cells[nextIndex].element.classList.add('is-clear');
}
```

### 2. CSS (style.css)
Update the carousel styles:
- Change `.is-selected` to `.is-clear`.
- Adjust `.carousel__item__container` width on desktop to `25%` (to have 3 full cards + 2 half cards visible: 3 + 0.5 + 0.5 = 4 units -> 100/4 = 25%).
- Ensure `is-clear` has `opacity: 1` and `filter: none`.
- Ensure everything not `is-clear` has `opacity: 0.3` and `filter: blur(6px)`.

## Source Code Changes
- **js/carousel-flickity.js**: Update initialization logic.
- **style.css**: Update class references and widths.

## Verification
- Visual inspection on desktop (1024px+).
- Verify 3 items are clear and 2 at the edges are blurred.
- Ensure smooth transitions on slide change.
- Verify mobile/tablet views are not negatively impacted.
