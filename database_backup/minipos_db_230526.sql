-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
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


--
-- GTID state at the beginning of the backup 
--

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'DM001','Sữa & Chế phẩm sữa','Bao gồm sữa tươi, sữa đặc, sữa chua, phô mai và các loại váng sữa',1,'2026-05-19 14:41:28','2026-05-19 14:41:28'),(2,'DM002','Bánh kẹo & Đồ ăn vặt','Các loại bánh quy, snack, kẹo ngọt, sô-cô-la và hạt sấy khô',1,'2026-05-19 14:41:28','2026-05-19 14:41:28'),(3,'DM003','Nước giải khát','Nước ngọt có ga, nước tăng lực, trà đóng chai, nước khoáng tinh khiết',1,'2026-05-19 14:41:28','2026-05-19 14:41:28'),(4,'DM004','Mì gói & Thực phẩm khô','Mì ăn liền, cháo gói, miến, bún khô và các loại gia vị đóng chai',1,'2026-05-19 14:41:28','2026-05-19 14:41:28'),(5,'DM005','Hóa mỹ phẩm quen thuộc','Nước rửa chén, bột giặt, dầu gội, kem đánh răng ',1,'2026-05-19 14:41:28','2026-05-22 02:14:42'),(6,'DM457','Gạo, Bột, Đồ Khô','Gạo, và các loại đồ khô',1,'2026-05-22 02:13:54','2026-05-22 02:14:15');
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
  KEY `fk_debts_customers` (`customer_id`),
  KEY `fk_debts_users` (`user_id`),
  CONSTRAINT `fk_debts_customers` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_debts_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_debts`
--

LOCK TABLES `customer_debts` WRITE;
/*!40000 ALTER TABLE `customer_debts` DISABLE KEYS */;
INSERT INTO `customer_debts` VALUES (9,9,1,'increase',70000.00,70000.00,'Khách nợ đơn hàng POS từ mã đơn HD-1779358890','2026-05-21 10:21:38'),(10,9,1,'decrease',30000.00,40000.00,'Bác 5 trả trước 30000','2026-05-21 15:37:48'),(11,9,1,'decrease',10000.00,30000.00,'Khách thanh toán tiền nợ tại quầy','2026-05-21 15:38:10');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (9,'KH6557','Bác 5','0899675643',NULL,'male',NULL,'Bác 5 gần nhà cô hương',45,450000.00,30000.00,1,NULL,'2026-05-21 10:20:20','2026-05-21 15:38:10'),(10,'KH8917','Cô Hương','0899035306',NULL,'female',NULL,NULL,0,0.00,0.00,1,NULL,'2026-05-21 15:12:40','2026-05-21 15:12:40'),(11,'KH7382','Anh Hữu','0865678432',NULL,'other',NULL,NULL,0,0.00,0.00,1,NULL,'2026-05-21 15:15:16','2026-05-21 15:15:16'),(12,'KH6847','Anh Cường','0765435789',NULL,'other',NULL,NULL,0,0.00,0.00,1,NULL,'2026-05-21 15:18:37','2026-05-21 15:18:37');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_check_details`
--

DROP TABLE IF EXISTS `inventory_check_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_check_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inventory_check_id` int NOT NULL,
  `product_variant_id` int NOT NULL,
  `system_qty` int NOT NULL,
  `actual_qty` int NOT NULL,
  `variance` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_details_checks` (`inventory_check_id`),
  KEY `fk_details_variants` (`product_variant_id`),
  CONSTRAINT `fk_details_inventory_checks` FOREIGN KEY (`inventory_check_id`) REFERENCES `inventory_checks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_details_product_variants` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_check_details`
--

