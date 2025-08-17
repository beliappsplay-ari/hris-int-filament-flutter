/*
 Navicat Premium Dump SQL

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 80040 (8.0.40)
 Source Host           : localhost:3306
 Source Schema         : hris_int

 Target Server Type    : MySQL
 Target Server Version : 80040 (8.0.40)
 File Encoding         : 65001

 Date: 18/08/2025 06:25:41
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of cache
-- ----------------------------
BEGIN;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES ('spatie.permission.cache', 'a:3:{s:5:\"alias\";a:0:{}s:11:\"permissions\";a:0:{}s:5:\"roles\";a:0:{}}', 1755497148);
COMMIT;

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cities
-- ----------------------------
DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of cities
-- ----------------------------
BEGIN;
INSERT INTO `cities` (`id`, `name`, `created_at`, `updated_at`) VALUES (1, 'JAKARTA', '2025-08-16 16:22:42', '2025-08-16 16:22:42');
COMMIT;

-- ----------------------------
-- Table structure for emp_masters
-- ----------------------------
DROP TABLE IF EXISTS `emp_masters`;
CREATE TABLE `emp_masters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `empno` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fullname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `city_id` bigint unsigned NOT NULL,
  `nationality_id` bigint unsigned NOT NULL,
  `gender_id` bigint unsigned NOT NULL,
  `maritalstatus_id` bigint unsigned NOT NULL,
  `religion_id` bigint unsigned NOT NULL,
  `users_id` bigint unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `emp_masters_user_id_foreign` (`user_id`),
  KEY `emp_masters_city_id_foreign` (`city_id`),
  KEY `emp_masters_nationality_id_foreign` (`nationality_id`),
  KEY `emp_masters_gender_id_foreign` (`gender_id`),
  KEY `emp_masters_maritalstatus_id_foreign` (`maritalstatus_id`),
  KEY `emp_masters_religion_id_foreign` (`religion_id`),
  KEY `emp_masters_users_id_foreign` (`users_id`),
  CONSTRAINT `emp_masters_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `emp_masters_gender_id_foreign` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `emp_masters_maritalstatus_id_foreign` FOREIGN KEY (`maritalstatus_id`) REFERENCES `maritalstatuses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `emp_masters_nationality_id_foreign` FOREIGN KEY (`nationality_id`) REFERENCES `nationalities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `emp_masters_religion_id_foreign` FOREIGN KEY (`religion_id`) REFERENCES `religions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `emp_masters_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `emp_masters_users_id_foreign` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of emp_masters
-- ----------------------------
BEGIN;
INSERT INTO `emp_masters` (`id`, `created_at`, `updated_at`, `empno`, `fullname`, `user_id`, `city_id`, `nationality_id`, `gender_id`, `maritalstatus_id`, `religion_id`, `users_id`) VALUES (5, '2025-08-16 16:28:35', '2025-08-16 16:28:35', 'EMP001', 'Test Admin Employee', 2, 1, 1, 1, 1, 1, 2);
INSERT INTO `emp_masters` (`id`, `created_at`, `updated_at`, `empno`, `fullname`, `user_id`, `city_id`, `nationality_id`, `gender_id`, `maritalstatus_id`, `religion_id`, `users_id`) VALUES (6, '2025-08-16 22:06:52', '2025-08-16 22:06:52', '22971', 'ARI RAHMADI', 1, 1, 1, 1, 1, 1, 1);
COMMIT;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
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

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for genders
-- ----------------------------
DROP TABLE IF EXISTS `genders`;
CREATE TABLE `genders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of genders
-- ----------------------------
BEGIN;
INSERT INTO `genders` (`id`, `name`, `created_at`, `updated_at`) VALUES (1, 'LAKI-LAKI', '2025-08-16 16:22:52', '2025-08-16 16:22:52');
INSERT INTO `genders` (`id`, `name`, `created_at`, `updated_at`) VALUES (2, 'PEREMPUAN', '2025-08-16 16:22:56', '2025-08-16 16:22:56');
COMMIT;

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of job_batches
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for maritalstatuses
-- ----------------------------
DROP TABLE IF EXISTS `maritalstatuses`;
CREATE TABLE `maritalstatuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of maritalstatuses
-- ----------------------------
BEGIN;
INSERT INTO `maritalstatuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (1, 'MENIKAH', '2025-08-16 16:23:05', '2025-08-16 16:23:05');
INSERT INTO `maritalstatuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (2, 'LAJANG', '2025-08-16 16:23:09', '2025-08-16 16:23:09');
COMMIT;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of migrations
-- ----------------------------
BEGIN;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27, '2025_01_26_015012_create_owners_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28, '2025_01_26_015028_create_cities_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29, '2025_01_26_015455_create_maritalstatuses_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30, '2025_01_26_015503_create_genders_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31, '2025_01_26_015510_create_religions_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32, '2025_01_26_015559_create_nationalities_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33, '2025_01_26_015735_create_emp_masters_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34, '2025_01_26_051105_create_payments_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35, '2025_01_26_082645_create_permission_tables', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36, '2025_01_28_080724_add_column_post', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37, '2025_01_28_102531_create_payrolls_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38, '2025_08_16_121706_create_personal_access_tokens_table', 1);
COMMIT;

-- ----------------------------
-- Table structure for model_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of model_has_permissions
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for model_has_roles
-- ----------------------------
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of model_has_roles
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for nationalities
-- ----------------------------
DROP TABLE IF EXISTS `nationalities`;
CREATE TABLE `nationalities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of nationalities
-- ----------------------------
BEGIN;
INSERT INTO `nationalities` (`id`, `name`, `created_at`, `updated_at`) VALUES (1, 'INDONESIA', '2025-08-16 16:23:19', '2025-08-16 16:23:19');
COMMIT;

-- ----------------------------
-- Table structure for owners
-- ----------------------------
DROP TABLE IF EXISTS `owners`;
CREATE TABLE `owners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of owners
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for payments
-- ----------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of payments
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for payrolls
-- ----------------------------
DROP TABLE IF EXISTS `payrolls`;
CREATE TABLE `payrolls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empno` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `basicsalary` decimal(10,2) NOT NULL,
  `transport` decimal(10,2) NOT NULL,
  `meal` decimal(10,2) NOT NULL,
  `overtime` decimal(10,2) NOT NULL,
  `medical` decimal(10,2) NOT NULL,
  `hospital` decimal(10,2) NOT NULL,
  `kacamata` decimal(10,2) NOT NULL,
  `tooth` decimal(10,2) NOT NULL,
  `premi` decimal(10,2) NOT NULL,
  `komisi` decimal(10,2) NOT NULL,
  `masabakti` decimal(10,2) NOT NULL,
  `thr` decimal(10,2) NOT NULL,
  `otherincome` decimal(10,2) NOT NULL,
  `othremark` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rumah` decimal(10,2) NOT NULL,
  `jabatan` decimal(10,2) NOT NULL,
  `listrik` decimal(10,2) NOT NULL,
  `leave` decimal(10,2) NOT NULL,
  `sanksi` decimal(10,2) NOT NULL,
  `fixedtax` decimal(10,2) NOT NULL,
  `jkm` decimal(10,2) NOT NULL,
  `jkk` decimal(10,2) NOT NULL,
  `jht` decimal(10,2) NOT NULL,
  `bpjskaryawan` decimal(10,2) NOT NULL,
  `bpjsperusahaan` decimal(10,2) NOT NULL,
  `refund` decimal(10,2) NOT NULL,
  `yayasan` decimal(10,2) NOT NULL,
  `personaladvance` decimal(10,2) NOT NULL,
  `koperasi` decimal(10,2) NOT NULL,
  `businessadvance` decimal(10,2) NOT NULL,
  `loancar` decimal(10,2) NOT NULL,
  `loanother` decimal(10,2) NOT NULL,
  `credit` decimal(10,2) NOT NULL,
  `milenium` decimal(10,2) NOT NULL,
  `grossincome` decimal(10,2) NOT NULL,
  `netincomeexpph22` decimal(10,2) NOT NULL,
  `taxamonth` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `thp` decimal(10,2) NOT NULL,
  `users_id` bigint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payrolls_users_id_foreign` (`users_id`),
  CONSTRAINT `payrolls_users_id_foreign` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of payrolls
-- ----------------------------
BEGIN;
INSERT INTO `payrolls` (`id`, `empno`, `period`, `basicsalary`, `transport`, `meal`, `overtime`, `medical`, `hospital`, `kacamata`, `tooth`, `premi`, `komisi`, `masabakti`, `thr`, `otherincome`, `othremark`, `rumah`, `jabatan`, `listrik`, `leave`, `sanksi`, `fixedtax`, `jkm`, `jkk`, `jht`, `bpjskaryawan`, `bpjsperusahaan`, `refund`, `yayasan`, `personaladvance`, `koperasi`, `businessadvance`, `loancar`, `loanother`, `credit`, `milenium`, `grossincome`, `netincomeexpph22`, `taxamonth`, `total`, `thp`, `users_id`, `created_at`, `updated_at`) VALUES (1, '23111', '202507', 2570400.00, 540000.00, 450000.00, 1253800.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 13880.00, 6169.00, 51408.00, 25704.00, 108000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 4486080.00, 0.00, 0.00, 4737088.00, 4737088.00, 1, '2025-08-17 11:21:12', '2025-08-17 11:21:12');
INSERT INTO `payrolls` (`id`, `empno`, `period`, `basicsalary`, `transport`, `meal`, `overtime`, `medical`, `hospital`, `kacamata`, `tooth`, `premi`, `komisi`, `masabakti`, `thr`, `otherincome`, `othremark`, `rumah`, `jabatan`, `listrik`, `leave`, `sanksi`, `fixedtax`, `jkm`, `jkk`, `jht`, `bpjskaryawan`, `bpjsperusahaan`, `refund`, `yayasan`, `personaladvance`, `koperasi`, `businessadvance`, `loancar`, `loanother`, `credit`, `milenium`, `grossincome`, `netincomeexpph22`, `taxamonth`, `total`, `thp`, `users_id`, `created_at`, `updated_at`) VALUES (2, '23126', '202507', 5700000.00, 504000.00, 420000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1750000.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 30780.00, 13680.00, 114000.00, 47250.00, 189000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6423780.00, 0.00, 150391.00, 8062359.00, 8062359.00, 1, '2025-08-17 11:21:12', '2025-08-17 11:21:12');
INSERT INTO `payrolls` (`id`, `empno`, `period`, `basicsalary`, `transport`, `meal`, `overtime`, `medical`, `hospital`, `kacamata`, `tooth`, `premi`, `komisi`, `masabakti`, `thr`, `otherincome`, `othremark`, `rumah`, `jabatan`, `listrik`, `leave`, `sanksi`, `fixedtax`, `jkm`, `jkk`, `jht`, `bpjskaryawan`, `bpjsperusahaan`, `refund`, `yayasan`, `personaladvance`, `koperasi`, `businessadvance`, `loancar`, `loanother`, `credit`, `milenium`, `grossincome`, `netincomeexpph22`, `taxamonth`, `total`, `thp`, `users_id`, `created_at`, `updated_at`) VALUES (3, '22988', '202507', 5600000.00, 693000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1650000.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 30240.00, 13440.00, 112000.00, 47250.00, 189000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6512240.00, 0.00, 81622.00, 7702128.00, 7702128.00, 1, '2025-08-17 11:21:12', '2025-08-17 11:21:12');
INSERT INTO `payrolls` (`id`, `empno`, `period`, `basicsalary`, `transport`, `meal`, `overtime`, `medical`, `hospital`, `kacamata`, `tooth`, `premi`, `komisi`, `masabakti`, `thr`, `otherincome`, `othremark`, `rumah`, `jabatan`, `listrik`, `leave`, `sanksi`, `fixedtax`, `jkm`, `jkk`, `jht`, `bpjskaryawan`, `bpjsperusahaan`, `refund`, `yayasan`, `personaladvance`, `koperasi`, `businessadvance`, `loancar`, `loanother`, `credit`, `milenium`, `grossincome`, `netincomeexpph22`, `taxamonth`, `total`, `thp`, `users_id`, `created_at`, `updated_at`) VALUES (4, '23119', '202507', 3000000.00, 648000.00, 480000.00, 1705000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 200000.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 16200.00, 7200.00, 60000.00, 30000.00, 120000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 5489200.00, 0.00, 46269.00, 5896731.00, 5896731.00, 1, '2025-08-17 11:21:12', '2025-08-17 11:21:12');
INSERT INTO `payrolls` (`id`, `empno`, `period`, `basicsalary`, `transport`, `meal`, `overtime`, `medical`, `hospital`, `kacamata`, `tooth`, `premi`, `komisi`, `masabakti`, `thr`, `otherincome`, `othremark`, `rumah`, `jabatan`, `listrik`, `leave`, `sanksi`, `fixedtax`, `jkm`, `jkk`, `jht`, `bpjskaryawan`, `bpjsperusahaan`, `refund`, `yayasan`, `personaladvance`, `koperasi`, `businessadvance`, `loancar`, `loanother`, `credit`, `milenium`, `grossincome`, `netincomeexpph22`, `taxamonth`, `total`, `thp`, `users_id`, `created_at`, `updated_at`) VALUES (5, '23134', '202507', 3000000.00, 828000.00, 805000.00, 381500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 200000.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 16200.00, 7200.00, 60000.00, 30000.00, 120000.00, 0.00, 0.00, 1250000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 4345700.00, 0.00, 0.00, 3874500.00, 3874500.00, 1, '2025-08-17 11:21:12', '2025-08-17 11:21:12');
INSERT INTO `payrolls` (`id`, `empno`, `period`, `basicsalary`, `transport`, `meal`, `overtime`, `medical`, `hospital`, `kacamata`, `tooth`, `premi`, `komisi`, `masabakti`, `thr`, `otherincome`, `othremark`, `rumah`, `jabatan`, `listrik`, `leave`, `sanksi`, `fixedtax`, `jkm`, `jkk`, `jht`, `bpjskaryawan`, `bpjsperusahaan`, `refund`, `yayasan`, `personaladvance`, `koperasi`, `businessadvance`, `loancar`, `loanother`, `credit`, `milenium`, `grossincome`, `netincomeexpph22`, `taxamonth`, `total`, `thp`, `users_id`, `created_at`, `updated_at`) VALUES (6, '22971', '202501', 5700000.00, 808500.00, 540000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 5283000.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 30780.00, 13680.00, 114000.00, 47250.00, 189000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6728280.00, 0.00, 376538.00, 11793712.00, 11793712.00, 1, '2025-08-17 11:25:14', '2025-08-17 11:25:14');
INSERT INTO `payrolls` (`id`, `empno`, `period`, `basicsalary`, `transport`, `meal`, `overtime`, `medical`, `hospital`, `kacamata`, `tooth`, `premi`, `komisi`, `masabakti`, `thr`, `otherincome`, `othremark`, `rumah`, `jabatan`, `listrik`, `leave`, `sanksi`, `fixedtax`, `jkm`, `jkk`, `jht`, `bpjskaryawan`, `bpjsperusahaan`, `refund`, `yayasan`, `personaladvance`, `koperasi`, `businessadvance`, `loancar`, `loanother`, `credit`, `milenium`, `grossincome`, `netincomeexpph22`, `taxamonth`, `total`, `thp`, `users_id`, `created_at`, `updated_at`) VALUES (7, '22971', '202502', 5700000.00, 808500.00, 540000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1650000.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 30780.00, 13680.00, 114000.00, 47250.00, 189000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6728280.00, 0.00, 111479.00, 8425771.00, 8425771.00, 1, '2025-08-17 11:25:14', '2025-08-17 11:25:14');
INSERT INTO `payrolls` (`id`, `empno`, `period`, `basicsalary`, `transport`, `meal`, `overtime`, `medical`, `hospital`, `kacamata`, `tooth`, `premi`, `komisi`, `masabakti`, `thr`, `otherincome`, `othremark`, `rumah`, `jabatan`, `listrik`, `leave`, `sanksi`, `fixedtax`, `jkm`, `jkk`, `jht`, `bpjskaryawan`, `bpjsperusahaan`, `refund`, `yayasan`, `personaladvance`, `koperasi`, `businessadvance`, `loancar`, `loanother`, `credit`, `milenium`, `grossincome`, `netincomeexpph22`, `taxamonth`, `total`, `thp`, `users_id`, `created_at`, `updated_at`) VALUES (8, '22971', '202503', 5700000.00, 847000.00, 570000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 5700000.00, 1650000.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 30780.00, 13680.00, 114000.00, 47250.00, 189000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6766780.00, 0.00, 650234.00, 8297516.00, 8297516.00, 1, '2025-08-17 11:25:14', '2025-08-17 11:25:14');
INSERT INTO `payrolls` (`id`, `empno`, `period`, `basicsalary`, `transport`, `meal`, `overtime`, `medical`, `hospital`, `kacamata`, `tooth`, `premi`, `komisi`, `masabakti`, `thr`, `otherincome`, `othremark`, `rumah`, `jabatan`, `listrik`, `leave`, `sanksi`, `fixedtax`, `jkm`, `jkk`, `jht`, `bpjskaryawan`, `bpjsperusahaan`, `refund`, `yayasan`, `personaladvance`, `koperasi`, `businessadvance`, `loancar`, `loanother`, `credit`, `milenium`, `grossincome`, `netincomeexpph22`, `taxamonth`, `total`, `thp`, `users_id`, `created_at`, `updated_at`) VALUES (9, '22971', '202504', 5700000.00, 808500.00, 570000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1650000.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 30780.00, 13680.00, 114000.00, 47250.00, 189000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6728280.00, 0.00, 111854.00, 8455396.00, 8455396.00, 1, '2025-08-17 11:25:14', '2025-08-17 11:25:14');
INSERT INTO `payrolls` (`id`, `empno`, `period`, `basicsalary`, `transport`, `meal`, `overtime`, `medical`, `hospital`, `kacamata`, `tooth`, `premi`, `komisi`, `masabakti`, `thr`, `otherincome`, `othremark`, `rumah`, `jabatan`, `listrik`, `leave`, `sanksi`, `fixedtax`, `jkm`, `jkk`, `jht`, `bpjskaryawan`, `bpjsperusahaan`, `refund`, `yayasan`, `personaladvance`, `koperasi`, `businessadvance`, `loancar`, `loanother`, `credit`, `milenium`, `grossincome`, `netincomeexpph22`, `taxamonth`, `total`, `thp`, `users_id`, `created_at`, `updated_at`) VALUES (10, '22971', '202505', 5700000.00, 770000.00, 450000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1650000.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 30780.00, 13680.00, 114000.00, 47250.00, 189000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6689780.00, 0.00, 87898.00, 8320852.00, 8320852.00, 1, '2025-08-17 11:25:14', '2025-08-17 11:25:14');
INSERT INTO `payrolls` (`id`, `empno`, `period`, `basicsalary`, `transport`, `meal`, `overtime`, `medical`, `hospital`, `kacamata`, `tooth`, `premi`, `komisi`, `masabakti`, `thr`, `otherincome`, `othremark`, `rumah`, `jabatan`, `listrik`, `leave`, `sanksi`, `fixedtax`, `jkm`, `jkk`, `jht`, `bpjskaryawan`, `bpjsperusahaan`, `refund`, `yayasan`, `personaladvance`, `koperasi`, `businessadvance`, `loancar`, `loanother`, `credit`, `milenium`, `grossincome`, `netincomeexpph22`, `taxamonth`, `total`, `thp`, `users_id`, `created_at`, `updated_at`) VALUES (11, '22971', '202506', 5700000.00, 770000.00, 510000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1650000.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 30780.00, 13680.00, 114000.00, 47250.00, 189000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6689780.00, 0.00, 88498.00, 8380252.00, 8380252.00, 1, '2025-08-17 11:25:14', '2025-08-17 11:25:14');
INSERT INTO `payrolls` (`id`, `empno`, `period`, `basicsalary`, `transport`, `meal`, `overtime`, `medical`, `hospital`, `kacamata`, `tooth`, `premi`, `komisi`, `masabakti`, `thr`, `otherincome`, `othremark`, `rumah`, `jabatan`, `listrik`, `leave`, `sanksi`, `fixedtax`, `jkm`, `jkk`, `jht`, `bpjskaryawan`, `bpjsperusahaan`, `refund`, `yayasan`, `personaladvance`, `koperasi`, `businessadvance`, `loancar`, `loanother`, `credit`, `milenium`, `grossincome`, `netincomeexpph22`, `taxamonth`, `total`, `thp`, `users_id`, `created_at`, `updated_at`) VALUES (12, '22971', '202507', 5700000.00, 616000.00, 420000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 5283000.00, NULL, 0.00, 0.00, 0.00, 2850000.00, 0.00, 0.00, 30780.00, 13680.00, 114000.00, 47250.00, 189000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6535780.00, 0.00, 754439.00, 13953311.00, 13953311.00, 1, '2025-08-17 11:25:14', '2025-08-17 11:25:14');
COMMIT;

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of permissions
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------
BEGIN;
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (16, 'App\\Models\\User', 2, 'authToken', '023ffccfa5d8b7f683f403b98e4789a117196e410dd28546335bb0d3fc081628', '[\"*\"]', NULL, NULL, '2025-08-17 07:15:40', '2025-08-17 07:15:40');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (18, 'App\\Models\\User', 1, 'authToken', '3ee3632e3df245520198410c011180a1021a4e5d2d35afec7f73b7dd67bf2ad3', '[\"*\"]', '2025-08-17 09:43:08', NULL, '2025-08-17 09:43:05', '2025-08-17 09:43:08');
COMMIT;

-- ----------------------------
-- Table structure for religions
-- ----------------------------
DROP TABLE IF EXISTS `religions`;
CREATE TABLE `religions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of religions
-- ----------------------------
BEGIN;
INSERT INTO `religions` (`id`, `name`, `created_at`, `updated_at`) VALUES (1, 'ISLAM', '2025-08-16 16:23:29', '2025-08-16 16:23:29');
INSERT INTO `religions` (`id`, `name`, `created_at`, `updated_at`) VALUES (2, 'CATHOLIC', '2025-08-16 16:23:36', '2025-08-16 16:23:36');
INSERT INTO `religions` (`id`, `name`, `created_at`, `updated_at`) VALUES (3, 'PROTESTAN', '2025-08-16 16:23:45', '2025-08-16 16:23:45');
INSERT INTO `religions` (`id`, `name`, `created_at`, `updated_at`) VALUES (4, 'HINDU', '2025-08-16 16:23:49', '2025-08-16 16:23:49');
INSERT INTO `religions` (`id`, `name`, `created_at`, `updated_at`) VALUES (5, 'BUDHA', '2025-08-16 16:23:53', '2025-08-16 16:23:53');
COMMIT;

-- ----------------------------
-- Table structure for role_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of role_has_permissions
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of roles
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sessions
-- ----------------------------
BEGIN;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('07LH5QNKoV02LyeteLuxDQi3VtCYaALLkWKwtVfN', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVWYzZHF3ZjFDYThTcEs1Ym5jZUR5bnM4NFVQUlJKdW4xSmxlNTdzNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755422824);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('14WVAe4irD4S8cHbvw3S31q0e0MlJpM6EkKGY0Px', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTldkZnZlYnN6RGlSRkVIRHJUR254aEw2TTEwSmxzdk5OVWxUanN6byI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755421663);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('3UaacPApyYaKp9O14Wp8orF7u0EKtI5Bohf15Pde', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid1VQOTdXSjNxMWwzWDJWVGlvWmp3aldRVlp0Q0tHOGZsdUZkelEydSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755418126);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('3uxVEZQ0bd0gLl5NHCbiQGe9Nkqxa7MHKREwaHXF', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibkpYWERPRDZjVnVPNGNFcGxOV0lzaml0NzRnM2Q3RFRFTHJ5MTBmcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvOC9wZGYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755420279);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('4Q7jsSJ1F2Ox6swsqnlEaFKe0kUa4tNmzvHQmjiB', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU2M5SkZlR0p4Vkt0dVFHcWNyaXN5dmd0NXN0UlZ3YlI4bkFtQ3NXaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755419376);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('4rv0lCHX2wI92jG0OaLZlMIzo4slPObEeSRRCyV7', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibFF4c2c4b25zOG9jQWVxR0dCS0N2MjJkRmp3MGVTM3pDRHZtam9qVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvNi9wZGYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755419405);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('4sBj40p0d1mbPJbnRYAwHKfAhDkmTTFuhnOlajvx', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWThHYmNBQVpNWjkwVXJSalRsUks0eG5CVlJtc0dVdlFraFlrTWlSSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418933);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('4t2d29Mrnd19NGfgP7F6OxxFUXReHSMFwcQFSfwP', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQkxFNVl2YkdtWEZDT0ZldnVUbWZtQ3JHR1ZjTFk3UzVaUnkxUXZWaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755419996);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('5HVeHKwiwSQxnDX29nYJvBHMgIdo9kWaM3qorkvy', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicHU3UXl3QVM2VGNLTHpPcjM4Qm5Hc2ZvY3oyYUtsTjJCRlRDYmpZYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755421755);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('5j0SWFsJRLaOHoVOJkDLnMXkKBlHadaJrbZh4dn1', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieXAyeHRYdEpFaEtVdGs0a3VoY0V3NlhtZ3BnSkZIb3JERWFBbTI4RCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755421552);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('5oFSYy4qCqEJLLRjqKjEEIxnOszFB7PLFOvZrhYG', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRVoyM1VCOU04REJaaktBUndFWWhZV3B4RDJjNG5WN20ycXVkY1RkTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTEvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755418094);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('7LKEqFvKHCGjMQ0UmKtDgLEZnVVLpiNAjY0RBIBr', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieHF5S3h0U2Z6WTFTbE14cjMxdllIRm15YkNKejJlQ3JJdnJsQWdiMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755422016);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('8CgtOjtosi3QxC02RrTH0bV5fOCIzNLuBeMNADHD', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib3k4TUdZSjZQMFhQRGlVUVdZZlVMMzlDZng5Q0dKVHViU2YwTlpiTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418144);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('8pqtK05E3T1zoaa2a1FuT7Z1qq4AmJgqsj8CquwO', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNlQ5OFdkdGNPR3FtSDAxTUNvYkJQUEhRa1UwdDVqTVJnR2lTeGE2YyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755418279);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('9cQtiTpus1JH2xGWssDSBt5LO80H4SSfnyrcLkF6', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiazlBT2dwVVZFVm5BaXhVMWFCajNpbVBqV21yRWpXMzlKbDluSzh0VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755419606);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('aa0lkSMO1XZ6d3ciqXc4n3eavnL6XDmoDfQWaYaI', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSEVQQUVZWDhpQ3lGNG1qelJPUENGMXhKQnNRM3c2R0FGRUdNeWxmeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755419375);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('AGs26fC1VuUQcW4CWnET52y1nrcfKn6nvCjNh5Iq', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ2hjemRweTlBOWFiaVROdlFaWVlYdkhYd2pSRXVDSGQwajN0djZjcCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755421592);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('aTZ5NDVavYKSzgw11p8S0Q0MqidSIhdoqjsIWUPt', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN3FrRTBTQ0JpclNuUHhOUXRUc3VremhXVVBZVjNHbkkwU0NrQkl3eCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755423035);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('axG0WQcUrKSRS3MacfZO0KlqydPD6W9Zz9G2Y4iB', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSmJiblJqM3Y4VzhxQ0VPQ3BNVHJiV1JXUVZac3RtNGpubUJ5dmpqVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755422838);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('BbdRGmMH8CB1SnHGTGnaCEHtVeRmsHXlIDsqkmBb', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWW1IWHJydFNwSVd5Z21kSlZHT3BzeFQyeEZOU1RvMEZyeWhrSm4yViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755415073);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('Bi8fv1PO5hGum8t9aCd9KhkvNG1EWgRXaH0jotsN', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS2RjWnFvUUJsand6Y1Q2YXUxdzRIUkZkYzVQeVhhMU84eGRJa0t4byI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755419997);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('bpGNxJAi9EwGNoCXVqcrfeFjAlxcMl37zN9DX6TE', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZTZQMk1CZmQxcWZscEZHV0wwZjNmV2RvY2lCTTRSTWFyTmxuZEVaMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755419469);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('bS7Gs6XwsqqVj6B8LTWGG34nbEuwSmGNosAZMZyF', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSnZvQ2prUGJBUjZLcXFsZGRjdTRpQnhrOUo0M1EwZ0dVY2VVVDdmMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755420771);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('Cj2BIW18h2obR143uLzXbvgLMWYF4BAq8rbGR8tf', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiejVzWnhhT1Axdk5LQW1qTjJNNVNOREY2VlJNNHNleklvZ3ZaRjZOaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418936);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('cop55Jz4xdwbnLvuumJJLPdPOmqPY3Wu8fcQ9U5z', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWmFYcnZqeTdvZEpSbXdtS2V1VTFLOG13VGVSM1NQNWpLN0NzZFR5ZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755419522);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('cZQgiwIRcCtzqArh8fe8unkLmVOWrP5ELujfuz5C', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU1gyeHlERjlGQnhxWHpDM29XY3lZejBqaGttd014bDdBU1JuUkRCWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755421535);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('DsF1eQLHB4RLke9AMUpke59uAZgENQ92NG3A3IpV', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUG11R2FqZ0dORTR0dm5tT21kaHZ3WEJqSFAwZnM0MTl4Tjl6WG15VyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755420771);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('dXBthQHbnT2qnaC2K5kAThPbOwYz31Thq1IbG0G1', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibjJRemVsdmE1REpPQWJUelp3QWoybUZDdDY0eHJjQURmYkt3QUw5ZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755418046);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('E0hsL86Q2YmucBdpAKk9UE4Quu92fJjtAqG7qa3T', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ0FRUmxDNEVIWHBhWUY3ekRicmI1NndER2xQQ3QzZHJMd2xXNU4zRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvNi9wZGYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755422204);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('EMVx3s8rI7FqvJTvkT91g6GitwlMOB2fyPKnEzDC', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibmZycnk2TmZMRmxGQkExY2pnUkVyOTFIdWtIVnRvWXB0TDdnaGJtVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvNi9wZGYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418178);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('f4A3HGoyXZkT4N9XjyhwvisYLWVZEVTE7PX7gXO4', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVU5IaXN5YmhBeGllV0I3VlZnbUZwaW1rSGhWc0k0eTg3dDZncXJWTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418892);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('fRRteUhkzR6u653fKhCDgPE3799E5llglaqNPvia', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibEQ2Z1ZyQnVYSXQzNGhKWDZ5MnZrUVR2Vm5xdm5UOXZuSVNGaE0yNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvOC9wZGYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755420609);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('fuDgUd39csU0JyK8okkG1SSesVjWNAWHbecrQEvr', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWHpHcTFrTUNSYUhSQjhoRUZpWnNTb3cxUzNXS09zeEJ4MnFpZVhCRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755418287);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('G9Kb5NyT7jirL3mPmhB4HdzqnkQEgbsuDkwVaAlb', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ2xhR1g3TnlPNVBVUm5VcGVSeHJIb1BrM2VDa2J3TlladEZkaExPMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418161);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('gEgcWq6ESebAFabUZM2XNguuXxjHfuYRfjAuov16', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibk1OUWJVMkdubmsyWjhnQ2ppRDRXOGtabmlVTGdZN3hxVDlGOHhMTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755421535);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('gOWjFE491kIYccIZkwwpe0otRUrphgylE5WJxurK', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZmU2SnBGNEVMUmxOOFJ6cUp3Q3Z4MnQ1N1VoRE1Id1JrZ0RtbUd3NiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755421632);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('gX7KQwIMO8hS8CCEu4PesUwcC5zCiNDpkH5yP4l3', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSUxQOXpQVXRvck0xRnQyR0hzRmc4Mjc5T21xMUQ3Rm1QUDBzcG1jUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755422179);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('GYBJoia8Y2n5b7gPeuNWqfLe5gCvQzbr4DJNKtYl', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSXdnSXdOSDF1eEwxOFRrZklSZ0xXTnJNRjZOT1lrU3dIUjgyRncyZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755418048);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('h4Rh3gHB9sHii6smREncpQ660cCJRuymflPORuji', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNmw0RnZUd1h2dzFkM09jSEFUSkd6STVmd1JJdmMwYWJJdzlKanBqUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755421979);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('htOWBXE0wEj4T9Ln772flcqyXNNagk9tHwSY8LQz', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWWFRZzFwUmRub05aY2JaRWoyYnBWRVVZcGZXVzQwY0Jhb0k3THlhSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418144);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('i35swDPRPIDpvoRQGFig43quSkvUqGl2SeDyak3O', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaDBRNDdTRTRxWTJmWEo5T25rekozMDdZUmVTS0FQRnFPeHJhRTlQRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755422847);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('I6zoT436vwfz2cwtyqpIuEGzTnCnAapmqXaRB7Zo', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOGxIakplU3Z6b0dZaEVxRWNsUWhoOFp4UzdVRHlGSWtJTDg0cEQ4bSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755421672);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('in0IFRX26fGasrjFGRCiaNpJYw40h2G60KEyVDLs', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ05ES3RXYm41NTQ0Vllmckh6NlY5cDVFeTlMaVBTUmp4UnRVaEpFNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755421652);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('iSdoWADHmrz66uRyHEQ7jfzDWvte4tCVHbg9JliG', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWFd6cWhidVlEdWZyTnpiVWpyNXpSaWxJZ2MzQ25HOWxMcnN3WDBFNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755415070);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('IXGSbEdHYZCVQvQoOtmX2dJ72HRbO9LVTsAEJDfs', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYjhYS2tpY1RVQjdLS1BmcXJFRG9nUWQ2OFoxM0FDWkh1NlhXS2oxZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755421655);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('j9nIqvahEw47NwPXKPKiqn7GdZzOCUNrOnwiiV2c', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSkV0a2JHcUFuMDI1YWdJY013YnFBenhZM3NHcjZhZFR3VnI5RFhrYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvOC9wZGYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755420590);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('jQqAdswIkNsBlbB7C9vDNVg8JDU0DICbybF6QQPG', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoielhUMjRUZFN0WlpRMnlDUHhWZHNSWHJRb3hPWlBYVG1oeUNnNkR2eSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755421655);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('kf4A8mIXmlP8317hG6k6Q4a5Hq60TFeiQIXRctDU', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUWNwUWgzS3ZGMXRGam1IazNVSGtBWUpqV3o0NWtwbFRZdWRQNmlWaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418343);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('kPa9gd8U51m8mCYJNXZPU6rdCqsWh5sFuZSM7nnZ', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTXVOMVdybHllRm5HZjE0cFBmQjhxWjVuakpvQmp0N2Q1NjhSamVZdyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755421636);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('l07PZXBtS6RiQIPWJdM5291q5lNdW8Z8WNElCtN4', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQUVlbG1pTDBJaEVWVG9yak1TejYyTGM2TThYTXlJY0FTQUdpMzlGVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755418916);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('L6LOdaDgsAE0nqimQX3dnRauziv1cZCnODlAj7u9', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibVF0N3FsdVlrenFhcU5Ka0xZZmxLVUd5NFdNYjAxYmdrUGFLRDBHYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755419376);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('lD1avfhguyCAR5rll1JWtTodmlT8Vzi4UKK7ewrR', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUXI3UzNURnFuME4yRjdXdUlYTmlKdVN0aWVtMWNSYnNDOUlYYVBKdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755419997);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('LmClDhqKSop7y63tkoJJtbxaffvU7fS4xGSo4waO', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMWxKTUNtTVZydkdIclE1S0NsRGxaVDl6Qkh3UnpNUU9jOU9ka2hidCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755422175);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('LR3YeppOJYIHcMZCSvlKk5ldNSyo81T6LKfYy5bF', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR1czOWx2ZVdBNFBucUJVWDB4SzZVWDJaVDFvOVBhelZta1dhYlJ0ZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755420574);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('LVZ8UzhHWs75wF24Td4AXkIBa8vWCtuSbHjvHmYt', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidGFlWVM5WnRpTmxnWlZTZnl4aFJSdXFCVll4cHBwaERWNUpRcG9lZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418160);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('lWoGtOAAPPjzgot2iAYltnjcIshESzekgrMbRffN', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUUNYU2hBT3BxYkxjWTdiQ3o5WVpGUHd5VmlBTDFSNU9rUks3bDJXYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755420597);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('MfjuSrP3uKgimLLiEric4dFxg3mRWnISKUMXCXMF', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidmx3aTlqWGRBRXhvNEo1cXRZbmtBTWg3cGVnZ251aWpNczhLQmd4WCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418889);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('mOdztBJ0mRSID7Uohjr6HKycifQfKykiuApFpyOg', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVVQ2R01kUkVXZDBHYWVUSWRCbHJxR2I3OUkyNzBaMXRDM3VPcUZXTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418248);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('mrTaa5CIBjyzz1m8fGspMebsUlYTLDoIc35ZjqN5', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOXFvZ05LV0JTSWRqWVdtYzl5ck05QWVRV1JaYjNYSFVvTEdnQnpxeCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755420266);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('mUwELvR8akDJiSMbztrv3HMiQohIPzZD2jzxP1ZM', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSGZoZzBrT3dUWVB6Q2FVMmVmRDVJbzNPZm93eVJoWkU5aHdMMzZmRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755422187);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('MyNvKzD66xQSwUuOuYAbRu25JCkZiqtE1rFiRsIF', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoidWxRQUdXWG1WVnJjTk4ybXBFSW1FUGtvZ1I1Y3pxT2lQeVZWSWFWRCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755423127);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('nn34rR0tf12gikCwME2ceKvb4rUurRAzlH18xqXF', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVzZNUjJKVDFUVFI0VjhrcVV2MUdqSlNJQzY2YzIxNVhuRm5iVFNRbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755422194);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('ogItA94cFuXd5rWMWF5oEKuxCuhGUaX8IQ6RSSzH', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOTFYeFVuZm5SUEQycTVVcVdNUWpMZU52Y2RNOFFVT29lNGtUM2czbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvNy9wZGYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755423088);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('OMvcxTAXhZqDEqPGVpK3mwuTGHcJICZnakbOycnH', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicE44WHpZME9Ib2FlaWljZ3FNbEp1ZmlHblF2MHdUZzFUblFsWVprQyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755423788);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('PBByb4PFDNoXDJcUdDaddrSe40ebgtdODzkeoEBr', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFpTUEVOVEllbFlPUUdVRGtHaVdGeEZMM1dkMDd6ZGFqajZ0ZzlOQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755422832);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('pPhp21KN0nKEcQvktqT0OJ0iRmKuk92KvMFbGH0K', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYXFTM1UzNjkwVVE4eGUzcFNmak9xblREZVdUUTdqN1hhV2tUNlBjZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755418237);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('PptqSaL1yv1t6ScfYDMmM1frVBGz9edzLTHl7FFq', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNVYyTzNFWHdNYmwwMHN2NGxEZmN2RWlCTjJLdEk4QUtDb01qblE4MCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvOC9wZGYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755420606);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('pTR1EcJL6LQ01xzfzoIf6FgxjsKZobhV17qobXsP', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaVh4WWpvV28wT2JtUVl2YVd1WHpSOFQzeUxtUjVDZk13T1l0T0p4ciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755418889);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('qB9eaIkOwqiOnnGhO2FVtg505NjsJglokEjMlzeK', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRzA5Q1B1bnN4ZjZIN1p2enFvQUQyeUw1UHNDRmd4eGJBWWpFbE5LdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755421981);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('QEbFjsrpAACQASefbq1OcbYsNtN1xnvkAN8uUzim', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidWZsME9JVGxFcjZaUnFVdndQRWVaWVhmWW9YVU03ZEF3QTZrakVHciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755420772);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('qocFlasuuwFsluyPxfkmapUdOrQjKZN8XUItbPm1', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiNEltaUZWc2VZRFpQV2tXd09WeGFXdDRFOXhUNWluN1NTVVhuVHRWSiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755418073);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('qPSg9DZ5r8xhIGVbu6gVR01isYyqPHyLRbclRImX', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVFl2SHpVZUowaGJLUzR1Zjd2Vk1JZ3lCREsxd2ZGaE9Xbmlselg3RSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvOC9wZGYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755420648);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('qsloxG2Omg92ePcYlEOurVEoQd1n5RP8JK3JYzni', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTmdTR3Q2MVpLS2pVMHd2dFVIYUhlNFg4VzlidW8xSkdIWXVXQ2xjYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755421531);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('qX9fOQFAYM5aP4IVdJKZUOw0ZxAsFTCyjsTH4Uar', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZmFWcHJJS2lWNDVTTWZIMTJzQ08wZ09JaDBuMmRhQTgyamNIOWlMSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755422211);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('R2gUfzBN4Y2IoVSnxOrbYPxDy6TsyI1sfM2eEMei', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTVNmdEc5ZkUwdTJiTjZBT0FNNkFpejJ0bWRyWTdwT2taaHVyVUo1MyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755422831);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('REbl4Ed3S0L3GkajHliFoak0AxJya6ScsNitFiaO', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWnJNR05QRjhxTVA0OE5CUUU0SldmMTd6RXJIRHRmaDBWMjBDZ0NmUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755418237);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('Rj0nzm4XhtqiGjMXFQZusyPOHPhTN1ngBvgdxrq9', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNXZoelFJend6YloyUjFWbGxYbUhFQ0NRZDdpSFR4VWg1bXFOV1NyOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418237);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('RMKCCnCmVdtCR6UqpqOC2cSNvhsxMONWlegY8OMw', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWWFEQ1lERGRnMk5La3lHcjlFQzZjOG16RVF2VkNHOEJCaTFVS3pIdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755423077);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('RScQGkB9N1yI0QEChpIhWtyN5jjfkThLBXIXZTCG', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic040NTV3VzUzSDlZdkp2RUhSQ2phbTZLYUROU3QxVzQ4WlJYVUlTZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755421977);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('sdU0VPwN3Gu3Vfhgc6hLekpIVHmtT1BkSGBKv3t7', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicHljWmd4a1ZpYkJuUXBFajVHaVVtcm9lcTFOd3lkc3pBd0RWQ2VGbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755418088);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('SNH7Vm8HB1Nw2VhS1SWK9XIXuAKLpUBgVkqCnJ3O', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUEJqNTVFRXViMXJIU3dpczM0bXIyQVp6MGprcDRVbUlmdXhQT3YzNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418343);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('SQK8wsvoTctocTMssUOJarNgsRBVcWEH7OdXWmfX', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYmY4eElaZVdsVnRqZlJIamNORERrTFV3WFFMcUxuR2Nmb2h0Uml1aCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755418091);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('T0iuHFK6yAS3r586C6n2xS8REY2sgMt6wrcYlH28', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieEIwZ1I1ZHFZQjF5TE84MUY0NXRydTRyM1o4ejN6aFg3YWxZTTFYRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755421655);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('TMgcslc7HiAnXDS3XJBiKBWPugivGlWA7uEMTcUo', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSmlFQTdyNlNWZWhWeVY5bzVlNVVqMmdLRTYwN1BzdHZQR1Y3T2lMbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418089);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('tynZUR50uXbjBf2PbKJ4PL4cSDD6OZs03UK0FsHI', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWGFzU3ZJMjlqS0hQMjNyRTU2dk93NnQ2ZDZUTnF2aVFzalhBcENvMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755420566);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('u2iYAV9LMMxNFfB6zIKV8Zj0Sxvz8Xo5Kd9kr9LN', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZkpINUI2MjBIWE9OcDczTU1TWmRYZW1hUnh6a2d1WjRxd3dqMTM1MCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755422828);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('U3aHx0BrlGynNLiMVX8uv0adPvjoeYIdfChtEKju', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS1Y0ZjlVa0F3ZlVQcGNTcWkwUktCcFV5U0g4SjZtMW52YXdITlFtQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755419390);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('U5ESXlt1XzLuoKxrmNBBLU1ULymWVykcvElqElW0', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUEF1Nm1qeUFRcnZaOEpFN2VCNGNTTE84Q1BIdUZ6RVgyZzg3aWpoTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418088);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('U6RplDzDSn4K2ImOMKPLrwNNF1BXplr1ePeQAF4T', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiREV0c2tUMU9odEtzNXhYdmxLMWh3RVhOUUFqUVB5QXNFclFQYmtFYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755423042);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('UBLADE5BA0atPPfKgqPdhRy18imhgspBmo4wj7oK', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY0Y4UEZiNmpPZTllQm9jS2N4c3Z0UXRNYUtEa1N5blBJUjlZRm1zVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755420028);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('ufvgCHFfHAVjiT1xlApIqSesM19AyKcADhkZ3zFU', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMXpwUU5kNFplMzc2OEY2bEtaQUlEQnJsYk5rNEpBalFGN1ZZMjRjeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418279);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('UJ1niaLhnWhIMwCZkTcCnE35o2za5GxcEPovzwai', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicjdQRlFZdlRNaGVkSHRhUHpadW1PVTFpc3NXd0FwbENURmpWVlNQMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418048);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('UucenaYXhr3uP2yFp5nCteFBnC03ZHuKz0l5X7Ps', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTEFxQml0MUpLbVpuQnlLTnBsdlRZZG5wdTBqbzZKRE43THpUblI2ZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTEvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755418150);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('V6gbUqf5VHQKX7s8e627TC0FKvdGBUCiCGN6tP3B', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicjdnUjdWUk5KeUdDTHJ6V0gxNEZBVEJaWVI4UXI1Y3dLcThXUlFsViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755418914);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('VFK1nlqhasBX0eJVVvQ1xWilOmiVhnrzp1bR1Tug', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibXdQVnc4VkhybkphRlpZaHNsUXpaQUlBQnNXWTkxTzlGRm5ERnZPdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755422176);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('VvxYCNiziGPjkahc6HKNl9GzqRhtwWyWPEuRuqOg', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieFFZUkJqc25paDhsak1tQlVQNU5rcXF5MFlWWHd4bHBoQnkxUTFzZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755422180);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('VX7Fo8Q1IJBYaB3qniwbHmrk2UHTZZkPYe1EHSTp', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicWJ2aG9LT2psQlh4Mm81N2RxbmhEdmIxMXJsZ3E0SFdpYW83MnZhNiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755422834);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('w19WXWoB5M5mlRrMrk6WJFwxkJLVm2rZOfaq2RB1', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid3hsMHRsTTJrbFRrcUZxWEJ5OUpNV2dFdHRlV2dTVHFDdFVabE4yTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755418110);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('W9Re18PKwxgD4T46Uv5kTzUSqalJkLyNMS1zHYzj', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaUh0TFdvczZpUjV1cWE1Z1lYMmo0d3pna3JCS0FGOVZYUUZ4VGtIZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvOC9wZGYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755420294);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('WELkS0Mu47TYbIeBdH5duVC2xVAEIxlD4wqyWxkA', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieDN4elE4RkFxeTNMcFVEbGttZEtNSFU1TENocjN4emY0dzU0Z0p4ayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418169);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('wrdCLJLNplJk8rRF4YO5yqGd8CDDYHi1o0TJH6Hs', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN2xpaDNXQURKWFdDd1Y5SWJPNE50RXU1eWlvTktnTU9ITHJBaW9QSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755420027);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('wtmKHIFi3bifK7Vtoy0Ii3P161y7tW9ZAvEv6zLS', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiekRCT2tnYzZXQkFpRDJPZUJ2OU92V1RFZ2tUb1NJcVZaM0tCMGgycSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755422823);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('X7PCS6ki2kX1BIZL7rT1UtQWFPZoyAkHTaqb9pi8', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN21pSGRlOUtTSHRCTnJCZ2NBRksxWGdZUXJqUDYzWDVZZERZY2lDbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418942);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('XH1nanGTGmNh2jnugxHjYPDYCaX0pXzfzYRzyPDL', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSllQUmJxRFpQbnRhdGFJaENhd0tzT0N0OWdPV0FHOHlHcXpTeXBMYiI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418088);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('XRGq2LsB5VDc7qygzqOtMEFYKimAcG9FMhJGAvbn', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTUtuUlVJRmpNdzRpZ1lWNFY0am45anJ2RkNJTmxjczgyR0ZxN3U2SyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755418249);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('y86IX3L0uMNjOz2vsy140PiiQGcACZh1TQubG3TP', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT0ZlMGpVY3pSQlg5bG9GdHJUcGZkbEZZdmdoOHNKTUJxZFhrcmFsYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755418285);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('ydwkuLwK91RSDUpvp4zuwVy6ouEAuWxKH9JzJykl', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTE9BbWk0UElaRjFVSEpvWDkwc1BrYW1LUXVFYXk5V1hZaWVuS3VLTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755418895);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('YeTohEMrkFxk3dj66uYs0kqLTPPi9G7jO7jtZUlV', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVnJiR0hHUTlnRGdCWHF3ZUF0VFpLMHdHY0pBZ2NJeVR6WWVqdW5ISCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755421556);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('YunKiSjF5dkOss7Aer05QcNcQDA67EookTvsLEXt', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUTZ0SFg3eHowRUhwWnVSdjJqSVRBMk8yZ1ZBc3k4Ukp0YmlZa2oxNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755423028);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('YzumdJ1gH0BscwCI8RjOOAYhLiVal57VQwJTZ06r', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaWYwMVM1enhiRng5eDRxbEloQVFrcnk2Z2hwZjh2YUlxMm9WWTZlMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755418101);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('z1GiU6nLPC4tF4JvQC6FbwcDjiPnzNwCJSRpV3uK', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicDRGUUVZOEt2QkxaTkpKVUtwWDBuU2puY3E5aWk1NWdOYnZvSzJ1diI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755423785);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('Z4QOjL3IeOrGbQv6ky5EAVihH7Nk8Yl4Z7ZjAj4v', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVTYxSURHRGxCZVBDUGloS3IzeVN2ZjludEZNR2cyY3BRUUF2WnBMMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755421752);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('z6RBuJagnDBknpMOzPsmhTh9gUnXK06wL9302gGa', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN2g1ZlJLTFlvZ1BxYjR0c1RXOVZFbXhwa1lxaHBwRXluM2RUY0JrWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9zYWxhcnktc2xpcHMvMTIvcGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755421565);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('ZavlHsJL7MpSyrqt42MidHcY7BLibZWgvDSGcmbK', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYkVUT2ZyZFB1eEgwMGRPQmNEYVpBenJJd2VxakdpYUNxcnFjSURXZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755418278);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('zEzvpGSyvzxmZpyKtltUFVLq264xpmF2QlGiFxqf', 1, '127.0.0.1', 'Dart/3.9 (dart:io)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicVlWcWxYcVBMRWt0RVVQN1E5aXliYnA4c0pyU1E1NFZQV1VDZ0pKeCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMC4wLjIuMjo4MDAwL2FwaS9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755418888);
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES (1, 'Ari Rahmadi', 'ri.rahmadi@gmail.com', NULL, '$2y$12$NCdauR6bcNvSOV8Ri4aT7OH25xSeo8q8Qg27Y508ETLk1LXfRG726', 'S67Rh1MLbtZx8HQVME4MWBqkfQDAPHpj5DJ0LCSf547uvgljvgWZeu5uLMse', '2025-08-16 13:51:43', '2025-08-16 13:51:43');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES (2, 'Test Admin', 'admin@test.com', NULL, '$2y$12$irng3Otj3imFtKx/4WuYyuoWeBrL/tRQ9mJ4tQ5bevoZ0vg.9APnO', NULL, '2025-08-16 15:49:59', '2025-08-16 15:49:59');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
