-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: iam
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `access_profile_role_iam_map`
--

DROP TABLE IF EXISTS `access_profile_role_iam_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `access_profile_role_iam_map` (
  `access_profile_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`access_profile_id`,`role_id`),
  KEY `access_profile_role_iam_map_role_id_foreign` (`role_id`),
  CONSTRAINT `access_profile_role_iam_map_access_profile_id_foreign` FOREIGN KEY (`access_profile_id`) REFERENCES `access_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `access_profile_role_iam_map_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `iam_roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_profile_role_iam_map`
--

LOCK TABLES `access_profile_role_iam_map` WRITE;
/*!40000 ALTER TABLE `access_profile_role_iam_map` DISABLE KEYS */;
INSERT INTO `access_profile_role_iam_map` VALUES (1,1,'2026-04-05 10:05:07','2026-04-05 10:05:07'),(2,2,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(3,3,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(4,4,'2026-04-05 10:05:08','2026-04-05 10:05:08');
/*!40000 ALTER TABLE `access_profile_role_iam_map` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `access_profiles`
--

DROP TABLE IF EXISTS `access_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `access_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_profiles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_profiles`
--

LOCK TABLES `access_profiles` WRITE;
/*!40000 ALTER TABLE `access_profiles` DISABLE KEYS */;
INSERT INTO `access_profiles` VALUES (1,'super_admin','Super Admin','Memiliki hak akses penuh terhadap seluruh fitur dan konfigurasi sistem.',1,1,'2026-04-05 10:05:07','2026-04-05 10:05:07'),(2,'tim_mutu','Tim Mutu','Mengelola dan evaluasi indikator mutu rumah sakit.',1,1,'2026-04-05 10:05:07','2026-04-05 10:05:07'),(3,'validator_pic','Unit Kerja: PIC Indikator','Validasi dan monitoring indikator unit kerja.',0,1,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(4,'pengumpul_data','Unit Kerja: Pengumpul Data','Mengumpulkan dan input data operasional.',0,1,'2026-04-05 10:05:08','2026-04-05 10:05:08');
/*!40000 ALTER TABLE `access_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `app_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `redirect_uris` json DEFAULT NULL,
  `callback_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `backchannel_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Optional internal URL used for IAM -> client backchannel calls like sync/roles',
  `logout_uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_expiry` int DEFAULT NULL COMMENT 'Token expiry in seconds (default: 3600)',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `applications_app_key_unique` (`app_key`),
  KEY `applications_created_by_foreign` (`created_by`),
  CONSTRAINT `applications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
INSERT INTO `applications` VALUES (1,'siimut','SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu','Aplikasi manajemen indikator kinerja mutu rumah sakit dan unit kerja',1,'[\"http://127.0.0.1:8088\"]','http://127.0.0.1:8088/sso/callback','http://127.0.0.1:8088',NULL,'4dbd0abf1a4551ace73e65c92781d8ba65066e90301241c04ce64fdc622a3f77',NULL,3600,1,'2026-04-05 10:05:07','2026-04-06 00:19:25',NULL);
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('iam_cache_spatie.permission.cache','a:3:{s:5:\"alias\";a:0:{}s:11:\"permissions\";a:0:{}s:5:\"roles\";a:0:{}}',1775495148),('iam_cache_sso_requests:127.0.0.1','a:1:{i:0;i:1775471352;}',1775471412),('iam_cache_token_expired:1:siimut','a:2:{s:10:\"expired_at\";s:20:\"2026-04-06T07:34:02Z\";s:11:\"notified_at\";s:25:\"2026-04-06T07:34:03+00:00\";}',1775547243),('iam_cache_user_logout_at:1','i:1775461218;',2090821218);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `iam_roles`
--

DROP TABLE IF EXISTS `iam_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `iam_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `application_id` bigint unsigned NOT NULL,
  `access_profile_role_iam_map` bigint unsigned DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iam_roles_application_id_slug_unique` (`application_id`,`slug`),
  KEY `iam_roles_access_profile_role_iam_map_foreign` (`access_profile_role_iam_map`),
  KEY `iam_roles_application_id_is_system_index` (`application_id`,`is_system`),
  CONSTRAINT `iam_roles_access_profile_role_iam_map_foreign` FOREIGN KEY (`access_profile_role_iam_map`) REFERENCES `access_profiles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `iam_roles_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `iam_roles`
--

LOCK TABLES `iam_roles` WRITE;
/*!40000 ALTER TABLE `iam_roles` DISABLE KEYS */;
INSERT INTO `iam_roles` VALUES (1,1,NULL,'super_admin','Super Admin','Hak penuh seluruh sistem',0,'2026-04-05 10:05:07','2026-04-05 10:05:07'),(2,1,NULL,'tim_mutu','Tim Mutu','Fokus pada indikator mutu',0,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(3,1,NULL,'validator_pic','Validator PIC','Validasi data indikator',0,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(4,1,NULL,'pengumpul_data','Pengumpul Data','Input data indikator',0,'2026-04-05 10:05:08','2026-04-05 10:05:08');
/*!40000 ALTER TABLE `iam_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `iam_user_application_roles`
--

DROP TABLE IF EXISTS `iam_user_application_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `iam_user_application_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `application_id` bigint unsigned NOT NULL,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iam_user_application_roles_user_id_role_id_application_id_unique` (`user_id`,`role_id`,`application_id`),
  KEY `iam_user_application_roles_role_id_foreign` (`role_id`),
  KEY `iam_user_application_roles_assigned_by_foreign` (`assigned_by`),
  KEY `iam_user_application_roles_user_id_role_id_index` (`user_id`,`role_id`),
  KEY `iam_user_application_roles_application_id_foreign` (`application_id`),
  CONSTRAINT `iam_user_application_roles_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `iam_user_application_roles_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `iam_user_application_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `iam_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `iam_user_application_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `iam_user_application_roles`
--

LOCK TABLES `iam_user_application_roles` WRITE;
/*!40000 ALTER TABLE `iam_user_application_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `iam_user_application_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_08_14_170933_add_two_factor_columns_to_users_table',1),(5,'2025_09_14_000000_create_notifications_table',1),(6,'2025_09_27_102334_create_applications_table',1),(7,'2025_09_27_102342_create_oauth_auth_codes_table',1),(8,'2025_09_27_102343_create_oauth_access_tokens_table',1),(9,'2025_09_27_102344_create_oauth_refresh_tokens_table',1),(10,'2025_09_27_102345_create_oauth_clients_table',1),(11,'2025_09_27_102346_create_oauth_personal_access_clients_table',1),(12,'2025_09_27_171700_add_active_to_users_table',1),(13,'2025_11_13_085959_create_access_profiles_table',1),(14,'2025_11_14_040138_create_permission_tables',1),(15,'2025_11_14_100000_create_iam_roles_table',1),(16,'2025_11_14_100001_create_user_application_roles_table',1),(17,'2025_11_15_113239_create_access_profile_role_iam_map_table',1),(18,'2025_11_15_121930_create_user_access_profiles_table',1),(19,'2025_11_17_070845_add_nip_to_users_table',1),(20,'2026_02_09_180030_update_password_reset_tokens_to_use_nip',1),(21,'2026_02_09_182000_make_email_nullable_in_users_table',1),(22,'2026_02_10_043130_create_activity_log_table',1),(23,'2026_02_10_043131_add_event_column_to_activity_log_table',1),(24,'2026_02_10_043132_add_batch_uuid_column_to_activity_log_table',1),(25,'2026_02_22_000000_add_application_id_to_user_application_roles',1),(26,'2026_03_22_000001_create_unit_kerja_table',1),(27,'2026_03_22_000002_create_user_unit_kerja_table',1),(28,'2026_03_26_000000_add_backchannel_url_to_applications_table',1),(30,'2026_04_06_071900_add_logout_uri_to_applications_table',2),(31,'2026_04_06_091446_add_id_to_user_access_profiles_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_access_tokens`
--

LOCK TABLES `oauth_access_tokens` WRITE;
/*!40000 ALTER TABLE `oauth_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_auth_codes`
--

LOCK TABLES `oauth_auth_codes` WRITE;
/*!40000 ALTER TABLE `oauth_auth_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_auth_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_clients`
--

LOCK TABLES `oauth_clients` WRITE;
/*!40000 ALTER TABLE `oauth_clients` DISABLE KEYS */;
INSERT INTO `oauth_clients` VALUES (1,NULL,'Personal Access Client','XTRpkBMZiwffZhn9PKRVZXth0KZEtuYukLRSFA41',NULL,'http://localhost',1,0,0,'2026-04-05 10:05:10','2026-04-05 10:05:10'),(2,NULL,'Password Grant Client','7lXIfOpK6xqv0DbgOXgRfqoc7A4RqfZWQj6Glq7q',NULL,'http://localhost',0,1,0,'2026-04-05 10:05:10','2026-04-05 10:05:10');
/*!40000 ALTER TABLE `oauth_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_personal_access_clients`
--

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_personal_access_clients`
--

LOCK TABLES `oauth_personal_access_clients` WRITE;
/*!40000 ALTER TABLE `oauth_personal_access_clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_personal_access_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_refresh_tokens`
--

LOCK TABLES `oauth_refresh_tokens` WRITE;
/*!40000 ALTER TABLE `oauth_refresh_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_refresh_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`nip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('th04YdQjbbnGzN9usRjyPrejg7f2m3ghJnUTmCDt',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','YTo4OntzOjY6Il90b2tlbiI7czo0MDoibU1vNnM3akdhYkRTMXVRSUhENkZLcG5yUUtYYVFLeGJWU3ZGSEgwWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAxMC9wYW5lbC91bml0LWtlcmphcy9yYXdhdC1qYWxhbi9lZGl0IjtzOjU6InJvdXRlIjtzOjQxOiJmaWxhbWVudC5wYW5lbC5yZXNvdXJjZXMudW5pdC1rZXJqYXMuZWRpdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl8zZGM3YTkxM2VmNWZkNGI4OTBlY2FiZTM0ODcwODU1NzNlMTZjZjgyIjtpOjE7czoyMjoiUEhQREVCVUdCQVJfU1RBQ0tfREFUQSI7YToyOntzOjI2OiIwMUtOR1ZYODJUS0czTUZORjFFN0FLN0RCRCI7TjtzOjI2OiIwMUtOSDIwVEU3R0pHQldDMU41OVFLTlkxMyI7Tjt9czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6Ijc4NjA5NDRhZTkxMjFhYjkzN2ViZjNkOGU3ZDRmNjBhNjU2M2UwMTFhZDY2MTIyZDc3MTAxOWQyNTA4NzY0ZTYiO3M6NjoidGFibGVzIjthOjc6e3M6NDA6IjgyYzljYjcyYWJjYjBjOGNjNzY3OGMzMzU1ZmY5NzIxX2NvbHVtbnMiO2E6MDp7fXM6NDA6IjY1MjEwNDI2OTVlYjJhZTE3NDI4ZDNhOTZiOTJkM2ZlX2NvbHVtbnMiO2E6NTp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJjYXVzZXIubmFtZSI7czo1OiJsYWJlbCI7czo0OiJVc2VyIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiZGVzY3JpcHRpb24iO3M6NToibGFiZWwiO3M6NjoiQWN0aW9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoic3ViamVjdF90eXBlIjtzOjU6ImxhYmVsIjtzOjc6IlN1YmplY3QiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImxvZ19uYW1lIjtzOjU6ImxhYmVsIjtzOjg6IkNhdGVnb3J5IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czo0OiJUaW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiIxYjY4NThjODY3YjM2YjNiMzVjN2ViODY3NTk0MDEyY19jb2x1bW5zIjthOjc6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjg6IlBlbmdndW5hIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE1OiJhY2Nlc3NpYmxlX2FwcHMiO3M6NToibGFiZWwiO3M6ODoiQXBsaWthc2kiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImlhbV9zdW1tYXJ5IjtzOjU6ImxhYmVsIjtzOjEzOiJSaW5na2FzYW4gSUFNIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6ImFjdGl2ZSI7czo1OiJsYWJlbCI7czo2OiJTdGF0dXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6Im1mYV9lbmFibGVkIjtzOjU6ImxhYmVsIjtzOjM6Ik1GQSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjA7fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoibGFzdF9sb2dpbl9hdCI7czo1OiJsYWJlbCI7czoxNDoiVGVyYWtoaXIgbG9naW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InVwZGF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTA6IkRpcGVyYmFydWkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO319czo0MDoiMWI2ODU4Yzg2N2IzNmIzYjM1YzdlYjg2NzU5NDAxMmNfZmlsdGVycyI7YTo0OntzOjY6ImFjdGl2ZSI7YToxOntzOjU6InZhbHVlIjtOO31zOjExOiJtZmFfZW5hYmxlZCI7YToxOntzOjU6InZhbHVlIjtOO31zOjE1OiJ1cGRhdGVkX2JldHdlZW4iO2E6Mjp7czo0OiJmcm9tIjtOO3M6NToidW50aWwiO047fXM6MTU6Im5ldmVyX2xvZ2dlZF9pbiI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fX1zOjM5OiIxYjY4NThjODY3YjM2YjNiMzVjN2ViODY3NTk0MDEyY19zZWFyY2giO3M6MDoiIjtzOjQwOiI0MDdlZDM1MmRkZDJlOTAyNjgyNmVmM2VlZjI4NmUwY19jb2x1bW5zIjthOjY6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjEyOiJQcm9maWxlIE5hbWUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6InNsdWciO3M6NToibGFiZWwiO3M6NDoiU2x1ZyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6InJvbGVzX2NvdW50IjtzOjU6ImxhYmVsIjtzOjE0OiJSb2xlcyBJbmNsdWRlZCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToiaXNfc3lzdGVtIjtzOjU6ImxhYmVsIjtzOjY6IlN5c3RlbSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToiaXNfYWN0aXZlIjtzOjU6ImxhYmVsIjtzOjY6IkFjdGl2ZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTY6InBpdm90LmNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTE6IkFzc2lnbmVkIEF0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9fXM6NDA6ImIxMzMzOWE1ZThkNjEwZTI4YThiZDNmNGMzMGFhNjczX2NvbHVtbnMiO2E6MTp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6InVuaXRfbmFtZSI7czo1OiJsYWJlbCI7czoxNjoiRGVwYXJ0ZW1lbnQgTmFtZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319fXM6ODoiZmlsYW1lbnQiO2E6MDp7fX0=',1775471112);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit_kerja`
--

DROP TABLE IF EXISTS `unit_kerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit_kerja` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unit_kerja_unit_name_unique` (`unit_name`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit_kerja`
--

LOCK TABLES `unit_kerja` WRITE;
/*!40000 ALTER TABLE `unit_kerja` DISABLE KEYS */;
INSERT INTO `unit_kerja` VALUES (1,'IGD','igd','Unit Gawat Darurat (IGD) menangani kasus-kasus medis darurat yang membutuhkan penanganan cepat dan tepat selama 24 jam.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(2,'Rawat Jalan','rawat-jalan','Unit Rawat Jalan melayani pemeriksaan dan pengobatan pasien tanpa perlu rawat inap, meliputi berbagai poliklinik spesialis.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(3,'Hemodialisa','hemodialisa','Unit Hemodialisa menyediakan layanan cuci darah bagi pasien dengan gangguan fungsi ginjal secara berkala.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(4,'OK','ok','Unit Operasi (OK) adalah tempat pelaksanaan tindakan pembedahan dengan standar steril dan tenaga medis profesional.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(5,'ICU','icu','Intensive Care Unit (ICU) merupakan unit perawatan intensif untuk pasien dengan kondisi kritis yang memerlukan pemantauan ketat.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(6,'Tulip','tulip','Unit Tulip adalah ruang rawat inap khusus dengan layanan keperawatan dan medis yang disesuaikan dengan kebutuhan pasien tertentu.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(7,'Rosalina','rosalina','Unit Rosalina memberikan pelayanan rawat inap dengan fasilitas dan perawatan yang mengutamakan kenyamanan dan keselamatan pasien.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(8,'Alamanda','alamanda','Unit Alamanda merupakan salah satu ruang rawat inap yang mendukung pemulihan pasien melalui perawatan medis dan keperawatan terstandar.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(9,'Teratai','teratai','Unit Teratai menyediakan layanan rawat inap dengan pendekatan asuhan keperawatan holistik dan berorientasi pada kebutuhan pasien.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(10,'Anturium','anturium','Unit Anturium melayani perawatan pasien rawat inap dengan dukungan tenaga medis dan keperawatan yang profesional dan responsif.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(11,'Laboratorium','laboratorium','Unit Laboratorium menyediakan layanan pemeriksaan sampel biologis untuk mendukung diagnosis, pemantauan, dan evaluasi kondisi medis pasien.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(12,'Radiologi','radiologi','Unit Radiologi melakukan pemeriksaan pencitraan medis seperti rontgen, CT-scan, dan USG untuk membantu diagnosa dan tindakan medis.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(13,'Farmasi Rawat Jalan','farmasi-rawat-jalan','Unit Farmasi Rawat Jalan bertanggung jawab dalam penyediaan dan pelayanan obat bagi pasien yang menjalani perawatan tanpa rawat inap.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(14,'Farmasi Rawat Inap','farmasi-rawat-inap','Unit Farmasi Rawat Inap mengelola kebutuhan obat dan bahan medis habis pakai bagi pasien yang dirawat di rumah sakit.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(15,'Gudang Farmasi','gudang-farmasi','Gudang Farmasi bertugas dalam pengelolaan logistik obat, alat kesehatan, dan distribusinya ke seluruh unit pelayanan rumah sakit.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(16,'Rekam Medis','rekam-medis','Unit Rekam Medis menyimpan, mengelola, dan menjaga kerahasiaan data medis pasien sebagai bagian dari sistem informasi rumah sakit.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(17,'Gizi','gizi','Unit Gizi bertanggung jawab dalam penyusunan dan penyediaan makanan bergizi bagi pasien sesuai dengan kondisi kesehatan masing-masing.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(18,'Kepegawaian & Diklat','kepegawaian-diklat','Unit Kepegawaian & Diklat menangani administrasi SDM dan pengembangan kompetensi staf melalui pelatihan dan pendidikan berkelanjutan.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(19,'Sanitasi & Kebersihan','sanitasi-kebersihan','Unit Sanitasi & Kebersihan menjaga kebersihan, kenyamanan, dan sanitasi lingkungan rumah sakit demi terciptanya pelayanan yang higienis.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(20,'Linen & Laundry','linen-laundry','Unit Linen & Laundry bertanggung jawab dalam pengelolaan, pencucian, dan distribusi linen bersih bagi seluruh unit pelayanan rumah sakit.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(21,'Informasi dan Komplain','informasi-dan-komplain','Unit ini melayani kebutuhan informasi pasien serta menampung dan menindaklanjuti keluhan demi peningkatan mutu pelayanan rumah sakit.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(22,'TIK','tik','Unit Teknologi Informasi dan Komunikasi (TIK) mendukung pengelolaan sistem informasi rumah sakit dan infrastruktur teknologi yang andal.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(23,'Keuangan','keuangan','Unit Keuangan bertugas dalam pengelolaan anggaran, pencatatan transaksi keuangan, dan pelaporan keuangan rumah sakit secara transparan.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(24,'Akuntansi','akuntansi','Unit Akuntansi mencatat, mengolah, dan melaporkan data keuangan secara akurat untuk mendukung pengambilan keputusan manajerial.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(25,'Perpajakan','perpajakan','Unit Perpajakan bertanggung jawab atas pengelolaan dan pelaporan kewajiban pajak rumah sakit sesuai peraturan perpajakan yang berlaku.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(26,'VK','vk','Unit Verlos Kamer (VK) bertugas menyediakan fasilitas dan peralatan medis yang diperlukan untuk mendukung proses persalinan. Selain itu, unit ini juga memiliki tanggung jawab untuk memastikan tersedianya tim medis yang terlatih guna memberikan perawatan optimal kepada ibu dan bayi selama dan setelah proses persalinan.','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(27,'Perinatologi','perinatologi','ruang perawatan bayi sehat dan bayi sakit','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(28,'Bagian Umum','bagian-umum','meliputi beberapa unit yaitu sanitasi dan jebersihan, IPSRS, Laundry, Kontruksi dan renovasi, keamanan dan transportasi','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(29,'Ruang Lotus','ruang-lotus','ruang perawatan rawat inap untuk kelas VIP dan VVIP (6 bed)','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(30,'TIM PPI','tim-ppi','tim yang mengelola pengendalian ind=feksi di seluruh unit di rmah sakit citra husada jember','2026-04-05 10:05:11','2026-04-05 10:05:11',NULL),(31,'UNIT CSSD','unit-cssd','merupakan unit yang melakukan pelayanana sterilisasi alat2 medis,','2026-04-05 10:05:12','2026-04-05 10:05:12',NULL),(32,'unit indikator lama','unit-indikator-lama','unit bayangan','2026-04-05 10:05:12','2026-04-05 10:05:12',NULL);
/*!40000 ALTER TABLE `unit_kerja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_access_profiles`
--

DROP TABLE IF EXISTS `user_access_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_access_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `access_profile_id` bigint unsigned NOT NULL,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_access_profiles_user_id_access_profile_id_unique` (`user_id`,`access_profile_id`),
  KEY `user_access_profiles_access_profile_id_user_id_index` (`access_profile_id`,`user_id`),
  KEY `user_access_profiles_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `user_access_profiles_access_profile_id_foreign` FOREIGN KEY (`access_profile_id`) REFERENCES `access_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_access_profiles_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_access_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_access_profiles`
--

LOCK TABLES `user_access_profiles` WRITE;
/*!40000 ALTER TABLE `user_access_profiles` DISABLE KEYS */;
INSERT INTO `user_access_profiles` VALUES (1,1,1,NULL,'2026-04-05 12:23:08','2026-04-05 12:23:08'),(2,2,1,NULL,'2026-04-06 01:42:21','2026-04-06 01:42:21'),(3,3,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(4,4,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(5,5,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(6,6,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(7,7,3,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(8,8,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(9,9,4,NULL,'2026-04-05 10:05:10','2026-04-05 10:05:10'),(10,10,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(11,11,3,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(12,12,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(13,13,3,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(14,14,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(15,15,3,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(16,16,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(17,17,3,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(18,18,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(19,19,3,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(20,20,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(21,21,3,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(22,22,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(23,23,3,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(24,24,3,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(25,25,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(26,26,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(27,27,3,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(28,28,4,NULL,'2026-04-05 10:05:08','2026-04-05 10:05:08'),(29,29,3,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(30,30,3,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(31,31,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(32,32,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(33,33,3,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(34,34,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(35,35,3,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(36,36,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(37,37,3,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(38,38,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(39,39,3,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(40,40,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(41,41,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(42,42,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(43,43,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(44,44,3,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(45,45,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(46,46,3,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(47,47,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(48,48,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(49,49,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(50,50,1,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(51,51,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(52,52,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(53,53,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(54,54,3,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(55,55,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(56,56,3,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(57,57,2,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(58,58,3,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(59,59,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(60,60,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(61,61,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(62,62,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(63,63,4,NULL,'2026-04-05 10:05:09','2026-04-05 10:05:09'),(64,64,3,NULL,'2026-04-05 10:05:10','2026-04-05 10:05:10'),(65,65,3,NULL,'2026-04-05 10:05:10','2026-04-05 10:05:10'),(66,66,4,NULL,'2026-04-05 10:05:10','2026-04-05 10:05:10'),(67,67,3,NULL,'2026-04-05 10:05:10','2026-04-05 10:05:10'),(68,68,4,NULL,'2026-04-05 10:05:10','2026-04-05 10:05:10'),(69,69,4,NULL,'2026-04-05 10:05:10','2026-04-05 10:05:10'),(70,71,4,NULL,'2026-04-05 10:05:10','2026-04-05 10:05:10'),(71,72,2,NULL,'2026-04-05 10:05:10','2026-04-05 10:05:10'),(77,74,1,NULL,'2026-04-06 02:58:48','2026-04-06 02:58:48');
/*!40000 ALTER TABLE `user_access_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_unit_kerja`
--

DROP TABLE IF EXISTS `user_unit_kerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_unit_kerja` (
  `user_id` bigint unsigned NOT NULL,
  `unit_kerja_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`,`unit_kerja_id`),
  KEY `user_unit_kerja_unit_kerja_id_foreign` (`unit_kerja_id`),
  CONSTRAINT `user_unit_kerja_unit_kerja_id_foreign` FOREIGN KEY (`unit_kerja_id`) REFERENCES `unit_kerja` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_unit_kerja_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_unit_kerja`
--

LOCK TABLES `user_unit_kerja` WRITE;
/*!40000 ALTER TABLE `user_unit_kerja` DISABLE KEYS */;
INSERT INTO `user_unit_kerja` VALUES (2,1,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(3,1,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(4,2,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(5,2,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(6,3,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(7,3,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(8,5,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(9,5,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(10,6,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(11,6,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(12,26,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(13,26,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(14,7,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(15,7,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(17,8,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(18,9,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(19,9,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(21,10,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(22,11,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(23,11,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(24,12,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(25,12,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(26,13,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(27,13,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(28,14,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(29,14,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(30,15,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(32,16,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(33,16,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(34,17,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(35,17,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(36,18,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(37,18,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(38,19,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(39,19,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(39,20,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(39,28,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(41,28,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(43,21,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(44,21,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(45,23,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(46,23,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(47,22,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(49,24,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(52,28,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(53,20,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(54,27,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(55,27,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(56,22,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(58,4,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(59,4,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(60,15,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(61,25,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(62,10,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(63,8,'2026-04-05 10:05:12','2026-04-05 10:05:12'),(65,29,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(66,29,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(67,30,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(68,31,'2026-04-05 10:05:13','2026-04-05 10:05:13'),(71,22,'2026-04-05 10:05:13','2026-04-05 10:05:13');
/*!40000 ALTER TABLE `user_unit_kerja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_nip_unique` (`nip`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'0000.00000','admin','trent.brakus@example.net',NULL,'$2y$12$RD6X3fXicfojrHw5T9aavO036TtNxu1e12vO6uf0xy4cRBCV05m0u',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:38','2026-04-05 10:04:38'),(2,'0120.01173','Chahyarina Putri Pangesti','chahyarinaputripangesti@example.com',NULL,'$2y$12$RZ89US8JuKivXBZZc0x5xOU921UtC/SonHPmymH4ARFbU8hiF2OX.',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:38','2026-04-05 10:04:38'),(3,'0309.01022','Suharnanik','suharnanik@example.com',NULL,'$2y$12$qCu4yWB.lHp/fbHBQUYrROkJUWZbAX3Y1b450TKeMyGZpvg0t8Zu6',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:39','2026-04-05 10:04:39'),(4,'0220.01176','Nur Lela Fitriyani','nurlelafitriyani@example.com',NULL,'$2y$12$9r8hDqib/oT2.5bzv3s2a.bqTALo/LZMeRWzqyX/h9TirFKbMh.M2',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:40','2026-04-05 10:04:40'),(5,'0220.01175','Yasinta Fransisca Anjela Egho','yasintafransiscaanjelaegho@example.com',NULL,'$2y$12$/VND35slNQro1tdODrap7uLCLjJNjYbLhhADrT5J1ACoWmonq1tlW',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:40','2026-04-05 10:04:40'),(6,'0415.01125','Mila Dwi Lestari','miladwilestari@example.com',NULL,'$2y$12$6zsZtLRyDwqA6bFyfoHXgeVEHgwU44L3rbTnlhSF/q406BWptJ3hK',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:41','2026-04-05 10:04:41'),(7,'0109.01008','Elfira Octa Periza','elfiraoctaperiza@example.com',NULL,'$2y$12$BzepbOkPTSHpowjrdasI5eysuKTvp4kpABa05SOtEfy1.TqBf9HxC',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:41','2026-04-05 10:04:41'),(8,'0313.01026','Hesti Diah Tri Pangranti','hestidiahtripangranti@example.com',NULL,'$2y$12$o1UUrksnBm52.QaWZhn/auJ.sXvul2C0vyaFX1QpFsCzI8OTrbhwO',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:42','2026-04-05 10:04:42'),(9,'0110.01016','Dwi Ningtyas Anggraeni','dwiningtyasanggraeni1@example.com',NULL,'$2y$12$4UG8Rf8eexu23yZLtmtH0eEcRaMn1JlnjFYBA8WoKC/yT6S3kfwZW',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:42','2026-04-05 10:05:05'),(10,'121801115','Lidya Octavia Rachmawati, S.Tr. Keb','lidyaoctaviarachmawati@gmail.com',NULL,'$2y$12$nnI6QwM2FwsO6d04NlvPjuPrNysBrjKQRZ1mm4mGyxDOOCjrNLjCK',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:43','2026-04-05 10:04:43'),(11,'1110.01042','Selfi Eka Suraidha Keb','selfiekasuraidhakeb@example.com',NULL,'$2y$12$Xv0wuGbRIzEVKUsFkAJS4O8E3NAXkoJ2.OioFRaP8STc8FuQOtJbK',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:43','2026-04-05 10:04:43'),(12,'1014.01113','Fareintis Rahayu Idamiah Keb','fareintisrahayuidamiahkeb@example.com',NULL,'$2y$12$byqrKI1nrvH2F8qlzJz0DehVIYb/YBj9T.Calz7T5IhhgqBgywY/O',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:44','2026-04-05 10:04:44'),(13,'0109.01043','Erna Wayanti Keb','ernawayantikeb@example.com',NULL,'$2y$12$lzU1J7wsp4QKFHn1m34btOAJFzPzmsFGKYXCsvOuWeLuZYf1sam9q',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:44','2026-04-05 10:04:44'),(14,'0919.011350','Ika Rahmawati','ikarahmawati@example.com',NULL,'$2y$12$4czAsw6fMupoTY3eVGudIe0T6gq/yqbA7.cKClV6rwwK0REk7ookG',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:45','2026-04-05 10:04:45'),(15,'1213.010860','Ida Istianah','idaistianah@example.com',NULL,'$2y$12$H61oFWvyOe.CEUKU1LMTteadS1siZwUt23Mmrf28/di4Z1czWbAWS',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:45','2026-04-05 10:04:45'),(16,'0819.01129','Diah Arum Indiastuti','diaharumindiastuti@example.com',NULL,'$2y$12$5wO.tubjkhySbvbgekJPsuOpD2BKR.Ql/No/lR742MAUkPPo.6Lru',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:46','2026-04-05 10:04:46'),(17,'0817.011680','Erfina Lestari','erfinalestari@example.com',NULL,'$2y$12$yGcJ1aLIL7jYal/2O3muZeZY7dpTswBGwChplkKCTf3/gpPe7D0.C',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:46','2026-04-05 10:04:46'),(18,'1022.02172','Muslehatun Hasanah','muslehatunhasanah@example.com',NULL,'$2y$12$ZJAtRigRtP21zB2k8gTMNe4WMTt5ZQ.7EF5vr436ujxeIT7jzhmvG',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:47','2026-04-05 10:04:47'),(19,'0316.01139','Yeni Susilowati','yenisusilowati@example.com',NULL,'$2y$12$lLWIx.GFTolCixQHnC7j7.AYaMXUFb9E5GIr1AsOAPn9KbyDJonuO',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:47','2026-04-05 10:04:47'),(20,'1220.01204','Afthon Yazid Abrori','afthonyazidabrori@example.com',NULL,'$2y$12$JH11aG/DmcssRJ59P3FM1OYyLnrLrehthBGH3uGtCYNEzbXqK.sHC',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:48','2026-04-05 10:04:48'),(21,'0518.01186','Ahmad Irvan Roviqi','ahmadirvanroviqi@example.com',NULL,'$2y$12$HxyqbBn0g1QX5orwPw0vUeJ2l.NnHZmgwuaC60bd9R6JQQAatx79y',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:48','2026-04-05 10:04:48'),(22,'1112.01068','Zatika Budianti Kes','zatikabudiantikes@example.com',NULL,'$2y$12$5UU1vmA7wxKN24baoijgrugny/9dHn6LD6llXFXD53u4uzryCJvXi',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:48','2026-04-05 10:04:48'),(23,'0212.01069','Leanidha Erywiyatno AK','leanidhaerywiyatnoak@example.com',NULL,'$2y$12$ixTyQB8iVTgBM9NnCipLPe6b3Rey5Iit8qyQPwTy9JWXqSJsa8QHS',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:48','2026-04-05 10:04:48'),(24,'0119.01105','Winda Ulifia c. Rad','windaulifiacrad@example.com',NULL,'$2y$12$rQB5ADr1Rnl/ubTHzL8eP.b55onBKwW9c25WY6iC3lXsimqlXp2ZS',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:49','2026-04-05 10:04:49'),(25,'1122.021740','Vidia Dwi Yatmasari Kes','vidiayatmasarikes@example.com',NULL,'$2y$12$Z8vrIVTO4XGHDAZARnpSne1BfKm5Y1VgKlGDoePc1fm1WJt8JbTfq',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:49','2026-04-05 10:04:49'),(26,'0220.01141','Lutvi Anggraeni','lutvianggraeni@example.com',NULL,'$2y$12$9JrJkaDPwf.tNYPiKEYR1uPAiCK7twSCI1T/.jGIYDZjPIZdm0c2q',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:49','2026-04-05 10:04:49'),(27,'0319.01114','Utari Ardiningdyah, Apt','utariardiningdyahapt@example.com',NULL,'$2y$12$.o3Qv7HOx7LXk68Z6mI5BeUBxZyGMEsStOYMHxwI4jK4GiY5uRDfG',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:50','2026-04-05 10:04:50'),(28,'102.202.173','Fatima Azzahra','fatimaazzahra@example.com',NULL,'$2y$12$gcJxuitFBQAYIFsEDle1tONbugQHaWBvGuQht/FL.rL/nNxZe.gcq',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:50','2026-04-05 10:04:50'),(29,'0121.01220','Elly Febry Taufany','ellyfebrytaufany@example.com',NULL,'$2y$12$lCYc1C1FQFQcKdLIhHG.vuAxXXupQ9mhPHnAhH0cv11dPvGsNldtO',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:50','2026-04-05 10:04:50'),(30,'0918.01194','Jalu Anggara Winadi','jaluanggarawinadi@example.com',NULL,'$2y$12$rC3rx9bkKYat3X3mFjqOae7GpG4Q.6heoFPT3GBXfvemtWwiZYKIG',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:51','2026-04-05 10:04:51'),(31,'0422.01253','Avan Aji Pratama Farm.','avanajipratamafarm@example.com',NULL,'$2y$12$v4KtgY4xeDwrHEexIp9IjOiLT1.MQ57rO9ZiHkO4a1UWGz4ekoNA6',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:51','2026-04-05 10:04:51'),(32,'0813.01059','Siti Syarifah','sitisyarifah@example.com',NULL,'$2y$12$JUrVlvNRLeajUrQhk6EQJ.5iGzAAwaDYW4yvkzjCDnez.gYh/j7xW',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:51','2026-04-05 10:04:51'),(33,'0111.01058','Fitri Nur Azizah Per. Kes','fitrinurazizahperkes@example.com',NULL,'$2y$12$9by4El2JtjF0rEkI2FZ8zOGGuF57jkVoGQbpgmazdbjs4GoWPzzpS',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:52','2026-04-05 10:04:52'),(34,'0419.01119','Freshtin Yuldhialita','freshtinyuldhialita@example.com',NULL,'$2y$12$HtIYOy8AL..1eCW3uyPhzep9tj2S/9MFLikx4feYIAXy272/ecEoi',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:52','2026-04-05 10:04:52'),(35,'0316.01140','Faidatul Andawiyah','faidatulandawiyah@example.com',NULL,'$2y$12$0YTDO8PimJJ.bmiIEubBBOmcQrqvYA5jMiQkWg0hC.wIJF7uwrUFW',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:52','2026-04-05 10:04:52'),(36,'0113.02099','Etty Rochayati, SE','ettyrochayatise@example.com',NULL,'$2y$12$by.C2y9h6iPoCf4Hw4ftDOniIgWOHGh8H5DGrMqL.a.cK5FfOg2zK',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:52','2026-04-05 10:04:52'),(37,'0613.02097','Brigitta Bika IndiatiKM','brigittabikaindiatikm@example.com',NULL,'$2y$12$kJ8f85K57XVQqGx9EC8lNuINIAqU096jj4vYX077TSYcm1Af2w8Zq',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:53','2026-04-05 10:04:53'),(38,'0124.02277','Firdha Aura AlvarezaKes','firdhaauraalvarezakes@example.com',NULL,'$2y$12$uyyrlxp9gtVksryu0UK1xuj/fBTcsV2Yq0dRByRyONML95gvbN4Da',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:53','2026-04-05 10:04:53'),(39,'0816.02139','Ageng Supriyadi','agengsupriyadi@example.com',NULL,'$2y$12$xoX0ph5LDle6kyrJdl8zOuAejc0oVxzVTJoj4xneOzRpNp1xYkCqu',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:53','2026-04-05 10:04:53'),(40,'0122.00046','MOH.SOLIHIN','mohsolihin@example.com',NULL,'$2y$12$PNxKyEFD/58JL3XpD8mZ3.s7/fTyI83Uhk2k9wXC5ivV8OD0Z.pCG',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:54','2026-04-05 10:04:54'),(41,'0215.02117','Nanang Wulid','nanangwulid@example.com',NULL,'$2y$12$u2x29AgzVz3tLgB/MBu18.4SVsNppz3CAgDeka/Bs/gtZQcOt./qy',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:54','2026-04-05 10:04:54'),(42,'0520.02223','Abdul Wahab','abdulwahab@example.com',NULL,'$2y$12$B4x51eoI5Vbu24WzqE8Ur.U4/7Nak9XXJOt5Nk3iojHneVRzRkT7G',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:54','2026-04-05 10:04:54'),(43,'0220.02192','Eka Meidawati, Amd','ekameidawatiamd@example.com',NULL,'$2y$12$2T4I4et6JoHUPiRmx1PqNuxykw0khqATLbQEm8O4pW2hUM890eHz6',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:55','2026-04-05 10:04:55'),(44,'1212.021000','Ratih Ika MaharaniKM','ratihikamaharanikm@example.com',NULL,'$2y$12$BafNpjGC6n6JHY8vrnd4husKD98GrEB8e09LpsphImLiAXVSyzLVq',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:55','2026-04-05 10:04:55'),(45,'0112.02082','Nila Kurnia Trisnovita','nilakurniatrisnovita@example.com',NULL,'$2y$12$RurSmde0CUt5e46WPbhdiuwjBeOh4D1Td5eeK/q/VYctV15EyE4Z2',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:55','2026-04-05 10:04:55'),(46,'0109.02086','Valentina Dini Pangesti','valentinadinipangesti@example.com',NULL,'$2y$12$SfkTBwDdU1FcEVp7ZK7vpuSW0zjwrwDjOatTg1Kp8zjvX6cPOnVe.',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:56','2026-04-05 10:04:56'),(47,'0518.02163','Rani Ekasari Pratiwi','raniekasaripratiwi@example.com',NULL,'$2y$12$gOZqGa/k/FGcmS1FvlSsZe7NjX.NVPGPNkRdNhQuCxscG.y3z5m5a',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:56','2026-04-05 10:04:56'),(48,'0610.02084','Endah Saripeni','endahsaripeni@example.com',NULL,'$2y$12$7PD9jPO5aZ65pdjBeKXUSugcx6Q/EzZmqSWor0T1fBWMgaT6yHXVC',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:57','2026-04-05 10:04:57'),(49,'1122.02238','Mareta Almalia','maretaalmalia@example.com',NULL,'$2y$12$NZZE.bpLHkgg2qlqeXdTa.YtiZcxo7.iZ12KMfWzbnfFn/LBEa31C',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:57','2026-04-05 10:04:57'),(50,'0309.02117','Andre KartawidjajaSc','andrekartawidjajasc@example.com',NULL,'$2y$12$IiudtfOw3X9Vt4N5WnFLkutOzCfCuiy0iEfurZiNQm2JYhvqUvTIm',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:57','2026-04-05 10:04:57'),(51,'0121.02229','Fafan Yuda NugrahaPn','fafanyudanugrahapn@example.com',NULL,'$2y$12$T.ge6sC.VpZDuY8ZN3d/PeyhgjNgffplNeMrIjgjmPde7A0pcSs/i',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:58','2026-04-05 10:04:58'),(52,'0222.01241','Yuliantri Selvi Anugrahni Kes','yuliantriselvianugrahnikes@example.com',NULL,'$2y$12$VDLYbwiOqyudtgvrGxy4AuAxOVyFMWO8zlMGG1b0DXBnxpTlTPRXK',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:58','2026-04-05 10:04:58'),(53,'0222.00060','TEDY RACHMAD PERMADI','tedyrachmadpermadi1@example.com',NULL,'$2y$12$NNmlWk716c1mJuh97r9DBOlLlTBqhhWAa88HTQmDgYvuaDXHTAH7K',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:58','2026-04-05 10:05:06'),(54,'0119.011020','Yohana Karolina Patty','yohanakarolinapatty@example.com',NULL,'$2y$12$iuU1d2n8AlazBkIycYPlIuKGKOWWXSyd6ubLrya28lEGXrdWkaxUy',NULL,NULL,NULL,1,NULL,'2026-04-05 10:04:59','2026-04-05 10:04:59'),(55,'0519.011240','Idza Amruhu Rochim','idzaamruhurochim@example.com',NULL,'$2y$12$/qayAQ5IYll24ohcLdMcq.7ctP1.GPmPDAKJShQaFI5ManRR0fdcW',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:00','2026-04-05 10:05:00'),(56,'0715.021240','Agung Sunaryo, S.Kom','agungsunaryokom@example.com',NULL,'$2y$12$57Lb7vxkLHUdmSVLJvuW5ufqOfD139qH9BX1a1NqMGU.6uh/6EZqy',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:00','2026-04-05 10:05:00'),(57,'0315.01166','Yogi Waskito S. Kep., Ners','Yogiwaskito90@gmail.com',NULL,'$2y$12$y7tfWyinmuTKefc.AjTbQeHFlwskrMVY8RFaQEcg9rB7rg4POgs5G',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:01','2026-04-05 10:05:01'),(58,'0313.01036','Adi Purnomo, Amd. Kep',NULL,NULL,'$2y$12$GYQKGCPB5rrodGu66h9Y4OMd6FQ2K5AgAMueei9r29tuB./hA0IO.',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:02','2026-04-05 10:05:02'),(59,'0823.02197','Erik Arisandi, S.Kep.Ners',NULL,NULL,'$2y$12$Zz7oHhzBZNfRrXS4VYF7S.459PC8s9C.7fJ.Dn6ZEgIap/S7SFOeC',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:02','2026-04-05 10:05:02'),(60,'0124.02274','Hasan Huda',NULL,NULL,'$2y$12$nbUlTrekMKbk7gjeol5WCeAllgKH.OHN7WSpCMQXIsD4X1H3qJi4e',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:02','2026-04-05 10:05:02'),(61,'0325.02250','Rizal Setyobudi',NULL,NULL,'$2y$12$j08/OnUQm97BX0ZLRDoCvutrpdPwDuMg2D.OWF1OrQANdL895KrL.',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:02','2026-04-05 10:05:02'),(62,'1023.02264','Dwi ayu Fitria sari',NULL,NULL,'$2y$12$QETCgWL/wz1zi1KbOMQejeS5dbBO7hPxizjKz1mDc7eYNGZWmXgyG',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:03','2026-04-05 10:05:03'),(63,'1122.02171','Cindhy Ayu Meillani, S.Kep., Ners',NULL,NULL,'$2y$12$ccLJmPEbRDmsdXWUsGNSgOs35RI6QuZKgw1O3fVfLyDJ8.9ZnD7ny',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:04','2026-04-05 10:05:04'),(64,'1116.01157','Khrisna Agung Cendekiawan, M.Farm., M.Kes','khrisna91agung@gmail.com',NULL,'$2y$12$qR2OMYIwcETnMFzGS2nhG.6fRxzGnQOHf1D56zetNKz/vgfiUAgRG',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:04','2026-04-05 10:05:04'),(65,'0119.01103','ROSITA DEBBY IRAWAN, S.Kep., Ners','rosita.debby@ymail.com',NULL,'$2y$12$9DF5TZs1np6yzHselSjAO.70WTlDNrrlQ7321f/oVOQKOYGHXJjmy',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:04','2026-04-05 10:05:04'),(66,'0424.02295','YENI PUSPARINI, S.Kep., Ners','yenipusparini04@gmail.com',NULL,'$2y$12$CiCRYETCN84cEomLikXK0OYIS0U3dy87uTi450hkM2NOtarKcUxem',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:05','2026-04-05 10:05:05'),(67,'1211.01020','Ely Diah Kristian Dini S. Kep','elydiah83@gmail.com',NULL,'$2y$12$mC6wDFJYFw7P0w0Kq1B5EujZdHTlyIPAUP/8Y7Eu7TxGlnOx1HkQe',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:05','2026-04-05 10:05:05'),(68,'0220.02207','Yudha agustio','yudhaagustio93@gmail.com',NULL,'$2y$12$RaoqGbWFvAc1UG2JFbIBT.bUsO8ivgGr4ueLMtTrwPl03i/usBnha',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:05','2026-04-05 10:05:05'),(69,'1122.02174','Vidia Yatmasari Kes','vidiayatmasarikes1@example.com',NULL,'$2y$12$PKAO7joEOiv1ucCLmawY..q9nIt/SCtDshTnNL1QtnRwMVz262uxK',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:06','2026-04-05 10:05:06'),(70,'0000.11111','Ahmad Ilyas',NULL,NULL,'$2y$12$jr7U5x/L6kI2mtpFNun/N.4fb5plP6Qk7adQFxKe8Fk4GVAgFL0eO',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:06','2026-04-05 10:05:06'),(71,'0523.02239','Haris Arifin, S.Kom','haris10790@gmail.com',NULL,'$2y$12$1JXcfNM3VnZ4uMxN98I1oO0SAvWicPnTZFJ2iKKHPr1/W6P0ldrKu',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:07','2026-04-05 10:05:07'),(72,'0713.01078','dr. Lilik Lailiyah, M.Kes','liliklailiyahjember28@gmail.com',NULL,'$2y$12$hLRxmwzcmR43si.m4GVuXey/A66IHFg3HGTtnXgyr0AldiWqp9k.u',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:07','2026-04-05 10:05:07'),(73,'9999.99999','Test User','test@example.com',NULL,'$2y$12$Di6SmdK0O9KzzYbJzuSwROiwT.qF1kaRJj5E4Nxg/EIzuMIqPuE.C',NULL,NULL,NULL,1,NULL,'2026-04-05 10:05:07','2026-04-05 10:05:07'),(74,'9090.909090','dahlana','ahmadilyasdahlan@gmail.com',NULL,'$2y$12$X0bwEk447AL1TL/RuQpZmO4SkV1oQxnbASHqj0JgytHFrtxXOd1N2',NULL,NULL,NULL,1,NULL,'2026-04-05 14:13:47','2026-04-05 14:13:47');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-06 19:14:59
