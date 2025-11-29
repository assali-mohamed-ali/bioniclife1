-- CREATE_DATABASE.SQL
-- Script to create the database schema for BionicLife (brasbionique)
-- Run this in phpMyAdmin or MySQL CLI

-- 0) Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `brasbionique` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `brasbionique`;

-- 1) Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `firstname` VARCHAR(150) NOT NULL,
  `lastname` VARCHAR(150) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `phone` VARCHAR(50) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Products table
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `sku` VARCHAR(100) DEFAULT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `stock` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Payments table (stores non-sensitive metadata only)
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `card_holder` VARCHAR(255) NOT NULL,
  `card_last4` VARCHAR(4) DEFAULT NULL,
  `card_masked` VARCHAR(64) DEFAULT NULL,
  `expiry_month` TINYINT DEFAULT NULL,
  `expiry_year` SMALLINT DEFAULT NULL,
  `amount` DECIMAL(10,2) DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional: example seed data (products)
-- INSERT statements can be run after creating the tables (see sql/seed_products.sql)

-- Optional: create an initial admin user
-- IMPORTANT: generate the password hash first (see instructions below), then paste it into <PASSWORD_HASH> and run the INSERT.
-- Example (do NOT use the plain password in production):
-- INSERT INTO users (firstname, lastname, email, phone, password, role) VALUES ('Admin','Principal','admin@example.com','000000000','<PASSWORD_HASH>','admin');

-- End of create_database.sql
