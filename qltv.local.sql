-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: qltv.local
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `chuc_vu`
--

DROP TABLE IF EXISTS `chuc_vu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chuc_vu` (
  `id_chuc_vu` int NOT NULL AUTO_INCREMENT,
  `ma_chuc_vu` text,
  `ten_chuc_vu` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT (now()),
  PRIMARY KEY (`id_chuc_vu`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chuc_vu`
--

LOCK TABLES `chuc_vu` WRITE;
/*!40000 ALTER TABLE `chuc_vu` DISABLE KEYS */;
INSERT INTO `chuc_vu` VALUES (29,'CS','Cán sự','2025-04-01 08:05:02','2025-04-01 08:05:02'),(30,'CV','Chuyên viên','2025-04-01 08:05:02','2025-04-01 08:05:02'),(31,'GĐ','Giám đốc Trung tâm','2025-04-01 08:05:02','2025-04-01 08:05:02'),(32,'GOV','Giáo viên','2025-04-01 08:05:02','2025-04-01 08:05:02'),(33,'GV','Giảng viên','2025-04-01 08:05:02','2025-04-01 08:05:02'),(34,'GVC','Giảng viên chính','2025-04-01 08:05:02','2025-04-01 08:05:02'),(35,'HT','Hiệu trưởng','2025-04-01 08:05:02','2025-04-01 08:05:02'),(36,'KS','Kỹ sư','2025-04-01 08:05:02','2025-04-01 08:05:02'),(37,'KTHV','Kỹ thuật viên','2025-04-01 08:05:02','2025-04-01 08:05:02'),(38,'KTV','Kế toán viên','2025-04-01 08:05:02','2025-04-01 08:05:02'),(39,'NV','Nhân viên','2025-04-01 08:05:02','2025-04-01 08:05:02'),(40,'NVBV','Nhân viên bảo vệ','2025-04-01 08:05:02','2025-04-01 08:05:02'),(41,'NVKT','Nhân viên kỹ thuật','2025-04-01 08:05:02','2025-04-01 08:05:02'),(42,'NVTV','Nhân viên thư viện','2025-04-01 08:05:02','2025-04-01 08:05:02'),(43,'NVVT','Nhân viên văn thư','2025-04-01 08:05:02','2025-04-01 08:05:02'),(44,'NVYT','Nhân viên y tế','2025-04-01 08:05:02','2025-04-01 08:05:02'),(45,'P.HT','Phó hiệu trưởng','2025-04-01 08:05:02','2025-04-01 08:05:02'),(46,'PGD','Phó giám đốc','2025-04-01 08:05:02','2025-04-01 08:05:02'),(47,'PK','Phó Trưởng Khoa','2025-04-01 08:05:02','2025-04-01 08:05:02'),(48,'PT.BM','Phụ trách Bộ môn','2025-04-01 08:05:02','2025-04-01 08:05:02'),(49,'PT.K','Phụ trách khoa','2025-04-01 08:05:02','2025-04-01 08:05:02'),(50,'PT.P','Phụ trách phòng','2025-04-01 08:05:02','2025-04-01 08:05:02'),(51,'PT.TT','Phụ trách TT','2025-04-01 08:05:02','2025-04-01 08:05:02'),(52,'PTP','Phó trưởng phòng','2025-04-01 08:05:02','2025-04-01 08:05:02'),(53,'TK','Trưởng Khoa','2025-04-01 08:05:02','2025-04-01 08:05:02'),(54,'TP','Trưởng phòng','2025-04-01 08:05:02','2025-04-01 08:05:02'),(55,'TR.BM','Trưởng bộ môn','2025-04-01 08:05:02','2025-04-01 08:05:02'),(56,'TX','Tài xế','2025-04-01 08:05:02','2025-04-01 08:05:02');
/*!40000 ALTER TABLE `chuc_vu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chuyen_nganh`
--

DROP TABLE IF EXISTS `chuyen_nganh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chuyen_nganh` (
  `id_chuyen_nganh` int NOT NULL AUTO_INCREMENT,
  `ma_chuyen_nganh` text,
  `ten_chuyen_nganh` text,
  `id_don_vi` int DEFAULT NULL,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_chuyen_nganh`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chuyen_nganh`
--

LOCK TABLES `chuyen_nganh` WRITE;
/*!40000 ALTER TABLE `chuyen_nganh` DISABLE KEYS */;
INSERT INTO `chuyen_nganh` VALUES (1,'OTO','Công nghệ kỹ thuật ô tô',14,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(2,'CTM','Công nghệ chế tạo máy',15,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(3,'DDT','Công nghệ kỹ thuật điện, điện tử',16,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(4,'CTT','Công nghệ thông tin',17,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(5,'CTP','Công nghệ thực phẩm',18,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(6,'CDT','Công nghệ kỹ thuật cơ điện tử',16,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(7,'TDH','Công nghệ kỹ thuật điều khiển và tự động hóa',15,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(8,'CCK','Công nghệ kỹ thuật cơ khí',15,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(9,'QGD','Quản lý giáo dục',19,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(10,'BTY','Thú y',18,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(11,'CXH','Công tác xã hội',19,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(12,'KGT','Công nghệ kỹ thuật giao thông',14,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(13,'KTN','Công nghệ kỹ thuật nhiệt',16,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(14,'CSH','Công nghệ sinh học',18,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(15,'DLH','Du lịch',19,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(16,'DVT','Công nghệ kỹ thuật điện tử viễn thông',16,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(17,'KTE','Kinh tế',20,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(18,'LAW','Luật',20,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(19,'CKD','Kỹ thuật CKĐL',14,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(20,'KXD','CNKT Công trình XD',14,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(21,'QDL','QTDV Du lịch và lữ hành',19,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(22,'KMT','Khoa học Máy tính',12,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(23,'SCN','Sư phạm công nghệ',19,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(24,'KHH','Kỹ thuật hóa học',18,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(25,'TDT','Thương mại điện tử',20,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(26,'OTD','Kỹ thuật ô tô (điện)',14,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(27,'LGT','Logistics và Quản lý chuỗi cung ứng',20,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(28,'KQT','Kinh doanh quốc tế',20,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(29,'DPT','Truyền thông đa phương tiện',17,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(30,'GDH','Giáo dục học',19,'2025-04-07 15:50:52','2025-04-07 15:50:52');
/*!40000 ALTER TABLE `chuyen_nganh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diem_luu_thong`
--

DROP TABLE IF EXISTS `diem_luu_thong`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diem_luu_thong` (
  `id_diem_luu_thong` int NOT NULL AUTO_INCREMENT,
  `ma_loai` text,
  `ten_diem` text,
  `id_kho_an_pham` int DEFAULT NULL,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_diem_luu_thong`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diem_luu_thong`
--

LOCK TABLES `diem_luu_thong` WRITE;
/*!40000 ALTER TABLE `diem_luu_thong` DISABLE KEYS */;
/*!40000 ALTER TABLE `diem_luu_thong` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_gia`
--

DROP TABLE IF EXISTS `doc_gia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doc_gia` (
  `id_doc_gia` int NOT NULL AUTO_INCREMENT,
  `ho_ten` text,
  `so_the` text,
  `mat_khau` text,
  `ngay_sinh` date DEFAULT NULL,
  `ngay_cap_the` int DEFAULT NULL,
  `han_the` date DEFAULT NULL,
  `lan_cap_the` int DEFAULT NULL,
  `ho_khau` text,
  `ghi_chu` text,
  `rut_han` int DEFAULT NULL,
  `id_don_vi` int DEFAULT NULL,
  `id_nien_khoa` int DEFAULT NULL,
  `id_doi_tuong_ban_doc` int DEFAULT NULL,
  `email` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_doc_gia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_gia`
--

LOCK TABLES `doc_gia` WRITE;
/*!40000 ALTER TABLE `doc_gia` DISABLE KEYS */;
/*!40000 ALTER TABLE `doc_gia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doi_tuong_ban_doc`
--

DROP TABLE IF EXISTS `doi_tuong_ban_doc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doi_tuong_ban_doc` (
  `id_doi_tuong_ban_doc` int NOT NULL AUTO_INCREMENT,
  `ma_loai` text,
  `ten_loai` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_doi_tuong_ban_doc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doi_tuong_ban_doc`
--

LOCK TABLES `doi_tuong_ban_doc` WRITE;
/*!40000 ALTER TABLE `doi_tuong_ban_doc` DISABLE KEYS */;
/*!40000 ALTER TABLE `doi_tuong_ban_doc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `don_vi`
--

DROP TABLE IF EXISTS `don_vi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `don_vi` (
  `id_don_vi` int NOT NULL AUTO_INCREMENT,
  `ma_don_vi` text,
  `ten_don_vi` text,
  `ngay_tao` timestamp NULL DEFAULT (now()),
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_don_vi`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `don_vi`
--

LOCK TABLES `don_vi` WRITE;
/*!40000 ALTER TABLE `don_vi` DISABLE KEYS */;
INSERT INTO `don_vi` VALUES (1,'CTSV','Phòng Công tác sinh viên','2025-01-16 19:29:34','2025-04-03 22:21:08'),(2,'PĐT','Phòng Đào tạo','2025-01-10 06:39:17','2025-04-03 22:21:08'),(3,'QLKH-HTQT','Phòng Quản lý khoa học & Hợp tác quốc tế','2025-01-10 06:39:40','2025-04-03 22:21:08'),(4,'KHTC','Phòng Kế hoạch - Tài chính','2025-01-10 06:40:02','2025-04-03 22:21:08'),(5,'TCHC','Phòng Tổ chức Hành Chính','2025-01-10 06:40:19','2025-04-03 22:21:08'),(6,'TTTH','Trung tâm thực hành','2025-01-10 06:40:30','2025-04-03 22:21:08'),(7,'KT-DBCLGD','Phòng Khảo thí và Đảm bảo chất lượng giáo dục','2025-01-10 06:41:32','2025-04-03 22:21:08'),(8,'QTTB','Phòng Quản trị thiết bị','2025-01-10 06:44:14','2025-04-03 22:21:08'),(9,'GDTC','Trung tâm GDTC và GDQP','2025-01-10 07:30:29','2025-04-03 22:21:08'),(11,'NN-TH','Trung tâm Ngoại ngữ - Tin học','2025-01-10 07:31:52','2025-04-03 22:21:08'),(12,'KHCB','Khoa Khoa học cơ bản','2025-01-10 07:32:22','2025-04-03 22:21:08'),(13,'LLCT','Khoa Lý luận chính trị','2025-01-10 07:32:39','2025-04-03 22:21:08'),(14,'KTCNOT','Khoa Kỹ thuật công nghệ Ô tô','2025-01-10 07:34:51','2025-04-03 22:21:08'),(15,'KTCNCK','Khoa Kỹ thuật công nghệ Cơ khí','2025-01-10 07:35:00','2025-04-03 22:21:08'),(16,'KTCNDDT','Khoa Kỹ thuật công nghệ Điện - Điện tử','2025-01-10 07:34:35','2025-04-03 22:21:08'),(17,'CNTT','Khoa Công nghệ thông tin','2025-01-10 07:35:34','2025-04-03 22:21:08'),(18,'KHSHUD','Khoa Khoa học Sinh học ứng dụng','2025-01-10 07:36:06','2025-04-03 22:21:08'),(19,'SPKT&XHNV','Khoa Sư phạm Kỹ thuật và Xã hội nhân văn','2025-01-10 07:36:36','2025-04-03 22:21:08'),(20,'KT-L','Khoa Kinh tế - Luật','2025-01-10 07:37:16','2025-04-03 22:21:08'),(21,'SV','Sinh Viên','2025-02-01 16:28:51','2025-04-03 22:21:08');
/*!40000 ALTER TABLE `don_vi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hoc_ky`
--

DROP TABLE IF EXISTS `hoc_ky`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hoc_ky` (
  `id_hoc_ky` int NOT NULL AUTO_INCREMENT,
  `ma_hoc_ky` text,
  `ten_hoc_ky` text,
  `nam_hoc` text,
  `loai_hoc_ky` int DEFAULT NULL,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_hoc_ky`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hoc_ky`
--

LOCK TABLES `hoc_ky` WRITE;
/*!40000 ALTER TABLE `hoc_ky` DISABLE KEYS */;
INSERT INTO `hoc_ky` VALUES (1,'242','Học kỳ 2, 2024-2025','2024',2,'2025-04-07 17:02:36','2025-04-07 16:56:42'),(2,'243','Học kỳ phụ, 2024 - 2025','2024',3,'2025-04-07 17:03:20','2025-04-07 16:56:42'),(3,'241','Học kỳ 1, 2024-2025','2024',1,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(4,'234','Học kỳ hè, 2023-2024','2023',4,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(5,'232','Học kỳ 2, 2023-2024','2023',2,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(6,'233','Học kỳ phụ, 2023-2024','2023',3,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(7,'231','Học kỳ 1, 2023-2024','2023',1,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(8,'224','Học kỳ hè, 2022-2023','2022',4,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(9,'222','Học kỳ 2, 2022-2023','2022',2,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(10,'223','Học kỳ phụ, 2022-2023','2022',3,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(11,'221','Học kỳ 1, 2022-2023','2022',1,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(12,'214','Học kỳ hè, 2021-2022','2021',4,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(13,'212','Học kỳ 2, 2021-2022','2021',2,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(14,'213','Học kỳ phụ, 2021-2022','2021',3,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(15,'211','Học kỳ 1, 2021-2022','2021',1,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(16,'204','Học kỳ hè, 2020-2021','2020',4,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(17,'202','Học kỳ 2, 2020-2021','2020',2,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(18,'203','Học kỳ phụ, 2020-2021','2020',3,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(19,'201','Học kỳ 1, 2020-2021','2020',1,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(20,'192','Học kỳ 2, 2019-2020','2019',2,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(21,'193','Học kỳ phụ, 2019-2020','2019',3,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(22,'191','Học kỳ 1, 2019-2020','2019',1,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(23,'184','Học kỳ phụ, 2018-2019, K43 và K44','2018',4,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(24,'182','Học kỳ 2, 2018-2019','2018',2,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(25,'183','Học kỳ phụ, 2018-2019','2018',3,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(26,'181','Học kỳ 1, 2018-2019','2018',1,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(27,'174','Học kỳ phụ, 2017-2018, K42 và K43','2017',4,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(28,'172','Học kỳ 2, 2017-2018','2017',2,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(29,'173','Học kỳ phụ, 2017-2018','2017',3,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(30,'171','Học kỳ 1, 2017-2018','2017',1,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(31,'164','Học kỳ phụ, 2016-2017, K41, K42','2016',4,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(32,'162','Học kỳ 2, 2016-2017','2016',2,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(33,'163','Học kỳ phụ, 2016-2017, K39-40','2016',3,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(34,'161','Học kỳ 1, 2016-2017','2016',1,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(35,'153','Học kỳ phụ, 2015-2016, K39','2015',3,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(36,'152','Học kỳ 2, 2015 - 2016','2015',2,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(37,'151','Học kỳ 1, 2015-2016','2015',1,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(38,'143','Học kỳ phụ, 2014-2015','2014',3,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(39,'142','Học kỳ 2, 2014-2015','2014',2,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(40,'131','Học kỳ 1, 2013-2014','2013',1,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(41,'141','Học kỳ 1, 2014-2015','2014',1,'2025-04-07 16:56:42','2025-04-07 16:56:42'),(43,'132','Học kỳ 2, 2013 - 2014','2013',2,'2025-04-07 17:03:20','2025-04-07 17:03:20');
/*!40000 ALTER TABLE `hoc_ky` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kho_an_pham`
--

DROP TABLE IF EXISTS `kho_an_pham`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kho_an_pham` (
  `id_kho_an_pham` int NOT NULL AUTO_INCREMENT,
  `ma_kho` text,
  `ten_kho` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kho_an_pham`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kho_an_pham`
--

LOCK TABLES `kho_an_pham` WRITE;
/*!40000 ALTER TABLE `kho_an_pham` DISABLE KEYS */;
/*!40000 ALTER TABLE `kho_an_pham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lop_hoc`
--

DROP TABLE IF EXISTS `lop_hoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lop_hoc` (
  `id_lop_hoc` int NOT NULL AUTO_INCREMENT,
  `ma_lop` text,
  `han_su_dung` date DEFAULT NULL,
  `ten_lop` text,
  `id_don_vi` int DEFAULT NULL,
  `doi_tuong` int DEFAULT NULL,
  `khoa_hoc` int DEFAULT NULL,
  `nam_hoc` int DEFAULT NULL,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_lop_hoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lop_hoc`
--

LOCK TABLES `lop_hoc` WRITE;
/*!40000 ALTER TABLE `lop_hoc` DISABLE KEYS */;
/*!40000 ALTER TABLE `lop_hoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nien_khoa`
--

DROP TABLE IF EXISTS `nien_khoa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nien_khoa` (
  `id_nien_khoa` int NOT NULL AUTO_INCREMENT,
  `ma_nien_khoa` int DEFAULT NULL,
  `nien_khoa` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_nien_khoa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nien_khoa`
--

LOCK TABLES `nien_khoa` WRITE;
/*!40000 ALTER TABLE `nien_khoa` DISABLE KEYS */;
/*!40000 ALTER TABLE `nien_khoa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phat_ban_doc`
--

DROP TABLE IF EXISTS `phat_ban_doc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phat_ban_doc` (
  `id_phat_ban_doc` int NOT NULL AUTO_INCREMENT,
  `ma_loai` text,
  `ten_loai_phat` text,
  `ghi_chu` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_phat_ban_doc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phat_ban_doc`
--

LOCK TABLES `phat_ban_doc` WRITE;
/*!40000 ALTER TABLE `phat_ban_doc` DISABLE KEYS */;
/*!40000 ALTER TABLE `phat_ban_doc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tai_khoan`
--

DROP TABLE IF EXISTS `tai_khoan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tai_khoan` (
  `id_tai_khoan` int NOT NULL AUTO_INCREMENT,
  `ho_ten` text,
  `email` text,
  `quyen` text,
  `ngay_tao` timestamp NULL DEFAULT NULL,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_tai_khoan`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tai_khoan`
--

LOCK TABLES `tai_khoan` WRITE;
/*!40000 ALTER TABLE `tai_khoan` DISABLE KEYS */;
INSERT INTO `tai_khoan` VALUES (1,'Admin ','admindvc@vlute.edu.vn','admin','2025-01-10 06:53:45','2025-04-07 15:56:04');
/*!40000 ALTER TABLE `tai_khoan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tai_lieu`
--

DROP TABLE IF EXISTS `tai_lieu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tai_lieu` (
  `id_tai_lieu` int NOT NULL AUTO_INCREMENT,
  `ma_tai_lieu` text,
  `ten_tai_lieu` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_tai_lieu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tai_lieu`
--

LOCK TABLES `tai_lieu` WRITE;
/*!40000 ALTER TABLE `tai_lieu` DISABLE KEYS */;
/*!40000 ALTER TABLE `tai_lieu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thu_muc`
--

DROP TABLE IF EXISTS `thu_muc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `thu_muc` (
  `id_thu_muc` int NOT NULL AUTO_INCREMENT,
  `nhan_truong` text,
  `con` text,
  `dau_cach` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_thu_muc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thu_muc`
--

LOCK TABLES `thu_muc` WRITE;
/*!40000 ALTER TABLE `thu_muc` DISABLE KEYS */;
/*!40000 ALTER TABLE `thu_muc` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-08  0:07:23
