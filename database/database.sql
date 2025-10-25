-- Doktor Sepeti Veritabanı Şeması
-- Bu dosya, phpMyAdmin üzerinden "İçeri Aktar" özelliği kullanılarak
-- projenin veritabanı tablolarını oluşturmak için kullanılır.
-- Versiyon: 1.0

-- Karakter setini ve karşılaştırmayı ayarla
SET NAMES utf8mb4;
SET time_zone = '+03:00';

--
-- Tablo yapısı: `Users` (Kullanıcılar)
--
CREATE TABLE IF NOT EXISTS `Users` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `isim` VARCHAR(255) NOT NULL,
  `soyisim` VARCHAR(255) NOT NULL,
  `e_posta` VARCHAR(255) NOT NULL UNIQUE,
  `sifre_hash` VARCHAR(255) NOT NULL,
  `telefon` VARCHAR(255),
  `rol` VARCHAR(255) NOT NULL DEFAULT 'user', -- 'user', 'doctor', 'admin'
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo yapısı: `Doctors` (Doktorlar)
--
CREATE TABLE IF NOT EXISTS `Doctors` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `unvan` VARCHAR(255),
  `uzmanlik_alani` VARCHAR(255) NOT NULL,
  `deneyim_yili` INT,
  `hakkinda` TEXT,
  `adres` VARCHAR(255),
  `sehir` VARCHAR(255),
  `ilce` VARCHAR(255),
  `muayene_ucreti` DECIMAL(10, 2),
  `user_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo yapısı: `Appointments` (Randevular)
--
CREATE TABLE IF NOT EXISTS `Appointments` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `randevu_tarihi` DATETIME NOT NULL,
  `durum` VARCHAR(255) NOT NULL DEFAULT 'beklemede', -- 'beklemede', 'onaylandi', 'iptal edildi'
  `hasta_notu` TEXT,
  `user_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `Doctors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo yapısı: `Reviews` (Yorumlar)
--
CREATE TABLE IF NOT EXISTS `Reviews` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `puan` INT NOT NULL, -- 1 ile 5 arasında bir değer
  `yorum_metni` TEXT,
  `user_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `Doctors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sequelize Meta Tablosu (Migrasyonları takip etmek için gereklidir)
--
CREATE TABLE IF NOT EXISTS `SequelizeMeta` (
  `name` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
