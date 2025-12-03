-- SQL Script untuk Proyek Lost and Found
-- Dibuat berdasarkan skema database yang telah Anda desain.

-- SET FOREIGN_KEY_CHECKS=0; -- Opsional: Nonaktifkan cek kunci asing sementara saat import

-- --------------------------------------------------------
-- Struktur Tabel untuk USERS
-- --------------------------------------------------------
CREATE TABLE `USERS` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `photo_profile` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('user','admin') NOT NULL DEFAULT 'user',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Struktur Tabel untuk REPORTS
-- --------------------------------------------------------
CREATE TABLE `REPORTS` (
  `report_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `type` ENUM('lost','found') NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `location_text` VARCHAR(255) DEFAULT NULL,
  `date_event` DATE DEFAULT NULL,
  `status` ENUM('active','resolved','archived') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_id`),
  KEY `fk_user_id` (`user_id`), -- Indeks untuk Kunci Asing
  CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `USERS` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Struktur Tabel untuk REPORT_PHOTOS
-- --------------------------------------------------------
CREATE TABLE `REPORT_PHOTOS` (
  `photo_id` INT(11) NOT NULL AUTO_INCREMENT,
  `report_id` INT(11) NOT NULL,
  `image_path` VARCHAR(255) NOT NULL COMMENT 'Lokasi file foto',
  PRIMARY KEY (`photo_id`),
  KEY `fk_report_id` (`report_id`), -- Indeks untuk Kunci Asing
  CONSTRAINT `fk_report_id` FOREIGN KEY (`report_id`) REFERENCES `REPORTS` (`report_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SET FOREIGN_KEY_CHECKS=1; -- Opsional: Aktifkan kembali cek kunci asing