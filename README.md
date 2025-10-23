# Yomali Website Traffic Tracker (PHP + MySQL)

A lightweight, privacy-friendly tracker to record **unique visits per page** and display them for a selected time period.

- **Server:** PHP 8 + MySQL (PDO)
- **Client:** Tiny JS snippet (`public/tracker.v1.js`) embeddable on any website
- **UI:** Simple, dependency-free dashboard (`/ui/index.html`)
- **Data model:** Unique visitors per page per selected date range (uniqueness based on a persistent `visitor_id` stored in the browser)

---

## ✨ Features

- Embed-and-go tracker `<script src="https://YOUR_HOST/public/tracker.v1.js"></script>`
- Uses a stable UUID in `localStorage` to identify the visitor (no cookies required)
- CORS enabled for cross-site embedding
- Stats API returns **unique** and **total** visits per page for a given date range
- Simple UI to pick a date range and view per-page counts

---

## 🛠️ Quick Start

### 1) Requirements

- PHP 8+ with PDO MySQL extension
- MySQL 5.7+/8
- A web server (Apache/Nginx) or PHP dev server (`php -S`)

### 2) Create Database & Table

Import the schema:

```sql
-- sql/migrations/001_init.sql
CREATE DATABASE IF NOT EXISTS traffic_tracker
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE traffic_tracker;

CREATE TABLE IF NOT EXISTS visits (
  id INT AUTO_INCREMENT PRIMARY KEY,
  page_url VARCHAR(1024) NOT NULL,
  visitor_id CHAR(36) NOT NULL,
  visit_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  user_agent VARCHAR(512) NULL,
  ip_address VARCHAR(45) NULL,
  referer VARCHAR(1024) NULL,
  INDEX idx_page_url (page_url(255)),
  INDEX idx_visit_time (visit_time),
  INDEX idx_visitor_id (visitor_id)
);
```

### 3) Configure PHP

Copy and edit `api/config.sample.php` to `api/config.php` with your DB credentials:

```php
<?php
return [
  'dsn'  => 'mysql:host=127.0.0.1;dbname=traffic_tracker;charset=utf8mb4',
  'user' => 'root',
  'pass' => 'password',
];
```

### 4) Run Locally

From the project root:

```bash
php -S localhost:8000
```

- Tracker will be served at: `http://localhost:8000/public/tracker.v1.js`
- API endpoints at: `http://localhost:8000/api/v1/visits.php` and `http://localhost:8000/api/v1/stats.php`
- UI dashboard at: `http://localhost:8000/ui/index.html`

> If you're using Apache/Nginx, point your document root to the project root (or set up routes).

### 5) Try the Demo

Open **two demo pages** in your browser to generate a few visits:

- `http://localhost:8000/public/demo/page1.html`
- `http://localhost:8000/public/demo/page2.html`

Then view the dashboard: `http://localhost:8000/ui/index.html`

Select a date range and click **Load Stats**.

---

## 📦 Embedding the Tracker on Any Site

Add this to the `<head>` (or end of `<body>`) on the target website pages:

```html
<script src="https://YOUR_HOST/public/tracker.v1.js" async></script>
```

The script captures the current URL as `page_url`, ensures there is a persistent visitor UUID, and posts a visit to `/api/v1/visits.php` using `sendBeacon` (with a `fetch` fallback).

---

## 🔌 Endpoints

### `POST /api/v1/visits.php`

**Body (JSON):**
```json
{
  "page_url": "https://site.com/page",
  "visitor_id": "uuid-v4",
  "referer": "https://google.com"
}
```

**Response:**
```json
{ "ok": true }
```

Notes:
- CORS: `Access-Control-Allow-Origin: *`
- Method: `POST` (JSON)

### `GET /api/v1/stats.php?from=YYYY-MM-DD&to=YYYY-MM-DD`

**Response:**
```json
{
  "from": "2025-10-01",
  "to": "2025-10-21",
  "rows": [
    { "page_url": "https://site.com/page1", "unique_visitors": 7, "total_visits": 12 },
    { "page_url": "https://site.com/about", "unique_visitors": 3, "total_visits": 3 }
  ]
}
```

- If `from`/`to` are omitted, defaults to the **last 7 days**.
- **Uniqueness** is per `visitor_id` within the selected period, **per page**.

---

## 🧩 Versioning

- **API**: Path-based versioning. Current stable is **`/api/v1`** with endpoints:
  - `POST /api/v1/visits.php` — record a visit (noun-based endpoint)
  - `GET  /api/v1/stats.php` — unique/total visits per page
- **Tracker**: SemVer files:
  - `public/tracker-1.0.0.js` (immutable build)
  - `public/tracker.v1.js` → alias to latest `1.x`
  - Recommended embed:
    ```html
    <script src="https://YOUR_HOST/public/tracker.v1.js" async></script>
    ```
- **Deprecations**: During early development you may keep `/api/track.php` as a temporary alias to `/api/v1/visits.php` for backward compatibility.

---

## 🧭 Data Flow

1. **Browser** loads `tracker.v1.js` → ensures a **UUID** is stored in `localStorage`.
2. `tracker.v1.js` posts `visitor_id`, `page_url`, `referer` to `POST /api/v1/visits.php`.
3. PHP stores a row in `visits` with timestamp + optional IP/UA.
4. Dashboard calls `GET /api/v1/stats.php` with `from`/`to` → receives per-page unique counts.

---

## 🧪 Testing

- Open demo pages multiple times / incognito to simulate new visitors.
- Clear `localStorage` to simulate a "new" visitor (or open in another browser/device).
- Use `curl` to test stats:
  ```bash
  curl 'http://localhost:8000/api/v1/stats.php?from=2025-10-01&to=2025-10-21'
  ```

---

## 🔒 Notes on Privacy & Accuracy

- Uses a **first-party UUID** in `localStorage` as a pragmatic unique ID.
- You can enhance accuracy by **salting** and hashing `IP + UA` as a tie-breaker.
- Consider adding **rate limits** and **idempotency** (see article suggestions) to reduce noise and ensure correctness.

---

## 🧰 Bonus Ideas (Optional)

- Line/bar charts for time-series per page (Chart.js)
- Breakdown by device (UA parsing)
- Export CSV
- Docker `docker-compose.yml` for DB + PHP-FPM + Nginx

---

## 🧾 Deliverables Mapping

- ✅ README (this file)
- ✅ Source code: `/public/tracker.v1.js`, `/api/v1/*.php`, `/ui/index.html`
- ✅ Database schema & migrations: `/sql/migrations/001_init.sql`
- ✅ Loom/video: Record a 3–5 min walkthrough:
  - Code tour: tracker → visits endpoint → DB → stats → UI
  - Demo: open demo pages; show dashboard with different date ranges

---

## 📄 License

MIT
