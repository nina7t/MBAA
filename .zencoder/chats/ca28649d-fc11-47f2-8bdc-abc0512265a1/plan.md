# Feature development workflow

---

## Workflow Steps

### [x] Step: Requirements
[requirements.md](./requirements.md)

### [x] Step: Technical Specification
[spec.md](./spec.md)

### [x] Step: Implementation Plan
Update plan.md with implementation tasks

### [ ] Step: Update Header Styles
Update `css/layout/_entete.scss` to add opaque background to `.header--scrolled .header__container`.
- **Reference**: `css/layout/_entete.scss`
- **Deliverable**: Compiled CSS with opaque header state.
- **Verification**: Check if `.header--scrolled .header__container` has `background-color: rgba(46, 46, 46, 0.95)`.

### [ ] Step: Create Centralized Scroll Handler
Create `js/locomotive-init.js` to handle both native and Locomotive Scroll events and toggle `.header--scrolled`.
- **Reference**: `js/menu.js`, existing inline Locomotive scripts.
- **Deliverable**: `js/locomotive-init.js`.
- **Verification**: Ensure script correctly toggles class on scroll.

### [ ] Step: Update HTML Files
Update `index.html`, `fiche_oeuvre.html`, `collections.html`, `evenement.html`, etc. to use the new centralized script and ensure `data-scroll-container` is present.
- **Reference**: All HTML files.
- **Deliverable**: Updated HTML files.
- **Verification**: Open pages and verify scroll effect works.

### [ ] Step: Final Review and Lint
Run linting and ensure everything is consistent.
- **Deliverable**: Lint-free code.
- **Verification**: Run `npm run lint` if available.
