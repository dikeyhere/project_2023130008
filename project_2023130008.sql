-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versi server:                 8.0.30 - MySQL Community Server - GPL
-- OS Server:                    Win64
-- HeidiSQL Versi:               12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Membuang struktur basisdata untuk project_2023130008
DROP DATABASE IF EXISTS `project_2023130008`;
CREATE DATABASE IF NOT EXISTS `project_2023130008` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `project_2023130008`;

-- membuang struktur untuk table project_2023130008.expenses
DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `expense_date` date NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_project_id_foreign` (`project_id`),
  KEY `expenses_user_id_foreign` (`user_id`),
  KEY `expenses_approved_by_foreign` (`approved_by`),
  CONSTRAINT `expenses_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.expenses: ~8 rows (lebih kurang)
DELETE FROM `expenses`;
INSERT INTO `expenses` (`id`, `project_id`, `user_id`, `category`, `amount`, `description`, `expense_date`, `status`, `approved_by`, `created_at`, `updated_at`) VALUES
	(1, 14, 1, 'Pembelian Server', 1000000.00, 'Melakukan pembelian server untuk hosting', '2025-12-18', 'pending', NULL, '2025-12-18 08:01:34', '2025-12-18 08:01:34'),
	(2, 14, 1, 'Pembelian Server', 1000000.00, 'Melakukan pembelian server untuk hosting', '2025-12-18', 'pending', NULL, '2025-12-18 08:51:41', '2025-12-18 08:51:41'),
	(3, 14, 1, 'Pembelian Server', 1000000.00, 'Melakukan pembelian server untuk hosting', '2025-12-18', 'pending', NULL, '2025-12-18 08:52:36', '2025-12-18 08:52:36'),
	(4, 14, 1, 'Pembelian Server', 1000000.00, 'Melakukan pembelian server untuk hosting', '2025-12-18', 'rejected', 1, '2025-12-18 09:06:29', '2025-12-18 12:12:51'),
	(5, 15, 3, 'Beli Semen', 500000.00, 'Beli Semen 50 karung', '2025-12-19', 'approved', 1, '2025-12-18 11:02:26', '2025-12-18 11:39:06'),
	(6, 15, 3, 'Beli Makan', 50000.00, 'Pengen beli makan', '2025-12-19', 'rejected', 1, '2025-12-18 11:39:59', '2025-12-18 11:40:12'),
	(7, 15, 2, 'Beli Pasir', 700000.00, 'Beli Pasir', '2025-12-19', 'pending', NULL, '2025-12-18 12:01:57', '2025-12-18 12:01:57'),
	(8, 2, 2, 'Beli PC', 2500000.00, NULL, '2025-12-19', 'approved', 2, '2025-12-18 12:13:27', '2025-12-18 12:13:43');

-- membuang struktur untuk table project_2023130008.failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.failed_jobs: ~0 rows (lebih kurang)
DELETE FROM `failed_jobs`;

-- membuang struktur untuk table project_2023130008.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.migrations: ~14 rows (lebih kurang)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2025_10_06_044245_add_role_to_users_table', 1),
	(6, '2025_10_06_045534_create_projects_table', 2),
	(7, '2025_10_06_045707_create_tasks_table', 2),
	(8, '2025_10_14_105304_add_fields_to_tasks_table', 3),
	(9, '2025_10_14_151636_add_avatar_to_users_table', 4),
	(10, '2025_10_16_075822_add_deadline_to_projects_table', 5),
	(11, '2025_10_16_170355_create_project_user_table', 6),
	(12, '2025_10_17_143409_add_priority_to_projects_table', 7),
	(13, '2025_10_18_025247_add_priority_to_tasks_table', 8),
	(14, '2025_10_18_030058_add_submission_fields_to_tasks_table', 9),
	(15, '2025_10_18_072922_add_team_leader_to_projects_table', 10),
	(16, '2025_10_19_161444_add_profile_fields_to_users_table', 11),
	(17, '2025_11_07_024655_create_permission_tables', 12),
	(18, '2025_12_18_111348_create_expenses_table', 13);

