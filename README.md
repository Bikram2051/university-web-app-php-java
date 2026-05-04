# EchoSphere — Premium Home Theatre Systems

A full-stack e-commerce web application for a fictional home theatre brand, built as a university project for **COS60004 — Creating Web Applications** at Swinburne University of Technology.

---

## 🚀 Live Demo

> ### **[Visit the live site →](https://echosphere-7i5.pages.dev/)**

The live demo is a static HTML/CSS/JS portfolio version. Form submissions show confirmation messages (no backend). To see the full PHP/MySQL implementation with database integration, clone this repo and run the PHP version locally (see below).

---

## Two versions in this repo

| Branch | Contents | Purpose |
|---|---|---|
| `main` | PHP/MySQL source code | Full-stack implementation with database |
| `static-portfolio` | Static HTML conversion | Live demo at https://echosphere-7i5.pages.dev/ |

Recruiters viewing the repo land on `main` to see the complete back-end implementation. The live demo showcases the front-end design and interactivity.

---

## Tech stack

### Original PHP/MySQL version (`main` — this branch)
- **PHP 8** — server-side form handling, session management, server-side validation
- **MySQL / MariaDB** — order storage via `mysqli` with prepared statements
- **HTML5 / CSS3** — semantic markup, CSS Grid, Flexbox, `@media print`, CSS keyframe animations
- **Vanilla JavaScript** — hero slider, product comparison panel, room-size audio planner, client-side price calculator

### Static portfolio version (`static-portfolio`)
- **HTML5 / CSS3 / Vanilla JS** — same front-end assets as the original
- **No build tools or frameworks** — deployed directly as static files to Cloudflare Pages

---

## Features

### Front-end
- Responsive layout across mobile, tablet, and desktop
- CSS-only hero image slider (four slides, keyframe animation)
- Product comparison panel — select up to three products for a side-by-side feature view
- Room Size Audio Planner — recommends a product based on room dimensions and auto-fills the enquiry form
- Real-time price calculator (base price × quantity + add-on features)
- Print stylesheet — strips navigation and decorative elements for clean printed output

### Back-end (PHP/MySQL — `main` branch only)
- Multi-step enquiry → payment → receipt flow using PHP sessions
- Server-side validation: regex patterns, Australian state–postcode rule enforcement, credit card format checks
- Order management dashboard (`manager.php`) with full-text search, multi-column sort, and inline status updates
- Four-tier order lifecycle: PENDING → FULFILLED → PAID → ARCHIVED
- Business-rule enforcement: only PENDING orders can be cancelled
- All database queries use `mysqli` prepared statements

---

## Project structure

```
├── index.php / index.html        # Home page — hero slider, feature grid
├── product.php / product.html    # Product catalogue (Aurora, Nexus, Essence)
├── enquire.php / enquire.html    # Enquiry form with validation
├── payment.php / payment.html    # Payment form with order summary
├── receipt.php / receipt.html    # Order confirmation
├── manager.php / manager.html    # Admin order dashboard
├── about.php / about.html        # Developer bio
├── enhancements*.php/html        # Enhancement documentation pages
├── process_enquiry.php           # POST handler — enquiry validation (PHP only)
├── process_order.php             # POST handler — order DB insert (PHP only)
├── update_order.php              # POST handler — status update (PHP only)
├── cancel_order.php              # POST handler — order deletion (PHP only)
├── settings.example.php          # DB config template (copy to settings.php)
├── styles/
│   └── style.css                 # All styles including print and animation
├── scripts/
│   ├── part2.js                  # Slider, price calculator, payment validation
│   └── enhancements.js           # Comparison panel, room size planner
└── images/                       # Product and profile images
```

---

## Running the PHP version locally

**Prerequisites:** PHP 8+, MySQL / MariaDB

```bash
# 1. Clone the repo and check out main
git clone https://github.com/Bikram2051/university-web-app-php-java.git
cd university-web-app-php-java
git checkout main

# 2. Create your local config from the template
cp settings.example.php settings.php
# Edit settings.php and fill in your DB host, user, password, and database name

# 3. Serve with the PHP built-in server
php -S localhost:8000
```

The `orders` table is created automatically on first load if it does not exist.

> **`settings.php` is listed in `.gitignore` and must never be committed.** Use `settings.example.php` as the reference.

---

## Screenshots

| Page | Preview |
|---|---|
| Home | *(add screenshot)* |
| Products | *(add screenshot)* |
| Enquiry form | *(add screenshot)* |
| Payment | *(add screenshot)* |
| Manager dashboard | *(add screenshot)* |

---

## Deployment

### Static portfolio (Cloudflare Pages)

The `static-portfolio` branch is deployed to **https://echosphere-7i5.pages.dev/** with no build step:

- **Branch:** `static-portfolio`
- **Build command:** *(none)*
- **Output directory:** `public`

### PHP version (local or your own server)

Deploy to any PHP 8+ host with database access:

1. Copy the PHP files from `main` branch to your server
2. Create `settings.php` with your database credentials
3. Ensure `mysqli` extension is enabled
4. Database tables are auto-created on first page load

---

## Author

**Bikram Bhattarai** — COS60004, Swinburne University of Technology