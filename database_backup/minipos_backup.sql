-- MySQL dump 10.13  Distrib 8.0.31, for Win64 (x86_64)
--
-- Host: db-mini-pos-system-dc24v7x603.e.aivencloud.com    Database: mini_pos_db
-- ------------------------------------------------------
-- Server version	8.4.8

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '3140c681-537e-11f1-8fa2-26869eef8b2d:1-45,
a95ad611-528e-11f1-9076-0eaa6385d470:1-86';

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_code` varchar(20) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_code` (`category_code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'DM001','Sữa & Chế phẩm sữa','Bao gồm sữa tươi, sữa đặc, sữa chua, phô mai và các loại váng sữa',1,'2026-05-19 14:41:28','2026-05-19 14:41:28'),(2,'DM002','Bánh kẹo & Đồ ăn vặt','Các loại bánh quy, snack, kẹo ngọt, sô-cô-la và hạt sấy khô',1,'2026-05-19 14:41:28','2026-05-19 14:41:28'),(3,'DM003','Nước giải khát','Nước ngọt có ga, nước tăng lực, trà đóng chai, nước khoáng tinh khiết',1,'2026-05-19 14:41:28','2026-05-19 14:41:28'),(4,'DM004','Mì gói & Thực phẩm khô','Mì ăn liền, cháo gói, miến, bún khô và các loại gia vị đóng chai',1,'2026-05-19 14:41:28','2026-05-19 14:41:28'),(5,'DM005','Hóa mỹ phẩm quen thuộc','Nước rửa chén, bột giặt, dầu gội, kem đánh răng (Tạm ẩn để sắp xếp lại quầy)',0,'2026-05-19 14:41:28','2026-05-19 14:41:28');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_debts`
--

DROP TABLE IF EXISTS `customer_debts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_debts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `user_id` int NOT NULL,
  `type` enum('increase','decrease') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `note` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `customer_debts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_debts`
--

LOCK TABLES `customer_debts` WRITE;
/*!40000 ALTER TABLE `customer_debts` DISABLE KEYS */;
INSERT INTO `customer_debts` VALUES (1,1,1,'increase',200000.00,200000.00,'Mua đơn hàng nước uống và đồ ăn vặt thanh toán thiếu','2026-05-17 12:31:45'),(2,1,1,'decrease',50000.00,150000.00,'Khách ghé ngang trả bớt 50k tiền mặt','2026-05-18 12:31:45'),(3,3,1,'increase',45000.00,45000.00,'Mua thiếu nhu yếu phẩm cuối tháng','2026-05-16 12:31:45'),(4,4,1,'increase',500000.00,500000.00,'Ghi nợ hóa đơn mua bia tụ tập bạn bè','2026-05-14 12:31:45'),(5,4,1,'decrease',500000.00,0.00,'Đã mang tiền mặt ra thanh toán dứt điểm sổ nợ','2026-05-19 12:31:45'),(6,3,1,'decrease',45000.00,0.00,'Khach trả tiền nợ','2026-05-19 12:45:30');
/*!40000 ALTER TABLE `customer_debts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT 'other',
  `date_of_birth` date DEFAULT NULL,
  `address` text,
  `points` int DEFAULT '0',
  `total_spent` decimal(15,2) DEFAULT '0.00',
  `debt` decimal(15,2) DEFAULT '0.00',
  `status` tinyint(1) DEFAULT '1',
  `note` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_code` (`customer_code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'KH8832','Nguyễn Minh Triết','0912345678','minhtriet@gmail.com','male','1995-08-12','123 Đường Ba Tháng Hai, Quận 10, HCM',450,4500000.00,150000.00,1,'Khách quen hay mua thuốc lá và cafe, hiện đang nợ tiền hóa đơn hôm qua.','2026-05-19 12:31:45','2026-05-19 12:31:45'),(2,'KH4129','Trần Thị Hồng Nhung','0987654321','hongnhung@gmail.com','female','1998-11-23','456 Lê Lợi, Quận 1, HCM',1200,12500000.00,0.00,1,'Khách hàng VIP, tích điểm nhiều, luôn thanh toán tiền mặt đầy đủ.','2026-05-19 12:31:45','2026-05-19 12:31:45'),(3,'KH9012','Lê Hoàng Long','0909123456','hoanglong@gmail.com','male','2000-05-30','789 Nguyễn Huệ, Quận 1, HCM',85,850000.00,0.00,1,'Sinh viên mua thiếu thùng mì gói và nước ngọt.','2026-05-19 12:31:45','2026-05-19 12:45:30'),(4,'KH3351','Phạm Minh Châu','0933445566','minhchau@gmail.com','female','1992-02-15','12 Đường số 5, Bình Tân, HCM',320,3200000.00,0.00,1,'Đã từng nợ tiền nhưng đã thanh toán sòng phẳng hết vào sáng nay.','2026-05-19 12:31:45','2026-05-19 12:31:45'),(5,'KH1102','Vũ Hoàng Yến','0977889900','hoangyen@gmail.com','female','1996-09-05','99 Xô Viết Nghệ Tĩnh, Bình Thạnh, HCM',0,0.00,0.00,0,'Khách hàng bom hàng, đổi trạng thái sang ngừng theo dõi.','2026-05-19 12:31:45','2026-05-19 12:31:45'),(6,'KH7855','Lê Khánh Duy','0354312222','l.k.duy2k1@gmail.com','female',NULL,'Phường Sóc Trăng, Cần Thơ',0,0.00,0.00,1,NULL,'2026-05-19 12:58:23','2026-05-19 13:15:54');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `variant_name` varchar(100) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `cost_price` decimal(15,2) DEFAULT '0.00',
  `sale_price` decimal(15,2) DEFAULT '0.00',
  `stock_qty` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
