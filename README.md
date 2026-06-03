# Project & Customer Management System

A bespoke, high-performance web platform tailored for managing projects, clients, time tracking, and automated invoicing. Built with native PHP and a relational SQL database using PDO, this application has undergone extensive security hardening and architectural refactoring to meet modern production and enterprise standards.

---

## Key Features

* **Client & Project Lifecycle Management:** Dedicated modules for onboarding clients, tracking ongoing project statuses, and evaluating administrative KPIs.
* **Granular Time Tracking:** Integrated work-hour logging with administrative tools for record correction, verification, and compliance checking.
* **Automated Billing Pipeline:** Aggregated data previews that dynamically pull logged hours and client rates to generate comprehensive billing previews and document exports.
* **Multi-Tenant User Management:** Secure login interfaces, user registration routes, and permission/role allocation levels.

---

## Security Hardening & Architectural Refactoring

The core application logic was significantly upgraded during a comprehensive refactoring phase to neutralize common web application vulnerabilities:

* **SQL Injection (SQLi) Elimination:** Converted over 40 raw, string-interpolated query paths across critical entities (Projects, Clients, Time Logs, and Upload fields) into safe, parameterized **PDO Prepared Statements**. This strictly separates user input from database execution logic.
* **Cross-Site Scripting (XSS) Mitigation:** Implemented strict context-aware output sanitization across more than 17 view templates using a native escaping helper `h()`. Safe variables injection into JavaScript scopes is handled via dynamic JSON script serialization (`pm_json_script()`).
* **Architectural Clean-up & Modularization (DRY Principle):** Eliminated structural redundancies by extracting repetitive checks (such as role permissions and site configurations) into efficient, centralized helper functions inside `mysql.php`.
* **Privilege Separation:** Refactored the logging ecosystem (`log.php`) to isolate operational libraries from controller execution paths. This strictly prevents non-privileged sessions from triggering sensitive API calls while protecting administrative interfaces using strict query-table whitelists.
* **Stability Fixes:** Corrected logical validation errors in the project creation workflow (`add_project.php`) and fixed pluralization parsing routines in the file exporter modules (`generate_word.php`).

---

## System Architecture & Directory Layout

The codebase enforces a clean separation of concerns, isolating public-facing entry points, system configurations, automated tasks, and third-party vendor dependencies:

```text
/server
│
├── /htdocs (Linux: /html)     --> Publicly accessible web document root (Frontend & Backend)
│   ├── /admin                  --> Admin dashboards, analytical tools, and KPI metrics
│   ├── /clients                --> Client onboarding and overview grids
│   ├── /css                    --> Core UI stylesheets and layout configurations
│   ├── /docx                   --> Document generation templates (Word/DOCX templates)
│   ├── /edit_client            --> Administrative client data editing masks
│   ├── /edit_project           --> Project scope modifiers and detail views
│   ├── /edit_time              --> Time tracking log auditing interfaces
│   ├── /js                     --> JavaScript handlers (AJAX cycles, real-time validations)
│   ├── /log                    --> System audit logs and diagnostic displays
│   ├── /login                  --> Secure session authentication pathways
│   ├── /projects               --> Primary project list indexes
│   ├── /register               --> User creation and initial privilege provisioning
│   ├── /settings               --> Global application runtime toggles
│   ├── /users                  --> Identity management and account directories
│   ├── /write_bill             --> Aggregated financial data & invoice previews
│   ├── index.php               --> Main central dashboard and application entry point
│   ├── logout.php              --> Safe session destruction handler
│   └── session_logout.php      --> Background session lifecycle monitor
│
├── /vendor                     --> Third-party PHP dependencies and libraries (e.g., dompdf)
├── /backups                    --> Local secure storage for automated database snapshots
├── backup_cron.php             --> Background cron task engine for automated database archiving
├── config.php                  --> Secure credentials store for database and API connections
├── setup.php                   --> Automated deployment initializer and system installer
└── sql_backup.sql              --> Core relational schema & default table structures
