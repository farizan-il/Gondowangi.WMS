/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.5.2-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: gondowangi-wms
-- ------------------------------------------------------
-- Server version	11.5.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'User yang melakukan aksi',
  `action` varchar(50) NOT NULL COMMENT 'Create, Update, Delete',
  `description` text DEFAULT NULL COMMENT 'Deskripsi aktivitas',
  `material_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `warehousebin_id` int(11) DEFAULT NULL,
  `user_id_target` int(11) DEFAULT NULL COMMENT 'User yang menjadi target (untuk log user)',
  `batch_lot` varchar(255) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `qty_before` decimal(15,2) DEFAULT NULL,
  `qty_after` decimal(15,2) DEFAULT NULL,
  `bin_from` varchar(255) DEFAULT NULL,
  `bin_to` varchar(255) DEFAULT NULL,
  `reference_document` varchar(255) DEFAULT NULL,
  `old_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_value`)),
  `new_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_value`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `device_info` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_material_id` (`material_id`),
  KEY `idx_supplier_id` (`supplier_id`),
  KEY `idx_warehousebin_id` (`warehousebin_id`),
  KEY `idx_user_id_target` (`user_id_target`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_activity_logs_material` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_activity_logs_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_activity_logs_user_target` FOREIGN KEY (`user_id_target`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_activity_logs_warehousebin` FOREIGN KEY (`warehousebin_id`) REFERENCES `warehouse_bins` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES
(97,1,'Login','User berhasil Login ke sistem',NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,NULL,NULL,'118.99.103.162','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 09:50:32','2025-12-19 09:50:32'),
(98,1,'Create Putaway TO','Membuat Transfer Order Putaway untuk 29990.00 Pcs Botol Shampo Natur 140 ml',74,NULL,NULL,NULL,'20008191225NP',NULL,0.00,29990.00,'152','188','TO-2025-12-001',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 10:27:58','2025-12-19 10:27:58'),
(99,1,'Create Putaway TO','Membuat Transfer Order Putaway untuk 29990.00 Pcs Tutup Hitam Botol Shampo Natur',97,NULL,NULL,NULL,'20013191225NP',NULL,0.00,29990.00,'152','205','TO-2025-12-001',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 10:27:58','2025-12-19 10:27:58'),
(100,1,'Complete TO Item','Menyelesaikan transfer 29990.00 Pcs dari QRT-HALAL ke F-2-1',74,NULL,NULL,NULL,'20008191225NP',NULL,0.00,29990.00,'152','188','TO-2025-12-001',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 10:29:03','2025-12-19 10:29:03'),
(101,1,'Complete TO Item','Menyelesaikan transfer 29990.00 Pcs dari QRT-HALAL ke G-1-3',97,NULL,NULL,NULL,'20013191225NP',NULL,0.00,29990.00,'152','205','TO-2025-12-001',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 10:29:03','2025-12-19 10:29:03'),
(102,1,'Create Putaway TO','Membuat Transfer Order Putaway untuk 2199.50 Kg Whimol 15 CG-I (white mineral oil)',12,NULL,NULL,NULL,'28.F.WML0001',NULL,0.00,2199.50,'152','147','TO-2025-12-002',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 10:34:46','2025-12-19 10:34:46'),
(103,1,'Complete TO Item','Menyelesaikan transfer 2199.50 Kg dari QRT-HALAL ke RM-RJT-1',12,NULL,NULL,NULL,'28.F.WML0001',NULL,0.00,2199.50,'152','147','TO-2025-12-002',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 10:35:23','2025-12-19 10:35:23'),
(104,1,'Create Putaway TO','Membuat Transfer Order Putaway untuk 659.60 Kg PMX 0245',10,NULL,NULL,NULL,'26.APMX',NULL,0.00,659.60,'152','102','TO-2025-12-003',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 10:49:57','2025-12-19 10:49:57'),
(105,1,'Complete TO Item','Menyelesaikan transfer 659.60 Kg dari QRT-HALAL ke C-1-4',10,NULL,NULL,NULL,'26.APMX',NULL,0.00,659.60,'152','102','TO-2025-12-003',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 10:50:30','2025-12-19 10:50:30'),
(106,1,'Create Putaway TO','Membuat Transfer Order Putaway untuk 2199.50 Kg Whimol 15 CG-I (white mineral oil)',12,NULL,NULL,NULL,'25.LWML',NULL,0.00,2199.50,'152','59','TO-2025-12-004',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 10:53:34','2025-12-19 10:53:34'),
(107,1,'Complete TO Item','Menyelesaikan transfer 2199.50 Kg dari QRT-HALAL ke B-3-1',12,NULL,NULL,NULL,'25.LWML',NULL,0.00,2199.50,'152','59','TO-2025-12-004',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 10:54:13','2025-12-19 10:54:13'),
(108,1,'Create','Menambahkan Material baru: Tegosoft (14216)',212,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14216\\\",\\\"nama_material\\\":\\\"Tegosoft\\\",\\\"satuan\\\":\\\"KG\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"subkategori\\\":\\\"RM\\\",\\\"halal_status\\\":\\\"Halal\\\",\\\"qc_required\\\":false,\\\"expiry_required\\\":false,\\\"status\\\":\\\"active\\\",\\\"updated_at\\\":\\\"2025-12-19T06:19:14.000000Z\\\",\\\"created_at\\\":\\\"2025-12-19T06:19:14.000000Z\\\",\\\"id\\\":212}\"','103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 13:19:14','2025-12-19 13:19:14'),
(109,1,'Create','Menambahkan Material baru: Cap transparan flip top neck 24 (23255)',213,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23255\\\",\\\"nama_material\\\":\\\"Cap transparan flip top neck 24\\\",\\\"satuan\\\":\\\"PCS\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"subkategori\\\":\\\"PM\\\",\\\"halal_status\\\":\\\"Halal\\\",\\\"qc_required\\\":false,\\\"expiry_required\\\":false,\\\"status\\\":\\\"active\\\",\\\"updated_at\\\":\\\"2025-12-19T06:29:35.000000Z\\\",\\\"created_at\\\":\\\"2025-12-19T06:29:35.000000Z\\\",\\\"id\\\":213}\"','103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 13:29:35','2025-12-19 13:29:35'),
(110,1,'Login','User berhasil Login ke sistem',NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,NULL,NULL,'118.99.103.162','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 13:30:48','2025-12-19 13:30:48'),
(111,1,'Update','Memperbarui Material: Botol Shampo Natur 80 ml (20011)',8,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"20011\\\",\\\"nama_material\\\":\\\"Botol Shampo Natur 80 ml\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP &\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":8,\\\"kode_item\\\":\\\"20011\\\",\\\"nama_material\\\":\\\"Botol Shampo Natur 80 ml\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP &\\\",\\\"satuan\\\":\\\"PCS\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T00:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T06:34:41.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','118.99.103.162','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 13:34:41','2025-12-19 13:34:41'),
(112,1,'Update','Memperbarui Material: Master box 150 ml isi 24 pcs - R25 (23590)',17,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23590\\\",\\\"nama_material\\\":\\\"Master box 150 ml isi 24 pcs - R25\\\",\\\"subkategori\\\":\\\"MASTER BOX\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":17,\\\"kode_item\\\":\\\"23590\\\",\\\"nama_material\\\":\\\"Master box 150 ml isi 24 pcs - R25\\\",\\\"subkategori\\\":\\\"MASTER BOX\\\",\\\"satuan\\\":\\\"PCS\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":true,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T00:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T06:34:48.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 13:34:48','2025-12-19 13:34:48'),
(113,1,'Create','Menambahkan Material baru: Shrink Box uk.230mmx170mm (23493)',214,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23493\\\",\\\"nama_material\\\":\\\"Shrink Box uk.230mmx170mm\\\",\\\"satuan\\\":\\\"PCS\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"subkategori\\\":\\\"PM\\\",\\\"halal_status\\\":\\\"Halal\\\",\\\"qc_required\\\":false,\\\"expiry_required\\\":false,\\\"status\\\":\\\"active\\\",\\\"updated_at\\\":\\\"2025-12-19T06:41:14.000000Z\\\",\\\"created_at\\\":\\\"2025-12-19T06:41:14.000000Z\\\",\\\"id\\\":214}\"','103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 13:41:14','2025-12-19 13:41:14'),
(114,1,'Login','User berhasil Login ke sistem',NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:49:45','2025-12-19 06:49:45'),
(115,1,'Update','Memperbarui Material: Purolan IHD (14319)',1,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14319\\\",\\\"nama_material\\\":\\\"Purolan IHD\\\",\\\"subkategori\\\":\\\"JERIGEN\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"PACKAGING MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":1,\\\"kode_item\\\":\\\"14319\\\",\\\"nama_material\\\":\\\"Purolan IHD\\\",\\\"subkategori\\\":\\\"JERIGEN\\\",\\\"satuan\\\":\\\"KG\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:20:08.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:20:08','2025-12-19 08:20:08'),
(116,1,'Bulk Update','Bulk Update Material: Celquad SC 240 C (14190)',2,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14190\\\",\\\"nama_material\\\":\\\"Celquad SC 240 C\\\",\\\"subkategori\\\":\\\"JERIGEN\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":2,\\\"kode_item\\\":\\\"14190\\\",\\\"nama_material\\\":\\\"Celquad SC 240 C\\\",\\\"subkategori\\\":\\\"JERIGEN\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:29:48.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:29:48','2025-12-19 08:29:48'),
(117,1,'Bulk Update','Bulk Update Material: DI Alpha Toc Acetate (60026)',3,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"60026\\\",\\\"nama_material\\\":\\\"DI Alpha Toc Acetate\\\",\\\"subkategori\\\":\\\"JERIGEN\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":3,\\\"kode_item\\\":\\\"60026\\\",\\\"nama_material\\\":\\\"DI Alpha Toc Acetate\\\",\\\"subkategori\\\":\\\"JERIGEN\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:29:48.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:29:48','2025-12-19 08:29:48'),
(118,1,'Bulk Update','Bulk Update Material: Dex (60014)',4,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"60014\\\",\\\"nama_material\\\":\\\"Dex\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":4,\\\"kode_item\\\":\\\"60014\\\",\\\"nama_material\\\":\\\"Dex\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:29:48.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:29:48','2025-12-19 08:29:48'),
(119,1,'Bulk Update','Bulk Update Material: Garam (60003)',5,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"60003\\\",\\\"nama_material\\\":\\\"Garam\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":5,\\\"kode_item\\\":\\\"60003\\\",\\\"nama_material\\\":\\\"Garam\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:30:35.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:30:35','2025-12-19 08:30:35'),
(120,1,'Bulk Update','Bulk Update Material: Salimid 115 (14237)',6,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14237\\\",\\\"nama_material\\\":\\\"Salimid 115\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":6,\\\"kode_item\\\":\\\"14237\\\",\\\"nama_material\\\":\\\"Salimid 115\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:30:35.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:30:35','2025-12-19 08:30:35'),
(121,1,'Bulk Update','Bulk Update Material: Botol Natur Shampoo 270 ml (22747)',7,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"22747\\\",\\\"nama_material\\\":\\\"Botol Natur Shampoo 270 ml\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP & \\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":7,\\\"kode_item\\\":\\\"22747\\\",\\\"nama_material\\\":\\\"Botol Natur Shampoo 270 ml\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP & \\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:31:17.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:17','2025-12-19 08:31:17'),
(122,1,'Bulk Update','Bulk Update Material: Botol Shampo Natur 80 ml (20011)',8,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"20011\\\",\\\"nama_material\\\":\\\"Botol Shampo Natur 80 ml\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP &\\\",\\\"satuan\\\":\\\"PCS\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','\"{\\\"id\\\":8,\\\"kode_item\\\":\\\"20011\\\",\\\"nama_material\\\":\\\"Botol Shampo Natur 80 ml\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP &\\\",\\\"satuan\\\":\\\"PCS\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T13:34:41.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:17','2025-12-19 08:31:17'),
(123,1,'Bulk Update','Bulk Update Material: Dus Satuan NATUR Shampoo Aloe Vera 140 ML - R23 (23365)',13,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23365\\\",\\\"nama_material\\\":\\\"Dus Satuan NATUR Shampoo Aloe Vera 140 ML - R23\\\",\\\"subkategori\\\":\\\"DUS SATUAN\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":13,\\\"kode_item\\\":\\\"23365\\\",\\\"nama_material\\\":\\\"Dus Satuan NATUR Shampoo Aloe Vera 140 ML - R23\\\",\\\"subkategori\\\":\\\"DUS SATUAN\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:31:17.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:17','2025-12-19 08:31:17'),
(124,1,'Bulk Update','Bulk Update Material: Dus satuan NATUR Shampoo Ginseng Extract 140 ML - R23 (23363)',14,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23363\\\",\\\"nama_material\\\":\\\"Dus satuan NATUR Shampoo Ginseng Extract 140 ML - R23\\\",\\\"subkategori\\\":\\\"DUS SATUAN\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":14,\\\"kode_item\\\":\\\"23363\\\",\\\"nama_material\\\":\\\"Dus satuan NATUR Shampoo Ginseng Extract 140 ML - R23\\\",\\\"subkategori\\\":\\\"DUS SATUAN\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:31:17.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:17','2025-12-19 08:31:17'),
(125,1,'Bulk Update','Bulk Update Material: Dus Satuan AZALEA Hair Vitamin With Zaitun Oil & Aloe Vera Extract 80 ml (23136)',15,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23136\\\",\\\"nama_material\\\":\\\"Dus Satuan AZALEA Hair Vitamin With Zaitun Oil & Aloe Vera Extract 80 ml\\\",\\\"subkategori\\\":\\\"DUS SATUAN\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":15,\\\"kode_item\\\":\\\"23136\\\",\\\"nama_material\\\":\\\"Dus Satuan AZALEA Hair Vitamin With Zaitun Oil & Aloe Vera Extract 80 ml\\\",\\\"subkategori\\\":\\\"DUS SATUAN\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:31:17.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:17','2025-12-19 08:31:17'),
(126,1,'Bulk Update','Bulk Update Material: Tube NATUR Conditioner 165 ML (23064)',16,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23064\\\",\\\"nama_material\\\":\\\"Tube NATUR Conditioner 165 ML\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP & \\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":16,\\\"kode_item\\\":\\\"23064\\\",\\\"nama_material\\\":\\\"Tube NATUR Conditioner 165 ML\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP & \\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:31:17.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:17','2025-12-19 08:31:17'),
(127,1,'Bulk Update','Bulk Update Material: Master Box MIZZU Hide\'em Contour And Concealer & Power Pop! Lip Glazed (23536)',18,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23536\\\",\\\"nama_material\\\":\\\"Master Box MIZZU Hide\'em Contour And Concealer & Power Pop! Lip Glazed\\\",\\\"subkategori\\\":\\\"MASTER BOX\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":18,\\\"kode_item\\\":\\\"23536\\\",\\\"nama_material\\\":\\\"Master Box MIZZU Hide\'em Contour And Concealer & Power Pop! Lip Glazed\\\",\\\"subkategori\\\":\\\"MASTER BOX\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:31:17.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:17','2025-12-19 08:31:17'),
(128,1,'Bulk Update','Bulk Update Material: Dus satuan NATUR Shampoo Ginseng Extract 80 ML - R23 (23357)',19,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23357\\\",\\\"nama_material\\\":\\\"Dus satuan NATUR Shampoo Ginseng Extract 80 ML - R23\\\",\\\"subkategori\\\":\\\"DUS SATUAN\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":19,\\\"kode_item\\\":\\\"23357\\\",\\\"nama_material\\\":\\\"Dus satuan NATUR Shampoo Ginseng Extract 80 ML - R23\\\",\\\"subkategori\\\":\\\"DUS SATUAN\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:31:17.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:17','2025-12-19 08:31:17'),
(129,1,'Bulk Update','Bulk Update Material: Sticker seal transparan 15 mm (23499)',20,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23499\\\",\\\"nama_material\\\":\\\"Sticker seal transparan 15 mm\\\",\\\"subkategori\\\":\\\"STICKER \\\\\\/ SACHE\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":20,\\\"kode_item\\\":\\\"23499\\\",\\\"nama_material\\\":\\\"Sticker seal transparan 15 mm\\\",\\\"subkategori\\\":\\\"STICKER \\\\\\/ SACHE\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:31:17.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:17','2025-12-19 08:31:17'),
(130,1,'Bulk Update','Bulk Update Material: Nipagin (60008)',9,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"60008\\\",\\\"nama_material\\\":\\\"Nipagin\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":9,\\\"kode_item\\\":\\\"60008\\\",\\\"nama_material\\\":\\\"Nipagin\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:31:38.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:38','2025-12-19 08:31:38'),
(131,1,'Bulk Update','Bulk Update Material: PMX 0245 (14316)',10,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14316\\\",\\\"nama_material\\\":\\\"PMX 0245\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":10,\\\"kode_item\\\":\\\"14316\\\",\\\"nama_material\\\":\\\"PMX 0245\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:31:38.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:38','2025-12-19 08:31:38'),
(132,1,'Bulk Update','Bulk Update Material: Menthyl Lactate (14341)',11,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14341\\\",\\\"nama_material\\\":\\\"Menthyl Lactate\\\",\\\"subkategori\\\":\\\"JERIGEN\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":11,\\\"kode_item\\\":\\\"14341\\\",\\\"nama_material\\\":\\\"Menthyl Lactate\\\",\\\"subkategori\\\":\\\"JERIGEN\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:31:38.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:38','2025-12-19 08:31:38'),
(133,1,'Bulk Update','Bulk Update Material: Whimol 15 CG-I (white mineral oil) (14294)',12,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14294\\\",\\\"nama_material\\\":\\\"Whimol 15 CG-I (white mineral oil)\\\",\\\"subkategori\\\":\\\"DRUM 100 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":12,\\\"kode_item\\\":\\\"14294\\\",\\\"nama_material\\\":\\\"Whimol 15 CG-I (white mineral oil)\\\",\\\"subkategori\\\":\\\"DRUM 100 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:31:38.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:31:38','2025-12-19 08:31:38'),
(134,1,'Bulk Update','Bulk Update Material: Sticker Cap MIZZU Hide\'em Contour And Concealer 5 gr (23478)',21,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23478\\\",\\\"nama_material\\\":\\\"Sticker Cap MIZZU Hide\'em Contour And Concealer 5 gr\\\",\\\"subkategori\\\":\\\"STICKER \\\\\\/ SACHE\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":21,\\\"kode_item\\\":\\\"23478\\\",\\\"nama_material\\\":\\\"Sticker Cap MIZZU Hide\'em Contour And Concealer 5 gr\\\",\\\"subkategori\\\":\\\"STICKER \\\\\\/ SACHE\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:32:22.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:32:22','2025-12-19 08:32:22'),
(135,1,'Bulk Update','Bulk Update Material: Tube putih 35 mm (23065)',23,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23065\\\",\\\"nama_material\\\":\\\"Tube putih 35 mm\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP & \\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":23,\\\"kode_item\\\":\\\"23065\\\",\\\"nama_material\\\":\\\"Tube putih 35 mm\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP & \\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:32:22.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:32:22','2025-12-19 08:32:22'),
(136,1,'Bulk Update','Bulk Update Material: Botol putih 150 ml (23543)',31,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23543\\\",\\\"nama_material\\\":\\\"Botol putih 150 ml\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP & \\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":31,\\\"kode_item\\\":\\\"23543\\\",\\\"nama_material\\\":\\\"Botol putih 150 ml\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP & \\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:32:22.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:32:22','2025-12-19 08:32:22'),
(137,1,'Bulk Update','Bulk Update Material: Dus Satuan Azalea Smooth Foot Cream 35 g (23279)',32,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23279\\\",\\\"nama_material\\\":\\\"Dus Satuan Azalea Smooth Foot Cream 35 g\\\",\\\"subkategori\\\":\\\"Dus Satuan Azal\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":32,\\\"kode_item\\\":\\\"23279\\\",\\\"nama_material\\\":\\\"Dus Satuan Azalea Smooth Foot Cream 35 g\\\",\\\"subkategori\\\":\\\"Dus Satuan Azal\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:32:22.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:32:22','2025-12-19 08:32:22'),
(138,1,'Bulk Update','Bulk Update Material: Lakban 0,5 Bening (20017)',33,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"20017\\\",\\\"nama_material\\\":\\\"Lakban 0,5 Bening\\\",\\\"subkategori\\\":\\\"\\\",\\\"satuan\\\":\\\"Roll\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":33,\\\"kode_item\\\":\\\"20017\\\",\\\"nama_material\\\":\\\"Lakban 0,5 Bening\\\",\\\"subkategori\\\":\\\"\\\",\\\"satuan\\\":\\\"Roll\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:32:22.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:32:22','2025-12-19 08:32:22'),
(139,1,'Bulk Update','Bulk Update Material: Botol Round Putih 250 ml - Neck 24 (23515)',36,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23515\\\",\\\"nama_material\\\":\\\"Botol Round Putih 250 ml - Neck 24\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP & \\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":36,\\\"kode_item\\\":\\\"23515\\\",\\\"nama_material\\\":\\\"Botol Round Putih 250 ml - Neck 24\\\",\\\"subkategori\\\":\\\"BOTOL, TUTUP & \\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:32:22.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:32:22','2025-12-19 08:32:22'),
(140,1,'Bulk Update','Bulk Update Material: Master box 9 in 1 - R23 (23492)',37,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23492\\\",\\\"nama_material\\\":\\\"Master box 9 in 1 - R23\\\",\\\"subkategori\\\":\\\"MASTER BOX\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":37,\\\"kode_item\\\":\\\"23492\\\",\\\"nama_material\\\":\\\"Master box 9 in 1 - R23\\\",\\\"subkategori\\\":\\\"MASTER BOX\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:32:22.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:32:22','2025-12-19 08:32:22'),
(141,1,'Bulk Update','Bulk Update Material: Master box 150 ml isi 24 pcs (23450)',38,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"23450\\\",\\\"nama_material\\\":\\\"Master box 150 ml isi 24 pcs\\\",\\\"subkategori\\\":\\\"MASTER BOX\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":38,\\\"kode_item\\\":\\\"23450\\\",\\\"nama_material\\\":\\\"Master box 150 ml isi 24 pcs\\\",\\\"subkategori\\\":\\\"MASTER BOX\\\",\\\"satuan\\\":\\\"Pcs\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:32:22.000000Z\\\",\\\"kategori\\\":\\\"Packaging\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:32:22','2025-12-19 08:32:22'),
(142,1,'Bulk Update','Bulk Update Material: Carbopol SC-800 Polymer (14340)',22,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14340\\\",\\\"nama_material\\\":\\\"Carbopol SC-800 Polymer\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":22,\\\"kode_item\\\":\\\"14340\\\",\\\"nama_material\\\":\\\"Carbopol SC-800 Polymer\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:33:04.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:33:04','2025-12-19 08:33:04'),
(143,1,'Bulk Update','Bulk Update Material: Alkohol Teknis (14001)',24,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14001\\\",\\\"nama_material\\\":\\\"Alkohol Teknis\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":24,\\\"kode_item\\\":\\\"14001\\\",\\\"nama_material\\\":\\\"Alkohol Teknis\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:33:04.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:33:04','2025-12-19 08:33:04'),
(144,1,'Bulk Update','Bulk Update Material: AOS 92 (14345)',25,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14345\\\",\\\"nama_material\\\":\\\"AOS 92\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":25,\\\"kode_item\\\":\\\"14345\\\",\\\"nama_material\\\":\\\"AOS 92\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:33:04.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:33:05','2025-12-19 08:33:05'),
(145,1,'Bulk Update','Bulk Update Material: Alkohol Food (14212)',26,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14212\\\",\\\"nama_material\\\":\\\"Alkohol Food\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":26,\\\"kode_item\\\":\\\"14212\\\",\\\"nama_material\\\":\\\"Alkohol Food\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:33:05.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:33:05','2025-12-19 08:33:05'),
(146,1,'Bulk Update','Bulk Update Material: Zinc Undecylenate (14240)',27,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14240\\\",\\\"nama_material\\\":\\\"Zinc Undecylenate\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":27,\\\"kode_item\\\":\\\"14240\\\",\\\"nama_material\\\":\\\"Zinc Undecylenate\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:33:05.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:33:05','2025-12-19 08:33:05'),
(147,1,'Bulk Update','Bulk Update Material: Xiameter PMX 1501 (14226)',28,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14226\\\",\\\"nama_material\\\":\\\"Xiameter PMX 1501\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":28,\\\"kode_item\\\":\\\"14226\\\",\\\"nama_material\\\":\\\"Xiameter PMX 1501\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:33:05.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:33:05','2025-12-19 08:33:05'),
(148,1,'Bulk Update','Bulk Update Material: Crinipan AD (14038)',29,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14038\\\",\\\"nama_material\\\":\\\"Crinipan AD\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":29,\\\"kode_item\\\":\\\"14038\\\",\\\"nama_material\\\":\\\"Crinipan AD\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:33:05.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:33:05','2025-12-19 08:33:05'),
(149,1,'Bulk Update','Bulk Update Material: Tk 1000 (60011)',30,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"60011\\\",\\\"nama_material\\\":\\\"Tk 1000\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":30,\\\"kode_item\\\":\\\"60011\\\",\\\"nama_material\\\":\\\"Tk 1000\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:33:05.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:33:05','2025-12-19 08:33:05'),
(150,1,'Bulk Update','Bulk Update Material: Ms 1000 (60006)',34,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"60006\\\",\\\"nama_material\\\":\\\"Ms 1000\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":34,\\\"kode_item\\\":\\\"60006\\\",\\\"nama_material\\\":\\\"Ms 1000\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:33:05.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:33:05','2025-12-19 08:33:05'),
(151,1,'Bulk Update','Bulk Update Material: Emulgin HRE (14005)',35,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14005\\\",\\\"nama_material\\\":\\\"Emulgin HRE\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":35,\\\"kode_item\\\":\\\"14005\\\",\\\"nama_material\\\":\\\"Emulgin HRE\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:33:05.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:33:05','2025-12-19 08:33:05'),
(152,1,'Bulk Update','Bulk Update Material: Lanette O (14039)',39,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"14039\\\",\\\"nama_material\\\":\\\"Lanette O\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":39,\\\"kode_item\\\":\\\"14039\\\",\\\"nama_material\\\":\\\"Lanette O\\\",\\\"subkategori\\\":\\\"SAK 25 KG\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:33:05.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:33:05','2025-12-19 08:33:05'),
(153,1,'Bulk Update','Bulk Update Material: Cs 2000 (60002)',40,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,'\"{\\\"kode_item\\\":\\\"60002\\\",\\\"nama_material\\\":\\\"Cs 2000\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"kategori\\\":\\\"RAW MATERIAL\\\",\\\"halal_status\\\":\\\"halal\\\"}\"','\"{\\\"id\\\":40,\\\"kode_item\\\":\\\"60002\\\",\\\"nama_material\\\":\\\"Cs 2000\\\",\\\"subkategori\\\":\\\"DRUM 190 KG s\\\\\\/d\\\",\\\"satuan\\\":\\\"Kg\\\",\\\"deskripsi\\\":null,\\\"qc_required\\\":true,\\\"expiry_required\\\":false,\\\"abc_class\\\":null,\\\"default_supplier_id\\\":null,\\\"status\\\":\\\"active\\\",\\\"created_at\\\":\\\"2025-12-08T07:30:22.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-19T08:33:05.000000Z\\\",\\\"kategori\\\":\\\"Raw Material\\\",\\\"halal_status\\\":\\\"Halal\\\"}\"','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:33:05','2025-12-19 08:33:05'),
(154,1,'Update Picking Status','Picking Task status diubah menjadi In Progress oleh Njan Surenjan',NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,'RSV/20251219/0001',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:47:06','2025-12-19 08:47:06'),
(155,1,'Update Picking Status','Picking Task status diubah menjadi In Progress oleh Njan Surenjan',NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,'RSV/20251219/0001',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 08:50:47','2025-12-19 08:50:47'),
(156,1,'Login','User berhasil Login ke sistem',NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 16:23:35','2025-12-19 16:23:35'),
(157,1,'Logout','User Logout dari sistem',NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,NULL,NULL,NULL,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 16:36:37','2025-12-19 16:36:37');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cycle_counts`
--

DROP TABLE IF EXISTS `cycle_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cycle_counts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cycle_number` varchar(255) NOT NULL COMMENT 'SerialNumber di gambar (misal: 22637281025SKI)',
  `material_id` int(11) NOT NULL,
  `warehouse_bin_id` int(11) NOT NULL COMMENT 'Location / Bin ID',
  `system_qty` decimal(10,2) NOT NULL COMMENT 'Onhand Qty dari Sistem',
  `physical_qty` decimal(10,2) DEFAULT NULL COMMENT 'Qty hasil hitungan user',
  `scanned_serial` varchar(255) DEFAULT NULL COMMENT 'Inputan Scan Serial Number',
  `scanned_bin` varchar(255) DEFAULT NULL COMMENT 'Inputan Scan Bin',
  `status` varchar(50) NOT NULL DEFAULT 'DRAFT' COMMENT 'DRAFT, REVIEW, APPROVED',
  `spv_note` text DEFAULT NULL COMMENT 'Catatan Supervisor jika ada selisih',
  `count_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cycle_counts_material_id_foreign` (`material_id`),
  KEY `cycle_counts_warehouse_bin_id_foreign` (`warehouse_bin_id`),
  CONSTRAINT `cycle_counts_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cycle_counts_warehouse_bin_id_foreign` FOREIGN KEY (`warehouse_bin_id`) REFERENCES `warehouse_bins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cycle_counts`
--

LOCK TABLES `cycle_counts` WRITE;
/*!40000 ALTER TABLE `cycle_counts` DISABLE KEYS */;
/*!40000 ALTER TABLE `cycle_counts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `good_receipts`
--

DROP TABLE IF EXISTS `good_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `good_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gr_number` varchar(100) NOT NULL,
  `qc_checklist_id` int(11) DEFAULT NULL,
  `incoming_item_id` int(11) DEFAULT NULL,
  `material_id` int(11) NOT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `qty_received` decimal(10,2) NOT NULL,
  `uom` varchar(50) NOT NULL,
  `status_material` enum('KARANTINA','RELEASED','HOLD') DEFAULT 'KARANTINA',
  `warehouse_location` varchar(100) DEFAULT NULL,
  `tanggal_gr` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `gr_number` (`gr_number`),
  KEY `incoming_item_id` (`incoming_item_id`),
  KEY `material_id` (`material_id`),
  KEY `created_by` (`created_by`),
  KEY `idx_gr_number` (`gr_number`),
  KEY `idx_qc_checklist_id` (`qc_checklist_id`),
  KEY `idx_status_material` (`status_material`),
  CONSTRAINT `good_receipts_ibfk_1` FOREIGN KEY (`qc_checklist_id`) REFERENCES `qc_checklists` (`id`) ON DELETE SET NULL,
  CONSTRAINT `good_receipts_ibfk_2` FOREIGN KEY (`incoming_item_id`) REFERENCES `incoming_goods_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `good_receipts_ibfk_3` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`),
  CONSTRAINT `good_receipts_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1239 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `good_receipts`
--

LOCK TABLES `good_receipts` WRITE;
/*!40000 ALTER TABLE `good_receipts` DISABLE KEYS */;
INSERT INTO `good_receipts` VALUES
(1228,'GR/20251219/0001',84,1235,74,'20008191225NP',29990.00,'Pcs','RELEASED','QRT','2025-12-19 10:22:25',1,'2025-12-19 10:22:25','2025-12-19 10:22:25'),
(1229,'GR/20251219/0002',85,1236,97,'20013191225NP',29990.00,'Pcs','RELEASED','QRT','2025-12-19 10:22:40',1,'2025-12-19 10:22:40','2025-12-19 10:22:40'),
(1230,'GR/20251219/0003',87,1238,10,'26.APMX',659.60,'Kg','RELEASED','QRT','2025-12-19 10:49:29',1,'2025-12-19 10:49:29','2025-12-19 10:49:29'),
(1231,'GR/20251219/0004',88,1239,12,'25.LWML',2199.50,'Kg','RELEASED','QRT','2025-12-19 10:53:18',1,'2025-12-19 10:53:18','2025-12-19 10:53:18'),
(1232,'GR/20251219/0005',89,1240,68,'28.F.IPM',599.90,'Kg','RELEASED','QRT','2025-12-19 13:46:42',1,'2025-12-19 13:46:42','2025-12-19 13:46:42'),
(1233,'GR/20251219/0006',90,1252,69,'28.A.PHENOXETOL',1099.90,'Kg','RELEASED','QRT','2025-12-19 13:46:51',1,'2025-12-19 13:46:51','2025-12-19 13:46:51'),
(1234,'GR/20251219/0007',91,1251,31,'23543',44990.00,'Pcs','RELEASED','QRT','2025-12-19 13:47:00',1,'2025-12-19 13:47:00','2025-12-19 13:47:00'),
(1235,'GR/20251219/0008',92,1250,212,'28.H.TEGOSOFT',399.90,'KG','RELEASED','QRT','2025-12-19 13:47:10',1,'2025-12-19 13:47:10','2025-12-19 13:47:10'),
(1236,'GR/20251219/0009',93,1249,88,'23548191228ML',99990.00,'Pcs','RELEASED','QRT','2025-12-19 13:47:19',1,'2025-12-19 13:47:19','2025-12-19 13:47:19'),
(1237,'GR/20251219/0010',94,1248,127,'22671191225SUM',359.00,'Pcs','RELEASED','QRT','2025-12-19 13:47:32',1,'2025-12-19 13:47:32','2025-12-19 13:47:32'),
(1238,'GR/20251219/0011',95,1247,118,'27.J.BLACKSEED',499.90,'Kg','RELEASED','QRT','2025-12-19 13:47:42',1,'2025-12-19 13:47:42','2025-12-19 13:47:42');
/*!40000 ALTER TABLE `good_receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incoming_activity_logs`
--

DROP TABLE IF EXISTS `incoming_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incoming_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `incoming_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `qty_before` decimal(10,2) DEFAULT NULL,
  `qty_after` decimal(10,2) DEFAULT NULL,
  `bin_from` varchar(100) DEFAULT 'QRT',
  `bin_to` varchar(100) DEFAULT NULL,
  `reference_document` varchar(100) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_incoming_id` (`incoming_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_material_id` (`material_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `incoming_activity_logs_ibfk_1` FOREIGN KEY (`incoming_id`) REFERENCES `incoming_goods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `incoming_activity_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incoming_activity_logs_ibfk_3` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incoming_activity_logs`
--

LOCK TABLES `incoming_activity_logs` WRITE;
/*!40000 ALTER TABLE `incoming_activity_logs` DISABLE KEYS */;
INSERT INTO `incoming_activity_logs` VALUES
(51,NULL,1,'Create','Penerimaan Material Botol Shampo Natur 140 ml (20008) Batch 20008191225NP ke Bin QRT-HALAL. Qty: 30000 Pcs',74,'20008191225NP','2028-12-19',0.00,30000.00,NULL,'152','018106/SJ/XII/2025',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 03:21:29'),
(52,NULL,1,'Create','Penerimaan Material Tutup Hitam Botol Shampo Natur (20013) Batch 20013191225NP ke Bin QRT-HALAL. Qty: 30000 Pcs',97,'20013191225NP','2028-12-12',0.00,30000.00,NULL,'152','018106/SJ/XII/2025',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 03:21:29'),
(53,NULL,1,'Create','Penerimaan Material  () Batch 28.F.WML0001 ke Bin . Qty: 2200 ',12,'28.F.WML0001','2028-06-22',0.00,2200.00,NULL,'152','3150101268',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 03:23:58'),
(54,NULL,1,'Create','Penerimaan Material  () Batch 26.APMX ke Bin . Qty: 660 ',10,'26.APMX','2026-12-01',0.00,660.00,NULL,'152','B9-DO2504680',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 03:48:38'),
(55,NULL,1,'Create','Penerimaan Material  () Batch 25.LWML ke Bin . Qty: 2200 ',12,'25.LWML','2025-12-01',0.00,2200.00,NULL,'152','3150101231',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 03:53:05'),
(56,NULL,1,'Create','Penerimaan Material  () Batch 28.F.IPM ke Bin . Qty: 600 ',68,'28.F.IPM','2028-12-05',0.00,600.00,NULL,'152','02536/XI/TAN/25',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:10:42'),
(57,NULL,1,'Create','Penerimaan Material  () Batch 27.D.POMACE ke Bin . Qty: 1000 ',67,'27.D.POMACE','2028-12-08',0.00,1000.00,NULL,'152','M1-3995K/25',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:11:46'),
(58,NULL,1,'Create','Penerimaan Material BHT (14185) Batch 28.G.BHT ke Bin QRT-HALAL. Qty: 125 Kg',110,'28.G.BHT','2028-12-09',0.00,125.00,NULL,'152','41099',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:13:42'),
(59,NULL,1,'Create','Penerimaan Material DI Alpha Toc Acetate (60026) Batch 28.D.ALPHA ke Bin QRT-HALAL. Qty: 100 Kg',3,'28.D.ALPHA','2028-09-12',0.00,100.00,NULL,'152','41099',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:13:42'),
(60,NULL,1,'Create','Penerimaan Material  () Batch 27.A.FO ke Bin . Qty: 500 ',90,'27.A.FO','2028-09-12',0.00,500.00,NULL,'152','507922',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:16:00'),
(61,NULL,1,'Create','Penerimaan Material  () Batch 28.H.ELIANE ke Bin . Qty: 450 ',46,'28.H.ELIANE','2028-09-09',0.00,450.00,NULL,'152','507921',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:18:12'),
(62,NULL,1,'Create','Penerimaan Material  () Batch 28.C.WHIMOL ke Bin . Qty: 8000 ',12,'28.C.WHIMOL','2028-05-09',0.00,8000.00,NULL,'152','3150101631',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:26:00'),
(63,NULL,1,'Create','Penerimaan Material  () Batch 27.J.BLACKSEED ke Bin . Qty: 500 ',118,'27.J.BLACKSEED','2028-09-29',0.00,500.00,NULL,'152','50175176',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:26:47'),
(64,NULL,1,'Create','Penerimaan Material  () Batch 22671191225SUM ke Bin . Qty: 360 ',127,'22671191225SUM','2028-12-12',0.00,360.00,NULL,'152','SCP-2512/00051',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:28:57'),
(65,NULL,1,'Create','Penerimaan Material  () Batch 23548191228ML ke Bin . Qty: 100000 ',88,'23548191228ML','2028-12-12',0.00,100000.00,NULL,'152','N/A',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:32:29'),
(66,NULL,1,'Create','Penerimaan Material  () Batch 28.H.TEGOSOFT ke Bin . Qty: 400 ',212,'28.H.TEGOSOFT','2028-12-19',0.00,400.00,NULL,'152','4112153056',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:36:48'),
(67,NULL,1,'Create','Penerimaan Material  () Batch 23543 ke Bin . Qty: 45000 ',31,'23543','2028-02-01',0.00,45000.00,NULL,'152','DO-25-10535',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:45:26'),
(68,NULL,1,'Create','Penerimaan Material  () Batch 28.A.PHENOXETOL ke Bin . Qty: 1100 ',69,'28.A.PHENOXETOL','2028-12-09',0.00,1100.00,NULL,'152','02809/XII/TAN/25',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:46:13');
/*!40000 ALTER TABLE `incoming_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incoming_documents`
--

DROP TABLE IF EXISTS `incoming_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incoming_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `incoming_id` int(11) NOT NULL,
  `document_type` enum('surat_jalan','po','coa','label','photo','other') NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `idx_incoming_id` (`incoming_id`),
  CONSTRAINT `incoming_documents_ibfk_1` FOREIGN KEY (`incoming_id`) REFERENCES `incoming_goods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `incoming_documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incoming_documents`
--

LOCK TABLES `incoming_documents` WRITE;
/*!40000 ALTER TABLE `incoming_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `incoming_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incoming_goods`
--

DROP TABLE IF EXISTS `incoming_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incoming_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `incoming_number` varchar(100) NOT NULL,
  `no_surat_jalan` varchar(100) DEFAULT NULL,
  `po_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) NOT NULL,
  `no_kendaraan` varchar(50) DEFAULT NULL,
  `nama_driver` varchar(100) DEFAULT NULL,
  `tanggal_terima` datetime NOT NULL,
  `kategori` enum('Raw Material','Packaging Material','Spare Part','Office Supply') NOT NULL,
  `status` enum('Karantina','Received','QC Pending','QC Approved','QC Rejected','Completed','RELEASED') DEFAULT 'Karantina',
  `received_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `po_id` (`po_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `received_by` (`received_by`),
  KEY `idx_no_surat_jalan` (`no_surat_jalan`),
  KEY `idx_tanggal_terima` (`tanggal_terima`),
  KEY `idx_status` (`status`),
  CONSTRAINT `incoming_goods_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `incoming_goods_ibfk_3` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incoming_goods_ibfk_4` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=471 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incoming_goods`
--

LOCK TABLES `incoming_goods` WRITE;
/*!40000 ALTER TABLE `incoming_goods` DISABLE KEYS */;
INSERT INTO `incoming_goods` VALUES
(453,'IN/27806','018106/SJ/XII/2025',274,7,'B 2345 FFG','DADANG','2025-12-09 16:47:00','Raw Material','Karantina',1,'2025-12-19 10:21:29','2025-12-19 10:21:29'),
(454,'IN/27806','018106/SJ/XII/2025',274,7,'B 2345 FFG','DADANG','2025-12-09 16:47:00','Raw Material','Karantina',1,'2025-12-19 10:21:29','2025-12-19 10:21:29'),
(455,'IN/27686','3150101268',275,12,'B 2345 FFB','ANTO','2025-11-26 13:34:00','Raw Material','Karantina',1,'2025-12-19 10:23:58','2025-12-19 10:23:58'),
(456,'IN/27677','B9-DO2504680',276,6,'B 6665 XCG','MANTO','2025-11-26 09:00:00','Raw Material','Karantina',1,'2025-12-19 10:48:38','2025-12-19 10:48:38'),
(457,'IN/27685','3150101231',275,12,'B 5252 HJK','KEN','2025-11-26 11:20:00','Raw Material','Karantina',1,'2025-12-19 10:53:05','2025-12-19 10:53:05'),
(458,'IN/27530','02536/XI/TAN/25',277,518,'B 2332 FNG','DADANG','2025-11-07 15:22:00','Raw Material','Karantina',1,'2025-12-19 13:10:42','2025-12-19 13:10:42'),
(459,'IN/27506','M1-3995K/25',278,549,'B 6656 GNF','ADUL','2025-11-04 11:59:00','Raw Material','Karantina',1,'2025-12-19 13:11:46','2025-12-19 13:11:46'),
(460,'IN/27327','41099',279,2,'B 8878 FXN','UDIN','2025-10-13 10:22:00','Raw Material','Karantina',1,'2025-12-19 13:13:42','2025-12-19 13:13:42'),
(461,'IN/27327','41099',279,2,'B 8878 FXN','UDIN','2025-10-13 10:22:00','Raw Material','Karantina',1,'2025-12-19 13:13:42','2025-12-19 13:13:42'),
(462,'IN/27868','507922',280,56,'B 2827 FGH','OTO','2025-12-18 09:47:00','Raw Material','Karantina',1,'2025-12-19 13:16:00','2025-12-19 13:16:00'),
(463,'IN/27869','507921',281,56,'B 8292 XN','RONI','2025-12-18 09:48:00','Raw Material','Karantina',1,'2025-12-19 13:18:12','2025-12-19 13:18:12'),
(464,'IN/27799','3150101631',282,12,'B 3646 FGH','HENDRA','2025-12-08 11:40:00','Raw Material','Karantina',1,'2025-12-19 13:26:00','2025-12-19 13:26:00'),
(465,'IN/27164','50175176',283,524,'B 6467 FGH','JENDA','2025-09-30 11:46:00','Raw Material','Karantina',1,'2025-12-19 13:26:47','2025-12-19 13:26:47'),
(466,'IN/27763','SCP-2512/00051',284,540,'B 2343 FJH','NOPAL','2025-12-02 16:26:00','Raw Material','Karantina',1,'2025-12-19 13:28:57','2025-12-19 13:28:57'),
(467,'IN/27522','N/A',285,532,'B 2898 GHG','OJAN','2025-11-27 11:33:00','Raw Material','Karantina',1,'2025-12-19 13:32:29','2025-12-19 13:32:29'),
(468,'IN/27396','4112153056',287,1,'B 1727 GHJ','OKI','2025-10-20 11:50:00','Raw Material','Karantina',1,'2025-12-19 13:36:48','2025-12-19 13:36:48'),
(469,'IN/27694','DO-25-10535',290,528,'B 8988 NNB','NDANG','2025-12-04 09:43:00','Raw Material','Karantina',1,'2025-12-19 13:45:26','2025-12-19 13:45:26'),
(470,'IN/27766','02809/XII/TAN/25',291,518,'B 2889 KLJ','KADI','2025-12-03 09:58:00','Raw Material','Karantina',1,'2025-12-19 13:46:13','2025-12-19 13:46:13');
/*!40000 ALTER TABLE `incoming_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incoming_goods_items`
--

DROP TABLE IF EXISTS `incoming_goods_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incoming_goods_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `incoming_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `bin_target` varchar(255) DEFAULT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `qty_wadah` int(11) DEFAULT NULL,
  `qty_unit` int(11) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `kondisi_baik` tinyint(1) DEFAULT 0,
  `kondisi_tidak_baik` tinyint(1) DEFAULT 0,
  `coa_ada` tinyint(1) DEFAULT 0,
  `coa_tidak_ada` tinyint(1) DEFAULT 0,
  `label_mfg_ada` tinyint(1) DEFAULT 0,
  `label_mfg_tidak_ada` tinyint(1) DEFAULT 0,
  `label_coa_sesuai` tinyint(1) DEFAULT 0,
  `label_coa_tidak_sesuai` tinyint(1) DEFAULT 0,
  `pabrik_pembuat` varchar(200) DEFAULT NULL,
  `status_qc` enum('To QC','In QC','PASS','REJECT','Direct Putaway') DEFAULT 'To QC',
  `is_halal` tinyint(1) NOT NULL DEFAULT 0,
  `is_non_halal` tinyint(1) NOT NULL DEFAULT 0,
  `qr_code` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `material_id` (`material_id`),
  KEY `idx_incoming_id` (`incoming_id`),
  KEY `idx_batch_lot` (`batch_lot`),
  KEY `idx_qr_code` (`qr_code`(255)),
  KEY `idx_status_qc` (`status_qc`),
  CONSTRAINT `incoming_goods_items_ibfk_1` FOREIGN KEY (`incoming_id`) REFERENCES `incoming_goods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `incoming_goods_items_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1253 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incoming_goods_items`
--

LOCK TABLES `incoming_goods_items` WRITE;
/*!40000 ALTER TABLE `incoming_goods_items` DISABLE KEYS */;
INSERT INTO `incoming_goods_items` VALUES
(1235,453,74,'QRT-HALAL','20008191225NP','2028-12-19',100,29990,'Pcs',1,0,1,0,1,0,1,0,'Natamas Plast, PT','PASS',1,0,'IN/27806|20008|20008191225NP|30000|2028-12-19',NULL,'2025-12-19 10:21:29','2025-12-19 10:22:25'),
(1236,454,97,'QRT-HALAL','20013191225NP','2028-12-12',10,29990,'Pcs',1,0,1,0,1,0,1,0,'Natamas Plast, PT','PASS',1,0,'IN/27806|20013|20013191225NP|30000|2028-12-12',NULL,'2025-12-19 10:21:29','2025-12-19 10:22:40'),
(1237,455,12,'QRT-HALAL','28.F.WML0001','2028-06-22',10,2200,'Kg',1,0,1,0,1,0,1,0,'Dunia Kimia Jaya, PT','REJECT',1,0,'IN/27686|14294|28.F.WML0001|2200|2028-06-22',NULL,'2025-12-19 10:23:58','2025-12-19 10:33:59'),
(1238,456,10,'QRT-HALAL','26.APMX','2026-12-01',3,660,'Kg',1,0,1,0,1,0,1,0,'Maha Kimia Indonesia, PT','PASS',1,0,'IN/27677|14316|26.APMX|660|2026-12-01',NULL,'2025-12-19 10:48:38','2025-12-19 10:49:29'),
(1239,457,12,'QRT-HALAL','25.LWML','2025-12-01',10,2200,'Kg',1,0,1,0,1,0,1,0,'Dunia Kimia Jaya, PT','PASS',1,0,'IN/27685|14294|25.LWML|2200|2025-12-01',NULL,'2025-12-19 10:53:05','2025-12-19 10:53:18'),
(1240,458,68,'QRT-HALAL','28.F.IPM','2028-12-05',5,600,'Kg',1,0,1,0,1,0,1,0,'Tentrem Artha Nugraha, PT','PASS',1,0,'IN/27530|14008|28.F.IPM|600|2028-12-05',NULL,'2025-12-19 13:10:42','2025-12-19 13:46:42'),
(1241,459,67,'QRT-HALAL','27.D.POMACE','2028-12-08',5,200,'Kg',1,0,1,0,1,0,1,0,'Merpati Mahardika, PT','To QC',1,0,'IN/27506|14067|27.D.POMACE|1000|2028-12-08',NULL,'2025-12-19 13:11:46','2025-12-19 13:11:46'),
(1242,460,110,'QRT-HALAL','28.G.BHT','2028-12-09',5,25,'Kg',1,0,1,0,1,0,1,0,'Tirta Buana Kemindo, PT','To QC',1,0,'IN/27327|14185|28.G.BHT|125|2028-12-09',NULL,'2025-12-19 13:13:42','2025-12-19 13:13:42'),
(1243,461,3,'QRT-HALAL','28.D.ALPHA','2028-09-12',5,20,'Kg',1,0,1,0,1,0,1,0,'Tirta Buana Kemindo, PT','To QC',1,0,'IN/27327|60026|28.D.ALPHA|100|2028-09-12',NULL,'2025-12-19 13:13:42','2025-12-19 13:13:42'),
(1244,462,90,'QRT-HALAL','27.A.FO','2028-09-12',5,100,'Kg',1,0,1,0,1,0,1,0,'Mane Indonesia, PT','To QC',1,0,'IN/27868|14209|27.A.FO|500|2028-09-12',NULL,'2025-12-19 13:16:00','2025-12-19 13:16:00'),
(1245,463,46,'QRT-HALAL','28.H.ELIANE','2028-09-09',6,75,'Kg',1,0,1,0,1,0,1,0,'Mane Indonesia, PT','To QC',1,0,'IN/27869|14210|28.H.ELIANE|450|2028-09-09',NULL,'2025-12-19 13:18:12','2025-12-19 13:18:12'),
(1246,464,12,'QRT-HALAL','28.C.WHIMOL','2028-05-09',40,200,'Kg',1,0,1,0,1,0,1,0,'Dunia Kimia Jaya, PT','To QC',1,0,'IN/27799|14294|28.C.WHIMOL|8000|2028-05-09',NULL,'2025-12-19 13:26:00','2025-12-19 13:26:00'),
(1247,465,118,'QRT-HALAL','27.J.BLACKSEED','2028-09-29',10,500,'Kg',1,0,1,0,1,0,1,0,'Haldin Pacific Semesta, PT','PASS',1,0,'IN/27164|14295|27.J.BLACKSEED|500|2028-09-29',NULL,'2025-12-19 13:26:47','2025-12-19 13:47:42'),
(1248,466,127,'QRT-HALAL','22671191225SUM','2028-12-12',5,359,'Pcs',1,0,1,0,1,0,1,0,'Sinar Utama Mandiri, PT','PASS',1,0,'IN/27763|22671|22671191225SUM|360|2028-12-12',NULL,'2025-12-19 13:28:57','2025-12-19 13:47:32'),
(1249,467,88,'QRT-HALAL','23548191228ML','2028-12-12',10,99990,'Pcs',1,0,1,0,1,0,1,0,'Master Label, PT','PASS',1,0,'IN/27522|23548|23548191228ML|100000|2028-12-12',NULL,'2025-12-19 13:32:29','2025-12-19 13:47:19'),
(1250,468,212,'QRT-HALAL','28.H.TEGOSOFT','2028-12-19',10,400,'KG',1,0,1,0,1,0,1,0,'DKSH Indonesia, PT','PASS',1,0,'IN/27396|14216|28.H.TEGOSOFT|400|2028-12-19',NULL,'2025-12-19 13:36:48','2025-12-19 13:47:10'),
(1251,469,31,'QRT-HALAL','23543','2028-02-01',300,44990,'Pcs',1,0,1,0,1,0,1,0,'Jayatama selaras, PT','PASS',1,0,'IN/27694|23543|23543|45000|2028-02-01',NULL,'2025-12-19 13:45:26','2025-12-19 13:47:00'),
(1252,470,69,'QRT-HALAL','28.A.PHENOXETOL','2028-12-09',5,1100,'Kg',1,0,1,0,1,0,1,0,'Tentrem Artha Nugraha, PT','PASS',1,0,'IN/27766|60017|28.A.PHENOXETOL|1100|2028-12-09',NULL,'2025-12-19 13:46:13','2025-12-19 13:46:51');
/*!40000 ALTER TABLE `incoming_goods_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_stock`
--

DROP TABLE IF EXISTS `inventory_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `bin_id` int(11) NOT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `exp_date` date NOT NULL DEFAULT '2026-12-31',
  `qty_on_hand` decimal(10,2) NOT NULL,
  `qty_reserved` decimal(10,2) DEFAULT 0.00,
  `qty_available` decimal(10,2) DEFAULT NULL,
  `uom` varchar(50) NOT NULL,
  `status` enum('KARANTINA','RELEASED','HOLD','REJECTED') NOT NULL,
  `gr_id` int(11) DEFAULT NULL,
  `last_movement_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_material_warehouse_bin_batch` (`material_id`,`warehouse_id`,`bin_id`,`batch_lot`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `idx_bin_id` (`bin_id`),
  KEY `idx_status` (`status`),
  KEY `inventory_stock_ibfk_4` (`gr_id`),
  CONSTRAINT `inventory_stock_ibfk_1` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`),
  CONSTRAINT `inventory_stock_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`),
  CONSTRAINT `inventory_stock_ibfk_3` FOREIGN KEY (`bin_id`) REFERENCES `warehouse_bins` (`id`),
  CONSTRAINT `inventory_stock_ibfk_4` FOREIGN KEY (`gr_id`) REFERENCES `incoming_goods` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=951 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_stock`
--

LOCK TABLES `inventory_stock` WRITE;
/*!40000 ALTER TABLE `inventory_stock` DISABLE KEYS */;
INSERT INTO `inventory_stock` VALUES
(931,74,1,188,'20008191225NP','2028-12-19',29990.00,0.00,29990.00,'Pcs','RELEASED',453,'2025-12-19 10:29:03','2025-12-19 10:29:03','2025-12-19 10:29:03'),
(932,97,1,205,'20013191225NP','2028-12-12',29990.00,0.00,29990.00,'Pcs','RELEASED',454,'2025-12-19 10:29:03','2025-12-19 10:29:03','2025-12-19 10:29:03'),
(935,10,1,102,'26.APMX','2026-12-01',659.60,0.00,659.60,'Kg','RELEASED',456,'2025-12-19 10:50:30','2025-12-19 10:50:30','2025-12-19 10:50:30'),
(937,12,1,59,'25.LWML','2025-12-01',2199.50,133.47,2066.03,'Kg','RELEASED',457,'2025-12-19 10:54:13','2025-12-19 10:54:13','2025-12-19 08:46:45'),
(938,68,1,152,'28.F.IPM','2028-12-05',599.90,3.00,596.90,'Kg','RELEASED',458,'2025-12-19 13:46:42','2025-12-19 13:10:42','2025-12-19 08:46:45'),
(939,67,1,152,'27.D.POMACE','2028-12-08',1000.00,0.00,1000.00,'Kg','KARANTINA',459,'2025-12-19 13:11:46','2025-12-19 13:11:46','2025-12-19 13:11:46'),
(940,110,1,152,'28.G.BHT','2028-12-09',125.00,0.00,125.00,'Kg','KARANTINA',460,'2025-12-19 13:13:42','2025-12-19 13:13:42','2025-12-19 13:13:42'),
(941,3,1,152,'28.D.ALPHA','2028-09-12',100.00,0.00,100.00,'Kg','KARANTINA',461,'2025-12-19 13:13:42','2025-12-19 13:13:42','2025-12-19 13:13:42'),
(942,90,1,152,'27.A.FO','2028-09-12',500.00,0.00,500.00,'Kg','KARANTINA',462,'2025-12-19 13:16:00','2025-12-19 13:16:00','2025-12-19 13:16:00'),
(943,46,1,152,'28.H.ELIANE','2028-09-09',450.00,0.00,450.00,'Kg','KARANTINA',463,'2025-12-19 13:18:12','2025-12-19 13:18:12','2025-12-19 13:18:12'),
(944,12,1,152,'28.C.WHIMOL','2028-05-09',8000.00,0.00,8000.00,'Kg','KARANTINA',464,'2025-12-19 13:26:00','2025-12-19 13:26:00','2025-12-19 13:26:00'),
(945,118,1,152,'27.J.BLACKSEED','2028-09-29',499.90,0.30,499.60,'Kg','RELEASED',465,'2025-12-19 13:47:42','2025-12-19 13:26:47','2025-12-19 08:46:46'),
(946,127,1,152,'22671191225SUM','2028-12-12',359.00,0.75,358.25,'Pcs','RELEASED',466,'2025-12-19 13:47:32','2025-12-19 13:28:57','2025-12-19 08:46:45'),
(947,88,1,152,'23548191228ML','2028-12-12',99990.00,0.00,99990.00,'Pcs','RELEASED',467,'2025-12-19 13:47:19','2025-12-19 13:32:29','2025-12-19 13:47:19'),
(948,212,1,152,'28.H.TEGOSOFT','2028-12-19',399.90,0.37,399.53,'KG','RELEASED',468,'2025-12-19 13:47:10','2025-12-19 13:36:48','2025-12-19 08:46:46'),
(949,31,1,152,'23543','2028-02-01',44990.00,1241.00,43749.00,'Pcs','RELEASED',469,'2025-12-19 13:47:00','2025-12-19 13:45:26','2025-12-19 08:46:45'),
(950,69,1,152,'28.A.PHENOXETOL','2028-12-09',1099.90,0.30,1099.60,'Kg','RELEASED',470,'2025-12-19 13:46:51','2025-12-19 13:46:13','2025-12-19 08:46:46');
/*!40000 ALTER TABLE `inventory_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material_labels`
--

DROP TABLE IF EXISTS `material_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `material_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label_number` varchar(100) NOT NULL,
  `qr_code` text DEFAULT NULL,
  `incoming_item_id` int(11) DEFAULT NULL,
  `material_id` int(11) NOT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `qty` decimal(10,2) DEFAULT NULL,
  `uom` varchar(50) DEFAULT NULL,
  `status` enum('KARANTINA','RELEASED','REJECT','HOLD','RETURNED') DEFAULT 'KARANTINA',
  `warehouse_location` varchar(100) DEFAULT NULL,
  `printed_at` datetime DEFAULT NULL,
  `printed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `label_number` (`label_number`),
  KEY `incoming_item_id` (`incoming_item_id`),
  KEY `material_id` (`material_id`),
  KEY `printed_by` (`printed_by`),
  KEY `idx_label_number` (`label_number`),
  KEY `idx_qr_code` (`qr_code`(255)),
  KEY `idx_batch_lot` (`batch_lot`),
  KEY `idx_status` (`status`),
  CONSTRAINT `material_labels_ibfk_1` FOREIGN KEY (`incoming_item_id`) REFERENCES `incoming_goods_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `material_labels_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`),
  CONSTRAINT `material_labels_ibfk_3` FOREIGN KEY (`printed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_labels`
--

LOCK TABLES `material_labels` WRITE;
/*!40000 ALTER TABLE `material_labels` DISABLE KEYS */;
/*!40000 ALTER TABLE `material_labels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material_reqc`
--

DROP TABLE IF EXISTS `material_reqc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `material_reqc` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `material_id` bigint(20) unsigned NOT NULL,
  `inventory_stock_id` bigint(20) unsigned NOT NULL,
  `batch_lot` varchar(255) NOT NULL,
  `old_exp_date` date NOT NULL,
  `new_exp_date` date DEFAULT NULL,
  `bin_from_id` bigint(20) unsigned NOT NULL,
  `bin_qrt_id` bigint(20) unsigned DEFAULT NULL,
  `qty` decimal(10,2) NOT NULL,
  `status` enum('PENDING_TRANSFER','IN_QRT','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING_TRANSFER',
  `qc_notes` text DEFAULT NULL,
  `qc_by` bigint(20) unsigned DEFAULT NULL,
  `qc_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_reqc`
--

LOCK TABLES `material_reqc` WRITE;
/*!40000 ALTER TABLE `material_reqc` DISABLE KEYS */;
/*!40000 ALTER TABLE `material_reqc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materials`
--

DROP TABLE IF EXISTS `materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_item` varchar(50) NOT NULL,
  `nama_material` varchar(200) NOT NULL,
  `subkategori` varchar(125) NOT NULL,
  `satuan` varchar(50) NOT NULL COMMENT 'UoM',
  `deskripsi` text DEFAULT NULL,
  `qc_required` tinyint(1) DEFAULT 1 COMMENT 'Apakah material ini perlu QC',
  `expiry_required` tinyint(1) DEFAULT 0 COMMENT 'Apakah material ini punya exp date',
  `abc_class` enum('A','B','C') DEFAULT NULL COMMENT 'ABC classification for inventory management',
  `default_supplier_id` int(11) DEFAULT NULL COMMENT 'Default supplier untuk material ini',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `kategori` varchar(255) DEFAULT NULL,
  `halal_status` varchar(12) NOT NULL DEFAULT 'halal',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_item` (`kode_item`),
  KEY `default_supplier_id` (`default_supplier_id`),
  KEY `idx_kode_item` (`kode_item`),
  KEY `idx_kategori` (`kategori`),
  KEY `idx_abc_class` (`abc_class`),
  KEY `idx_status` (`status`),
  CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`default_supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materials`
--

LOCK TABLES `materials` WRITE;
/*!40000 ALTER TABLE `materials` DISABLE KEYS */;
INSERT INTO `materials` VALUES
(1,'14319','Purolan IHD','JERIGEN','KG',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:20:08','Raw Material','Halal'),
(2,'14190','Celquad SC 240 C','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:29:48','Raw Material','Halal'),
(3,'60026','DI Alpha Toc Acetate','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:29:48','Raw Material','Halal'),
(4,'60014','Dex','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:29:48','Raw Material','Halal'),
(5,'60003','Garam','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:30:35','Raw Material','Halal'),
(6,'14237','Salimid 115','SAK 25 KG','kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:30:35','Raw Material','Halal'),
(7,'22747','Botol Natur Shampoo 270 ml','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:31:17','Packaging','Halal'),
(8,'20011','Botol Shampo Natur 80 ml','BOTOL, TUTUP &','PCS',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 13:34:41','Packaging','Halal'),
(9,'60008','Nipagin','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:31:38','Raw Material','Halal'),
(10,'14316','PMX 0245','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:31:38','Raw Material','Halal'),
(11,'14341','Menthyl Lactate','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:31:38','Raw Material','Halal'),
(12,'14294','Whimol 15 CG-I (white mineral oil)','DRUM 100 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:31:38','Raw Material','Halal'),
(13,'23365','Dus Satuan NATUR Shampoo Aloe Vera 140 ML - R23','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:31:17','Packaging','Halal'),
(14,'23363','Dus satuan NATUR Shampoo Ginseng Extract 140 ML - R23','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:31:17','Packaging','Halal'),
(15,'23136','Dus Satuan AZALEA Hair Vitamin With Zaitun Oil & Aloe Vera Extract 80 ml','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:31:17','Packaging','Halal'),
(16,'23064','Tube NATUR Conditioner 165 ML','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:31:17','Packaging','Halal'),
(17,'23590','Master box 150 ml isi 24 pcs - R25','MASTER BOX','PCS',NULL,1,1,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 13:34:48','Packaging','Halal'),
(18,'23536','Master Box MIZZU Hide\'em Contour And Concealer & Power Pop! Lip Glazed','MASTER BOX','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:31:17','Packaging','Halal'),
(19,'23357','Dus satuan NATUR Shampoo Ginseng Extract 80 ML - R23','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:31:17','Packaging','Halal'),
(20,'23499','Sticker seal transparan 15 mm','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:31:17','Packaging','Halal'),
(21,'23478','Sticker Cap MIZZU Hide\'em Contour And Concealer 5 gr','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:32:22','Packaging','Halal'),
(22,'14340','Carbopol SC-800 Polymer','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:33:04','Raw Material','Halal'),
(23,'23065','Tube putih 35 mm','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:32:22','Packaging','Halal'),
(24,'14001','Alkohol Teknis','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:33:04','Raw Material','Halal'),
(25,'14345','AOS 92','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:33:04','Raw Material','Halal'),
(26,'14212','Alkohol Food','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:33:05','Raw Material','Halal'),
(27,'14240','Zinc Undecylenate','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:33:05','Raw Material','Halal'),
(28,'14226','Xiameter PMX 1501','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:33:05','Raw Material','Halal'),
(29,'14038','Crinipan AD','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:33:05','Raw Material','Halal'),
(30,'60011','Tk 1000','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:33:05','Raw Material','Halal'),
(31,'23543','Botol putih 150 ml','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:32:22','Packaging','Halal'),
(32,'23279','Dus Satuan Azalea Smooth Foot Cream 35 g','Dus Satuan Azal','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:32:22','Packaging','Halal'),
(33,'20017','Lakban 0,5 Bening','','Roll',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:32:22','Packaging','Halal'),
(34,'60006','Ms 1000','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:33:05','Raw Material','Halal'),
(35,'14005','Emulgin HRE','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:33:05','Raw Material','Halal'),
(36,'23515','Botol Round Putih 250 ml - Neck 24','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:32:22','Packaging','Halal'),
(37,'23492','Master box 9 in 1 - R23','MASTER BOX','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:32:22','Packaging','Halal'),
(38,'23450','Master box 150 ml isi 24 pcs','MASTER BOX','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:32:22','Packaging','Halal'),
(39,'14039','Lanette O','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:33:05','Raw Material','Halal'),
(40,'60002','Cs 2000','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-19 08:33:05','Raw Material','Halal'),
(41,'60010','Tea Tree Oil Pharma','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(42,'14063','Propylene Glycol USP','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(43,'60001','Cs 1000','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(44,'14033','L- Lysine','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(45,'14247','Vanilla Cheese Cake LL (R1513259)','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(46,'14210','Eliane','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(47,'14337','Hair Fantasy','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(48,'23433','Sachet NATUR Conditioner Argan Oil & Olive Oil 8 ML - R23','STICKER / SACHE','Roll',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(49,'23432','Sachet NATUR Conditioner Olive Oil & Aloe Vera 8 ML - R23','STICKER / SACHE','Roll',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(50,'22636','Botol Natur Hair Serum 60ml','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(51,'14342','Garam Pharma Grade','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(52,'60021','Edenor ST 01 L','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(64,'60009','Nipasol','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(65,'14071','Emulgade SE PF','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(66,'20138','Shrink Box Ms 140 ml','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(67,'14067','Pomace Olive Oil','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(68,'14008','IPM','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(69,'60017','Phenoxetol','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(70,'14298','Honey Glycolic Exract','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(71,'14268','Aloe Vera GE (ekstrak jadi)','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(72,'14292','Panax Ginseng GE (ekstrak jadi)','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(73,'23423','Masterbox Natur Conditioner all variant 160 ml','MASTER BOX','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(74,'20008','Botol Shampo Natur 140 ml','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(75,'14307','Bamboo Charcoal Powder','SAK 25 KG','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(76,'14289','Citric acid','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(77,'14238','Focogel 1.5P','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(78,'14100','Allantoin','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(79,'14147','Vaslina COP 3150','DRUM 100 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(80,'23564','Shrink Label Mizzu Swipe Up Micellar Water 150 ml','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(81,'23504','Sticker HG Facial Wash Brightening & Deep Cleansing 100 ml - R25','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(82,'23502','Sticker HG Facial Wash Acne Care & Oil Control 100 ml - R25','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(83,'23115','Botol Putih AZALEA Shampoo - New','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(84,'22401','Double Tape 1/2 inci','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(85,'14213','Rosehip Oil','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(86,'60030','Hydrolyzed collagen B','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(87,'14034','L- Glutamic Acid','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(88,'23548','Shrink Azalea Zaitun Oil with Habbatussauda Oil 135 ml - R25','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(89,'23366','S/L NATUR Shampoo Tea Tree Oil 140 ML - R23','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(90,'14209','Fresh Ocean','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(91,'14302','Viscolam CK 1','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(95,'60012','Tk 2000','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(96,'14099','Cutina AGST','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(97,'20013','Tutup Hitam Botol Shampo Natur','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(98,'20006','Lakban 2\" Bening','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(99,'14068','Parsol MCX','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(100,'14073','KOH','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(101,'14293','Nicotinamide Pharma Grade','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(102,'14335','Amodimethicone','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(103,'23462','Dus Satuan Natur Hair Vitamin Argan Oil & Olive','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(104,'23364','S/L NATUR Shampoo Aloe Vera 140 ML - R23','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(105,'23362','S/L NATUR Shampoo Ginseng Extract 140 ML - R23','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(106,'23245','Masterbox 4 in 1-New (Shampoo 140 R, Tonik Spray, 2 in 1 spray)','MASTER BOX','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(107,'14191','Carbomer 940','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(108,'22191','Shrink Conditioner 200 ml','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(109,'22893','Spray Hitam Tonik 90 ml','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(110,'14185','BHT','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(111,'14338','Argan Oil','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(112,'14250','White Velvet Cake LL (R1513181)','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(113,'60022','EDTA (Tetrasodium EDTA)','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(114,'23424','Botol transparan 150 ml','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(115,'22892','Botol Tonik HDPE Hitam 90 ml','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(116,'14233','Menthol Crystal','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(117,'14040','Gliserin','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(118,'14295','Black Seed Oil Soluble (Habbatussauda Oil)','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(119,'60016','Olyvem','DRUM 100 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(120,'14193','Arquat 16-29','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(121,'14192','Genamin KDMP','DRUM 100 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(122,'23390','S/L NATUR Shampoo Argan Oil 140 ML - R23','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(123,'23381','Dus Satuan Natur Kit Ginseng (shampoo & tonik) - R23 : GNAA002 + GNTV090 Redesign','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(124,'23368','S/L NATUR Shampoo Ginseng Extract 270 ML - R23','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(125,'23278','Stiker Tube AZALEA Smooth Foot Cream 35 g','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(126,'14205','Nusil CPF 3300','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(127,'22671','OPP Tape 2\" x 90 Yard Warna','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(128,'14046','Lanolin','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(129,'14219','Sweet Almond Oil','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(130,'14214','Cantella Asiatica Extract','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(131,'14224','Ekstrak Panax Ginseng','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(132,'23511','Sticker Natur Conditioner Tanpa Bilas Argan Oil & Olive Oil 30 ml','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(133,'23303','Sticker Belakang Botol HG For Men Shampoo 180 ml','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(134,'23302','Sticker Depan Botol HG For Men Shampoo 180 ml','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(135,'23370','S/L NATUR Shampoo Aloe Vera 270 ML - R23','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(136,'60013','Glydan','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(137,'23374','Sticker Tube NATUR Conditioner Ginseng & Olive Oil 160 ml – R23','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(138,'23359','Dus Satuan NATUR Shampoo Aloe Vera 80 ML - R23','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(139,'23391','Dus satuan NATUR Shampoo Argan Oil 140 ML - R23','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(140,'23387','Dus satuan NATUR Shampoo Moringa 140 ML - R23','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(159,'14239','Zinc Gluconate','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(160,'14183','Ms 300','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(161,'23411','Sticker Botol Natur Natural Extract Hair Tonic Ginseng Extract 90 ML - Redesign','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(162,'23386','S/L NATUR Shampoo Moringa 140 ML - R23','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(163,'14225','Panax Ginseng Oil','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(164,'23530','Shrink Botol Manila 600 ml (320 x 310 x 0.03 mm)','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(165,'14201','Ekstrak Swertia Japonica','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(166,'14334','Mond 807384','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(167,'22517','Shrink Botol Manila 600 ml (340 x 300 x 0.025 mm)','SHRINK LABEL / ','PCS',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(168,'14362','Silicone Elastomer RH-SEB-6061','JERIGEN','KG',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(169,'14203','Jojoba Oil','JERIGEN','KG',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(170,'14189','N-Hance CCG 45','SAK 25 KG','KG',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(171,'23517','Pump Disp Twist Lock Hijau - Neck 24','BOTOL, TUTUP & ','PCS',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(172,'23516','Pump Disp Twist Lock Biru - Neck 24','BOTOL, TUTUP & ','PCS',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(173,'23529','Sachet Natur Gentle Pure Hydrating Conditioner Moringa 8 mL','STICKER / SACHE','ROLL',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(174,'23528','Sachet Natur Gentle Pure Hydrating Conditioner Argan 8 mL','STICKER / SACHE','ROLL',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(175,'23527','Sachet Natur Gentle Pure Hydrating Shampoo Moringa 8 mL','STICKER / SACHE','ROLL',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(176,'23526','Sachet Natur Gentle Pure Hydrating Shampoo Argan 8 mL','STICKER / SACHE','ROLL',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(177,'23508','Sachet Azalea Zaitun Oil with Habbatussauda Oil 8 ml','STICKER / SACHE','ROLL',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(178,'14354','Cosmol 222','SAK 25 KG','KG',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(179,'14314','Sodium Hyaluronate','SAK 25 KG','KG',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(180,'14353','Sunsphere H-121','SAK 25 KG','KG',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(181,'23522','Master box Natur Gentle Pure Hyrating Shampoo 210 ml isi 24 pcs','MASTER BOX','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(182,'23521','Dus Satuan Natur Gentle Pure Hydrating Shampoo Moringa 210 mL','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(183,'23520','Dus Satuan Natur Gentle Pure Hydrating Shampoo Argan 210 mL','DUS SATUAN','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(184,'14347','Syn Mica 1500 SSA','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(185,'14344','Dreamspell Aura','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(186,'14351','I.O Black 3 AS','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(187,'14350','I.O Red 3 AS','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(188,'14232','Butylene Glycol','DRUM 190 KG s/d','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(189,'14343','Green Tea Flora 133','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(190,'23531','Tube Putih Glossy 165 mL - Cap Hijau','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(191,'23523','Tube Putih Glossy 165 mL - Cap Biru','BOTOL, TUTUP & ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(192,'22939','Masterbox Natur Shampoo 270 ml CR, SB, CK, RR','MASTER BOX','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(193,'23525','Sticker Natur Gentle Pure Hydrating Conditioner Moringa 165 mL','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(194,'23524','Sticker Natur Gentle Pure Hydrating Conditioner Argan 165 mL','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(195,'23519','Sticker Natur Gentle Pure Hydrating Shampoo Moringa 210 mL','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(196,'23518','Sticker Natur Gentle Pure Hydrating Shampoo Argan 210 mL','STICKER / SACHE','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(197,'14211','Aloe Vera Oil','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(198,'23356','S/L NATUR Shampoo Ginseng Extract 80 ML - R23','SHRINK LABEL / ','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(199,'14365','Cucumber Extract','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(200,'14366','Caffeine Extract','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(201,'14360','Lexfeel WOW DT','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(202,'14364','HDK H15','SAK 25 KG','Pcs',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(203,'14349','I.O Yellow 3 AS','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(204,'14348','TiO2 CR 50 AS','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(205,'14352','Dowsil ES 5600','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(206,'14356','Belsil TMS 803','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(207,'14358','Abil EM 90','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(208,'14363','Innosei PEG','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(209,'14357','Purolan IDD','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(210,'14359','Bentone Gel ISDV','JERIGEN','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(211,'14355','Belsil PMS MK','SAK 25 KG','Kg',NULL,1,0,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-15 15:52:54','RAW MATERIAL','halal'),
(212,'14216','Tegosoft','RM','KG',NULL,0,0,NULL,NULL,'active','2025-12-19 13:19:14','2025-12-19 13:19:14','Raw Material','Halal'),
(213,'23255','Cap transparan flip top neck 24','PM','PCS',NULL,0,0,NULL,NULL,'active','2025-12-19 13:29:35','2025-12-19 13:29:35','Packaging','Halal'),
(214,'23493','Shrink Box uk.230mmx170mm','PM','PCS',NULL,0,0,NULL,NULL,'active','2025-12-19 13:41:14','2025-12-19 13:41:14','Packaging','Halal');
/*!40000 ALTER TABLE `materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2025_12_01_164653_create_qc_reqc_history_table',1),
(2,'2025_12_09_140000_add_last_seen_at_to_users_table',2),
(3,'2025_12_11_155623_add_duration_tracking_to_reservation_requests_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expired_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(100) NOT NULL,
  `module` varchar(255) NOT NULL,
  `action` varchar(225) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES
(1,'incoming.view','incoming','view','Lihat data penerimaan barang','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(2,'incoming.create','incoming','create','Buat penerimaan barang baru','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(3,'incoming.edit','incoming','edit','Edit data penerimaan barang','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(4,'incoming.delete','incoming','delete','Hapus data penerimaan barang','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(5,'bin-to-bin.view','bin-to-bin','approve','Approve pemindahan barang','2025-10-14 23:27:05','2025-10-15 07:03:44'),
(6,'qc.view','qc','view','Lihat data quality control','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(7,'qc.input_qc_result','qc','input_qc_result','Input hasil QC','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(8,'qc.approve','qc','approve','Approve hasil QC','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(9,'qc.reject','qc','reject','Reject hasil QC','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(10,'label_karantina.view','label_karantina','view','Lihat label karantina','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(11,'label_karantina.cetak_label','label_karantina','cetak_label','Cetak label karantina','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(12,'label_karantina.release','label_karantina','release','Release dari karantina','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(13,'label_karantina.reject','label_karantina','reject','Reject barang karantina','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(14,'putaway.view','putaway','view','Lihat putaway & TO','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(15,'putaway.kerjakan_to','putaway','kerjakan_to','Kerjakan transfer order','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(16,'putaway.cetak_slip','putaway','cetak_slip','Cetak slip putaway','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(17,'reservation.view','reservation','view','Lihat reservation','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(18,'reservation.create_request','reservation','create_request','Buat request reservation','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(19,'reservation.approve_request','reservation','approve_request','Approve request reservation','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(20,'reservation.cetak_form','reservation','cetak_form','Cetak form reservation','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(21,'picking.view','picking','view','Lihat picking list','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(22,'picking.create','picking','kerjakan_picking','Kerjakan picking','2025-10-14 23:27:05','2025-11-12 07:49:52'),
(23,'picking.cetak_picking_list','picking','cetak_picking_list','Cetak picking list','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(24,'return.view','return','view','Lihat data return','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(25,'return.create_return','return','create_return','Buat return baru','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(26,'return.approve_return','return','approve_return','Approve return','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(27,'return.cetak_slip','return','cetak_slip','Cetak slip return','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(28,'central_data.sku_management_view','central_data','sku_management_view','Lihat SKU','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(29,'central_data.sku_management_create','central_data','sku_management_create','Buat SKU','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(30,'central_data.sku_management_edit','central_data','sku_management_edit','Edit SKU','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(31,'central_data.sku_management_delete','central_data','sku_management_delete','Hapus SKU','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(32,'central_data.sku_management_admin','central_data','sku_management_admin','Admin SKU','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(33,'central_data.supplier_management_view','central_data','supplier_management_view','Lihat Supplier','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(34,'central_data.supplier_management_create','central_data','supplier_management_create','Buat Supplier','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(35,'central_data.supplier_management_edit','central_data','supplier_management_edit','Edit Supplier','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(36,'central_data.supplier_management_delete','central_data','supplier_management_delete','Hapus Supplier','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(37,'central_data.supplier_management_admin','central_data','supplier_management_admin','Admin Supplier','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(38,'central_data.bin_management_view','central_data','bin_management_view','Lihat Bin','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(39,'central_data.bin_management_create','central_data','bin_management_create','Buat Bin','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(40,'central_data.bin_management_edit','central_data','bin_management_edit','Edit Bin','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(41,'central_data.bin_management_delete','central_data','bin_management_delete','Hapus Bin','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(42,'central_data.bin_management_admin','central_data','bin_management_admin','Admin Bin','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(43,'central_data.user_management_view','central_data','user_management_view','Lihat User','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(44,'central_data.user_management_create','central_data','user_management_create','Buat User','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(45,'central_data.user_management_edit','central_data','user_management_edit','Edit User','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(46,'central_data.user_management_delete','central_data','user_management_delete','Hapus User','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(47,'central_data.user_management_admin','central_data','user_management_admin','Admin User','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(48,'central_data.role_management_view','central_data','role_management_view','Lihat Role','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(49,'central_data.role_management_create','central_data','role_management_create','Buat Role','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(50,'central_data.role_management_edit','central_data','role_management_edit','Edit Role','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(51,'central_data.role_management_delete','central_data','role_management_delete','Hapus Role','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(52,'central_data.role_management_admin','central_data','role_management_admin','Admin Role','2025-10-14 23:27:05','2025-10-14 23:27:05'),
(53,'reservation.create','reservation-create','create','Add Reservation','2025-10-30 03:08:58','2025-10-30 03:09:19');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `po_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `qty_order` decimal(10,2) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_po_id` (`po_id`),
  KEY `idx_material_id` (`material_id`),
  CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_items`
--

LOCK TABLES `purchase_order_items` WRITE;
/*!40000 ALTER TABLE `purchase_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_po` varchar(100) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `tanggal_po` date NOT NULL,
  `tanggal_kirim_diharapkan` date DEFAULT NULL,
  `total_nilai` decimal(15,2) DEFAULT 0.00,
  `status` enum('pending','open','completed','cancelled') DEFAULT 'pending',
  `keterangan` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_po` (`no_po`),
  KEY `created_by` (`created_by`),
  KEY `idx_no_po` (`no_po`),
  KEY `idx_supplier_id` (`supplier_id`),
  KEY `idx_status` (`status`),
  KEY `idx_tanggal_po` (`tanggal_po`),
  CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=292 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
INSERT INTO `purchase_orders` VALUES
(274,'PO65766',7,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 10:21:29','2025-12-19 10:21:29'),
(275,'PO63670',12,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 10:23:58','2025-12-19 10:23:58'),
(276,'PO65789',6,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 10:48:38','2025-12-19 10:48:38'),
(277,'PO63628',518,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 13:10:42','2025-12-19 13:10:42'),
(278,'PO65111',549,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 13:11:46','2025-12-19 13:11:46'),
(279,'PO63617',2,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 13:13:42','2025-12-19 13:13:42'),
(280,'PO66811',56,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 13:16:00','2025-12-19 13:16:00'),
(281,'PO66661',56,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 13:18:12','2025-12-19 13:18:12'),
(282,'PO65012',12,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 13:26:00','2025-12-19 13:26:00'),
(283,'PO63616',524,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 13:26:47','2025-12-19 13:26:47'),
(284,'PO65770',540,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 13:28:57','2025-12-19 13:28:57'),
(285,'PO63743',532,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 13:32:29','2025-12-19 13:32:29'),
(287,'PO63624',1,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 13:36:48','2025-12-19 13:36:48'),
(290,'PO64892',528,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 13:45:26','2025-12-19 13:45:26'),
(291,'PO65777',518,'2025-12-19',NULL,0.00,'open',NULL,1,'2025-12-19 13:46:13','2025-12-19 13:46:13');
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qc_activity_logs`
--

DROP TABLE IF EXISTS `qc_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qc_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qc_checklist_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `qty_before` decimal(10,2) DEFAULT NULL,
  `qty_after` decimal(10,2) DEFAULT NULL,
  `bin_from` varchar(100) DEFAULT NULL,
  `bin_to` varchar(100) DEFAULT NULL,
  `reference_document` varchar(100) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_qc_checklist_id` (`qc_checklist_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_material_id` (`material_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `qc_activity_logs_ibfk_1` FOREIGN KEY (`qc_checklist_id`) REFERENCES `qc_checklists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `qc_activity_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `qc_activity_logs_ibfk_3` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qc_activity_logs`
--

LOCK TABLES `qc_activity_logs` WRITE;
/*!40000 ALTER TABLE `qc_activity_logs` DISABLE KEYS */;
INSERT INTO `qc_activity_logs` VALUES
(44,NULL,1,'PASS','Pemeriksaan QC untuk Botol Shampo Natur 140 ml dengan hasil PASS. Qty Tersisa: 29990.00.',74,'20008191225NP',NULL,0.00,29990.00,NULL,NULL,'QC/20251219/0001',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 03:22:25'),
(45,NULL,1,'PASS','Pemeriksaan QC untuk Tutup Hitam Botol Shampo Natur dengan hasil PASS. Qty Tersisa: 29990.00.',97,'20013191225NP',NULL,0.00,29990.00,NULL,NULL,'QC/20251219/0002',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 03:22:40'),
(46,NULL,1,'REJECT','Pemeriksaan QC untuk Whimol 15 CG-I (white mineral oil) dengan hasil REJECT. Qty Tersisa: 2199.50.',12,'28.F.WML0001',NULL,0.00,2199.50,NULL,NULL,'QC/20251219/0003',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 03:33:59'),
(47,NULL,1,'PASS','Pemeriksaan QC untuk PMX 0245 dengan hasil PASS. Qty Tersisa: 659.60.',10,'26.APMX',NULL,0.00,659.60,NULL,NULL,'QC/20251219/0004',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 03:49:29'),
(48,NULL,1,'PASS','Pemeriksaan QC untuk Whimol 15 CG-I (white mineral oil) dengan hasil PASS. Qty Tersisa: 2199.50.',12,'25.LWML',NULL,0.00,2199.50,NULL,NULL,'QC/20251219/0005',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 03:53:18'),
(49,NULL,1,'PASS','Pemeriksaan QC untuk IPM dengan hasil PASS. Qty Tersisa: 599.90.',68,'28.F.IPM',NULL,0.00,599.90,NULL,NULL,'QC/20251219/0006',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:46:42'),
(50,NULL,1,'PASS','Pemeriksaan QC untuk Phenoxetol dengan hasil PASS. Qty Tersisa: 1099.90.',69,'28.A.PHENOXETOL',NULL,0.00,1099.90,NULL,NULL,'QC/20251219/0007',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:46:51'),
(51,NULL,1,'PASS','Pemeriksaan QC untuk Botol putih 150 ml dengan hasil PASS. Qty Tersisa: 44990.00.',31,'23543',NULL,0.00,44990.00,NULL,NULL,'QC/20251219/0008',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:47:00'),
(52,NULL,1,'PASS','Pemeriksaan QC untuk Tegosoft dengan hasil PASS. Qty Tersisa: 399.90.',212,'28.H.TEGOSOFT',NULL,0.00,399.90,NULL,NULL,'QC/20251219/0009',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:47:10'),
(53,NULL,1,'PASS','Pemeriksaan QC untuk Shrink Azalea Zaitun Oil with Habbatussauda Oil 135 ml - R25 dengan hasil PASS. Qty Tersisa: 99990.00.',88,'23548191228ML',NULL,0.00,99990.00,NULL,NULL,'QC/20251219/0010',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:47:19'),
(54,NULL,1,'PASS','Pemeriksaan QC untuk OPP Tape 2\" x 90 Yard Warna dengan hasil PASS. Qty Tersisa: 359.00.',127,'22671191225SUM',NULL,0.00,359.00,NULL,NULL,'QC/20251219/0011',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:47:32'),
(55,NULL,1,'PASS','Pemeriksaan QC untuk Black Seed Oil Soluble (Habbatussauda Oil) dengan hasil PASS. Qty Tersisa: 499.90.',118,'27.J.BLACKSEED',NULL,0.00,499.90,NULL,NULL,'QC/20251219/0012',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 06:47:42');
/*!40000 ALTER TABLE `qc_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qc_checklist_details`
--

DROP TABLE IF EXISTS `qc_checklist_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qc_checklist_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qc_checklist_id` int(11) NOT NULL,
  `jumlah_box_utuh` int(11) DEFAULT NULL,
  `qty_box_utuh` decimal(10,2) DEFAULT NULL,
  `jumlah_box_tidak_utuh` int(11) DEFAULT NULL,
  `qty_box_tidak_utuh` decimal(10,2) DEFAULT NULL,
  `qty_sample` decimal(10,2) NOT NULL,
  `total_incoming` decimal(10,2) DEFAULT NULL,
  `uom` varchar(50) DEFAULT NULL,
  `defect_count` decimal(11,0) DEFAULT 0,
  `catatan_qc` text DEFAULT NULL,
  `hasil_qc` enum('PASS','REJECT') DEFAULT NULL,
  `qc_date` datetime DEFAULT NULL,
  `qc_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `qc_by` (`qc_by`),
  KEY `idx_qc_checklist_id` (`qc_checklist_id`),
  CONSTRAINT `qc_checklist_details_ibfk_1` FOREIGN KEY (`qc_checklist_id`) REFERENCES `qc_checklists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `qc_checklist_details_ibfk_2` FOREIGN KEY (`qc_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qc_checklist_details`
--

LOCK TABLES `qc_checklist_details` WRITE;
/*!40000 ALTER TABLE `qc_checklist_details` DISABLE KEYS */;
INSERT INTO `qc_checklist_details` VALUES
(49,84,NULL,NULL,NULL,NULL,10.00,300.00,'Pcs',0,'','PASS','2025-12-19 10:22:25',1,'2025-12-19 10:22:25','2025-12-19 10:22:25'),
(50,85,NULL,NULL,NULL,NULL,10.00,3000.00,'Pcs',0,'','PASS','2025-12-19 10:22:40',1,'2025-12-19 10:22:40','2025-12-19 10:22:40'),
(51,86,NULL,NULL,NULL,NULL,0.50,220.00,'Kg',0,'TMS','REJECT','2025-12-19 10:33:59',1,'2025-12-19 10:33:59','2025-12-19 10:33:59'),
(52,87,NULL,NULL,NULL,NULL,0.40,220.00,'Kg',0,'','PASS','2025-12-19 10:49:29',1,'2025-12-19 10:49:29','2025-12-19 10:49:29'),
(53,88,NULL,NULL,NULL,NULL,0.50,220.00,'Kg',0,'','PASS','2025-12-19 10:53:18',1,'2025-12-19 10:53:18','2025-12-19 10:53:18'),
(54,89,NULL,NULL,NULL,NULL,0.10,120.00,'Kg',0,'','PASS','2025-12-19 13:46:42',1,'2025-12-19 13:46:42','2025-12-19 13:46:42'),
(55,90,NULL,NULL,NULL,NULL,0.10,220.00,'Kg',0,'','PASS','2025-12-19 13:46:51',1,'2025-12-19 13:46:51','2025-12-19 13:46:51'),
(56,91,NULL,NULL,NULL,NULL,10.00,150.00,'Pcs',0,'','PASS','2025-12-19 13:47:00',1,'2025-12-19 13:47:00','2025-12-19 13:47:00'),
(57,92,NULL,NULL,NULL,NULL,0.10,40.00,'KG',0,'','PASS','2025-12-19 13:47:10',1,'2025-12-19 13:47:10','2025-12-19 13:47:10'),
(58,93,NULL,NULL,NULL,NULL,10.00,10000.00,'Pcs',0,'','PASS','2025-12-19 13:47:19',1,'2025-12-19 13:47:19','2025-12-19 13:47:19'),
(59,94,NULL,NULL,NULL,NULL,1.00,72.00,'Pcs',0,'','PASS','2025-12-19 13:47:32',1,'2025-12-19 13:47:32','2025-12-19 13:47:32'),
(60,95,NULL,NULL,NULL,NULL,0.10,50.00,'Kg',0,'','PASS','2025-12-19 13:47:42',1,'2025-12-19 13:47:42','2025-12-19 13:47:42');
/*!40000 ALTER TABLE `qc_checklist_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qc_checklists`
--

DROP TABLE IF EXISTS `qc_checklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qc_checklists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_form_checklist` varchar(100) NOT NULL,
  `incoming_item_id` int(11) DEFAULT NULL,
  `incoming_id` int(11) DEFAULT NULL,
  `po_id` varchar(123) DEFAULT NULL,
  `no_surat_jalan` varchar(100) DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `kategori` enum('Raw Material','Packaging Material','Spare Part','Office Supply') DEFAULT NULL,
  `no_kendaraan` varchar(50) DEFAULT NULL,
  `nama_driver` varchar(100) DEFAULT NULL,
  `tanggal_qc` datetime DEFAULT NULL,
  `qc_by` int(11) DEFAULT NULL,
  `status` enum('Draft','In Progress','Completed') DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_form_checklist` (`no_form_checklist`),
  KEY `incoming_id` (`incoming_id`),
  KEY `po_id` (`po_id`),
  KEY `material_id` (`material_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `qc_by` (`qc_by`),
  KEY `idx_no_form_checklist` (`no_form_checklist`),
  KEY `idx_incoming_item_id` (`incoming_item_id`),
  KEY `idx_tanggal_qc` (`tanggal_qc`),
  KEY `idx_status` (`status`),
  CONSTRAINT `qc_checklists_ibfk_1` FOREIGN KEY (`incoming_item_id`) REFERENCES `incoming_goods_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `qc_checklists_ibfk_2` FOREIGN KEY (`incoming_id`) REFERENCES `incoming_goods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `qc_checklists_ibfk_4` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE SET NULL,
  CONSTRAINT `qc_checklists_ibfk_5` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `qc_checklists_ibfk_6` FOREIGN KEY (`qc_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qc_checklists`
--

LOCK TABLES `qc_checklists` WRITE;
/*!40000 ALTER TABLE `qc_checklists` DISABLE KEYS */;
INSERT INTO `qc_checklists` VALUES
(84,'QC/20251219/0001',1235,453,'274','018106/SJ/XII/2025',74,7,NULL,'Raw Material','B 2345 FFG','DADANG','2025-12-19 10:22:25',1,'Completed','2025-12-19 10:22:25','2025-12-19 10:22:25'),
(85,'QC/20251219/0002',1236,454,'274','018106/SJ/XII/2025',97,7,NULL,'Raw Material','B 2345 FFG','DADANG','2025-12-19 10:22:40',1,'Completed','2025-12-19 10:22:40','2025-12-19 10:22:40'),
(86,'QC/20251219/0003',1237,455,'275','3150101268',12,12,NULL,'Raw Material','B 2345 FFB','ANTO','2025-12-19 10:33:59',1,'Completed','2025-12-19 10:33:59','2025-12-19 10:33:59'),
(87,'QC/20251219/0004',1238,456,'276','B9-DO2504680',10,6,NULL,'Raw Material','B 6665 XCG','MANTO','2025-12-19 10:49:29',1,'Completed','2025-12-19 10:49:29','2025-12-19 10:49:29'),
(88,'QC/20251219/0005',1239,457,'275','3150101231',12,12,NULL,'Raw Material','B 5252 HJK','KEN','2025-12-19 10:53:18',1,'Completed','2025-12-19 10:53:18','2025-12-19 10:53:18'),
(89,'QC/20251219/0006',1240,458,'277','02536/XI/TAN/25',68,518,NULL,'Raw Material','B 2332 FNG','DADANG','2025-12-19 13:46:42',1,'Completed','2025-12-19 13:46:42','2025-12-19 13:46:42'),
(90,'QC/20251219/0007',1252,470,'291','02809/XII/TAN/25',69,518,NULL,'Raw Material','B 2889 KLJ','KADI','2025-12-19 13:46:51',1,'Completed','2025-12-19 13:46:51','2025-12-19 13:46:51'),
(91,'QC/20251219/0008',1251,469,'290','DO-25-10535',31,528,NULL,'Raw Material','B 8988 NNB','NDANG','2025-12-19 13:47:00',1,'Completed','2025-12-19 13:47:00','2025-12-19 13:47:00'),
(92,'QC/20251219/0009',1250,468,'287','4112153056',212,1,NULL,'Raw Material','B 1727 GHJ','OKI','2025-12-19 13:47:10',1,'Completed','2025-12-19 13:47:10','2025-12-19 13:47:10'),
(93,'QC/20251219/0010',1249,467,'285','N/A',88,532,NULL,'Raw Material','B 2898 GHG','OJAN','2025-12-19 13:47:19',1,'Completed','2025-12-19 13:47:19','2025-12-19 13:47:19'),
(94,'QC/20251219/0011',1248,466,'284','SCP-2512/00051',127,540,NULL,'Raw Material','B 2343 FJH','NOPAL','2025-12-19 13:47:32',1,'Completed','2025-12-19 13:47:32','2025-12-19 13:47:32'),
(95,'QC/20251219/0012',1247,465,'283','50175176',118,524,NULL,'Raw Material','B 6467 FGH','JENDA','2025-12-19 13:47:42',1,'Completed','2025-12-19 13:47:42','2025-12-19 13:47:42');
/*!40000 ALTER TABLE `qc_checklists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qc_photos`
--

DROP TABLE IF EXISTS `qc_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qc_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qc_checklist_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `idx_qc_checklist_id` (`qc_checklist_id`),
  CONSTRAINT `qc_photos_ibfk_1` FOREIGN KEY (`qc_checklist_id`) REFERENCES `qc_checklists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `qc_photos_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qc_photos`
--

LOCK TABLES `qc_photos` WRITE;
/*!40000 ALTER TABLE `qc_photos` DISABLE KEYS */;
/*!40000 ALTER TABLE `qc_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qc_reqc_history`
--

DROP TABLE IF EXISTS `qc_reqc_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qc_reqc_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qc_checklist_id` bigint(20) unsigned DEFAULT NULL COMMENT 'FK to qc_checklists after Re-QC completed',
  `inventory_stock_id` bigint(20) unsigned NOT NULL COMMENT 'FK to inventory_stock',
  `incoming_item_id` bigint(20) unsigned DEFAULT NULL COMMENT 'FK to incoming_goods_items',
  `reqc_number` varchar(255) NOT NULL COMMENT 'REQC/YYYYMMDD/0001',
  `old_status` enum('PASS','REJECT','To QC') NOT NULL COMMENT 'Status before Re-QC',
  `old_exp_date` date DEFAULT NULL COMMENT 'Exp date before Re-QC',
  `reason` text NOT NULL DEFAULT 'Material Expired' COMMENT 'Reason for Re-QC',
  `initiated_by` bigint(20) unsigned NOT NULL COMMENT 'User who initiated Re-QC',
  `initiated_at` timestamp NOT NULL COMMENT 'When Re-QC was initiated',
  `status` enum('PENDING','COMPLETED','CANCELLED') NOT NULL DEFAULT 'PENDING' COMMENT 'Re-QC status',
  `new_status` enum('PASS','REJECT') DEFAULT NULL COMMENT 'Status after Re-QC completed',
  `new_exp_date` date DEFAULT NULL COMMENT 'New exp date if PASS',
  `qty_sample_previous` decimal(10,2) DEFAULT NULL COMMENT 'Previous cumulative sample qty',
  `qty_sample_new` decimal(10,2) DEFAULT NULL COMMENT 'New sample qty taken',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'When Re-QC was completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qc_reqc_history_reqc_number_unique` (`reqc_number`),
  KEY `qc_reqc_history_inventory_stock_id_status_index` (`inventory_stock_id`,`status`),
  KEY `qc_reqc_history_reqc_number_index` (`reqc_number`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qc_reqc_history`
--

LOCK TABLES `qc_reqc_history` WRITE;
/*!40000 ALTER TABLE `qc_reqc_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `qc_reqc_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation_activity_logs`
--

DROP TABLE IF EXISTS `reservation_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservation_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `qty_before` decimal(10,2) DEFAULT NULL,
  `qty_after` decimal(10,2) DEFAULT NULL,
  `bin_from` varchar(100) DEFAULT NULL,
  `bin_to` varchar(100) DEFAULT NULL,
  `reference_document` varchar(100) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_request_id` (`request_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_material_id` (`material_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `reservation_activity_logs_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `reservation_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservation_activity_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservation_activity_logs_ibfk_3` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation_activity_logs`
--

LOCK TABLES `reservation_activity_logs` WRITE;
/*!40000 ALTER TABLE `reservation_activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservation_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation_request_items`
--

DROP TABLE IF EXISTS `reservation_request_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservation_request_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `material_id` int(11) DEFAULT NULL,
  `kode_item` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `qty` decimal(10,2) DEFAULT NULL,
  `uom` varchar(50) DEFAULT NULL,
  `nama_material` varchar(200) DEFAULT NULL,
  `kode_pm` varchar(100) DEFAULT NULL,
  `jumlah_permintaan` decimal(10,2) DEFAULT NULL,
  `kode_bahan` varchar(100) DEFAULT NULL,
  `nama_bahan` varchar(200) DEFAULT NULL,
  `jumlah_kebutuhan` decimal(10,2) DEFAULT NULL,
  `jumlah_kirim` decimal(10,2) DEFAULT NULL,
  `alasan_penambahan` enum('Reject Produksi','Bulk Lebih','Reject Supplier','Kurang Supplier') DEFAULT NULL,
  `qty_picked` decimal(10,2) DEFAULT 0.00,
  `qty_remaining` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','partial','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_request_id` (`request_id`),
  KEY `idx_material_id` (`material_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `reservation_request_items_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `reservation_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservation_request_items_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation_request_items`
--

LOCK TABLES `reservation_request_items` WRITE;
/*!40000 ALTER TABLE `reservation_request_items` DISABLE KEYS */;
INSERT INTO `reservation_request_items` VALUES
(55,52,NULL,NULL,NULL,NULL,NULL,'OPP Tape 2\" x 90 Yard Warna',NULL,NULL,'22671','OPP Tape 2\" x 90 Yard Warna',0.75,NULL,NULL,0.00,NULL,'pending','2025-12-19 08:46:45','2025-12-19 08:46:45'),
(56,52,NULL,NULL,NULL,NULL,NULL,'Botol putih 150 ml',NULL,NULL,'23543','Botol putih 150 ml',1241.00,NULL,NULL,0.00,NULL,'pending','2025-12-19 08:46:45','2025-12-19 08:46:45'),
(57,52,NULL,NULL,NULL,NULL,NULL,'Whimol 15 CG-I (white mineral oil)',NULL,NULL,'14294','Whimol 15 CG-I (white mineral oil)',133.47,NULL,NULL,0.00,NULL,'pending','2025-12-19 08:46:45','2025-12-19 08:46:45'),
(58,52,NULL,NULL,NULL,NULL,NULL,'IPM',NULL,NULL,'14008','IPM',3.00,NULL,NULL,0.00,NULL,'pending','2025-12-19 08:46:45','2025-12-19 08:46:45'),
(59,52,NULL,NULL,NULL,NULL,NULL,'Phenoxetol',NULL,NULL,'60017','Phenoxetol',0.30,NULL,NULL,0.00,NULL,'pending','2025-12-19 08:46:45','2025-12-19 08:46:45'),
(60,52,NULL,NULL,NULL,NULL,NULL,'Tegosoft',NULL,NULL,'14216','Tegosoft',0.37,NULL,NULL,0.00,NULL,'pending','2025-12-19 08:46:45','2025-12-19 08:46:45'),
(61,52,NULL,NULL,NULL,NULL,NULL,'Black Seed Oil Soluble (Habbatussauda Oil)',NULL,NULL,'14295','Black Seed Oil Soluble (Habbatussauda Oil)',0.30,NULL,NULL,0.00,NULL,'pending','2025-12-19 08:46:45','2025-12-19 08:46:45');
/*!40000 ALTER TABLE `reservation_request_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation_requests`
--

DROP TABLE IF EXISTS `reservation_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservation_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_reservasi` varchar(100) NOT NULL,
  `request_type` enum('FOH-RS','Packaging','raw-material','Additional') NOT NULL,
  `tanggal_permintaan` datetime NOT NULL,
  `status` enum('Draft','Submitted','Approved','Rejected','Picked','Completed','Cancelled','In Progress') DEFAULT 'Draft',
  `picking_started_at` timestamp NULL DEFAULT NULL,
  `picking_completed_at` timestamp NULL DEFAULT NULL,
  `alasan_reservasi` text DEFAULT NULL,
  `departemen` varchar(100) DEFAULT NULL,
  `nama_produk` varchar(200) DEFAULT NULL,
  `no_bets_filling` varchar(100) DEFAULT NULL,
  `kode_produk` varchar(100) DEFAULT NULL,
  `no_bets` varchar(100) DEFAULT NULL,
  `besar_bets` decimal(10,2) DEFAULT NULL,
  `requested_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejected_by` int(11) DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_reservasi` (`no_reservasi`),
  KEY `requested_by` (`requested_by`),
  KEY `approved_by` (`approved_by`),
  KEY `rejected_by` (`rejected_by`),
  KEY `idx_no_reservasi` (`no_reservasi`),
  KEY `idx_request_type` (`request_type`),
  KEY `idx_status` (`status`),
  KEY `idx_tanggal_permintaan` (`tanggal_permintaan`),
  CONSTRAINT `reservation_requests_ibfk_1` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservation_requests_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservation_requests_ibfk_3` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation_requests`
--

LOCK TABLES `reservation_requests` WRITE;
/*!40000 ALTER TABLE `reservation_requests` DISABLE KEYS */;
INSERT INTO `reservation_requests` VALUES
(52,'RSV/20251219/0001','raw-material','2025-12-19 08:42:00','In Progress','2025-12-19 08:47:06',NULL,NULL,NULL,NULL,NULL,'GZBB001 Azalea Zaitun Oil with Habbatussauda Oil 135 ml - R25','512073',1241.00,1,NULL,NULL,NULL,NULL,NULL,'2025-12-19 08:46:45','2025-12-19 08:47:06');
/*!40000 ALTER TABLE `reservation_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_no` varchar(100) NOT NULL,
  `reservation_request_id` int(11) DEFAULT NULL,
  `reservation_type` enum('raw-material','packaging','Transfer','Production') NOT NULL,
  `material_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `bin_id` int(11) NOT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `qty_reserved` decimal(10,2) NOT NULL,
  `uom` varchar(50) NOT NULL,
  `status` enum('Active','Picked','Cancelled','Expired','Reserved') DEFAULT 'Active',
  `reference_no` varchar(100) DEFAULT NULL,
  `reservation_date` datetime NOT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `picked_qty` decimal(10,2) DEFAULT 0.00,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reservation_request_id` (`reservation_request_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `bin_id` (`bin_id`),
  KEY `created_by` (`created_by`),
  KEY `idx_reservation_no` (`reservation_no`),
  KEY `idx_material_id` (`material_id`),
  KEY `idx_status` (`status`),
  KEY `idx_reservation_date` (`reservation_date`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`reservation_request_id`) REFERENCES `reservation_requests` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`),
  CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`),
  CONSTRAINT `reservations_ibfk_4` FOREIGN KEY (`bin_id`) REFERENCES `warehouse_bins` (`id`),
  CONSTRAINT `reservations_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES
(36,'RSV/20251219/0001',52,'raw-material',127,1,152,'22671191225SUM',0.75,'Pcs','Reserved',NULL,'2025-12-19 15:46:45','2028-12-12 00:00:00',0.00,1,'2025-12-19 08:46:45','2025-12-19 08:46:45'),
(37,'RSV/20251219/0001',52,'raw-material',31,1,152,'23543',1241.00,'Pcs','Reserved',NULL,'2025-12-19 15:46:45','2028-02-01 00:00:00',0.00,1,'2025-12-19 08:46:45','2025-12-19 08:46:45'),
(38,'RSV/20251219/0001',52,'raw-material',12,1,59,'25.LWML',133.47,'Kg','Reserved',NULL,'2025-12-19 15:46:45','2025-12-01 00:00:00',0.00,1,'2025-12-19 08:46:45','2025-12-19 08:46:45'),
(39,'RSV/20251219/0001',52,'raw-material',68,1,152,'28.F.IPM',3.00,'Kg','Reserved',NULL,'2025-12-19 15:46:45','2028-12-05 00:00:00',0.00,1,'2025-12-19 08:46:45','2025-12-19 08:46:45'),
(40,'RSV/20251219/0001',52,'raw-material',69,1,152,'28.A.PHENOXETOL',0.30,'Kg','Reserved',NULL,'2025-12-19 15:46:46','2028-12-09 00:00:00',0.00,1,'2025-12-19 08:46:46','2025-12-19 08:46:46'),
(41,'RSV/20251219/0001',52,'raw-material',212,1,152,'28.H.TEGOSOFT',0.37,'KG','Reserved',NULL,'2025-12-19 15:46:46','2028-12-19 00:00:00',0.00,1,'2025-12-19 08:46:46','2025-12-19 08:46:46'),
(42,'RSV/20251219/0001',52,'raw-material',118,1,152,'27.J.BLACKSEED',0.30,'Kg','Reserved',NULL,'2025-12-19 15:46:46','2028-09-29 00:00:00',0.00,1,'2025-12-19 08:46:46','2025-12-19 08:46:46');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_activity_logs`
--

DROP TABLE IF EXISTS `return_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `return_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `qty_before` decimal(10,2) DEFAULT NULL,
  `qty_after` decimal(10,2) DEFAULT NULL,
  `bin_from` varchar(100) DEFAULT NULL,
  `bin_to` varchar(100) DEFAULT NULL,
  `reference_document` varchar(100) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_return_id` (`return_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_material_id` (`material_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `return_activity_logs_ibfk_1` FOREIGN KEY (`return_id`) REFERENCES `returns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `return_activity_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `return_activity_logs_ibfk_3` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_activity_logs`
--

LOCK TABLES `return_activity_logs` WRITE;
/*!40000 ALTER TABLE `return_activity_logs` DISABLE KEYS */;
INSERT INTO `return_activity_logs` VALUES
(4,NULL,1,'Return Rejected Material','Return 2199.50 Kg of Whimol 15 CG-I (white mineral oil) (REJECTED) to Supplier.',12,'28.F.WML0001',NULL,0.00,0.00,NULL,NULL,'IN/27686',NULL,NULL,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2025-12-19 03:39:03');
/*!40000 ALTER TABLE `return_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_documents`
--

DROP TABLE IF EXISTS `return_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `return_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) NOT NULL,
  `document_type` enum('photo','invoice','delivery_note','other') NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `idx_return_id` (`return_id`),
  CONSTRAINT `return_documents_ibfk_1` FOREIGN KEY (`return_id`) REFERENCES `returns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `return_documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_documents`
--

LOCK TABLES `return_documents` WRITE;
/*!40000 ALTER TABLE `return_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `return_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_items`
--

DROP TABLE IF EXISTS `return_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `return_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `qty_return` decimal(10,2) NOT NULL,
  `uom` varchar(50) NOT NULL,
  `return_reason` enum('QC Reject','Expired','Damage','Excess Production','Wrong Delivery','Kelebihan Produksi') NOT NULL,
  `reason_notes` text DEFAULT NULL,
  `from_bin_id` int(11) DEFAULT NULL,
  `stock_deducted` tinyint(1) DEFAULT 0,
  `deducted_at` datetime DEFAULT NULL,
  `item_condition` enum('Good','Damaged','Expired','Rejected') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `from_bin_id` (`from_bin_id`),
  KEY `idx_return_id` (`return_id`),
  KEY `idx_material_id` (`material_id`),
  KEY `idx_batch_lot` (`batch_lot`),
  CONSTRAINT `return_items_ibfk_1` FOREIGN KEY (`return_id`) REFERENCES `returns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `return_items_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`),
  CONSTRAINT `return_items_ibfk_3` FOREIGN KEY (`from_bin_id`) REFERENCES `warehouse_bins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_items`
--

LOCK TABLES `return_items` WRITE;
/*!40000 ALTER TABLE `return_items` DISABLE KEYS */;
INSERT INTO `return_items` VALUES
(11,18,12,'28.F.WML0001',2199.50,'Kg','QC Reject',NULL,NULL,1,NULL,'Rejected','2025-12-19 10:39:03','2025-12-19 10:39:03');
/*!40000 ALTER TABLE `return_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_slips`
--

DROP TABLE IF EXISTS `return_slips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `return_slips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_number` varchar(100) NOT NULL COMMENT 'Nomor unik slip return',
  `qty_return` decimal(10,2) NOT NULL COMMENT 'Jumlah material yang di-return',
  `uom` varchar(50) NOT NULL COMMENT 'Unit of Measure',
  `alasan_reject` text DEFAULT NULL COMMENT 'Alasan material ditolak/direturn',
  `status` varchar(50) NOT NULL DEFAULT 'Draft' COMMENT 'Status return (e.g., Draft, Submitted, Completed)',
  `tanggal_return` datetime NOT NULL COMMENT 'Tanggal dilakukannya return',
  `qc_checklist_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel qc_checklists',
  `incoming_item_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel incoming_goods_items',
  `material_id` int(11) NOT NULL COMMENT 'FK ke tabel materials',
  `supplier_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel suppliers',
  `created_by` int(11) DEFAULT NULL COMMENT 'FK ke tabel users (Creator)',
  `batch_lot` varchar(255) DEFAULT NULL COMMENT 'Nomor Batch/Lot material yang di-return',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `return_number` (`return_number`),
  KEY `return_slips_qc_checklist_id_foreign` (`qc_checklist_id`),
  KEY `return_slips_incoming_item_id_foreign` (`incoming_item_id`),
  KEY `return_slips_material_id_foreign` (`material_id`),
  KEY `return_slips_supplier_id_foreign` (`supplier_id`),
  KEY `return_slips_created_by_foreign` (`created_by`),
  KEY `idx_return_number` (`return_number`),
  CONSTRAINT `return_slips_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `return_slips_incoming_item_id_foreign` FOREIGN KEY (`incoming_item_id`) REFERENCES `incoming_goods_items` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `return_slips_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `return_slips_qc_checklist_id_foreign` FOREIGN KEY (`qc_checklist_id`) REFERENCES `qc_checklists` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `return_slips_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_slips`
--

LOCK TABLES `return_slips` WRITE;
/*!40000 ALTER TABLE `return_slips` DISABLE KEYS */;
INSERT INTO `return_slips` VALUES
(7,'RTN/20251219/0001',2199.50,'Kg','TMS','Pending Return','2025-12-19 10:33:59',86,1237,12,12,1,'28.F.WML0001','2025-12-19 10:33:59','2025-12-19 10:33:59');
/*!40000 ALTER TABLE `return_slips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `returns`
--

DROP TABLE IF EXISTS `returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_number` varchar(100) NOT NULL,
  `return_type` enum('Supplier','Production') NOT NULL,
  `return_date` date NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `incoming_id` int(11) DEFAULT NULL,
  `reservation_request_id` int(11) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `status` enum('Draft','Submitted','Pending Approval','Approved','Cancelled','Returned') DEFAULT 'Draft',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `return_number` (`return_number`),
  KEY `supplier_id` (`supplier_id`),
  KEY `incoming_id` (`incoming_id`),
  KEY `reservation_request_id` (`reservation_request_id`),
  KEY `created_by` (`created_by`),
  KEY `approved_by` (`approved_by`),
  KEY `idx_return_number` (`return_number`),
  KEY `idx_return_type` (`return_type`),
  KEY `idx_status` (`status`),
  KEY `idx_return_date` (`return_date`),
  CONSTRAINT `returns_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `returns_ibfk_2` FOREIGN KEY (`incoming_id`) REFERENCES `incoming_goods` (`id`) ON DELETE SET NULL,
  CONSTRAINT `returns_ibfk_3` FOREIGN KEY (`reservation_request_id`) REFERENCES `reservation_requests` (`id`) ON DELETE SET NULL,
  CONSTRAINT `returns_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `returns_ibfk_5` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `returns`
--

LOCK TABLES `returns` WRITE;
/*!40000 ALTER TABLE `returns` DISABLE KEYS */;
INSERT INTO `returns` VALUES
(18,'RET/20251219/0001','Supplier','2025-12-19',NULL,NULL,NULL,NULL,'IN/27686','Returned',NULL,1,NULL,NULL,NULL,'2025-12-19 10:39:03','2025-12-19 10:39:03');
/*!40000 ALTER TABLE `returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES
(17,1,17,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(18,1,18,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(19,1,19,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(20,1,20,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(24,1,24,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(25,1,25,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(26,1,26,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(27,1,27,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(28,1,28,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(29,1,29,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(30,1,30,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(31,1,31,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(32,1,32,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(33,1,33,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(34,1,34,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(35,1,35,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(36,1,36,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(37,1,37,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(38,1,38,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(39,1,39,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(40,1,40,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(41,1,41,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(42,1,42,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(43,1,43,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(44,1,44,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(45,1,45,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(46,1,46,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(47,1,47,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(48,1,48,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(49,1,49,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(50,1,50,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(51,1,51,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(52,1,52,'2025-10-14 23:27:06','2025-10-14 23:27:06'),
(63,1,7,'2025-10-22 20:12:28','2025-10-22 20:12:28'),
(64,1,8,'2025-10-22 20:12:28','2025-10-22 20:12:28'),
(65,1,9,'2025-10-22 20:12:28','2025-10-22 20:12:28'),
(66,1,5,'2025-10-23 03:47:15','2025-10-23 03:47:15'),
(67,1,53,'2025-10-30 03:09:50','2025-10-30 03:09:50'),
(69,2,6,'2025-11-03 08:33:14','2025-11-03 08:33:14'),
(70,3,5,'2025-11-03 23:25:15','2025-11-03 23:25:15'),
(71,1,6,'2025-11-03 23:25:51','2025-11-03 23:25:51'),
(72,1,1,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(73,1,2,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(74,1,3,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(75,1,4,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(76,1,10,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(77,1,11,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(78,1,12,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(79,1,13,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(80,1,14,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(81,1,15,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(82,1,16,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(83,1,21,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(84,1,22,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(85,1,23,'2025-11-14 09:29:22','2025-11-14 09:29:22'),
(86,2,7,'2025-11-27 10:02:37','2025-11-27 10:02:37'),
(87,2,8,'2025-11-27 10:02:37','2025-11-27 10:02:37'),
(88,2,9,'2025-11-27 10:02:37','2025-11-27 10:02:37'),
(89,2,5,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(90,2,28,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(91,2,29,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(92,2,30,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(93,2,31,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(94,2,32,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(95,2,33,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(96,2,34,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(97,2,35,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(98,2,36,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(99,2,37,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(100,2,38,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(101,2,39,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(102,2,40,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(103,2,41,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(104,2,42,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(105,2,43,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(106,2,44,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(107,2,45,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(108,2,46,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(109,2,47,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(110,2,48,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(111,2,49,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(112,2,50,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(113,2,51,'2025-12-10 03:25:41','2025-12-10 03:25:41'),
(114,2,52,'2025-12-10 03:25:41','2025-12-10 03:25:41');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'Super Admin','Administrator dengan akses penuh ke seluruh sistem','2025-10-14 23:14:03','2025-10-14 23:14:03'),
(2,'QC Inspector','Role untuk tim QC yang memeriksa barang masuk','2025-10-14 23:14:04','2025-10-14 23:14:04'),
(3,'Warehouse Supervisor','Supervisor gudang dengan akses approve dan monitoring','2025-10-14 23:14:04','2025-10-14 23:14:04'),
(4,'Operator Gudang','Operator untuk kegiatan operasional gudang sehari-hari','2025-10-14 23:14:04','2025-10-14 23:14:04'),
(7,'QC Inspector','Role untuk tim QC yang memeriksa barang masuk','2025-10-14 23:15:19','2025-10-14 23:15:19');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('emLbw1zdjN9Xa9WUhxQWAud1WnoU8smVraLIq87Z',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YToyOntzOjY6Il90b2tlbiI7czo0MDoibHAwRXl0YndMdUdBOHhOYmV0SmcwQ28wejhYQk1GVUJnbHJ2TWFpNyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1766163474),
('hk7vWFlOo9jt82Hu6hpHtwvA0h6SfxZ9lpjD7fVw',1,'118.99.103.162','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMVYzTkxLZmNiRDRobmNIcmZKVDB4cjREMGlPa3NhV2VPc0pZampiZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzU6Imh0dHBzOi8vbGlnaHRibHVlLWNhdHRsZS00MzcxNzYuaG9zdGluZ2Vyc2l0ZS5jb20vdHJhbnNhY3Rpb24vZ29vZHMtcmVjZWlwdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1766126304),
('iqSUOGLQFCKMvcC3HAjsKiDawgCTx2y4nx7SkRyB',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOWVhNHlIV1VtRkQzM01EdGtOWjBnY0VOMnlBQklIZGdFajJNR2xTbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1766160884),
('ROmf89FnkvJtWvxApBXhDp3tkjEjuPvDDBeXMgiL',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWGRpbnU4Z0h0Q3M1REZiWFZQVzJMd0pxckluZzhSV1hERGp3bnEwUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1766132355),
('Tt0rW5bk6903MLjId2FCauhhaVirtmv8Co7OSvVm',1,'103.182.209.66','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY0lzcEZNQk52bjVUcVpnbkxTRzZNNlBDNVA1V2J3QUxOWm03MnB5YyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzU6Imh0dHBzOi8vbGlnaHRibHVlLWNhdHRsZS00MzcxNzYuaG9zdGluZ2Vyc2l0ZS5jb20vdHJhbnNhY3Rpb24vZ29vZHMtcmVjZWlwdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1766126862),
('xASrYcOgHYztI9D18tcpKM9Xolx0C5XFuWtcXVYF',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWXdDUVFNVExyRUtZbkZidUJ5VVdVa25kQlVZR0hEN0FUN3J6THF1MSI7czozOiJ1cmwiO2E6MDp7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTAyOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvdHJhbnNhY3Rpb24vY3ljbGUtY291bnQ/Y2F0ZWdvcnk9UEFDS0FHSU5HJTIwTUFURVJJQUwmZnJlcXVlbmN5PSZzZWFyY2g9JnN0YXR1cz0iO319',1766138963);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movement_number` varchar(100) NOT NULL,
  `movement_type` enum('INCOMING','QC_RELEASE','PUTAWAY','TRANSFER','PICKING','RETURN','QC_SAMPLING','BIN_TO_BIN','STATUS_CHANGE','QC_REJECT','OUT','RETURN_REJECTED','APPROVE RETURN MATERIAL') NOT NULL,
  `material_id` int(11) NOT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `from_warehouse_id` int(11) DEFAULT NULL,
  `from_bin_id` int(11) DEFAULT NULL,
  `to_warehouse_id` int(11) DEFAULT NULL,
  `to_bin_id` int(11) DEFAULT NULL,
  `qty` decimal(10,2) NOT NULL,
  `uom` varchar(50) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` varchar(112) DEFAULT NULL,
  `movement_date` datetime NOT NULL,
  `executed_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `movement_number` (`movement_number`),
  KEY `from_warehouse_id` (`from_warehouse_id`),
  KEY `from_bin_id` (`from_bin_id`),
  KEY `to_warehouse_id` (`to_warehouse_id`),
  KEY `to_bin_id` (`to_bin_id`),
  KEY `executed_by` (`executed_by`),
  KEY `idx_movement_number` (`movement_number`),
  KEY `idx_material_id` (`material_id`),
  KEY `idx_movement_type` (`movement_type`),
  KEY `idx_movement_date` (`movement_date`),
  KEY `idx_reference` (`reference_type`,`reference_id`),
  CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`),
  CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_ibfk_3` FOREIGN KEY (`from_bin_id`) REFERENCES `warehouse_bins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_ibfk_4` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_ibfk_5` FOREIGN KEY (`to_bin_id`) REFERENCES `warehouse_bins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_ibfk_6` FOREIGN KEY (`executed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
INSERT INTO `stock_movements` VALUES
(123,'MOV/20251219/0001','QC_SAMPLING',74,'20008191225NP',1,152,NULL,NULL,-10.00,'Pcs','qc_checklist','84','2025-12-19 10:22:25',1,'Pengambilan sampel QC sebesar 10 Pcs.','2025-12-19 03:22:25'),
(124,'MOV/20251219/0002','STATUS_CHANGE',74,'20008191225NP',1,152,1,152,29990.00,'Pcs','good_receipt','1228','2025-12-19 10:22:25',1,'QC PASS - Status stok di Bin Karantina diubah menjadi RELEASED.','2025-12-19 03:22:25'),
(125,'MOV/20251219/0003','QC_SAMPLING',97,'20013191225NP',1,152,NULL,NULL,-10.00,'Pcs','qc_checklist','85','2025-12-19 10:22:40',1,'Pengambilan sampel QC sebesar 10 Pcs.','2025-12-19 03:22:40'),
(126,'MOV/20251219/0004','STATUS_CHANGE',97,'20013191225NP',1,152,1,152,29990.00,'Pcs','good_receipt','1229','2025-12-19 10:22:40',1,'QC PASS - Status stok di Bin Karantina diubah menjadi RELEASED.','2025-12-19 03:22:40'),
(127,'MOV/20251219/0005','QC_SAMPLING',12,'28.F.WML0001',1,152,NULL,NULL,-0.50,'Kg','qc_checklist','86','2025-12-19 10:33:59',1,'Pengambilan sampel QC sebesar 0.5 Kg.','2025-12-19 03:33:59'),
(128,'MOV/20251219/0066','STATUS_CHANGE',12,'28.F.WML0001',1,152,1,152,2199.50,'Kg','return_slip','RTN/20251219/0001','2025-12-19 10:33:59',1,'QC REJECT - Status stok di Bin Karantina diubah menjadi REJECTED. Menunggu pemindahan ke Bin Reject.','2025-12-19 03:33:59'),
(134,'MOV/20251219/0006','RETURN_REJECTED',12,'28.F.WML0001',1,147,NULL,NULL,-2199.50,'Kg','return_model','18','2025-12-19 10:39:03',1,'Return Rejected Material to Supplier. Ref: IN/27686','2025-12-19 03:39:03'),
(135,'MOV/20251219/0007','QC_SAMPLING',10,'26.APMX',1,152,NULL,NULL,-0.40,'Kg','qc_checklist','87','2025-12-19 10:49:29',1,'Pengambilan sampel QC sebesar 0.4 Kg.','2025-12-19 03:49:29'),
(136,'MOV/20251219/0008','STATUS_CHANGE',10,'26.APMX',1,152,1,152,659.60,'Kg','good_receipt','1230','2025-12-19 10:49:29',1,'QC PASS - Status stok di Bin Karantina diubah menjadi RELEASED.','2025-12-19 03:49:29'),
(137,'MOV/20251219/0009','QC_SAMPLING',12,'25.LWML',1,152,NULL,NULL,-0.50,'Kg','qc_checklist','88','2025-12-19 10:53:18',1,'Pengambilan sampel QC sebesar 0.5 Kg.','2025-12-19 03:53:18'),
(138,'MOV/20251219/0010','STATUS_CHANGE',12,'25.LWML',1,152,1,152,2199.50,'Kg','good_receipt','1231','2025-12-19 10:53:18',1,'QC PASS - Status stok di Bin Karantina diubah menjadi RELEASED.','2025-12-19 03:53:18'),
(139,'MOV/20251219/0011','QC_SAMPLING',68,'28.F.IPM',1,152,NULL,NULL,-0.10,'Kg','qc_checklist','89','2025-12-19 13:46:42',1,'Pengambilan sampel QC sebesar 0.1 Kg.','2025-12-19 06:46:42'),
(140,'MOV/20251219/0012','STATUS_CHANGE',68,'28.F.IPM',1,152,1,152,599.90,'Kg','good_receipt','1232','2025-12-19 13:46:42',1,'QC PASS - Status stok di Bin Karantina diubah menjadi RELEASED.','2025-12-19 06:46:42'),
(141,'MOV/20251219/0013','QC_SAMPLING',69,'28.A.PHENOXETOL',1,152,NULL,NULL,-0.10,'Kg','qc_checklist','90','2025-12-19 13:46:51',1,'Pengambilan sampel QC sebesar 0.1 Kg.','2025-12-19 06:46:51'),
(142,'MOV/20251219/0014','STATUS_CHANGE',69,'28.A.PHENOXETOL',1,152,1,152,1099.90,'Kg','good_receipt','1233','2025-12-19 13:46:51',1,'QC PASS - Status stok di Bin Karantina diubah menjadi RELEASED.','2025-12-19 06:46:51'),
(143,'MOV/20251219/0015','QC_SAMPLING',31,'23543',1,152,NULL,NULL,-10.00,'Pcs','qc_checklist','91','2025-12-19 13:47:00',1,'Pengambilan sampel QC sebesar 10 Pcs.','2025-12-19 06:47:00'),
(144,'MOV/20251219/0016','STATUS_CHANGE',31,'23543',1,152,1,152,44990.00,'Pcs','good_receipt','1234','2025-12-19 13:47:00',1,'QC PASS - Status stok di Bin Karantina diubah menjadi RELEASED.','2025-12-19 06:47:00'),
(145,'MOV/20251219/0017','QC_SAMPLING',212,'28.H.TEGOSOFT',1,152,NULL,NULL,-0.10,'KG','qc_checklist','92','2025-12-19 13:47:10',1,'Pengambilan sampel QC sebesar 0.1 KG.','2025-12-19 06:47:10'),
(146,'MOV/20251219/0018','STATUS_CHANGE',212,'28.H.TEGOSOFT',1,152,1,152,399.90,'KG','good_receipt','1235','2025-12-19 13:47:10',1,'QC PASS - Status stok di Bin Karantina diubah menjadi RELEASED.','2025-12-19 06:47:10'),
(147,'MOV/20251219/0019','QC_SAMPLING',88,'23548191228ML',1,152,NULL,NULL,-10.00,'Pcs','qc_checklist','93','2025-12-19 13:47:19',1,'Pengambilan sampel QC sebesar 10 Pcs.','2025-12-19 06:47:19'),
(148,'MOV/20251219/0020','STATUS_CHANGE',88,'23548191228ML',1,152,1,152,99990.00,'Pcs','good_receipt','1236','2025-12-19 13:47:19',1,'QC PASS - Status stok di Bin Karantina diubah menjadi RELEASED.','2025-12-19 06:47:19'),
(149,'MOV/20251219/0021','QC_SAMPLING',127,'22671191225SUM',1,152,NULL,NULL,-1.00,'Pcs','qc_checklist','94','2025-12-19 13:47:32',1,'Pengambilan sampel QC sebesar 1 Pcs.','2025-12-19 06:47:32'),
(150,'MOV/20251219/0022','STATUS_CHANGE',127,'22671191225SUM',1,152,1,152,359.00,'Pcs','good_receipt','1237','2025-12-19 13:47:32',1,'QC PASS - Status stok di Bin Karantina diubah menjadi RELEASED.','2025-12-19 06:47:32'),
(151,'MOV/20251219/0023','QC_SAMPLING',118,'27.J.BLACKSEED',1,152,NULL,NULL,-0.10,'Kg','qc_checklist','95','2025-12-19 13:47:42',1,'Pengambilan sampel QC sebesar 0.1 Kg.','2025-12-19 06:47:42'),
(152,'MOV/20251219/0024','STATUS_CHANGE',118,'27.J.BLACKSEED',1,152,1,152,499.90,'Kg','good_receipt','1238','2025-12-19 13:47:42',1,'QC PASS - Status stok di Bin Karantina diubah menjadi RELEASED.','2025-12-19 06:47:42');
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_supplier` varchar(50) NOT NULL,
  `nama_supplier` varchar(200) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_supplier` (`kode_supplier`),
  KEY `idx_kode_supplier` (`kode_supplier`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=775 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES
(1,'SUP-c14c8084','DKSH Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(2,'SUP-c14c8128','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(3,'SUP-c14c8150','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(4,'SUP-c14c8173','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(5,'SUP-c14c8193','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(6,'SUP-c14c81b0','Maha Kimia Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(7,'SUP-c14c81d0','Natamas Plast, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(8,'SUP-c14c81eb','Natamas Plast, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(9,'SUP-c14c8207','Global Chemindo Megatrading, P',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(10,'SUP-c14c8228','Maha Kimia Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(11,'SUP-c14c8244','Maha Kimia Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(12,'SUP-c14c8261','Dunia Kimia Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(13,'SUP-c14c827d','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(14,'SUP-c14c829b','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(15,'SUP-c14c82b9','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(16,'SUP-c14c82d9','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(17,'SUP-c14c82f7','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(18,'SUP-c14c8318','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(19,'SUP-c14c8339','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(20,'SUP-c14c835a','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(21,'SUP-c14c83d3','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(22,'SUP-c14c83f5','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(23,'SUP-c14c841b','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(24,'SUP-c14c843e','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(25,'SUP-c14c8461','Tritunggal Artha Makmur, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(26,'SUP-c14c8480','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(27,'SUP-c14c849e','Graha Niaga Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(28,'SUP-c14c84b8','Wika Intinusa Niagatama PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(29,'SUP-c14c84d8','KARSAVICTA SATYA, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(30,'SUP-c14c84f5','Hensan Trimitra Gemilang, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(31,'SUP-c14c8511','Mata Pelangi Tradindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(32,'SUP-c14c8534','Hensan Trimitra Gemilang, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(33,'SUP-c14c854e','Hensan Trimitra Gemilang, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(34,'SUP-c14c856b','Dunia Kimia Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(35,'SUP-c14c8586','Ekacitta Dian Persada, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(36,'SUP-c14c85a2','Ekacitta Dian Persada, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(37,'SUP-c14c85c1','Jayatama selaras, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(38,'SUP-c14c85df','Dwijaya Grafika Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(39,'SUP-c14c85fb','Borneo Stationary, Toko',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(40,'SUP-c14c8616','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(41,'SUP-c14c862f','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(42,'SUP-c14c8649','Jayatama selaras, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(43,'SUP-c14c8665','Mata Pelangi Tradindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(44,'SUP-c14c8680','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(45,'SUP-c14c86a2','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(46,'SUP-c14c86bf','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(47,'SUP-c14c86de','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(48,'SUP-c14c86fe','Indokemika Jayatama, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(49,'SUP-c14c871c','Indokemika Jayatama, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(50,'SUP-c14c873b','Bronson & Jacobs Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(51,'SUP-c14c8759','Green Innovation Sejati, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(52,'SUP-c14c8775','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(53,'SUP-c14c8791','Tentrem Artha Nugraha, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(54,'SUP-c14c87ac','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(55,'SUP-c14c87c5','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(56,'SUP-c14c87dd','Mane Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(57,'SUP-c14c87f6','Mane Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(58,'SUP-c14c880f',': Tirta Aroma Sari PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(59,'SUP-c14c8827','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(60,'SUP-c14c883f','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(61,'SUP-c14c8855','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(62,'SUP-c14c886e','Sumber Kemas Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(63,'SUP-c14c8886','Putra Abtar Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(64,'SUP-c14c88a1','Green Innovation Sejati, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:22','2025-12-08 07:30:22'),
(128,'SUP-d0e246e9','Global Chemindo Megatrading, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(129,'SUP-d0e24791','Global Chemindo Megatrading, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(130,'SUP-d0e247c5','DKSH Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(131,'SUP-d0e24800','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(132,'SUP-d0e2482e','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(133,'SUP-d0e24861','Merpati Mahardika, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(134,'SUP-d0e24893','Tentrem Artha Nugraha, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(135,'SUP-d0e248c6','Tentrem Artha Nugraha, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(136,'SUP-d0e248dc','Bronson & Jacobs Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(137,'SUP-d0e248f4','Bronson & Jacobs Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(138,'SUP-d0e24910','Tentrem Artha Nugraha, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(139,'SUP-d0e24934','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(140,'SUP-d0e24956','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(141,'SUP-d0e24979','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(142,'SUP-d0e24993','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(143,'SUP-d0e249ac','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(144,'SUP-d0e249c9','Megahlestari Printing Packindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(145,'SUP-d0e249ea','Megahlestari Printing Packindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(146,'SUP-d0e24a05','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(147,'SUP-d0e24a1c','Arkanindoplast Utama, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(148,'SUP-d0e24a35','Arkanindoplast Utama, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(149,'SUP-d0e24a4a','Chemco Prima Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(150,'SUP-d0e24a5f','Chemco Prima Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(151,'SUP-d0e24a83','Avantchem, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(152,'SUP-d0e24a9d','Chemco Prima Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(153,'SUP-d0e24ac9','Hensan Trimitra Gemilang, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(154,'SUP-d0e24b2a','Dunia Kimia Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(155,'SUP-d0e24b90','Mane Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(156,'SUP-d0e24bd9','Mane Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(157,'SUP-d0e24c0d','Ekacitta Dian Persada, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(158,'SUP-d0e24c45','Master Label, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(159,'SUP-d0e24c6d','Bahtera Mitra Rajawali, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(160,'SUP-d0e24c9c','Bahtera Mitra Rajawali, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(161,'SUP-d0e24cca','Furindosakti Sejahtera, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(162,'SUP-d0e24cf1','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(163,'SUP-d0e24d21','Jayaindo Abadi Makmur, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(164,'SUP-d0e24d4f','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(165,'SUP-d0e24d7d','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(166,'SUP-d0e24da9','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(167,'SUP-d0e24dd3','Petrakemindo Pratama Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(168,'SUP-d0e24dfd','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(169,'SUP-d0e24e2e','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(170,'SUP-d0e24e43','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(171,'SUP-d0e24e82','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(172,'SUP-d0e24eb9','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(173,'SUP-d0e24ef2','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(174,'SUP-d0e24f2b','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(175,'SUP-d0e24f49','Master Label, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(176,'SUP-d0e24f6a','Master Label, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(177,'SUP-d0e24fb2','Maha Kimia Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(178,'SUP-d0e24fed','Bell Flavors & Fragrances Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(179,'SUP-d0e2501e','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(180,'SUP-d0e25035','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(181,'SUP-d0e25053','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(182,'SUP-d0e25072','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 07:30:48','2025-12-08 07:30:48'),
(191,'SUP-8143845c','Merpati Mahardika, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(192,'SUP-8143b9fe','DKSH Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(193,'SUP-8143bb28','Green Innovation Sejati, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(194,'SUP-8143bcaf','Green Innovation Sejati, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(195,'SUP-8143bce7','Natamas Plast, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(196,'SUP-8143bd26','Arkanindoplast Utama, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(197,'SUP-8143bd43','Arkanindoplast Utama, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(198,'SUP-8143bd70','Sinar Utama Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(199,'SUP-8143bdf7','Global Chemindo Megatrading, P',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(200,'SUP-8143be5b','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(201,'SUP-8143beb6','Maha Kimia Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(202,'SUP-8143bf1b','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(203,'SUP-8143bf68','Hensan Trimitra Gemilang, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(204,'SUP-8143bf97','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(205,'SUP-8143c0c6','Natamas Plast, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(206,'SUP-8143c11a','Natamas Plast, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(207,'SUP-8143c135','Tigakadistrindo Perkasa,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(208,'SUP-8143c150','Tigakadistrindo Perkasa,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(209,'SUP-8143c168','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(210,'SUP-8143c181','Multi Saka Abadi, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(211,'SUP-8143c198','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(212,'SUP-8143c1b1','Multi Saka Abadi, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(213,'SUP-8143c1cb','Arkanindoplast Utama, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(214,'SUP-8143c1f1','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(215,'SUP-8143c22a','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(216,'SUP-8143c25d','Avantchem, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(217,'SUP-8143c289','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(218,'SUP-8143c2b6','Mata Pelangi Tradindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(219,'SUP-8143c2e9','Arkanindoplast Utama, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(220,'SUP-8143c31c','Tentrem Artha Nugraha, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(221,'SUP-8143c344','Maha Kimia Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(222,'SUP-8143c362','Tentrem Artha Nugraha, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(223,'SUP-8143c379','Tirta Aroma Sari PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(224,'SUP-8143c391','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(225,'SUP-8143c3af','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(226,'SUP-8143c3de','Master Label, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(227,'SUP-8143c411','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(228,'SUP-8143c443','Chemco Prima Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(229,'SUP-8143c46e','Chemco Prima Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(230,'SUP-8143c4a6','Chemco Prima Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(231,'SUP-8143c4d8','Kreasi Prima Kemasindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(232,'SUP-8143c506','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(233,'SUP-8143c5a0','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(234,'SUP-8143c5e7','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(235,'SUP-8143c610','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(236,'SUP-8143c637','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(237,'SUP-8143c6a6','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(238,'SUP-8143c70c','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(239,'SUP-8143c76b','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(240,'SUP-8143c7cc','Tentrem Artha Nugraha, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(241,'SUP-8143c835','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(242,'SUP-8143c898','Mane Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(243,'SUP-8143c8eb','Mane Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(244,'SUP-8143c949','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(245,'SUP-8143c9a7','Natamas Plast, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(246,'SUP-8143ca0b','Hensan Trimitra Gemilang, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(247,'SUP-8143ca5f','Green Innovation Sejati, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(248,'SUP-8143ca8b','Hensan Trimitra Gemilang, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(249,'SUP-8143cabc','Green Innovation Sejati, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(250,'SUP-8143caec','Pintu Mas Mulia Kimia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(251,'SUP-8143cb1d','Dunia Kimia Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(252,'SUP-8143cb4f','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(253,'SUP-8143cb7c','Jayatama selaras, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(254,'SUP-8143cba8','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(255,'SUP-8143cbd6','Sinar Utama Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(256,'SUP-8143cc0f','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(257,'SUP-8143cc40','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(258,'SUP-8143cc6c','Furindosakti Sejahtera, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(259,'SUP-8143cc9e','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(260,'SUP-8143ccd0','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(261,'SUP-8143cd05','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(262,'SUP-8143cd3f','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(263,'SUP-8143cd6c','Tentrem Artha Nugraha, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(264,'SUP-8143cd98','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(265,'SUP-8143cdbc','Tirta Aroma Sari PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(266,'SUP-8143cdf4','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(267,'SUP-8143ce29','Borneo Stationary, Toko',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(268,'SUP-8143ce5b','Radichem Artha Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(269,'SUP-8143ce92','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(270,'SUP-8143cec7','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(271,'SUP-8143cef7','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(272,'SUP-8143cf23','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(273,'SUP-8143cf50','Jayatama selaras, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(274,'SUP-8143cf7c','Lautan Luas TBK, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(275,'SUP-8143cfaf','Kemiko Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(276,'SUP-8143cfdb','Kemiko Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(277,'SUP-8143d007','Master Label, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(278,'SUP-8143d032','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(279,'SUP-8143d061','Maha Kimia Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(280,'SUP-8143d095','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(281,'SUP-8143d0c5','Cahaya Jakarta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(282,'SUP-8143d0ef','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(283,'SUP-8143d125','Hensan Trimitra Gemilang, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(284,'SUP-8143d155','Marga Dwi Kencana, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(285,'SUP-8143d185','Sinar Utama Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(286,'SUP-8143d1b2','Tigakadistrindo Perkasa,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(287,'SUP-8143d1e1','Dunia Kimia Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(288,'SUP-8143d215','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(289,'SUP-8143d248','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(290,'SUP-8143d276','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(291,'SUP-8143d2ab','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(292,'SUP-8143d2d9','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(293,'SUP-8143d307','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(294,'SUP-8143d33b','Merpati Mahardika, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(295,'SUP-8143d364','Merpati Mahardika, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(296,'SUP-8143d397','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(297,'SUP-8143d3be','Global Chemindo Megatrading, P',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(298,'SUP-8143d3f5','Global Chemindo Megatrading, P',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(299,'SUP-8143d427','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(300,'SUP-8143d45c','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(301,'SUP-8143d495','Chemco Prima Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(302,'SUP-8143d4cb','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(303,'SUP-8143d4f6','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(304,'SUP-8143d51a','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(305,'SUP-8143d539','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(306,'SUP-8143d557','Bahtera Mitra Rajawali, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(307,'SUP-8143d581','Bahtera Mitra Rajawali, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(308,'SUP-8143d5e1','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(309,'SUP-8143d641','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(310,'SUP-8143d676','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(311,'SUP-8143d6a1','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(312,'SUP-8143d6d0','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(313,'SUP-8143d700','Cahaya Jakarta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(314,'SUP-8143d732','Cahaya Jakarta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(315,'SUP-8143d75d','Bahtera Mitra Rajawali, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(316,'SUP-8143d79a','Bahtera Mitra Rajawali, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(317,'SUP-8143d7c6','Natamas Plast, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(318,'SUP-8143d7ef','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(319,'SUP-8143d816','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(320,'SUP-8143d837','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(321,'SUP-8143d869','Best Label',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(322,'SUP-8143d88c','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(323,'SUP-8143d8b5','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(324,'SUP-8143d8f1','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(325,'SUP-8143d918','PUTRACIPTA KARINDOMAS',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(326,'SUP-8143d946','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(327,'SUP-8143d95f','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(328,'SUP-8143d9a7','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:11:31','2025-12-08 08:11:31'),
(446,'SUP-94c54d58','Merpati Mahardika, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(447,'SUP-94c54dfb','DKSH Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(448,'SUP-94c54e1a','Green Innovation Sejati, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(449,'SUP-94c54e33','Green Innovation Sejati, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(450,'SUP-94c54e4c','Natamas Plast, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(451,'SUP-94c54e61','Arkanindoplast Utama, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(452,'SUP-94c54e79','Arkanindoplast Utama, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(453,'SUP-94c54e90','Sinar Utama Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(454,'SUP-94c54ea6','Global Chemindo Megatrading, P',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(455,'SUP-94c54ebf','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(456,'SUP-94c54ed6','Maha Kimia Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(457,'SUP-94c54ef0','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(458,'SUP-94c54f06','Hensan Trimitra Gemilang, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(459,'SUP-94c54f1e','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(460,'SUP-94c54f35','Natamas Plast, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(461,'SUP-94c54f49','Natamas Plast, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(462,'SUP-94c54f5d','Tigakadistrindo Perkasa,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(463,'SUP-94c54f74','Tigakadistrindo Perkasa,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(464,'SUP-94c54f8a','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(465,'SUP-94c54fa1','Multi Saka Abadi, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(466,'SUP-94c54fb5','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(467,'SUP-94c54fcc','Multi Saka Abadi, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(468,'SUP-94c54fe5','Arkanindoplast Utama, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(469,'SUP-94c54ffd','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(470,'SUP-94c55016','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(471,'SUP-94c5502d','Avantchem, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(472,'SUP-94c55042','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(473,'SUP-94c55059','Mata Pelangi Tradindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(474,'SUP-94c55071','Arkanindoplast Utama, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(475,'SUP-94c55089','Tentrem Artha Nugraha, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(476,'SUP-94c5509f','Maha Kimia Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(477,'SUP-94c550b6','Tentrem Artha Nugraha, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(478,'SUP-94c550cb','Tirta Aroma Sari PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(479,'SUP-94c550e3','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(480,'SUP-94c550f9','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(481,'SUP-94c5510e','Master Label, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(482,'SUP-94c55125','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(483,'SUP-94c5513c','Chemco Prima Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(484,'SUP-94c55151','Chemco Prima Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(485,'SUP-94c55166','Chemco Prima Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(486,'SUP-94c5517c','Kreasi Prima Kemasindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(487,'SUP-94c55193','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(488,'SUP-94c551b2','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(489,'SUP-94c551cb','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(490,'SUP-94c551e2','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(491,'SUP-94c551f9','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(492,'SUP-94c55211','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(493,'SUP-94c55228','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(494,'SUP-94c5523e','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(495,'SUP-94c55252','Tentrem Artha Nugraha, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(496,'SUP-94c55266','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(497,'SUP-94c5527b','Mane Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(498,'SUP-94c55290','Mane Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(499,'SUP-94c552a4','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(500,'SUP-94c552b9','Natamas Plast, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(501,'SUP-94c552cd','Hensan Trimitra Gemilang, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(502,'SUP-94c552e3','Green Innovation Sejati, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(503,'SUP-94c552f7','Hensan Trimitra Gemilang, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(504,'SUP-94c5530b','Green Innovation Sejati, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(505,'SUP-94c55321','Pintu Mas Mulia Kimia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(506,'SUP-94c55338','Dunia Kimia Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(507,'SUP-94c5534c','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(508,'SUP-94c55362','Jayatama selaras, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(509,'SUP-94c55378','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(510,'SUP-94c55391','Sinar Utama Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(511,'SUP-94c553a8','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(512,'SUP-94c553be','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(513,'SUP-94c553d4','Furindosakti Sejahtera, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(514,'SUP-94c553ea','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(515,'SUP-94c55402','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(516,'SUP-94c5541a','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(517,'SUP-94c55432','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(518,'SUP-94c55445','Tentrem Artha Nugraha, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(519,'SUP-94c5545c','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(520,'SUP-94c55470','Tirta Aroma Sari PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(521,'SUP-94c55486','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(522,'SUP-94c55499','Borneo Stationary, Toko',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(523,'SUP-94c554ad','Radichem Artha Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(524,'SUP-94c554c4','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(525,'SUP-94c554d9','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(526,'SUP-94c554ed','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(527,'SUP-94c55504','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(528,'SUP-94c5551a','Jayatama selaras, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(529,'SUP-94c5552f','Lautan Luas TBK, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(530,'SUP-94c55543','Kemiko Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(531,'SUP-94c55557','Kemiko Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(532,'SUP-94c5556c','Master Label, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(533,'SUP-94c55583','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(534,'SUP-94c55598','Maha Kimia Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(535,'SUP-94c555af','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(536,'SUP-94c555c6','Cahaya Jakarta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(537,'SUP-94c555da','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(538,'SUP-94c555f2','Hensan Trimitra Gemilang, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(539,'SUP-94c55607','Marga Dwi Kencana, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(540,'SUP-94c5561e','Sinar Utama Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(541,'SUP-94c55633','Tigakadistrindo Perkasa,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(542,'SUP-94c5564c','Dunia Kimia Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(543,'SUP-94c55662','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(544,'SUP-94c55677','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(545,'SUP-94c5568e','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(546,'SUP-94c556a3','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(547,'SUP-94c556ba','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(548,'SUP-94c556ce','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(549,'SUP-94c556e5','Merpati Mahardika, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(550,'SUP-94c556f9','Merpati Mahardika, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(551,'SUP-94c5570e','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(552,'SUP-94c55723','Global Chemindo Megatrading, P',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(553,'SUP-94c55738','Global Chemindo Megatrading, P',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(554,'SUP-94c5574d','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(555,'SUP-94c55765','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(556,'SUP-94c5577a','Chemco Prima Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(557,'SUP-94c55790','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(558,'SUP-94c557a5','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(559,'SUP-94c557bb','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(560,'SUP-94c557d0','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(561,'SUP-94c557e6','Bahtera Mitra Rajawali, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(562,'SUP-94c557fc','Bahtera Mitra Rajawali, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(563,'SUP-94c55812','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(564,'SUP-94c55827','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(565,'SUP-94c5583f','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(566,'SUP-94c55858','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(567,'SUP-94c5586d','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(568,'SUP-94c55882','Cahaya Jakarta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(569,'SUP-94c55896','Cahaya Jakarta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(570,'SUP-94c558aa','Bahtera Mitra Rajawali, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(571,'SUP-94c558c9','Bahtera Mitra Rajawali, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(572,'SUP-94c558df','Natamas Plast, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(573,'SUP-94c558f4','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(574,'SUP-94c5590b','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(575,'SUP-94c55921','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(576,'SUP-94c55936','Best Label',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(577,'SUP-94c55949','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(578,'SUP-94c55961','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(579,'SUP-94c55977','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(580,'SUP-94c5598b','PUTRACIPTA KARINDOMAS',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(581,'SUP-94c559a1','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(582,'SUP-94c559b9','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(583,'SUP-94c559cf','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:19:13','2025-12-08 08:19:13'),
(701,'SUP-0e712cb3','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(702,'SUP-0e712dc7','IMCD Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(703,'SUP-0e712de9','Tirta Buana Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(704,'SUP-0e712e04','Bahtera Adi Jaya, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(705,'SUP-0e712e19','Green Innovation Sejati, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(706,'SUP-0e712e2f','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(707,'SUP-0e712e47','Cahaya Jakarta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(708,'SUP-0e712e76','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(709,'SUP-0e712e8c','Merpati Mahardika, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(710,'SUP-0e712f85','Merpati Mahardika, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(711,'SUP-0e7130d2','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(712,'SUP-0e713115','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(713,'SUP-0e71314a','Teruna Perkasa Offset Printing, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(714,'SUP-0e7131cd','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(715,'SUP-0e713232','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(716,'SUP-0e713267','Menjangan Sakti, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(717,'SUP-0e713296','Esthetic Concorindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(718,'SUP-0e7132de','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(719,'SUP-0e71330b','Bahtera Mitra Rajawali, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(720,'SUP-0e71333e','Cahaya Jakarta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(721,'SUP-0e71336a','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(722,'SUP-0e71339c','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(723,'SUP-0e7133c5','PT, BEHN MEYER CHEII4ICALS',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(724,'SUP-0e7133f4','Merpati Mahardika, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(725,'SUP-0e71340f','Master Label, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(726,'SUP-0e713424','Nardevchem Kemindo, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(727,'SUP-0e71343f','Jayatama selaras, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(728,'SUP-0e713455','Jayatama selaras, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(729,'SUP-0e71346a','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(730,'SUP-0e7134bd','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(731,'SUP-0e71351d','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(732,'SUP-0e713550','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(733,'SUP-0e71357f','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(734,'SUP-0e7135ed','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(735,'SUP-0e71361f','Omni Kemas Industry, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(736,'SUP-0e71364c','PT. Takaha Multichem Indonesia',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(737,'SUP-0e71368e','Chemco Prima Mandiri, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(738,'SUP-0e7136b9','PT. Takaha Multichem Indonesia',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(739,'SUP-0e7136cc','MEGAHLESTARI PRINTING PACKINDO, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(740,'SUP-0e7136e9','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(741,'SUP-0e71371b','Universal Lestari Grafika , PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(742,'SUP-0e71373a','PT Sinergi Multi Lestarindo Tbk ( SMLE )',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(743,'SUP-0e713755','Mane Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(744,'SUP-0e71376b','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(745,'SUP-0e71379b','PT Sinergi Multi Lestarindo Tbk ( SMLE )',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(746,'SUP-0e7137b4','PT Sinergi Multi Lestarindo Tbk ( SMLE )',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(747,'SUP-0e7137c9','Global Chemindo Megatrading, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(748,'SUP-0e7137e1','Tirta Aroma Sari PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(749,'SUP-0e71380b','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(750,'SUP-0e713820','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(751,'SUP-0e713836','Cakrawala Mega Indah, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(752,'SUP-0e71384e','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(753,'SUP-0e713864','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(754,'SUP-0e713879','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(755,'SUP-0e71388e','Sinar Universal Labelindo,PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(756,'SUP-0e7138a1','Haldin Pacific Semesta, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(757,'SUP-0e7138bb','Karya Indah Pesona, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(758,'SUP-0e7138cf','Multi Saka Abadi, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(759,'SUP-0e7138e3','Penjalindo Nusantara, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(760,'SUP-0e7138fd','Maha Kimia Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(761,'SUP-0e713914','Maha Kimia Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(762,'SUP-0e71392a','Namsiang Trading Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(763,'SUP-0e713941','PT SAHABAT INOVASI PERINTIS/ PT SAINTIS',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(764,'SUP-0e713959','PT Sinergi Multi Lestarindo Tbk ( SMLE )',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(765,'SUP-0e713971','PT Sinergi Multi Lestarindo Tbk ( SMLE )',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(766,'SUP-0e71398d','PT Sinergi Multi Lestarindo Tbk ( SMLE )',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(767,'SUP-0e7139a7','PT Sinergi Multi Lestarindo Tbk ( SMLE )',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(768,'SUP-0e7139bd','PT Sinergi Multi Lestarindo Tbk ( SMLE )',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(769,'SUP-0e7139d5','DKSH Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(770,'SUP-0e7139eb','DKSH Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(771,'SUP-0e713a00','Tritunggal Artha Makmur, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(772,'SUP-0e713a1a','DKSH Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(773,'SUP-0e713a2e','DKSH Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47'),
(774,'SUP-0e713a45','DKSH Indonesia, PT',NULL,NULL,NULL,NULL,'active','2025-12-08 08:29:47','2025-12-08 08:29:47');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transfer_order_items`
--

DROP TABLE IF EXISTS `transfer_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transfer_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `source_bin_id` int(11) DEFAULT NULL,
  `destination_bin_id` int(11) DEFAULT NULL,
  `qty_planned` decimal(10,2) NOT NULL,
  `qty_actual` decimal(10,2) DEFAULT NULL,
  `uom` varchar(50) NOT NULL,
  `status` enum('pending','in_progress','picked','short_pick','completed','cancelled') DEFAULT 'pending',
  `box_scanned` tinyint(1) DEFAULT 0,
  `box_scan_time` datetime DEFAULT NULL,
  `source_bin_scanned` tinyint(1) DEFAULT 0,
  `source_bin_scan_time` datetime DEFAULT NULL,
  `dest_bin_scanned` tinyint(1) DEFAULT 0,
  `dest_bin_scan_time` datetime DEFAULT NULL,
  `scanned_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `picker_user_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `picker_user_id` (`picker_user_id`),
  KEY `idx_to_id` (`to_id`),
  KEY `idx_material_id` (`material_id`),
  KEY `idx_source_bin_id` (`source_bin_id`),
  KEY `idx_destination_bin_id` (`destination_bin_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `transfer_order_items_ibfk_1` FOREIGN KEY (`to_id`) REFERENCES `transfer_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transfer_order_items_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`),
  CONSTRAINT `transfer_order_items_ibfk_3` FOREIGN KEY (`source_bin_id`) REFERENCES `warehouse_bins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transfer_order_items_ibfk_4` FOREIGN KEY (`destination_bin_id`) REFERENCES `warehouse_bins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transfer_order_items_ibfk_5` FOREIGN KEY (`picker_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transfer_order_items`
--

LOCK TABLES `transfer_order_items` WRITE;
/*!40000 ALTER TABLE `transfer_order_items` DISABLE KEYS */;
INSERT INTO `transfer_order_items` VALUES
(33,33,74,'20008191225NP',152,188,29990.00,29990.00,'Pcs','completed',1,NULL,1,NULL,1,NULL,'2025-12-19 10:29:03','2025-12-19 10:29:03',NULL,NULL,'2025-12-19 10:27:58','2025-12-19 10:29:03'),
(34,33,97,'20013191225NP',152,205,29990.00,29990.00,'Pcs','completed',1,NULL,1,NULL,1,NULL,'2025-12-19 10:29:03','2025-12-19 10:29:03',NULL,NULL,'2025-12-19 10:27:58','2025-12-19 10:29:03'),
(35,34,12,'28.F.WML0001',152,147,2199.50,2199.50,'Kg','completed',1,NULL,1,NULL,1,NULL,'2025-12-19 10:35:23','2025-12-19 10:35:23',NULL,NULL,'2025-12-19 10:34:46','2025-12-19 10:35:23'),
(36,35,10,'26.APMX',152,102,659.60,659.60,'Kg','completed',1,NULL,1,NULL,1,NULL,'2025-12-19 10:50:30','2025-12-19 10:50:30',NULL,NULL,'2025-12-19 10:49:57','2025-12-19 10:50:30'),
(37,36,12,'25.LWML',152,59,2199.50,2199.50,'Kg','completed',1,NULL,1,NULL,1,NULL,'2025-12-19 10:54:13','2025-12-19 10:54:13',NULL,NULL,'2025-12-19 10:53:34','2025-12-19 10:54:13');
/*!40000 ALTER TABLE `transfer_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transfer_order_scans`
--

DROP TABLE IF EXISTS `transfer_order_scans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transfer_order_scans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_item_id` int(11) NOT NULL,
  `scan_type` enum('source_bin','box','dest_bin','confirm_qty') NOT NULL,
  `scan_code` varchar(255) NOT NULL,
  `scan_time` datetime NOT NULL,
  `scanned_by` int(11) DEFAULT NULL,
  `is_valid` tinyint(1) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `qty_scanned` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `scanned_by` (`scanned_by`),
  KEY `idx_to_item_id` (`to_item_id`),
  KEY `idx_scan_time` (`scan_time`),
  KEY `idx_scan_type` (`scan_type`),
  CONSTRAINT `transfer_order_scans_ibfk_1` FOREIGN KEY (`to_item_id`) REFERENCES `transfer_order_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transfer_order_scans_ibfk_2` FOREIGN KEY (`scanned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transfer_order_scans`
--

LOCK TABLES `transfer_order_scans` WRITE;
/*!40000 ALTER TABLE `transfer_order_scans` DISABLE KEYS */;
/*!40000 ALTER TABLE `transfer_order_scans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transfer_orders`
--

DROP TABLE IF EXISTS `transfer_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transfer_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_number` varchar(100) NOT NULL,
  `transaction_type` enum('Putaway - QC Release','Transfer - Internal','Transfer - Bin to Bin','Picking - Production','Picking - Sales Order') NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `reservation_request_id` int(11) DEFAULT NULL,
  `reservation_no` varchar(100) DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `scheduled_date` datetime DEFAULT NULL,
  `completion_date` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed','Short-Pick','Cancelled') DEFAULT 'Pending',
  `created_by` int(11) DEFAULT NULL,
  `executed_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `to_number` (`to_number`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `reservation_request_id` (`reservation_request_id`),
  KEY `created_by` (`created_by`),
  KEY `executed_by` (`executed_by`),
  KEY `idx_to_number` (`to_number`),
  KEY `idx_transaction_type` (`transaction_type`),
  KEY `idx_status` (`status`),
  KEY `idx_creation_date` (`creation_date`),
  CONSTRAINT `transfer_orders_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transfer_orders_ibfk_2` FOREIGN KEY (`reservation_request_id`) REFERENCES `reservation_requests` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transfer_orders_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transfer_orders_ibfk_4` FOREIGN KEY (`executed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transfer_orders`
--

LOCK TABLES `transfer_orders` WRITE;
/*!40000 ALTER TABLE `transfer_orders` DISABLE KEYS */;
INSERT INTO `transfer_orders` VALUES
(33,'TO-2025-12-001','Putaway - QC Release',1,NULL,NULL,'2025-12-19 10:27:58',NULL,'2025-12-19 10:29:03',NULL,'Completed',1,1,'Auto-generated from QC Released materials','2025-12-19 10:27:58','2025-12-19 10:29:03'),
(34,'TO-2025-12-002','Putaway - QC Release',1,NULL,NULL,'2025-12-19 10:34:46',NULL,'2025-12-19 10:35:23',NULL,'Completed',1,1,'Auto-generated from QC Released materials','2025-12-19 10:34:46','2025-12-19 10:35:23'),
(35,'TO-2025-12-003','Putaway - QC Release',1,NULL,NULL,'2025-12-19 10:49:57',NULL,'2025-12-19 10:50:30',NULL,'Completed',1,1,'Auto-generated from QC Released materials','2025-12-19 10:49:57','2025-12-19 10:50:30'),
(36,'TO-2025-12-004','Putaway - QC Release',1,NULL,NULL,'2025-12-19 10:53:34',NULL,'2025-12-19 10:54:13',NULL,'Completed',1,1,'Auto-generated from QC Released materials','2025-12-19 10:53:34','2025-12-19 10:54:13');
/*!40000 ALTER TABLE `transfer_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nik` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `departement` varchar(100) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_seen_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `nik` (`nik`),
  KEY `role_id` (`role_id`),
  KEY `idx_email` (`email`),
  KEY `idx_nik` (`nik`),
  KEY `idx_status` (`status`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Njan Surenjan','farizan0706@gmail.com','10020','$2y$12$jcqZeoH./MrI9.ff2YSFLONJ.xBZJv7aeEGzmRgDQqNi4g66kEP.a','IT Finance','IT Product Manager','active',1,'2025-10-14 23:07:51','2025-12-19 16:35:50','2025-12-19 16:35:50'),
(2,'gondowangi-123','gondowangi-123@company.com','237455','$2y$12$XSKXJmoxs2BWbI8rW61o3.hHcu2tRSbJdIlR7JJrvRy/Pghp.wU06','QC','Staff','active',1,'2025-11-27 09:57:35','2025-12-10 04:15:37','2025-12-10 04:15:37');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_activity_logs`
--

DROP TABLE IF EXISTS `warehouse_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouse_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  `batch_lot` varchar(100) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `qty_before` decimal(10,2) DEFAULT NULL,
  `qty_after` decimal(10,2) DEFAULT NULL,
  `bin_from` varchar(100) DEFAULT NULL,
  `bin_to` varchar(100) DEFAULT NULL,
  `reference_document` varchar(100) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_to_id` (`to_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_material_id` (`material_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `warehouse_activity_logs_ibfk_1` FOREIGN KEY (`to_id`) REFERENCES `transfer_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warehouse_activity_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `warehouse_activity_logs_ibfk_3` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_activity_logs`
--

LOCK TABLES `warehouse_activity_logs` WRITE;
/*!40000 ALTER TABLE `warehouse_activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `warehouse_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_bins`
--

DROP TABLE IF EXISTS `warehouse_bins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouse_bins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `bin_code` varchar(100) NOT NULL,
  `bin_type` enum('Normal','Quarantine','Reject','Staging','Production') DEFAULT 'Normal' COMMENT 'Jenis bin untuk filtering',
  `capacity` int(11) DEFAULT NULL COMMENT 'Maximum items/pallets',
  `status` enum('full','available','','') NOT NULL DEFAULT 'available',
  `current_items` int(11) DEFAULT 0,
  `qr_code_path` varchar(225) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `idx_zone_id` (`zone_id`),
  KEY `idx_bin_type` (`bin_type`),
  CONSTRAINT `warehouse_bins_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `warehouse_zones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warehouse_bins_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=853 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_bins`
--

LOCK TABLES `warehouse_bins` WRITE;
/*!40000 ALTER TABLE `warehouse_bins` DISABLE KEYS */;
INSERT INTO `warehouse_bins` VALUES
(1,5,1,'A-1-1','Normal',NULL,'available',1,'qrcodes/bins/RM-NA_A-1-1.png','2025-12-15 08:14:48','2025-12-18 21:20:08'),
(2,5,1,'A-1-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(3,5,1,'A-1-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-1-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(4,5,1,'A-1-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-1-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(5,5,1,'A-2-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(6,5,1,'A-2-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(7,5,1,'A-2-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(8,5,1,'A-2-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-2-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(9,5,1,'A-3-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-3-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(10,5,1,'A-3-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(11,5,1,'A-3-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-3-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(12,5,1,'A-3-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-3-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(13,5,1,'A-4-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(14,5,1,'A-4-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(15,5,1,'A-4-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-4-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(16,5,1,'A-4-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-4-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(17,5,1,'A-5-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(18,5,1,'A-5-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(19,5,1,'A-5-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-5-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(20,5,1,'A-5-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-5-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(21,5,1,'A-6-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-6-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(22,5,1,'A-6-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-6-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(23,5,1,'A-6-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-6-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(24,5,1,'A-6-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-6-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(25,5,1,'A-7-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-7-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(26,5,1,'A-7-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-7-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(27,5,1,'A-7-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-7-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(28,5,1,'A-7-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-7-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(29,5,1,'A-8-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-8-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(30,5,1,'A-8-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-8-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(40,5,1,'A-10-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-10-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(41,5,1,'A-8-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-8-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(42,5,1,'A-8-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-8-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(43,5,1,'A-9-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-9-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(44,5,1,'A-9-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-9-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(45,5,1,'A-9-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-9-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(46,5,1,'A-9-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-9-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(47,5,1,'A-10-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-10-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(48,5,1,'A-10-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-10-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(49,5,1,'A-10-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_A-10-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(50,5,1,'B-3-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(51,5,1,'B-1-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(52,5,1,'B-1-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(53,5,1,'B-1-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-1-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(54,5,1,'B-1-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-1-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(55,5,1,'B-2-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(56,5,1,'B-2-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(57,5,1,'B-2-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(58,5,1,'B-2-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-2-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(59,5,1,'B-3-1','Normal',NULL,'available',1,'qrcodes/bins/RM-NA_B-3-1.png','2025-12-15 08:14:48','2025-12-19 10:54:13'),
(60,5,1,'B-5-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-5-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(61,5,1,'B-3-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-3-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(62,5,1,'B-3-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-3-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(63,5,1,'B-4-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(64,5,1,'B-4-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(65,5,1,'B-4-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-4-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(66,5,1,'B-4-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-4-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(67,5,1,'B-5-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(68,5,1,'B-5-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(69,5,1,'B-5-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-5-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(71,5,1,'B-6-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-6-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(72,5,1,'B-6-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-6-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(73,5,1,'B-6-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-6-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(74,5,1,'B-6-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-6-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(75,5,1,'B-7-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-7-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(76,5,1,'B-7-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-7-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(77,5,1,'B-7-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-7-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(78,5,1,'B-7-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-7-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(79,5,1,'B-8-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-8-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(80,5,1,'B-8-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-8-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(81,5,1,'B-8-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-8-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(82,5,1,'B-8-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-8-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(83,5,1,'B-9-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-9-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(84,5,1,'B-9-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-9-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(85,5,1,'B-9-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-9-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(86,5,1,'B-9-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-9-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(87,5,1,'B-10-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-10-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(88,5,1,'B-10-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-10-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(89,5,1,'B-10-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-10-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(90,5,1,'B-10-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-10-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(91,5,1,'B-11-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-11-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(92,5,1,'B-11-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-11-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(93,5,1,'B-11-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-11-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(94,5,1,'B-11-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-11-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(95,5,1,'B-12-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-12-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(96,5,1,'B-12-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-12-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(97,5,1,'B-12-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-12-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(98,5,1,'B-12-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_B-12-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(99,5,1,'C-1-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(100,5,1,'C-1-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(101,5,1,'C-1-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-1-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(102,5,1,'C-1-4','Normal',NULL,'available',1,'qrcodes/bins/RM-NA_C-1-4.png','2025-12-15 08:14:48','2025-12-19 10:50:30'),
(103,5,1,'C-2-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(104,5,1,'C-2-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(105,5,1,'C-2-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(106,5,1,'C-2-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-2-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(107,5,1,'C-3-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-3-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(108,5,1,'C-3-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(109,5,1,'C-3-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-3-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(110,5,1,'C-3-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-3-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(111,5,1,'C-4-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(112,5,1,'C-4-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(113,5,1,'C-4-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-4-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(114,5,1,'C-4-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-4-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(115,5,1,'C-5-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(116,5,1,'C-5-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(117,5,1,'C-5-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-5-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(118,5,1,'C-5-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-5-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(119,5,1,'C-6-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-6-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(120,5,1,'C-6-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-6-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(121,5,1,'C-6-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-6-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(122,5,1,'C-6-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-6-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(123,5,1,'C-7-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-7-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(124,5,1,'C-7-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-7-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(125,5,1,'C-7-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-7-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(126,5,1,'C-7-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-7-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(127,5,1,'C-8-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-8-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(128,5,1,'C-8-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-8-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(129,5,1,'C-8-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-8-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(130,5,1,'C-8-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-8-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(131,5,1,'C-9-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-9-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(132,5,1,'C-9-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-9-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(133,5,1,'C-9-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-9-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(134,5,1,'C-9-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-9-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(135,5,1,'C-10-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-10-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(136,5,1,'C-10-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-10-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(137,5,1,'C-10-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-10-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(138,5,1,'C-10-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-10-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(139,5,1,'C-11-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-11-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(140,5,1,'C-11-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-11-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(141,5,1,'C-11-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-11-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(142,5,1,'C-11-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-11-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(143,5,1,'C-12-1','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-12-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(144,5,1,'C-12-2','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-12-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(145,5,1,'C-12-3','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-12-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(146,5,1,'C-12-4','Normal',NULL,'available',0,'qrcodes/bins/RM-NA_C-12-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(147,5,1,'RM-RJT-1','Reject',NULL,'available',1,'qrcodes/bins/RM-NA_RM-RJT-1.png','2025-12-15 08:14:48','2025-12-19 10:35:23'),
(148,5,1,'RM-RJT-2','Reject',NULL,'available',1,'qrcodes/bins/RM-NA_RM-RJT-2.png','2025-12-15 08:14:48','2025-12-18 08:59:55'),
(149,5,1,'RM-RJT-3','Reject',NULL,'available',0,'qrcodes/bins/RM-NA_RM-RJT-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(150,5,1,'RM-RJT-4','Reject',NULL,'available',0,'qrcodes/bins/RM-NA_RM-RJT-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(151,5,1,'RM-RJT-Floor','Reject',NULL,'available',0,'qrcodes/bins/RM-NA_RM-RJT-Floor.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(152,5,1,'QRT-HALAL','',NULL,'available',0,'qrcodes/bins/RM-NA_QRT-HALAL.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(153,5,1,'FLOOR-A','',NULL,'available',0,'qrcodes/bins/RM-NA_FLOOR-A.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(154,5,1,'FLOOR-B','',NULL,'available',0,'qrcodes/bins/RM-NA_FLOOR-B.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(155,6,1,'C-1-1','Normal',NULL,'available',0,'qrcodes/bins/RM-AC_C-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(156,6,1,'C-1-2','Normal',NULL,'available',0,'qrcodes/bins/RM-AC_C-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(157,6,1,'C-2-1','Normal',NULL,'available',0,'qrcodes/bins/RM-AC_C-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(158,6,1,'C-2-2','Normal',NULL,'available',0,'qrcodes/bins/RM-AC_C-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(159,6,1,'C-3-1','Normal',NULL,'available',0,'qrcodes/bins/RM-AC_C-3-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(160,6,1,'C-3-2','Normal',NULL,'available',0,'qrcodes/bins/RM-AC_C-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(161,6,1,'C-4-1','Normal',NULL,'available',0,'qrcodes/bins/RM-AC_C-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(162,6,1,'C-4-2','Normal',NULL,'available',0,'qrcodes/bins/RM-AC_C-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(163,6,1,'C-5-1','Normal',NULL,'available',0,'qrcodes/bins/RM-AC_C-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(164,6,1,'C-5-2','Normal',NULL,'available',0,'qrcodes/bins/RM-AC_C-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(165,6,1,'C-6-1','Normal',NULL,'available',0,'qrcodes/bins/RM-AC_C-6-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(166,6,1,'C-6-2','Normal',NULL,'available',0,'qrcodes/bins/RM-AC_C-6-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(167,6,1,'QRT-HALAL-AC','',NULL,'available',0,'qrcodes/bins/RM-AC_QRT-HALAL-AC.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(168,6,1,'QRT-NON HALAL','',NULL,'available',0,'qrcodes/bins/RM-AC_QRT-NON-HALAL.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(169,6,1,'RM-F-AC-R','',NULL,'available',0,'qrcodes/bins/RM-AC_RM-F-AC-R.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(170,7,1,'E-1-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(171,7,1,'E-1-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(172,7,1,'E-1-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-1-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(173,7,1,'E-2-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(174,7,1,'E-2-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(175,7,1,'E-2-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(176,7,1,'E-3-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-3-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(177,7,1,'E-3-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(178,7,1,'E-3-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-3-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(179,7,1,'E-4-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(180,7,1,'E-4-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(181,7,1,'E-4-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-4-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(182,7,1,'E-5-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(183,7,1,'E-5-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(184,7,1,'E-5-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_E-5-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(185,7,1,'F-1-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(186,7,1,'F-1-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(187,7,1,'F-1-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-1-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(188,7,1,'F-2-1','Normal',NULL,'available',1,'qrcodes/bins/PM-NA_F-2-1.png','2025-12-15 08:14:48','2025-12-19 10:29:03'),
(189,7,1,'F-2-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(190,7,1,'F-2-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(191,7,1,'F-3-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-3-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(192,7,1,'F-3-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(193,7,1,'F-3-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-3-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(194,7,1,'F-4-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(195,7,1,'F-4-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(196,7,1,'F-4-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-4-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(197,7,1,'F-5-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(198,7,1,'F-5-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(199,7,1,'F-5-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-5-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(200,7,1,'F-6-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-6-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(201,7,1,'F-6-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-6-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(202,7,1,'F-6-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_F-6-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(203,7,1,'G-1-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(204,7,1,'G-1-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(205,7,1,'G-1-3','Normal',NULL,'available',1,'qrcodes/bins/PM-NA_G-1-3.png','2025-12-15 08:14:48','2025-12-19 10:29:03'),
(206,7,1,'G-2-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(207,7,1,'G-2-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(208,7,1,'G-2-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(209,7,1,'G-3-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-3-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(210,7,1,'G-3-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(211,7,1,'G-3-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-3-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(212,7,1,'G-4-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(213,7,1,'G-4-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(214,7,1,'G-4-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-4-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(215,7,1,'G-5-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(216,7,1,'G-5-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(217,7,1,'G-5-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-5-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(218,7,1,'G-6-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-6-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(219,7,1,'G-6-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-6-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(220,7,1,'G-6-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_G-6-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(221,7,1,'H-1-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(222,7,1,'H-1-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(223,7,1,'H-1-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-1-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(224,7,1,'H-2-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(225,7,1,'H-2-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(226,7,1,'H-2-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(227,7,1,'H-3-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-3-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(228,7,1,'H-3-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(229,7,1,'H-3-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-3-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(230,7,1,'H-4-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(231,7,1,'H-4-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(232,7,1,'H-4-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-4-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(233,7,1,'H-5-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(234,7,1,'H-5-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(235,7,1,'H-5-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-5-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(236,7,1,'H-6-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-6-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(237,7,1,'H-6-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-6-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(238,7,1,'H-6-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_H-6-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(239,7,1,'I-1-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(240,7,1,'I-1-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(241,7,1,'I-1-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-1-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(242,7,1,'I-2-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(243,7,1,'I-2-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(244,7,1,'I-2-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(245,7,1,'I-3-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-3-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(246,7,1,'I-3-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(247,7,1,'I-3-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-3-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(248,7,1,'I-4-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(249,7,1,'I-4-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(250,7,1,'I-4-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-4-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(251,7,1,'I-5-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(252,7,1,'I-5-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(253,7,1,'I-5-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-5-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(254,7,1,'I-6-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-6-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(255,7,1,'I-6-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-6-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(256,7,1,'I-6-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_I-6-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(257,7,1,'J-1-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(258,7,1,'J-1-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(259,7,1,'J-1-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-1-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(260,7,1,'J-2-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(261,7,1,'J-2-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(262,7,1,'J-2-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(263,7,1,'J-3-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-3-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(264,7,1,'J-3-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(265,7,1,'J-3-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-3-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(266,7,1,'J-4-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(267,7,1,'J-4-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(268,7,1,'J-4-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-4-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(269,7,1,'J-5-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(270,7,1,'J-5-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(271,7,1,'J-5-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-5-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(272,7,1,'J-6-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-6-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(273,7,1,'J-6-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-6-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(274,7,1,'J-6-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_J-6-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(275,7,1,'K-1-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(276,7,1,'K-1-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(277,7,1,'K-1-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-1-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(278,7,1,'K-2-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(279,7,1,'K-2-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(280,7,1,'K-2-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(281,7,1,'K-3-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-3-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(282,7,1,'K-3-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(283,7,1,'K-3-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-3-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(284,7,1,'K-4-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(285,7,1,'K-4-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(286,7,1,'K-4-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-4-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(287,7,1,'K-5-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(288,7,1,'K-5-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(289,7,1,'K-5-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-5-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(290,7,1,'K-6-1','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-6-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(291,7,1,'K-6-2','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-6-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(292,7,1,'K-6-3','Normal',NULL,'available',0,'qrcodes/bins/PM-NA_K-6-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(293,7,1,'PM-RJT-1-1','Reject',NULL,'available',0,'qrcodes/bins/PM-NA_PM-RJT-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(294,7,1,'PM-RJT-1-2','Reject',NULL,'available',0,'qrcodes/bins/PM-NA_PM-RJT-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(295,7,1,'PM-RJT-1-3','Reject',NULL,'available',0,'qrcodes/bins/PM-NA_PM-RJT-1-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(296,7,1,'PM-RJT-2-1','Reject',NULL,'available',0,'qrcodes/bins/PM-NA_PM-RJT-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(297,7,1,'PM-RJT-2-2','Reject',NULL,'available',0,'qrcodes/bins/PM-NA_PM-RJT-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(298,7,1,'PM-RJT-2-3','Reject',NULL,'available',0,'qrcodes/bins/PM-NA_PM-RJT-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(299,7,1,'Floor-E','',NULL,'available',0,'qrcodes/bins/PM-NA_Floor-E.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(300,7,1,'Floor-F','',NULL,'available',0,'qrcodes/bins/PM-NA_Floor-F.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(301,7,1,'Floor-G','',NULL,'available',0,'qrcodes/bins/PM-NA_Floor-G.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(302,7,1,'Floor-H','',NULL,'available',0,'qrcodes/bins/PM-NA_Floor-H.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(303,7,1,'Floor-I','',NULL,'available',0,'qrcodes/bins/PM-NA_Floor-I.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(304,7,1,'Floor-J','',NULL,'available',0,'qrcodes/bins/PM-NA_Floor-J.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(305,7,1,'Floor-K','',NULL,'available',0,'qrcodes/bins/PM-NA_Floor-K.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(306,8,1,'PM-AC-FA1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FA1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(307,8,1,'PM-AC-FA2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FA2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(308,8,1,'PM-AC-FA3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FA3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(309,8,1,'PM-AC-FA4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FA4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(310,8,1,'PM-AC-FA5','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FA5.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(311,8,1,'PM-AC-FA6','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FA6.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(312,8,1,'PM-AC-FA7','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FA7.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(313,8,1,'PM-AC-FB1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FB1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(314,8,1,'PM-AC-FB2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FB2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(315,8,1,'PM-AC-FB3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FB3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(316,8,1,'PM-AC-FB4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FB4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(317,8,1,'PM-AC-FB5','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FB5.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(318,8,1,'PM-AC-FC1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FC1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(319,8,1,'PM-AC-FC2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FC2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(320,8,1,'PM-AC-FC3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FC3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(321,8,1,'PM-AC-FC4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FC4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(322,8,1,'PM-AC-FC5','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FC5.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(323,8,1,'PM-AC-FE1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FE1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(324,8,1,'PM-AC-FE2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FE2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(325,8,1,'PM-AC-FE3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FE3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(326,8,1,'PM-AC-FE4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FE4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(327,8,1,'PM-AC-FE5','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FE5.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(328,8,1,'PM-AC-FF1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FF1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(329,8,1,'PM-AC-FF2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FF2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(330,8,1,'PM-AC-FF3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FF3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(331,8,1,'PM-AC-FF4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FF4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(332,8,1,'PM-AC-FF5','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_PM-AC-FF5.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(333,8,1,'L-1-1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(334,8,1,'L-1-2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(335,8,1,'L-1-3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-1-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(336,8,1,'L-1-4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-1-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(337,8,1,'L-2-1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(338,8,1,'L-2-2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(339,8,1,'L-2-3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(340,8,1,'L-2-4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-2-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(341,8,1,'L-3-1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-3-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(342,8,1,'L-3-2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(343,8,1,'L-3-3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-3-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(344,8,1,'L-3-4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-3-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(345,8,1,'L-4-1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(346,8,1,'L-4-2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(347,8,1,'L-4-3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-4-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(348,8,1,'L-4-4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-4-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(349,8,1,'L-5-1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(350,8,1,'L-5-2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(351,8,1,'L-5-3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-5-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(352,8,1,'L-5-4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_L-5-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(353,8,1,'M-1-1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-1-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(354,8,1,'M-1-2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-1-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(355,8,1,'M-1-3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-1-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(356,8,1,'M-1-4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-1-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(357,8,1,'M-2-1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-2-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(358,8,1,'M-2-2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-2-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(359,8,1,'M-2-3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-2-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(360,8,1,'M-2-4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-2-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(361,8,1,'M-3-1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-3-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(362,8,1,'M-3-2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-3-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(363,8,1,'M-3-3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-3-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(364,8,1,'M-3-4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-3-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(365,8,1,'M-4-1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-4-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(366,8,1,'M-4-2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-4-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(367,8,1,'M-4-3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-4-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(368,8,1,'M-4-4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-4-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(369,8,1,'M-5-1','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-5-1.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(370,8,1,'M-5-2','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-5-2.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(371,8,1,'M-5-3','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-5-3.png','2025-12-15 08:14:48','2025-12-15 08:14:48'),
(372,8,1,'M-5-4','Normal',NULL,'available',0,'qrcodes/bins/PM-AC_M-5-4.png','2025-12-15 08:14:48','2025-12-15 08:14:48');
/*!40000 ALTER TABLE `warehouse_bins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_zones`
--

DROP TABLE IF EXISTS `warehouse_zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouse_zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL,
  `zone_code` varchar(50) NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `zone_type` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_warehouse_zone` (`warehouse_id`,`zone_code`),
  KEY `idx_zone_type` (`zone_type`),
  CONSTRAINT `warehouse_zones_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_zones`
--

LOCK TABLES `warehouse_zones` WRITE;
/*!40000 ALTER TABLE `warehouse_zones` DISABLE KEYS */;
INSERT INTO `warehouse_zones` VALUES
(5,1,'RM-Non AC','RM-Non AC','Raw Material','active','2025-10-26 23:55:46','2025-10-26 23:55:46'),
(6,1,'RM-AC','RM-AC','Raw Material','active','2025-10-26 23:55:46','2025-10-26 23:55:46'),
(7,1,'PM-Non AC','PM-Non AC','Packaging Material','active','2025-10-26 23:55:46','2025-10-26 23:55:46'),
(8,1,'PM-AC','PM-AC','Packaging Material','active','2025-10-26 23:55:46','2025-10-26 23:55:46'),
(16,1,'Z-GEN','General Storage','STORAGE','active','2025-12-08 07:30:21','2025-12-08 07:30:21');
/*!40000 ALTER TABLE `warehouse_zones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_code` varchar(50) NOT NULL,
  `warehouse_name` varchar(200) NOT NULL,
  `alamat` text DEFAULT NULL,
  `tipe` enum('Main','Quarantine','Production','Finished Goods','Return') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `warehouse_code` (`warehouse_code`),
  KEY `idx_warehouse_code` (`warehouse_code`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouses`
--

LOCK TABLES `warehouses` WRITE;
/*!40000 ALTER TABLE `warehouses` DISABLE KEYS */;
INSERT INTO `warehouses` VALUES
(1,'WH-001','Gudang Utama','Jl. Industri No. 1','Main','active','2025-10-15 08:08:26','2025-10-15 08:08:26');
/*!40000 ALTER TABLE `warehouses` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-12-19 23:58:56