LOCK TABLES `inventory_check_details` WRITE;
/*!40000 ALTER TABLE `inventory_check_details` DISABLE KEYS */;
INSERT INTO `inventory_check_details` VALUES (1,1,31,50,45,-5),(3,3,31,44,40,-4);
/*!40000 ALTER TABLE `inventory_check_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_checks`
--

DROP TABLE IF EXISTS `inventory_checks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_checks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `check_code` varchar(20) NOT NULL,
  `user_id` int NOT NULL,
  `status` tinyint DEFAULT '0',
  `note` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `check_code` (`check_code`),
  KEY `fk_checks_users` (`user_id`),
  CONSTRAINT `fk_checks_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_checks`
--

LOCK TABLES `inventory_checks` WRITE;
/*!40000 ALTER TABLE `inventory_checks` DISABLE KEYS */;
INSERT INTO `inventory_checks` VALUES (1,'PK20776',1,1,'Kiểm tra kho kem đánh răng','2026-05-21 02:09:03','2026-05-21 02:09:17'),(3,'PK15270',1,0,'Kiểm tra kem đánh răng 2 tuýp trong kho kiểm còn 40','2026-05-22 02:43:45','2026-05-22 02:43:45');
/*!40000 ALTER TABLE `inventory_checks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_details`
--

DROP TABLE IF EXISTS `order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `variant_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_details`
--

LOCK TABLES `order_details` WRITE;
/*!40000 ALTER TABLE `order_details` DISABLE KEYS */;
INSERT INTO `order_details` VALUES (3,3,31,1,80000.00),(4,4,12,1,370000.00);
/*!40000 ALTER TABLE `order_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_code` varchar(50) NOT NULL,
  `customer_id` int DEFAULT '0',
  `user_id` int NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `customer_paid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pay_method` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_code` (`order_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (3,'HD-1779358847',9,1,80000.00,80000.00,'cash','2026-05-21 10:20:54'),(4,'HD-1779358890',9,1,370000.00,300000.00,'cash','2026-05-21 10:21:37');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
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
  `low_stock_threshold` int DEFAULT '10',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`),
  KEY `fk_variants_products` (`product_id`),
  CONSTRAINT `fk_variants_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
INSERT INTO `product_variants` VALUES (10,2,'Hộp lẻ 180ml','8934563222214',6500.00,8500.00,216,10,'2026-05-19 16:27:14'),(11,2,'Lốc 4 hộp','8934563222221',25000.00,33000.00,45,10,'2026-05-19 16:27:14'),(12,2,'Thùng 48 hộp','8934563222238',290000.00,370000.00,11,10,'2026-05-19 16:27:15'),(13,1,'Tuýp nhỏ 100g','8934563111112',15000.00,20000.00,46,10,'2026-05-20 07:00:17'),(14,1,'Tuýp lớn 150g','8934563111129',22000.00,30000.00,40,10,'2026-05-20 07:00:17'),(15,1,'Hộp sỉ (Thùng 24 tuýp 150g)','8934563111136',480000.00,650000.00,5,10,'2026-05-20 07:00:17'),(26,4,'Thùng 48 Hộp',NULL,235000.00,335000.00,9,5,'2026-05-21 01:37:44'),(27,4,'Lóc 4 Hộp',NULL,22000.00,35000.00,20,10,'2026-05-21 01:37:44'),(30,6,'Tuýt 230g',NULL,30000.00,35000.00,20,10,'2026-05-21 02:05:39'),(31,7,'2 Tuýp 225G',NULL,70000.00,80000.00,44,10,'2026-05-21 02:08:05'),(32,8,'Túi 5KG',NULL,100000.00,130000.00,20,10,'2026-05-22 02:27:25');
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
  CONSTRAINT `fk_products_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'SP001','Kem đánh răng P/S Bảo Vệ Nụ Cười','https://res.cloudinary.com/dnjbvgejr/image/upload/v1779260415/mini_pos_products/pjd9ykrvqyqymdb6lrfo.jpg','Kem đánh răng PS kệ A2 Mỹ Phẩm',5,0,'2026-05-19 14:58:50','2026-05-22 02:34:18'),(2,'SP002','Sữa tươi Vinamilk có đường 180ml','https://res.cloudinary.com/dnjbvgejr/image/upload/v1779208032/mini_pos_products/cxrphkks3sq5ega6hniy.webp',NULL,1,1,'2026-05-19 14:58:50','2026-05-19 16:27:14'),(4,'SP7713','Thùng 48 hộp sữa lúa mạch ít đường Milo 180ml','https://res.cloudinary.com/dnjbvgejr/image/upload/v1779260637/mini_pos_products/alahfknimo6csiit1cal.webp','Kệ A1, Trong kho \r\nKệ B2, Ngoài sảnh',1,1,'2026-05-20 07:03:58','2026-05-21 01:37:43'),(6,'SP1562','Kem đánh răng P/S trắng răng muối hồng và hoa cúc 230g','https://res.cloudinary.com/dnjbvgejr/image/upload/v1779329138/mini_pos_products/qlqnjjajyvdtvehqke5o.webp',NULL,5,1,'2026-05-21 01:48:52','2026-05-21 02:05:39'),(7,'SP9689','2 tuýp kem đánh răng Colgate MaxFresh hương bạc hà 225g tặng bàn chải đánh răng','https://res.cloudinary.com/dnjbvgejr/image/upload/v1779329284/mini_pos_products/c59f7im951qu6mp6x8pt.webp','Khuyến Mại Mua 1 tặng 1',5,1,'2026-05-21 02:08:04','2026-05-21 02:08:04'),(8,'SP3979','Gạo thơm Vua Gạo ST25 + túi 5kg','https://res.cloudinary.com/dnjbvgejr/image/upload/v1779416844/mini_pos_products/i9rxhocspoyrl89ipbcg.webp','Gạo thơm Vua Gạo ST25 + túi 5kg đổi mẫu mới',6,1,'2026-05-22 02:27:25','2026-05-22 02:27:25');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_details`
--

DROP TABLE IF EXISTS `purchase_order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_order_id` int NOT NULL,
  `product_variant_id` int NOT NULL,
  `quantity` int NOT NULL,
  `import_price` decimal(15,2) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `product_variant_id` (`product_variant_id`),
  CONSTRAINT `purchase_order_details_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  CONSTRAINT `purchase_order_details_ibfk_2` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_details`
--

LOCK TABLES `purchase_order_details` WRITE;
/*!40000 ALTER TABLE `purchase_order_details` DISABLE KEYS */;
INSERT INTO `purchase_order_details` VALUES (1,1,10,6,6500.00,39000.00);
/*!40000 ALTER TABLE `purchase_order_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_code` varchar(50) NOT NULL,
  `supplier_id` int NOT NULL,
  `user_id` int NOT NULL,
  `total_amount` decimal(15,2) DEFAULT '0.00',
  `status` tinyint DEFAULT '0',
  `note` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_code` (`purchase_code`),
  KEY `supplier_id` (`supplier_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
INSERT INTO `purchase_orders` VALUES (1,'NH93369',1,1,39000.00,1,'','2026-05-22 07:10:33');
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
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
-- Table structure for table `stock_logs`
--

DROP TABLE IF EXISTS `stock_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_variant_id` int NOT NULL,
  `user_id` int NOT NULL,
  `action_type` varchar(20) NOT NULL,
  `reference_code` varchar(50) DEFAULT NULL,
  `old_qty` int NOT NULL,
  `change_qty` int NOT NULL,
  `new_qty` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_logs_variants` (`product_variant_id`),
  KEY `fk_logs_users` (`user_id`),
  CONSTRAINT `fk_stock_logs_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_logs_variants` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_logs`
--

LOCK TABLES `stock_logs` WRITE;
/*!40000 ALTER TABLE `stock_logs` DISABLE KEYS */;
INSERT INTO `stock_logs` VALUES (1,31,1,'ADJUST','PK20776',50,-5,45,'2026-05-21 02:09:17'),(2,26,1,'export','HD-1779357439',10,-1,9,'2026-05-21 09:57:26'),(3,13,1,'export','HD-1779357665',50,-1,49,'2026-05-21 10:01:12'),(4,13,1,'export','HD-1779357795',49,-1,48,'2026-05-21 10:03:22'),(5,13,1,'export','HD-1779358090',48,-1,47,'2026-05-21 10:08:18'),(6,13,1,'export','HD-1779358242',47,-1,46,'2026-05-21 10:10:49'),(7,31,1,'export','HD-1779358847',45,-1,44,'2026-05-21 10:20:54'),(8,12,1,'export','HD-1779358890',12,-1,11,'2026-05-21 10:21:37'),(9,10,1,'IMPORT','NH93369',210,6,216,'2026-05-22 07:10:45');
/*!40000 ALTER TABLE `stock_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_code` varchar(50) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_code` (`supplier_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'NCC1244','Vinamilk (Việt Nam)','+84 0354 854 625','dadksadas@gmail.com','',1,'2026-05-22 07:10:03');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
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
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_roles` (`role_id`),
  CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'NV000','admin','$2y$10$GG2.RqzH9rV1K/glrw8oc.K2cHobFg3tMH3fNDX0wNwiFOk02iP7m','Quản Trị Viên','admin@gmail.com','0901234567',NULL,'other',NULL,NULL,1,1,NULL,'2026-05-18 08:16:00','2026-05-18 15:09:52'),(7,'NV1709','dc24v7x603','$2y$10$BOaLJ6CCh81tAiJe8cRWfeCGOYNVH3QVAlPgTHID6XJ1Y7wga5k96','Lê Khánh Duy','lkdffst@gmail.com','0354317360',NULL,'male','2001-06-20','Cần Thơ',2,1,'','2026-05-21 15:06:46','2026-05-23 03:09:41'),(14,'NV9282','Duy','$2y$10$3eZng6H3wtLIYa4fHVWzsuISCShib3nBfjidpqbKvRBv48SeqpRii','Lê Khánh Duy','l.k.duy2k1@gmail.com','',NULL,'male',NULL,'',1,1,'','2026-05-21 15:33:59','2026-05-23 03:10:21');
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

-- Dump completed on 2026-05-23 10:18:51
