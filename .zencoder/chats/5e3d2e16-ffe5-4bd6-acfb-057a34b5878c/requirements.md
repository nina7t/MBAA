# Product Requirements Document (PRD) - Carousel Blur Fix

## Context
The project has a Flickity-based carousel on the front-page. In Desktop mode, it displays 5 images (20% width each).

## Current Issue
Currently, only the `is-selected` item is clear (opacity: 1, filter: none). All other 4 visible items are blurred (opacity: 0.3, filter: blur(6px)).

## Goal
Modify the carousel styling/behavior so that:
- 3 items in the middle of the focal group are clear (not blurred).
- 2 items at the extremities (partially visible) are blurred.
- The carousel starts aligned to the left of the screen (not centered in the viewport).

## Requirements
- Maintain the current layout (visible items on desktop).
- The transition between clear and blurred should be smooth.
- The solution should work with Flickity's `wrapAround: true` mode.
- Alignment should be to the left, but preserving the "peeking cards" effect where cards at extremities are cut off.

## Constraints
- Flickity `cellAlign: "center"` should be preserved if possible.
- Avoid breaking Mobile/Tablet layouts.
