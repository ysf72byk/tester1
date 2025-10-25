CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `specialty_id` int(11) NOT NULL,
  `description` text,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `specialty_id` (`specialty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `specialties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `doctor_id` (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `comment` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `doctor_id` (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctors_ibfk_2` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE CASCADE;

ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;
