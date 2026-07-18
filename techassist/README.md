# TechAssist — Vanilla PHP/MySQL Edition

A phone-diagnosis & repair-technician marketplace localized for **Northern Nigeria**, built in
**pure HTML, CSS, JavaScript, PHP and MySQL** — no Composer, npm, frameworks, or libraries.

- 🔍 Diagnostic wizard (brand → model → problem → symptom) with ₦ repair-cost estimates
- 🛠️ Verified technician directory (filter by city & skill) + WhatsApp contact + bookings
- 👩‍🔧 Technician dashboard (profile/skills/photo + booking lifecycle)
- 🛡️ Admin technician approvals · ⭐ customer reviews & ratings · 📱 installable PWA

## Requirements
XAMPP/WAMP/LAMP with **PHP 8.1+** (GD enabled) and **MySQL/MariaDB**.

## Setup
1. Copy this folder into your web root (e.g. `C:\xampp\htdocs\techassist`).
2. Create the database and import the SQL (schema first, then seed):
   ```bash
   mysql -u root -e "CREATE DATABASE techassist CHARACTER SET utf8mb4"
   mysql -u root techassist < sql/schema.sql
   mysql -u root techassist < sql/seed.sql
   ```
   (Or import both files via phpMyAdmin.)
3. Copy `includes/config.sample.php` → `includes/config.php` and set DB credentials + `BASE_URL`
   (`/techassist` for Apache, `` empty for PHP's built-in server at root).
4. Ensure `uploads/` is writable.
5. Open **http://localhost/techassist/**.

## Demo accounts
| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@techassist.ng` | `Admin@1234` |
| Customer | `aisha@techassist.ng` | `Demo@1234` |
| Customer | `musa@techassist.ng` | `Demo@1234` |
| Technician (Kano, has bookings) | `demo.kano@techassist.ng` | `Demo@1234` |
| Technicians (Kaduna/Maiduguri/Sokoto/Zaria/Jos) | `demo.<city>@techassist.ng` | `Demo@1234` |
| Technician (pending approval) | `pending.gombe@techassist.ng` | `Demo@1234` |

> Remove or change these demo accounts before any real deployment.

## Project structure
```
includes/   config, db (PDO), auth (sessions/roles), helpers, header, footer
assets/     css/styles.css (Northern Nigeria palette), js/app.js, img/icon.svg
sql/        schema.sql, seed.sql
uploads/    technician photos (GD-resized, no PHP execution)
*.php       pages (index, diagnose, result, technicians, technician, book,
            login/register/logout, forgot/reset-password, become-technician,
            dashboard, account, bookings, admin)
manifest.webmanifest · sw.js · offline.html   (PWA)
```

## Security
PDO prepared statements; all output escaped; CSRF tokens on every POST; `password_hash`/`verify`;
GD-validated & resized image uploads in a no-exec folder; Nigerian phone normalization; server-side
role checks on every protected page.

## Design
Northern Nigeria palette — **Kano indigo**, **Sahel gold**, **terracotta**, **green** on warm
**sand** — high-contrast and mobile-first for sunlight readability and low-end Android phones.