INSERT INTO `product_variants` VALUES (1,1,'Tuýp nhỏ 100g','8934563111112',15000.00,20000.00,50,'2026-05-19 14:58:51'),(2,1,'Tuýp lớn 150g','8934563111129',22000.00,30000.00,40,'2026-05-19 14:58:51'),(3,1,'Hộp sỉ (Thùng 24 tuýp 150g)','8934563111136',480000.00,650000.00,5,'2026-05-19 14:58:51'),(10,2,'Hộp lẻ 180ml','8934563222214',6500.00,8500.00,200,'2026-05-19 16:27:14'),(11,2,'Lốc 4 hộp','8934563222221',25000.00,33000.00,45,'2026-05-19 16:27:14'),(12,2,'Thùng 48 hộp','8934563222238',290000.00,370000.00,12,'2026-05-19 16:27:15');
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_code` varchar(50) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `short_description` text,
  `category_id` int NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_code` (`product_code`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'SP001','Kem đánh răng P/S Bảo Vệ Nụ Cười',NULL,NULL,2,1,'2026-05-19 14:58:50','2026-05-19 14:58:50'),(2,'SP002','Sữa tươi Vinamilk có đường 180ml','https://res.cloudinary.com/dnjbvgejr/image/upload/v1779208032/mini_pos_products/cxrphkks3sq5ega6hniy.webp',NULL,1,1,'2026-05-19 14:58:50','2026-05-19 16:27:14');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','Chủ cửa hàng - Toàn quyền hệ thống','2026-05-18 15:08:04','2026-05-18 15:08:04'),(2,'staff','Thu ngân - Quyền bán hàng tại quầy','2026-05-18 15:08:04','2026-05-18 15:08:04');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_code` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT 'other',
  `date_of_birth` date DEFAULT NULL,
  `address` text,
  `role_id` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `note` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_code` (`user_code`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'NV000','admin','$2y$10$GG2.RqzH9rV1K/glrw8oc.K2cHobFg3tMH3fNDX0wNwiFOk02iP7m','Quản Trị Viên','admin@gmail.com','0901234567',NULL,'other',NULL,NULL,1,1,NULL,'2026-05-18 08:16:00','2026-05-18 15:09:52'),(5,'NV8664','dc24v7x603','$2y$10$V5wkFd0KMhTWBfvKD1yEKOxGzzN2dQiXIF7Ini3kk.3OH9bBwrRaC','Lê Khánh Duy','lkdffst@gmail.com','0987562753',NULL,'male','2001-06-20','Phường Sóc Trăng, Cần Thơ',2,1,'','2026-05-18 16:28:38','2026-05-19 01:00:30');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-19 23:30:14