-- membuang struktur untuk table project_2023130008.model_has_permissions
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.model_has_permissions: ~0 rows (lebih kurang)
DELETE FROM `model_has_permissions`;

-- membuang struktur untuk table project_2023130008.model_has_roles
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.model_has_roles: ~6 rows (lebih kurang)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 1),
	(2, 'App\\Models\\User', 2),
	(3, 'App\\Models\\User', 3),
	(1, 'App\\Models\\User', 4),
	(2, 'App\\Models\\User', 5),
	(3, 'App\\Models\\User', 6);

-- membuang struktur untuk table project_2023130008.password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.password_reset_tokens: ~0 rows (lebih kurang)
DELETE FROM `password_reset_tokens`;
INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
	('holy.poly.life@gmail.com', '$2y$10$d1o7/TMIU.katRxgm4Ged.DyvEe.LjEjK/479nDWhvGjb82OrG1eC', '2025-11-20 18:48:15');

-- membuang struktur untuk table project_2023130008.permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.permissions: ~30 rows (lebih kurang)
DELETE FROM `permissions`;
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'create project', 'web', '2025-11-13 19:21:19', '2025-11-13 19:21:19'),
	(2, 'edit project', 'web', '2025-11-13 19:21:19', '2025-11-13 19:21:19'),
	(3, 'delete project', 'web', '2025-11-13 19:21:19', '2025-11-13 19:21:19'),
	(4, 'view project', 'web', '2025-11-13 19:21:19', '2025-11-13 19:21:19'),
	(5, 'view projects', 'web', '2025-11-13 19:28:08', '2025-11-13 19:28:08'),
	(6, 'create projects', 'web', '2025-11-13 19:28:08', '2025-11-13 19:28:08'),
	(7, 'edit projects', 'web', '2025-11-13 19:28:08', '2025-11-13 19:28:08'),
	(8, 'delete projects', 'web', '2025-11-13 19:28:08', '2025-11-13 19:28:08'),
	(9, 'view tasks', 'web', '2025-11-13 19:28:08', '2025-11-13 19:28:08'),
	(10, 'create tasks', 'web', '2025-11-13 19:28:08', '2025-11-13 19:28:08'),
	(11, 'edit tasks', 'web', '2025-11-13 19:28:08', '2025-11-13 19:28:08'),
	(12, 'delete tasks', 'web', '2025-11-13 19:28:08', '2025-11-13 19:28:08'),
	(13, 'assign tasks', 'web', '2025-11-13 19:28:08', '2025-11-13 19:28:08'),
	(14, 'view reports', 'web', '2025-11-13 19:28:08', '2025-11-13 19:28:08'),
	(15, 'export reports', 'web', '2025-11-13 19:28:08', '2025-11-13 19:28:08'),
	(16, 'view dashboard', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(17, 'view project detail', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(18, 'upload task file', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(19, 'update task progress', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(20, 'submit task', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(21, 'view profile', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(22, 'edit profile', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(23, 'delete profile', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(24, 'view financial', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(25, 'submit expense', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(26, 'approve expense', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(27, 'reject expense', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(28, 'manage permissions', 'web', '2025-12-18 12:35:10', '2025-12-18 12:35:10'),
	(29, 'view own projects', 'web', '2025-12-18 20:34:55', '2025-12-18 20:34:55'),
	(30, 'view assigned tasks', 'web', '2025-12-18 20:34:55', '2025-12-18 20:34:55'),
	(31, 'export reports pdf', 'web', '2025-12-18 20:36:09', '2025-12-18 20:36:09'),
	(32, 'export reports excel', 'web', '2025-12-18 20:36:09', '2025-12-18 20:36:09'),
	(33, 'update profile', 'web', '2025-12-18 23:03:33', '2025-12-18 23:03:33'),
	(34, 'change password', 'web', '2025-12-18 23:03:33', '2025-12-18 23:03:33');

-- membuang struktur untuk table project_2023130008.personal_access_tokens
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.personal_access_tokens: ~0 rows (lebih kurang)
DELETE FROM `personal_access_tokens`;

-- membuang struktur untuk table project_2023130008.projects
DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `deadline` date DEFAULT NULL,
  `status` enum('Planning','In Progress','Completed','On Hold') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Planning',
  `priority` enum('high','medium','low') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'low',
  `budget` decimal(15,0) DEFAULT '0',
  `team_leader_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_created_by_foreign` (`created_by`),
  KEY `projects_team_leader_id_foreign` (`team_leader_id`),
  CONSTRAINT `projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_team_leader_id_foreign` FOREIGN KEY (`team_leader_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.projects: ~7 rows (lebih kurang)
DELETE FROM `projects`;
INSERT INTO `projects` (`id`, `name`, `description`, `deadline`, `status`, `priority`, `budget`, `team_leader_id`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 'Proyek Web App', 'Bangun aplikasi web', '2025-10-28', 'In Progress', 'high', 0, 2, 1, '2025-10-07 20:07:32', '2025-10-18 07:38:20'),
	(2, 'Proyek Mobile', 'Aplikasi mobile', '2025-12-31', 'In Progress', 'low', 3000000, 2, 2, '2025-10-07 20:07:32', '2025-12-18 12:12:38'),
	(3, 'Proyek Database', 'Optimasi DB', '2025-11-01', 'Completed', 'medium', 0, 2, 1, '2025-10-07 20:07:32', '2025-10-07 20:07:32'),
	(4, 'Proyek UI/UX', 'Desain interface', '2025-10-17', 'In Progress', 'low', 0, 2, 2, '2025-10-07 20:07:32', '2025-10-07 20:07:32'),
	(5, 'Proyek Testing', 'QA testing', '2025-11-06', 'On Hold', 'low', 0, 5, 1, '2025-10-07 20:07:32', '2025-10-07 20:07:32'),
	(14, 'Proyek ADG', 'Membuat karakter', '2025-11-01', 'Completed', 'medium', 0, 5, 4, '2025-10-20 08:11:43', '2025-10-20 08:20:49'),
	(15, 'Proyek Candi Borobudur', 'Membangun candi borobudur dalam satu malam', '2026-01-01', 'Planning', 'high', 1000000, 2, 1, '2025-12-04 18:41:57', '2025-12-18 10:02:12');

-- membuang struktur untuk table project_2023130008.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.roles: ~4 rows (lebih kurang)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'web', '2025-11-12 18:40:42', '2025-11-12 18:40:42'),
	(2, 'ketua_tim', 'web', '2025-11-12 18:40:43', '2025-11-12 18:40:43'),
	(3, 'anggota_tim', 'web', '2025-11-12 18:40:43', '2025-11-12 18:40:43');

-- membuang struktur untuk table project_2023130008.role_has_permissions
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.role_has_permissions: ~66 rows (lebih kurang)
DELETE FROM `role_has_permissions`;
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 1),
	(2, 1),
	(3, 1),
	(4, 1),
	(5, 1),
	(6, 1),
	(7, 1),
	(8, 1),
	(9, 1),
	(10, 1),
	(11, 1),
	(12, 1),
	(13, 1),
	(14, 1),
	(15, 1),
	(16, 1),
	(17, 1),
	(18, 1),
	(19, 1),
	(20, 1),
	(21, 1),
	(22, 1),
	(23, 1),
	(24, 1),
	(25, 1),
	(26, 1),
	(27, 1),
	(28, 1),
	(29, 1),
	(30, 1),
	(31, 1),
	(32, 1),
	(33, 1),
	(34, 1),
	(5, 2),
	(6, 2),
	(7, 2),
	(9, 2),
	(10, 2),
	(11, 2),
	(14, 2),
	(15, 2),
	(16, 2),
	(17, 2),
	(21, 2),
	(22, 2),
	(24, 2),
	(25, 2),
	(26, 2),
	(27, 2),
	(29, 2),
	(31, 2),
	(32, 2),
	(34, 2),
	(5, 3),
	(9, 3),
	(16, 3),
	(17, 3),
	(18, 3),
	(19, 3),
	(20, 3),
	(21, 3),
	(22, 3),
	(25, 3),
	(30, 3),
	(34, 3);

-- membuang struktur untuk table project_2023130008.tasks
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('Pending','In Progress','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `priority` enum('high','medium','low') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `progress` int NOT NULL DEFAULT '0',
  `submission_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_project_id_foreign` (`project_id`),
  KEY `tasks_assigned_to_foreign` (`assigned_to`),
  CONSTRAINT `tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.tasks: ~20 rows (lebih kurang)
DELETE FROM `tasks`;
INSERT INTO `tasks` (`id`, `name`, `description`, `status`, `priority`, `due_date`, `project_id`, `assigned_to`, `created_at`, `updated_at`, `progress`, `submission_file`, `completed_at`) VALUES
	(1, 'Desain Database', NULL, 'Completed', 'medium', '2024-01-15', 1, 3, '2025-10-07 20:07:32', '2025-10-07 20:07:32', 0, NULL, NULL),
	(2, 'Implementasi API', NULL, 'Pending', 'low', '2025-10-20', 1, 2, '2025-10-07 20:07:32', '2025-10-18 00:09:55', 0, NULL, NULL),
	(3, 'Testing Unit', NULL, 'Pending', 'low', '2024-01-20', 3, 3, '2025-10-07 20:07:32', '2025-10-07 20:07:32', 0, NULL, NULL),
	(5, 'Review Code', NULL, 'In Progress', 'low', '2024-02-05', 2, 2, '2025-10-07 20:07:32', '2025-10-07 20:07:32', 0, NULL, NULL),
	(6, 'Buat Wireframe', NULL, 'Completed', 'high', '2024-01-25', 4, 3, '2025-10-07 20:07:32', '2025-10-17 20:48:42', 0, 'submissions/1760759321_Penjelasan UTS  Github.ppt', '2025-10-17 20:48:42'),
	(7, 'Optimasi Query', NULL, 'In Progress', 'medium', '2025-10-11', 3, 5, '2025-10-07 20:07:32', '2025-10-08 07:56:13', 0, NULL, NULL),
	(8, 'Integrasi Frontend', NULL, 'In Progress', 'high', '2025-10-18', 1, 3, '2025-10-07 20:07:33', '2025-10-17 04:21:51', 0, NULL, NULL),
	(9, 'Dokumentasi', NULL, 'Pending', 'medium', '2024-02-15', 5, 2, '2025-10-07 20:07:33', '2025-10-07 20:07:33', 0, NULL, NULL),
	(10, 'Bug Fix', NULL, 'Completed', 'high', '2024-01-18', 4, 1, '2025-10-07 20:07:33', '2025-10-07 20:07:33', 0, NULL, NULL),
	(11, 'Tentukan Topik', 'Tentukan topik tugas besar', 'In Progress', 'medium', '2025-10-20', 1, 2, '2025-10-17 04:36:19', '2025-10-17 04:36:19', 0, NULL, NULL),
	(15, 'Integrasi Backend', NULL, 'In Progress', 'medium', '2025-10-27', 1, 6, '2025-10-20 07:57:24', '2025-10-20 07:57:42', 0, NULL, NULL),
	(16, 'Unit Testing', NULL, 'Pending', 'medium', '2025-10-30', 5, 6, '2025-10-20 08:03:58', '2025-10-20 08:03:58', 0, NULL, NULL),
	(17, 'Deployment', NULL, 'Completed', 'high', '2025-10-28', 3, 2, '2025-10-20 08:06:25', '2025-10-20 08:06:39', 0, NULL, NULL),
	(18, 'Menentukan Topik', NULL, 'Completed', 'low', '2025-10-23', 14, 5, '2025-10-20 08:13:32', '2025-10-20 08:19:50', 0, 'submissions/1760973590_blogstv_net.jpg', '2025-10-20 08:19:50'),
	(19, 'Membuat Sketsa', NULL, 'Completed', 'low', '2025-10-25', 14, 6, '2025-10-20 08:14:13', '2025-10-20 08:17:17', 0, 'submissions/1760973437_blogstv_net.jpg', '2025-10-20 08:17:17'),
	(20, 'Melakukan Tracing Sketsa', NULL, 'Completed', 'high', '2025-10-27', 14, 3, '2025-10-20 08:14:39', '2025-10-20 08:18:05', 0, 'submissions/1760973485_blogstv_net.jpg', '2025-10-20 08:18:05'),
	(21, 'Menyelesaikan Karakter', NULL, 'Completed', 'high', '2025-10-29', 14, 5, '2025-10-20 08:15:17', '2025-10-20 08:20:06', 0, 'submissions/1760973606_blogstv_net.jpg', '2025-10-20 08:20:06'),
	(22, 'Mencari vendor', 'mencari vendor perancangan dan pembangunan', 'In Progress', 'high', '2025-12-10', 15, 3, '2025-12-04 18:46:05', '2025-12-04 18:46:05', 0, NULL, NULL),
	(23, 'Menentukan rancangan', 'membuat dan menentukan anggaran proyek', 'In Progress', 'high', '2025-12-09', 15, 2, '2025-12-04 18:46:44', '2025-12-04 18:46:44', 0, NULL, NULL),
	(24, 'Membuat usulan solusi', 'Bagaimana cara membangun candi dalam satu malam', 'In Progress', 'high', '2025-12-06', 15, 2, '2025-12-04 19:41:05', '2025-12-04 19:41:05', 0, NULL, NULL);

-- membuang struktur untuk table project_2023130008.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','ketua_tim','anggota_tim') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'anggota_tim',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'images/default_profile.jpg',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `github` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel project_2023130008.users: ~6 rows (lebih kurang)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `avatar`, `phone`, `address`, `github`) VALUES
	(1, 'Amanda', 'admin@example.com', NULL, '$2y$10$XhppeAF6xARUcWElX8Yc2uqbNwjcIG5eJqYHFuenbF2y2S3.qvPV2', NULL, '2025-10-05 21:53:48', '2025-10-20 07:41:13', 'admin', '1760971273_unduhan (3).jpg', NULL, NULL, NULL),
	(2, 'Sunwoo', 'ketua@example.com', NULL, '$2y$10$EBwgbx0p7EoavL6fMg1.GetXh0wqkcWH8IL5mViNA67Vjz/CPvMyy', NULL, '2025-10-05 21:53:48', '2025-10-20 07:54:01', 'ketua_tim', '1760971894_Gsrfnl3acAARB9x.jpg', NULL, NULL, NULL),
	(3, 'Nadia', 'anggota@example.com', NULL, '$2y$10$xX6PEn//JTQGpF16tZKAr.qMuL/SsXxboB2weFI/b5r9E3uZ/nXwi', NULL, '2025-10-05 21:53:48', '2025-10-19 22:59:58', 'anggota_tim', '1760933777_unduhan (1).jpg', '08827102580', 'Jl. Juanda No. 96, Bandung', '@nadtoya'),
	(4, 'dikey', 'andika.dk07@gmail.com', NULL, '$2y$10$gbHOEdr6qSVZyRUwT2pWBubMLjSx16.nlftAxJbdHGUN.wQ6mvuLu', NULL, '2025-10-06 05:14:39', '2025-10-20 07:40:17', 'admin', '1760929533_個性を輝かせるジュエリー。大平修蔵と「Jouete（ジュエッテ）」４つのデイリーコーデ.jpg', NULL, NULL, '@dikeyhere'),
	(5, 'Budi', 'haven.famous@gmail.com', NULL, '$2y$10$r5hpFfDUPEBJ5RCUGMCYlO.jufe7bOfKE1iTKcXXsOgvJer1FMzDa', NULL, '2025-10-06 07:04:44', '2025-10-20 08:00:27', 'ketua_tim', '1760972427_unduhan (2).jpg', NULL, NULL, NULL),
	(6, 'Deni', 'holy.poly.life@gmail.com', NULL, '$2y$10$JeHMT/GDr9/Yx7smMa3ENeExYrStcwXpV3i4Le.M22jE9KHDFwtUm', 'NlcvYRApaAlJHaac5KesB6Jue8C0HQRWFbDzGL44kh9eK46o8v1QwAGDlj1Y', '2025-10-06 08:31:25', '2025-11-20 18:43:46', 'anggota_tim', '1760972680_unduhan (4).jpg', NULL, NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
