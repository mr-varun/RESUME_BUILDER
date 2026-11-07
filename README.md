# ResumeForge

A self-hosted PHP web app for building resumes, CVs, and biodata documents — fill in a guided wizard, pick from 15 professional templates, and export a polished PDF.

## Features

- **Guided wizard** – step-by-step form for experience, education, skills, and photo upload, with autosave as you go
- **15 ready-made templates** – including classic, modern, minimal, corporate, executive, creative, tech, timeline, and dedicated Indian marriage-biodata layouts
- **PDF export** – server-side rendering via [mpdf](https://mpdf.github.io/)
- **Shareable public links** – publish a document behind a short, expiring slug for others to view online
- **Quick mode** – a lightweight flow for print-shop style walk-in customers who just need a fast printout
- **Accounts & dashboard** – registration, login, and a dashboard to manage all of a user's saved documents
- **Document preview** – review a document in-browser before exporting or sharing it

## Tech stack

- PHP 8+ (no framework — plain PSR-4 autoloaded classes under `app/`)
- MySQL (schema in [database.sql](database.sql))
- [mpdf/mpdf](https://mpdf.github.io/) for PDF generation
- Vanilla JS/CSS on the frontend ([assets/](assets/))

## Getting started

1. Install dependencies:
   ```bash
   composer install
   ```
2. Create the database:
   ```bash
   mysql -u root -p < database.sql
   ```
3. Copy the config template and fill in your own DB credentials:
   ```bash
   cp config/config.example.php config/config.php
   ```
4. Point your webserver (or `php -S localhost:8000`) at the project root and open it in a browser. `install.php` can walk you through first-time setup.

A demo account is seeded from `database.sql`: `demo@example.com`.

## Project structure

```
api/        AJAX endpoints (autosave, upload, PDF export, sharing, preview)
app/        Core classes (Auth, CSRF, Document, Helpers)
assets/     CSS and JS
config/     App + DB configuration
templates/  The 15 document templates
uploads/    User-uploaded photos (gitignored)
```

## License

MIT — see [LICENSE](LICENSE).
