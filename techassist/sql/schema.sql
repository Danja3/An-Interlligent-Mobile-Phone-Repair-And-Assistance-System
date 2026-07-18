-- TechAssist — MySQL schema (vanilla PHP/MySQL edition)
-- Import this first, then seed.sql.
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS technician_skills;
DROP TABLE IF EXISTS technician_profiles;
DROP TABLE IF EXISTS diagnoses;
DROP TABLE IF EXISTS symptoms;
DROP TABLE IF EXISTS problem_categories;
DROP TABLE IF EXISTS phone_models;
DROP TABLE IF EXISTS phone_brands;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('customer','technician','admin') NOT NULL DEFAULT 'customer',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE phone_brands (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  slug VARCHAR(80) NOT NULL UNIQUE,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE phone_models (
  id INT AUTO_INCREMENT PRIMARY KEY,
  brand_id INT NOT NULL,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  release_year INT NULL,
  UNIQUE(brand_id, slug),
  FOREIGN KEY (brand_id) REFERENCES phone_brands(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE problem_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  slug VARCHAR(80) NOT NULL UNIQUE,
  icon VARCHAR(40) NULL,
  description VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE symptoms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  description VARCHAR(255) NULL,
  FOREIGN KEY (category_id) REFERENCES problem_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE diagnoses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  symptom_id INT NOT NULL,
  model_id INT NULL,
  probable_cause TEXT NOT NULL,
  diy_solution TEXT NULL,
  cost_min INT NOT NULL DEFAULT 0,
  cost_max INT NOT NULL DEFAULT 0,
  currency CHAR(3) NOT NULL DEFAULT 'NGN',
  severity ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  required_skill VARCHAR(80) NULL,
  requires_technician TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (symptom_id) REFERENCES symptoms(id) ON DELETE CASCADE,
  FOREIGN KEY (model_id)   REFERENCES phone_models(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_diag_symptom_model ON diagnoses(symptom_id, model_id);

CREATE TABLE technician_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  business_name VARCHAR(120) NOT NULL,
  bio TEXT NULL,
  shop_address VARCHAR(200) NOT NULL,
  city VARCHAR(80) NOT NULL,
  whatsapp_number VARCHAR(20) NOT NULL,
  photo_url VARCHAR(255) NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  avg_rating DECIMAL(3,2) NOT NULL DEFAULT 0,
  review_count INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_tech_status_city ON technician_profiles(status, city);

CREATE TABLE technician_skills (
  id INT AUTO_INCREMENT PRIMARY KEY,
  technician_id INT NOT NULL,
  skill VARCHAR(80) NOT NULL,
  UNIQUE(technician_id, skill),
  FOREIGN KEY (technician_id) REFERENCES technician_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  technician_id INT NOT NULL,
  diagnosis_id INT NULL,
  symptom_label VARCHAR(150) NULL,
  model_label VARCHAR(120) NULL,
  customer_notes TEXT NULL,
  technician_notes TEXT NULL,
  status ENUM('requested','accepted','declined','in_progress','completed','cancelled')
         NOT NULL DEFAULT 'requested',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id)   REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (technician_id) REFERENCES technician_profiles(id) ON DELETE CASCADE,
  FOREIGN KEY (diagnosis_id)  REFERENCES diagnoses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_book_customer ON bookings(customer_id);
CREATE INDEX idx_book_tech ON bookings(technician_id);

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL UNIQUE,
  technician_id INT NOT NULL,
  customer_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comment TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id)    REFERENCES bookings(id) ON DELETE CASCADE,
  FOREIGN KEY (technician_id) REFERENCES technician_profiles(id) ON DELETE CASCADE,
  FOREIGN KEY (customer_id)   REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_review_tech ON reviews(technician_id);

CREATE TABLE password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token CHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
