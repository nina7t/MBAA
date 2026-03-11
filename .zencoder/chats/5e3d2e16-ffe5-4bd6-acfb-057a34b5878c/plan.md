# Full SDD workflow

## Workflow Steps

### [x] Step: Requirements

Create a Product Requirements Document (PRD) based on the feature description.

1. Review existing codebase to understand current architecture and patterns
2. Analyze the feature definition and identify unclear aspects
3. Ask the user for clarifications on aspects that significantly impact scope or user experience
4. Make reasonable decisions for minor details based on context and conventions
5. If user can't clarify, make a decision, state the assumption, and continue

Save the PRD to `/Applications/MAMP/htdocs/Mbaa/.zencoder/chats/5e3d2e16-ffe5-4bd6-acfb-057a34b5878c/requirements.md`.

### [x] Step: Technical Specification

Create a technical specification based on the PRD in `/Applications/MAMP/htdocs/Mbaa/.zencoder/chats/5e3d2e16-ffe5-4bd6-acfb-057a34b5878c/requirements.md`.

1. Review existing codebase architecture and identify reusable components
2. Define the implementation approach

Save to `/Applications/MAMP/htdocs/Mbaa/.zencoder/chats/5e3d2e16-ffe5-4bd6-acfb-057a34b5878c/spec.md` with:

- Technical context (language, dependencies)
- Implementation approach referencing existing code patterns
- Source code structure changes
- Data model / API / interface changes
- Delivery phases (incremental, testable milestones)
- Verification approach using project lint/test commands

### [x] Step: Planning

Create a detailed implementation plan based on `/Applications/MAMP/htdocs/Mbaa/.zencoder/chats/5e3d2e16-ffe5-4bd6-acfb-057a34b5878c/spec.md`.

1. Break down the work into concrete tasks
2. Each task should reference relevant contracts and include verification steps
3. Replace the Implementation step below with the planned tasks

### [x] Task 1: Update JavaScript logic for `.is-clear` class

Modify `js/carousel-flickity.js` to manage the `.is-clear` class on selected and adjacent items.
- Ensure the class is applied on `select` and `settle` events.
- Correctly handle `wrapAround` mode using modulo for neighbor indices.

### [x] Task 2: Update CSS for clear/blurred effect and layout

Update `style.css` to:
- Set desktop width of `.carousel__item__container` to `25%` (3 full items + 2 half items).
- Replace `.is-selected` rules with `.is-clear` to have 3 clear cards.
- Ensure items without `.is-clear` remain blurred.

### [x] Task 3: Verification and Final Polish

- Test the carousel across different screen sizes.
- Ensure transitions are smooth.
- Run any available linting tools.

