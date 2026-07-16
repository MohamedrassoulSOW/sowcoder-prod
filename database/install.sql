-- Installation complète SowCoder (WAMP / MySQL)
CREATE DATABASE IF NOT EXISTS `sowcoder`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `sowcoder`;

CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(180) NOT NULL,
  `phone` VARCHAR(40) DEFAULT NULL,
  `subject` VARCHAR(160) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `ip` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contact_created_at` (`created_at`),
  KEY `idx_contact_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(180) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Compte admin : admin@sowcoder.com / Admin123!
INSERT INTO `users` (`name`, `email`, `password`, `role`)
SELECT 'Administrateur', 'admin@sowcoder.com', '$2y$12$dYIquwmKaN3AXrA3JoM1k.mehmbzy/jvAm67bT5.E6pVfbetHkI6a', 'admin'
WHERE NOT EXISTS (
  SELECT 1 FROM `users` WHERE `email` = 'admin@sowcoder.com'
);

-- Tables site_* : voir site_tables.sql puis php scripts/seed-site.php
