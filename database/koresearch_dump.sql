SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `koresearch`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `koresearch`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','instructor','student') NOT NULL DEFAULT 'student',
  `avatar` varchar(255) DEFAULT NULL,
  `headline` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `instructor_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `enrolled_count` int NOT NULL DEFAULT '0',
  `rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `duration` varchar(255) DEFAULT NULL,
  `topics` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courses_slug_unique` (`slug`),
  KEY `courses_instructor_id_foreign` (`instructor_id`),
  CONSTRAINT `courses_instructor_id_foreign`
    FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `transaction_number` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_course_id_foreign` (`course_id`),
  CONSTRAINT `orders_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_course_id_foreign`
    FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` VALUES
(1,'2024_01_01_000001_create_users_table',1),
(2,'2024_01_01_000002_create_courses_table',1),
(3,'2024_01_01_000003_create_orders_table',1);

INSERT INTO `users` (`id`,`name`,`email`,`email_verified_at`,`password`,`role`,`avatar`,`headline`,`bio`,`location`,`phone`,`remember_token`,`created_at`,`updated_at`) VALUES
(1,'Admin User','admin@koresearch.com','2024-01-01 00:00:00','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin',NULL,'Platform Administrator','Managing the KoreSearch platform.','Dhaka, Bangladesh',NULL,NULL,'2024-01-01 00:00:00','2024-01-01 00:00:00'),
(2,'Ataur Rahman Sakib','instructor@koresearch.com','2024-01-01 00:00:00','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','instructor',NULL,'Senior Web Developer & Instructor','Full-stack developer with 8+ years of experience. Teaching Laravel, React and Vue on KoreSearch.','Dhaka, Bangladesh',NULL,NULL,'2024-01-01 00:00:00','2024-01-01 00:00:00'),
(3,'Student User','student@koresearch.com','2024-01-01 00:00:00','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','student',NULL,'Aspiring Developer','Learning web development on KoreSearch.','Chittagong, Bangladesh',NULL,NULL,'2024-01-15 09:00:00','2024-01-15 09:00:00');

INSERT INTO `courses` (`id`,`instructor_id`,`title`,`slug`,`description`,`thumbnail`,`category`,`level`,`price`,`is_published`,`enrolled_count`,`rating`,`duration`,`topics`,`created_at`,`updated_at`) VALUES
(1,2,'Complete Laravel 10 for Beginners','complete-laravel-10-beginners','Learn Laravel 10 from scratch. Build real-world applications with PHP and the most popular MVC framework.','https://placehold.co/800x450','Backend','beginner',0.00,1,142,4.70,'12 hours','["Introduction to Laravel and MVC","Routing and Controllers","Blade Templating Engine","Eloquent ORM and Migrations","Authentication and Authorization","Building REST APIs with Laravel"]','2024-01-01 00:00:00','2024-01-01 00:00:00'),
(2,2,'Vue.js 3 Complete Guide','vuejs-3-complete-guide','Master Vue.js 3 with Composition API, Pinia, and Vue Router. Build reactive, component-based applications.','https://placehold.co/800x450','Frontend','intermediate',1500.00,1,89,4.50,'10 hours','["Vue.js Fundamentals and Setup","Component Architecture","Composition API Deep Dive","State Management with Pinia","Vue Router for SPAs"]','2024-02-01 00:00:00','2024-02-01 00:00:00'),
(3,2,'MySQL Database Design Mastery','mysql-database-design-mastery','Learn database design principles, normalization, indexing, and query optimization with MySQL 8.','https://placehold.co/800x450','Database','intermediate',1200.00,1,67,4.30,'8 hours','["Relational Database Concepts","Schema Design and Normalization","Indexes and Query Optimization","Stored Procedures and Triggers","Backup and Recovery Strategies"]','2024-02-15 00:00:00','2024-02-15 00:00:00'),
(4,2,'React.js for Modern Web Development','reactjs-modern-web-development','Build powerful SPAs with React 18, hooks, context API, and modern tooling.','https://placehold.co/800x450','Frontend','beginner',0.00,1,210,4.80,'14 hours','["JSX and Component Basics","Props, State and Lifecycle","React Hooks In Depth","Context API and Global State","Fetching Data and Side Effects"]','2023-12-01 00:00:00','2023-12-01 00:00:00');

INSERT INTO `orders` (`id`,`user_id`,`course_id`,`transaction_number`,`amount`,`status`,`created_at`,`updated_at`) VALUES
(1,3,1,'8NK2031ABC',0.00,'completed','2024-03-10 11:30:00','2024-03-10 11:30:00');

SET FOREIGN_KEY_CHECKS = 1;
