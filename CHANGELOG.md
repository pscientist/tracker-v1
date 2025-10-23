# Changelog

## 1.0.0 — Initial stable
- PHP + MySQL tracker with public ingest and stats endpoints
- Demo pages and dashboard UI

## 1.1.0 — API v1 and Tracker versioning
- Switched to noun-based endpoint: `POST /api/v1/visits.php`
- `GET /api/v1/stats.php` for analytics
- Added `public/tracker-1.0.0.js` and `public/tracker.v1.js`
- Introduced `sql/migrations/001_init.sql`
- (Optional) Kept `/api/track.php` as a temporary compatibility shim
