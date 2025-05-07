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
-- Table structure for table `bien_muc_bieu_ghi`
--

DROP TABLE IF EXISTS `bien_muc_bieu_ghi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bien_muc_bieu_ghi` (
  `id_bien_muc_bieu_ghi` int NOT NULL AUTO_INCREMENT,
  `id_sach` int DEFAULT NULL,
  `id_tai_lieu` int DEFAULT NULL,
  `trang_thai_bieu_ghi` text,
  `id_chuyen_nganh` int DEFAULT NULL,
  `id_don_vi` int DEFAULT NULL,
  PRIMARY KEY (`id_bien_muc_bieu_ghi`),
  KEY `bien_muc_bieu_ghi_sach_id_sach_fk` (`id_sach`),
  CONSTRAINT `bien_muc_bieu_ghi_sach_id_sach_fk` FOREIGN KEY (`id_sach`) REFERENCES `sach` (`id_sach`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bien_muc_bieu_ghi`
--

LOCK TABLES `bien_muc_bieu_ghi` WRITE;
/*!40000 ALTER TABLE `bien_muc_bieu_ghi` DISABLE KEYS */;
INSERT INTO `bien_muc_bieu_ghi` VALUES (15,22,1,'dang-bien-muc',30,19),(16,23,1,'dang-bien-muc',30,19),(17,24,1,'dang-bien-muc',30,19);
/*!40000 ALTER TABLE `bien_muc_bieu_ghi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bien_muc_truong_cha`
--

DROP TABLE IF EXISTS `bien_muc_truong_cha`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bien_muc_truong_cha` (
  `id_bien_muc_truong_cha` int NOT NULL AUTO_INCREMENT,
  `id_bien_muc_bieu_ghi` int DEFAULT NULL,
  `ma_truong` varchar(3) DEFAULT NULL,
  `ct1` varchar(1) DEFAULT NULL,
  `ct2` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id_bien_muc_truong_cha`),
  KEY `bien_muc_truong_cha_bien_muc_bieu_ghi_id_bien_muc_bieu_ghi_fk` (`id_bien_muc_bieu_ghi`),
  CONSTRAINT `bien_muc_truong_cha_bien_muc_bieu_ghi_id_bien_muc_bieu_ghi_fk` FOREIGN KEY (`id_bien_muc_bieu_ghi`) REFERENCES `bien_muc_bieu_ghi` (`id_bien_muc_bieu_ghi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bien_muc_truong_cha`
--

LOCK TABLES `bien_muc_truong_cha` WRITE;
/*!40000 ALTER TABLE `bien_muc_truong_cha` DISABLE KEYS */;
INSERT INTO `bien_muc_truong_cha` VALUES (95,15,'245','0','#'),(96,15,'260','#','#'),(97,15,'904','#','#'),(98,15,'100','1','#'),(101,15,'082','1','4'),(102,15,'300','#','#'),(103,15,'520','#','#'),(104,15,'530','#','#'),(105,15,'650','#','#'),(106,15,'653','#','#'),(107,15,'700','0','#'),(117,15,'123','3','3'),(123,16,'245','0','#'),(124,16,'260','#','#'),(125,16,'904','#','#'),(126,16,'100','1','#'),(127,16,'020','#','#'),(128,16,'041','#','#'),(129,16,'082','1','4'),(130,16,'300','#','#'),(131,16,'520','#','#'),(132,16,'530','#','#'),(133,16,'650','#','#'),(134,16,'653','#','#'),(135,16,'700','0','#'),(136,17,'245','0','#'),(137,17,'260','#','#'),(138,17,'904','#','#'),(139,17,'100','1','#'),(140,17,'020','#','#'),(141,17,'041','#','#'),(142,17,'082','1','4'),(143,17,'300','#','#'),(144,17,'520','#','#'),(145,17,'530','#','#'),(146,17,'650','#','#'),(147,17,'653','#','#'),(148,17,'700','0','#');
/*!40000 ALTER TABLE `bien_muc_truong_cha` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bien_muc_truong_con`
--

DROP TABLE IF EXISTS `bien_muc_truong_con`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bien_muc_truong_con` (
  `id_bien_muc_truong_con` int NOT NULL AUTO_INCREMENT,
  `id_bien_muc_truong_cha` int DEFAULT NULL,
  `ma_truong_con` varchar(1) DEFAULT NULL,
  `noi_dung` text,
  PRIMARY KEY (`id_bien_muc_truong_con`),
  KEY `fk_bmtc_idcha` (`id_bien_muc_truong_cha`),
  CONSTRAINT `fk_bmtc_idcha` FOREIGN KEY (`id_bien_muc_truong_cha`) REFERENCES `bien_muc_truong_cha` (`id_bien_muc_truong_cha`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=266 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bien_muc_truong_con`
--

LOCK TABLES `bien_muc_truong_con` WRITE;
/*!40000 ALTER TABLE `bien_muc_truong_con` DISABLE KEYS */;
INSERT INTO `bien_muc_truong_con` VALUES (166,95,'a','GS. Trần Hồng Quân với sự nghiệp giáo dục đào tạo VIỆT NAM'),(167,95,'c','Trần Xuân Nhĩ'),(168,95,'b',''),(169,95,'n',''),(170,95,'p',''),(171,96,'a','Hà Nội'),(172,96,'b','TT&TT'),(173,96,'c','2024'),(174,97,'i','Admin '),(175,98,'a','Trần Xuân Nhĩ'),(176,98,'b',NULL),(181,101,'a','371.1'),(182,101,'b','NH300'),(183,102,'a',''),(184,102,'c',''),(185,102,'e',''),(186,103,'a',''),(187,104,'a',''),(188,105,'a',''),(189,106,'a',''),(190,107,'a',''),(206,117,'a','123'),(216,123,'a','Cải cách hành chính công phục vụ phát triển kinh tế cải thiện môi trường kinh doanh'),(217,123,'c','Đoàn Duy Khương'),(218,123,'b',''),(219,123,'n',''),(220,123,'p',''),(221,124,'a','Hà Nội'),(222,124,'b','CTQG'),(223,124,'c','2016'),(224,125,'i','Admin '),(225,126,'a','Đoàn Duy Khương'),(226,126,'b',''),(227,127,'a',''),(228,127,'c',''),(229,128,'a',''),(231,129,'a','351'),(232,129,'b','KH561'),(233,130,'a',''),(234,130,'c',''),(235,130,'e',''),(236,131,'a',''),(237,132,'a',''),(238,133,'a',''),(239,134,'a',''),(240,135,'a',''),(241,136,'a','Luật quy hoạch'),(242,136,'c',''),(243,136,'b',''),(244,136,'n',''),(245,136,'p',''),(246,137,'a','Cần Thơ'),(247,137,'b','CTQGST'),(248,137,'c','2017'),(249,138,'i','Admin '),(250,139,'a',''),(251,139,'b',''),(252,140,'a',''),(253,140,'c',''),(254,141,'a',''),(255,142,'2',''),(256,142,'a','346.05'),(257,142,'b','L504Q'),(258,143,'a',''),(259,143,'c',''),(260,143,'e',''),(261,144,'a',''),(262,145,'a',''),(263,146,'a',''),(264,147,'a',''),(265,148,'a','');
/*!40000 ALTER TABLE `bien_muc_truong_con` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chi_tiet_diem_luu_thong`
--

DROP TABLE IF EXISTS `chi_tiet_diem_luu_thong`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chi_tiet_diem_luu_thong` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_diem_luu_thong` int DEFAULT NULL,
  `id_kho_an_pham` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chi_tiet_diem_luu_thong`
--

LOCK TABLES `chi_tiet_diem_luu_thong` WRITE;
/*!40000 ALTER TABLE `chi_tiet_diem_luu_thong` DISABLE KEYS */;
INSERT INTO `chi_tiet_diem_luu_thong` VALUES (20,2,2),(21,6,6),(28,9,5),(36,1,1),(37,1,2);
/*!40000 ALTER TABLE `chi_tiet_diem_luu_thong` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chi_tiet_tham_so_lt`
--

DROP TABLE IF EXISTS `chi_tiet_tham_so_lt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chi_tiet_tham_so_lt` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_doi_tuong_ban_doc` int DEFAULT NULL,
  `id_diem_luu_thong` int DEFAULT NULL,
  `id_kho_an_pham` int DEFAULT NULL,
  `muon` text,
  `giu` text,
  `gia_han` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chi_tiet_tham_so_lt`
--

LOCK TABLES `chi_tiet_tham_so_lt` WRITE;
/*!40000 ALTER TABLE `chi_tiet_tham_so_lt` DISABLE KEYS */;
INSERT INTO `chi_tiet_tham_so_lt` VALUES (9,4,9,5,'1','2','1'),(11,4,1,1,'0','0','0'),(13,4,6,6,'0','0','0'),(14,4,2,2,'0','0','0'),(15,1,1,1,'2','2','2'),(17,1,2,2,'0','0','0'),(18,1,9,5,'123','123','123'),(19,2,2,2,'0','0','0'),(20,2,9,5,'123','123','123'),(21,1,6,6,'0','0','0'),(22,3,9,5,'0','0','0'),(23,3,6,6,'0','0','0'),(25,3,1,1,'0','0','0'),(27,2,1,1,'0','0','0'),(28,1,1,2,'123','123','123'),(29,2,1,2,'0','0','0');
/*!40000 ALTER TABLE `chi_tiet_tham_so_lt` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chuyen_nganh`
--

LOCK TABLES `chuyen_nganh` WRITE;
/*!40000 ALTER TABLE `chuyen_nganh` DISABLE KEYS */;
INSERT INTO `chuyen_nganh` VALUES (1,'OTO','Công nghệ kỹ thuật ô tô',14,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(2,'CTM','Công nghệ chế tạo máy',15,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(3,'DDT','Công nghệ kỹ thuật điện, điện tử',16,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(4,'CTT','Công nghệ thông tin',17,'2025-04-08 17:11:26','2025-04-07 15:50:52'),(5,'CTP','Công nghệ thực phẩm',18,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(6,'CDT','Công nghệ kỹ thuật cơ điện tử',16,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(7,'TDH','Công nghệ kỹ thuật điều khiển và tự động hóa',15,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(8,'CCK','Công nghệ kỹ thuật cơ khí',15,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(9,'QGD','Quản lý giáo dục',19,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(10,'BTY','Thú y',18,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(11,'CXH','Công tác xã hội',19,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(12,'KGT','Công nghệ kỹ thuật giao thông',14,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(13,'KTN','Công nghệ kỹ thuật nhiệt',16,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(14,'CSH','Công nghệ sinh học',18,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(15,'DLH','Du lịch',19,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(16,'DVT','Công nghệ kỹ thuật điện tử viễn thông',16,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(17,'KTE','Kinh tế',20,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(18,'LAW','Luật',20,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(19,'CKD','Kỹ thuật CKĐL',14,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(20,'KXD','CNKT Công trình XD',14,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(21,'QDL','QTDV Du lịch và lữ hành',19,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(22,'KMT','Khoa học Máy tính',12,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(23,'SCN','Sư phạm công nghệ',19,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(24,'KHH','Kỹ thuật hóa học',18,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(25,'TDT','Thương mại điện tử',20,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(26,'OTD','Kỹ thuật ô tô (điện)',14,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(27,'LGT','Logistics và Quản lý chuỗi cung ứng',20,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(28,'KQT','Kinh doanh quốc tế',20,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(29,'DPT','Truyền thông đa phương tiện',17,'2025-04-07 15:50:52','2025-04-07 15:50:52'),(30,'GDH','Giáo dục học',19,'2025-04-07 15:50:52','2025-04-07 15:50:52');
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
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_diem_luu_thong`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diem_luu_thong`
--

LOCK TABLES `diem_luu_thong` WRITE;
/*!40000 ALTER TABLE `diem_luu_thong` DISABLE KEYS */;
INSERT INTO `diem_luu_thong` VALUES (1,'MVN','Mượn về nhà','2025-04-17 14:25:54','2025-04-17 14:25:54'),(2,'DTC','Mượn đọc tại chỗ','2025-04-17 14:26:29','2025-04-17 14:26:29'),(6,'LV','Sách Luận Văn','2025-04-17 15:53:50','2025-04-17 15:53:50'),(9,'BC','Báo cáo','2025-04-17 18:53:30','2025-04-17 18:53:30');
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
  `ma_doi_tuong_ban_doc` text,
  `ten_doi_tuong_ban_doc` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_doi_tuong_ban_doc`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doi_tuong_ban_doc`
--

LOCK TABLES `doi_tuong_ban_doc` WRITE;
/*!40000 ALTER TABLE `doi_tuong_ban_doc` DISABLE KEYS */;
INSERT INTO `doi_tuong_ban_doc` VALUES (1,'CBGV','Cán bộ - Giảng viên','2025-04-17 16:59:19','2025-04-15 21:13:03'),(2,'SV-CQ','Sinh viên (ĐH,CĐ chính quy)','2025-04-15 21:13:03','2025-04-15 21:13:03'),(3,'SV-VLVH','LT,SV hệ ĐH liên thông,VLVH','2025-04-15 21:13:03','2025-04-15 21:13:03'),(4,'CH','Học viên Cao học','2025-04-17 16:59:07','2025-04-15 21:13:03');
/*!40000 ALTER TABLE `doi_tuong_ban_doc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `don_nhan`
--

DROP TABLE IF EXISTS `don_nhan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `don_nhan` (
  `id_don_nhan` int NOT NULL AUTO_INCREMENT,
  `nguoi_tao` text,
  `ten_don_nhan` text,
  `id_nguon_nhan` int DEFAULT NULL,
  `id_loai_nhap` int DEFAULT NULL,
  `id_trang_thai_don` int DEFAULT NULL,
  `ngay_nhan` date DEFAULT NULL,
  `id_nha_cung_cap` int DEFAULT NULL,
  `so_chung_tu` text,
  `ghi_chu` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_don_nhan`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `don_nhan`
--

LOCK TABLES `don_nhan` WRITE;
/*!40000 ALTER TABLE `don_nhan` DISABLE KEYS */;
INSERT INTO `don_nhan` VALUES (4,'Admin ','Sách tặng',1,4,6,'2025-04-24',29,NULL,NULL,'2025-04-29 18:44:42','2025-04-24 16:02:11');
/*!40000 ALTER TABLE `don_nhan` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kho_an_pham`
--

LOCK TABLES `kho_an_pham` WRITE;
/*!40000 ALTER TABLE `kho_an_pham` DISABLE KEYS */;
INSERT INTO `kho_an_pham` VALUES (1,'KM','Kho Sách Mượn','2025-04-17 11:16:27','2025-04-17 11:16:27'),(2,'KD','Kho Sách Đọc','2025-04-17 11:16:27','2025-04-17 11:16:27'),(3,'KTC','Kho Tra cứu','2025-04-17 11:16:27','2025-04-17 11:16:27'),(4,'KK','Kho khác (chưa phân kho)','2025-04-17 11:16:31','2025-04-17 11:16:27'),(5,'KB','Kho báo tạp chí','2025-04-17 11:16:27','2025-04-17 11:16:27'),(6,'LV','Luận văn','2025-04-17 11:16:27','2025-04-17 11:16:27'),(7,'CD','Kho ĐT (CD)','2025-04-17 11:16:27','2025-04-17 11:16:27');
/*!40000 ALTER TABLE `kho_an_pham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loai_nhap`
--

DROP TABLE IF EXISTS `loai_nhap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loai_nhap` (
  `id_loai_nhap` int NOT NULL AUTO_INCREMENT,
  `ten_loai_nhap` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_loai_nhap`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loai_nhap`
--

LOCK TABLES `loai_nhap` WRITE;
/*!40000 ALTER TABLE `loai_nhap` DISABLE KEYS */;
INSERT INTO `loai_nhap` VALUES (2,'Mua','2025-04-24 10:53:46','2025-04-24 10:53:46'),(3,'Cấp','2025-04-24 10:53:49','2025-04-24 10:53:49'),(4,'Tặng','2025-04-24 10:53:55','2025-04-24 10:53:55'),(5,'Khác','2025-04-24 10:53:59','2025-04-24 10:53:59');
/*!40000 ALTER TABLE `loai_nhap` ENABLE KEYS */;
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
  `id_doi_tuong_ban_doc` int DEFAULT NULL,
  `khoa_hoc` int DEFAULT NULL,
  `nien_khoa` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_lop_hoc`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lop_hoc`
--

LOCK TABLES `lop_hoc` WRITE;
/*!40000 ALTER TABLE `lop_hoc` DISABLE KEYS */;
INSERT INTO `lop_hoc` VALUES (1,'0CTP23A','2025-04-16','CH. CNTP 2023',18,2,56,'2026-2029','2025-04-15 23:43:39','2025-04-15 22:14:52');
/*!40000 ALTER TABLE `lop_hoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nguon_nhan`
--

DROP TABLE IF EXISTS `nguon_nhan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nguon_nhan` (
  `id_nguon_nhan` int NOT NULL AUTO_INCREMENT,
  `ma_nguon_nhan` text,
  `ten_nguon` text,
  `kinh_phi` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_nguon_nhan`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nguon_nhan`
--

LOCK TABLES `nguon_nhan` WRITE;
/*!40000 ALTER TABLE `nguon_nhan` DISABLE KEYS */;
INSERT INTO `nguon_nhan` VALUES (1,'NS','Ngân sách','100.000.000','2025-04-24 10:31:48','2025-04-24 10:31:38'),(3,'SPKTVL','Trường ĐHSPKTVL',NULL,'2025-04-24 10:32:16','2025-04-24 10:32:16');
/*!40000 ALTER TABLE `nguon_nhan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nha_cung_cap`
--

DROP TABLE IF EXISTS `nha_cung_cap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nha_cung_cap` (
  `id_nha_cung_cap` int NOT NULL AUTO_INCREMENT,
  `ma_nha_cung_cap` text,
  `ten_nha_cung_cap` text,
  `dia_chi` text,
  `dien_thoai` text,
  `email` text,
  `lien_he` text,
  `stk` text,
  `ngan_hang` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_nha_cung_cap`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nha_cung_cap`
--

LOCK TABLES `nha_cung_cap` WRITE;
/*!40000 ALTER TABLE `nha_cung_cap` DISABLE KEYS */;
INSERT INTO `nha_cung_cap` VALUES (2,'MK','Nhà sách minh khai','TP. HCM','0839250591','minhkhai@yahoo.com','luy','656750008','techcombank','2025-04-24 09:28:27','2025-04-24 09:25:24'),(3,'GD VN','NXB Giáo dục Việt Nam','Cần Thơ',NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(4,'ĐH QG','NXB ĐHQG TP HCM','TP.HCM','38 239 170','nxb@yahoo.com.vn','Thuy','102010001837207','TMCP Công thương VN','2025-04-24 09:27:34','2025-04-24 09:25:24'),(5,'FHS','FAHASA','TP HCM','84.838225446','fahasa sg@hcm.vnn.vn','Thuy','102010000101684','Viettinbank chi nhanh1 TP HCM','2025-04-24 09:27:50','2025-04-24 09:25:24'),(6,'ĐHSPKTVL','Trường ĐHSPKTVL','Vĩnh Long',NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(7,'KHKT','NXB Khoa học-Kỹ thuật',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(8,'KB','Không biết',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(9,'HVNDGVN','Hội Văn Nghệ Dân gian Việt Nam',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(10,'GD2','Công ty cổ phần thiết bị giáo dục 2','116 Đinh Tiên Hoàng,P1,Q.BT,TPHCM','0835118928',NULL,NULL,'102010000151153','NH TMCP Công thương VN','2025-04-24 09:28:00','2025-04-24 09:25:24'),(11,'BKHN','NXB Bách Khoa Hà Nội','Số 1 - Đường Đại Cồ Việt, quận Hai Bà Trưng, Hà Nội','0438684569',NULL,NULL,'102010000390705','NH Công thương, Hai Bà Trưng, Hà Nội','2025-04-24 09:28:17','2025-04-24 09:25:24'),(12,'XD','Nhà xuất bản Xây dựng','37 Lê Đại Hành, Q Hai Bà Trưng. Hà nội',NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(13,'CTQGCT','Nhà xuất bản chính trị quốc gia Cần Thơ',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(14,'TQ-ĐHCT','Thư quán - Đại học Cần thơ','Khu II đHCT, 3/2 p Xuân khánh, Q Ninh kiêu','07103.734.652',NULL,'Ràng',NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(15,'CHGTSĐH','Cửa hàng giới thiệu sách đại học','TP.HCM','0862726350','vnuhp@vnuhcm.edu.vn',NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(16,'TUVL','Tỉnh Ủy - UBND Vĩnh Long',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(17,'TTCGCN&DV','Trung tâm Chuyển giao Công nghệ & Dịch vụ','Khu II, Trường Đại học Cần Thơ, đường 3/2, P. Xuân Khánh, Q. Ninh Kiều. TP. Cần Thơ','07103.738333',NULL,'Ràng','000000005176 tại ngân hàng PVCombank chi nhánh Cần',NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(18,'CDIMEX','Công Ty Cổ phần Xuất Nhập khẩu & Phát triển Văn hóa - CDIMEX',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(19,'ĐHCT','NXB Đại học Cần Thơ',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(20,'KH&CN','NXB Khoa học tự nhiên và công nghệ',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(21,'ĐH LUẬT TP','Đại học Luật tp.HCM','02 Nguyễn Tất Thành, P12, Q4, tp.HCM','02839400989',NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(22,'LĐ','NXB Lao Động',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(23,'TTDL','NXB Thể thao và du lịch',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(24,'ĐHSP','NXB Đại học Sư phạm',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(25,'ĐHQGTPHCM','Cửa hàng giới thiệu sách NXB ĐHQG TP HCM',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(26,'NS KTTC','Nhà sách chính trị pháp luật kiểm toán tài chính',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(27,'ĐHQGHN','Đại học Quốc gia Hà Nội',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(28,'TP','NXB Tư pháp',NULL,NULL,NULL,NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24'),(29,'TT&TT','NXB Thông tin và truyền thông','115 Trần Duy Hưng, Cầu giấy, Hà Nội','024.35772139','nxb.tttt@mic.gov.vn',NULL,NULL,NULL,'2025-04-24 09:25:24','2025-04-24 09:25:24');
/*!40000 ALTER TABLE `nha_cung_cap` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phat_ban_doc`
--

LOCK TABLES `phat_ban_doc` WRITE;
/*!40000 ALTER TABLE `phat_ban_doc` DISABLE KEYS */;
INSERT INTO `phat_ban_doc` VALUES (2,'M-4','Mất sách (sách không giá bìa hoặc sách từ năm 2000)','Phạt 500đ/trang','2025-04-17 20:22:18','2025-04-17 20:22:18'),(3,'R-VB-3','Rách hoặc vẽ bẩn trên 10 tr hoặc xé mất 1 tr','Phạt 100% giá trị TL','2025-04-17 20:22:18','2025-04-17 20:22:18'),(4,'R-VB-2','Rách hoặc vẽ bẩn 4-10 tr TL','Phạt 50% giá trị TL','2025-04-17 20:22:18','2025-04-17 20:22:18'),(5,'TH','Trễ hạn','Phạt tiền 1000/tài liệu/ngày','2025-04-17 20:22:18','2025-04-17 20:22:18'),(6,'R-VB-1','Rách hoặc vẽ bẩn 1-3 tr TL','Phạt 30% giá trị TL','2025-04-17 20:22:18','2025-04-17 20:22:18'),(7,'M-3','Mất tài liệu (nếu chỉ 1 bản hoặc ko còn bán)','Phạt 300%+ 20.000 XLKT','2025-04-17 20:22:18','2025-04-17 20:22:18'),(8,'M-1','Mất tài liệu (nếu trên 10 bản)','Phạt 150% giá trị TL + 20.000 XLKT','2025-04-17 20:22:18','2025-04-17 20:22:18'),(9,'M-2','Mất tài liệu (nếu có từ 2-10 bản)','Phạt 200% giá trị TL + 20.000 XLKT','2025-04-17 20:22:18','2025-04-17 20:22:18');
/*!40000 ALTER TABLE `phat_ban_doc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sach`
--

DROP TABLE IF EXISTS `sach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sach` (
  `id_sach` int NOT NULL AUTO_INCREMENT,
  `id_don_nhan` int DEFAULT NULL,
  `nhan_de` text,
  `tac_gia` text,
  `nam_xuat_ban` text,
  `nha_xuat_ban` text,
  `noi_xuat_ban` text,
  `gia` text,
  `so_luong` int DEFAULT NULL,
  `thanh_tien` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sach`),
  KEY `sach_don_nhan_id_don_nhan_fk` (`id_don_nhan`),
  CONSTRAINT `sach_don_nhan_id_don_nhan_fk` FOREIGN KEY (`id_don_nhan`) REFERENCES `don_nhan` (`id_don_nhan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sach`
--

LOCK TABLES `sach` WRITE;
/*!40000 ALTER TABLE `sach` DISABLE KEYS */;
INSERT INTO `sach` VALUES (22,4,'GS. Trần Hồng Quân với sự nghiệp giáo dục đào tạo VIỆT NAM','Trần Xuân Nhĩ','2024','TT&TT','Hà Nội','200000',10,'2000000','2025-04-29 20:50:13','2025-04-26 00:50:38'),(23,4,'Cải cách hành chính công phục vụ phát triển kinh tế cải thiện môi trường kinh doanh','Đoàn Duy Khương','2016','CTQG','Hà Nội','100000',3,'300000','2025-04-29 20:50:22','2025-04-29 16:52:28'),(24,4,'Luật quy hoạch',NULL,'2017','CTQGST','Cần Thơ','200000',2,'400000','2025-04-29 20:50:28','2025-04-29 16:52:48');
/*!40000 ALTER TABLE `sach` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tai_lieu`
--

LOCK TABLES `tai_lieu` WRITE;
/*!40000 ALTER TABLE `tai_lieu` DISABLE KEYS */;
INSERT INTO `tai_lieu` VALUES (1,'TK','Sách tham khảo','2025-04-17 10:56:04','2025-04-17 10:56:04'),(2,'NV','Sách ngoại văn','2025-04-17 10:56:04','2025-04-17 10:56:04'),(3,'LV','Luận văn thạc sĩ','2025-04-17 10:56:04','2025-04-17 10:56:04'),(4,'GT','Giáo trình','2025-04-17 10:56:04','2025-04-17 10:56:04'),(5,'LA','Luận án tiến sĩ','2025-04-17 10:56:05','2025-04-17 10:56:05'),(6,'NC','Đề tài NCKH','2025-04-17 10:56:05','2025-04-17 10:56:05'),(7,'DATN','Khóa luận tốt nghiệp','2025-04-17 10:56:05','2025-04-17 10:56:05'),(8,'GTT','Giáo trình trường','2025-04-17 10:56:05','2025-04-17 10:56:05'),(9,'STKT','Sách tham khảo - Trường','2025-04-17 10:56:05','2025-04-17 10:56:05');
/*!40000 ALTER TABLE `tai_lieu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tham_so_luu_thong`
--

DROP TABLE IF EXISTS `tham_so_luu_thong`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tham_so_luu_thong` (
  `id_tham_so_luu_thong` int NOT NULL AUTO_INCREMENT,
  `id_diem_luu_thong` int DEFAULT NULL,
  `id_doi_tuong_ban_doc` int DEFAULT NULL,
  PRIMARY KEY (`id_tham_so_luu_thong`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tham_so_luu_thong`
--

LOCK TABLES `tham_so_luu_thong` WRITE;
/*!40000 ALTER TABLE `tham_so_luu_thong` DISABLE KEYS */;
INSERT INTO `tham_so_luu_thong` VALUES (26,1,1),(27,2,1),(28,6,1),(29,2,2),(30,6,2),(31,1,2),(32,2,3),(33,6,3),(34,1,3),(35,2,4),(36,6,4),(37,1,4),(38,9,1),(39,9,2),(40,9,3),(41,9,4);
/*!40000 ALTER TABLE `tham_so_luu_thong` ENABLE KEYS */;
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

--
-- Table structure for table `trang_thai_don`
--

DROP TABLE IF EXISTS `trang_thai_don`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trang_thai_don` (
  `id_trang_thai_don` int NOT NULL AUTO_INCREMENT,
  `trang_thai` text,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_trang_thai_don`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trang_thai_don`
--

LOCK TABLES `trang_thai_don` WRITE;
/*!40000 ALTER TABLE `trang_thai_don` DISABLE KEYS */;
INSERT INTO `trang_thai_don` VALUES (3,'Đang đặt','2025-04-24 10:18:38','2025-04-24 10:18:38'),(4,'Đang bổ sung','2025-04-24 10:18:43','2025-04-24 10:18:43'),(5,'Hoàn thành','2025-04-24 10:18:52','2025-04-24 10:18:52'),(6,'Đang nhận','2025-04-24 10:18:57','2025-04-24 10:18:57'),(7,'Chưa xong','2025-04-24 10:19:03','2025-04-24 10:19:03');
/*!40000 ALTER TABLE `trang_thai_don` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-07 13:55:24
