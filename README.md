# The Code Munk — Ed-Tech Platform (PHP + MySQL)

The original marketing site (HTML/CSS by the design developer) is preserved
**exactly as-is** and served as the public website. On top of it sits a full PHP
+ MySQL application:

- **Public site** — the original static pages (`index.html`, `programs.html`,
  `course-details.html`, `event-details.html`, `insights.html`, `community.html`,
  `contact.html`). A small `assets/tcm-app.js` wires them to the backend
  (auth-aware "Join Now", contact form → API, WhatsApp click-to-chat) **without
  changing the design**.
- **Admin dashboard** (`/admin`) — CMS for courses, programs, live classes,
  events, insights, testimonials, categories, students, leads, internship
  applications, messages and settings.
- **Student dashboard** (`/student`) — onboarding, courses + lesson progress,
  programs & live sessions, events, internship applications, portfolio.
- **No online payments** — every enrol/enquire becomes a **lead** and a
  **WhatsApp** hand-off (number-to-number). Admin converts leads to enrolments.
- **JSON API** (`/api/...`) used by the static site and available headless.

Plain PHP 8.1+, no framework, no Composer dependencies.

---

## Project structure (web root = project root)
```
tcm-2.0/                      <- upload these contents into public_html
├── index.html ... *.html     # original design (served as-is)
├── assets/                   # original CSS + dashboard.css + tcm-app.js
├── uploads/                  # public uploads (writable)
├── app.php                   # front controller for dynamic routes
├── setup.php                 # one-time web installer (delete after use)
├── .htaccess                 # routes app paths to app.php, blocks internals
├── router.php                # local dev server only (php -S)
├── .env.example              # copy to .env
├── src/                      # PHP app (Core, Models, Controllers) — blocked from web
├── config/  database/  views/  storage/  bin/   # blocked from web
└── README.md
```
`src`, `config`, `database`, `views`, `storage`, `bin` and `.env` are denied
direct web access by `.htaccess`. Résumés live in `storage/` (never public).

---

## Deploy to Hostinger (no SSH needed)

1. **Pick PHP 8.1+** — hPanel → *Advanced → PHP Configuration* (ensure `pdo_mysql`
   is on; it is by default).
2. **Create a database** — hPanel → *Databases → MySQL Databases*. Note the DB
   name, user and password (host is usually `localhost`).
3. **Upload the files** — put everything in this folder into `public_html/`
   (File Manager → Upload, or unzip there).
4. **Configure** — copy `.env.example` to `.env` and fill in the DB credentials,
   `APP_URL`, a random `APP_KEY`, and keep `APP_BASE_PATH` empty (root domain).
5. **Install the database** — open `https://your-domain.com/setup.php`, click
   *Run installation* (leave "Load demo data" checked for the first run).
6. **Delete `setup.php`** from the server (the installer reminds you).
7. Visit your domain — the homepage is the original design. Sign in at
   `/auth/login`, then set your **WhatsApp number** in Admin → Settings.

> Prefer phpMyAdmin? Instead of step 5, import `database/schema.sql` then
> `database/seed.sql` from hPanel → *Databases → phpMyAdmin*.

### Default logins (change immediately)
| Role    | Email                      | Password   |
|---------|----------------------------|------------|
| Admin   | admin@thecodemunk.com      | admin123   |
| Student | student@thecodemunk.com    | student123 |

---

## Local development
```bash
cp .env.example .env            # point to a local MySQL
php bin/install.php             # or: php bin/install.php --fresh
php -S 127.0.0.1:8000 router.php
# open http://127.0.0.1:8000
```

## Email (Gmail SMTP)
The platform sends real email via Gmail SMTP (a small built-in SMTP client,
no dependencies). Configure `MAIL_*` in `.env` with a Gmail **App Password**.
Powers:
- **OTP login / passwordless sign-up** and **password reset** in the site's
  auth modal (the designer's UI, now wired to the backend)
- **Welcome** email on sign-up
- **Contact** form: admin notification + acknowledgement to the sender
- **Lead** notifications to the team
- **Internship**: acknowledgement to the applicant, notification to the team,
  and a status-update email on shortlist/select/reject

Test delivery from the server with:
```bash
php bin/test-mail.php you@example.com
```

## How enrolment works (lead + WhatsApp, no simulated payment)
```
Visitor clicks Enrol / Enquire  ->  lead saved in DB  ->  Admin → Leads
                                 ->  opens WhatsApp (their phone → TCM number)
Admin "Convert" on a lead        ->  enrols the student + records a paid order
```
Free courses/events enrol instantly; paid ones go through the lead + WhatsApp
flow. Set the business WhatsApp number in Admin → Settings.

## Security notes
- bcrypt passwords, CSRF on all web forms, session-id regeneration, prepared
  statements, output escaping, secure cookies on HTTPS.
- No card data is handled (payment settled over WhatsApp), avoiding PCI scope.
- Set `APP_DEBUG=false` and a strong `APP_KEY` in production; delete `setup.php`.
```
