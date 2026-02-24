# COP4331 Small Project

PHP + MySQL contact manager backend with GitHub Actions CI/CD.

## Project Timeline & Milestones (Target: March 24)

| Phase | Milestone | Target Date |
| :--- | :--- | :--- |
| **Phase 1: Setup** | Repo Setup & DigitalOcean Config | Feb 25 - Mar 02 |
| **Phase 2: Backend** | MySQL Tables, Auth & CRUD APIs | Mar 02 - Mar 14 |
| **Phase 3: Frontend** | UI Layout, AJAX, & Partial Search | Mar 02 - Mar 17 |
| **Phase 4: Polish** | Lighthouse Audit & Slide Deck | Mar 18 - Mar 21 |
| **🚨 Pre-Flight** | **UCF IT Network Live Check** | **Mar 22** |
| **🚀 Delivery** | **Presentation Day** | **Mar 24** |

### Gantt Chart
*(GitHub automatically renders this chart)*

```mermaid
gantt
    title Contact Manager Project Timeline (Target: March 24)
    dateFormat  YYYY-MM-DD
    axisFormat  %b %d
    
    section Setup & Design
    Github & Repo Setup       :a1, 2026-02-25, 2d
    Database Design (ERD)     :a2, after a1, 3d
    Digital Ocean/LAMP Setup  :a3, 2026-02-27, 4d

    section Backend Dev
    MySQL Implementation      :b1, after a2, 2d
    Login/Register API (PHP)  :b2, after b1, 4d
    CRUD Contacts API         :b3, after b2, 5d
    SwaggerHub Config         :b4, after b3, 2d

    section Frontend Dev
    UI Layout (HTML/CSS)      :c1, 2026-03-02, 5d
    AJAX Integration          :c2, after b2, 7d
    
    section Final Polish
    Partial Search Logic      :d1, after c2, 3d
    Lighthouse Audit          :d2, after d1, 2d
    Slide Deck Creation       :d3, 2026-03-19, 3d
    UCF IT Network Check      :milestone, 2026-03-22, 0d
    Code Freeze & Practice    :d4, 2026-03-23, 1d
    Presentation Day          :milestone, 2026-03-24, 0d
