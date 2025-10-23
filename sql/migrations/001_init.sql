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
