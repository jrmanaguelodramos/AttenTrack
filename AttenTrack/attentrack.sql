/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.5-10.4.32-MariaDB : Database - attentrack
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`attentrack` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `attentrack`;

/*Table structure for table `atten_logs` */

CREATE TABLE `atten_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sID` int(10) unsigned DEFAULT NULL,
  `sName` varchar(128) DEFAULT NULL,
  `cID` int(10) unsigned DEFAULT NULL,
  `room` varchar(16) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `stud_num` varchar(30) NOT NULL,
  `card_uid` varchar(30) DEFAULT NULL,
  `checkindate` date DEFAULT NULL,
  `timein` text DEFAULT NULL,
  `timeout` text DEFAULT NULL,
  `card_out` tinyint(1) DEFAULT 0,
  `remarks` varchar(32) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `logs_ibfk_1` (`sID`),
  KEY `cID` (`cID`),
  CONSTRAINT `atten_logs_ibfk_1` FOREIGN KEY (`sID`) REFERENCES `students` (`id`),
  CONSTRAINT `atten_logs_ibfk_2` FOREIGN KEY (`cID`) REFERENCES `classes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `atten_logs` */

insert  into `atten_logs`(`id`,`sID`,`sName`,`cID`,`room`,`section`,`stud_num`,`card_uid`,`checkindate`,`timein`,`timeout`,`card_out`,`remarks`,`updated_at`) values (6,3,'Jerry O Fojas Jr',4,'IL102',NULL,'24-1644','146132124121','2026-04-18','12:13:28 PM','12:13:54 PM',1,NULL,'2026-04-18 12:13:54'),(7,8,'Guilberto Managuelod Ramos Jr.',4,'IL102',NULL,'24-1573','112224234223','2026-04-22','02:19:23 PM','02:19:36 PM',1,NULL,'2026-04-22 14:19:36'),(8,3,'Jerry O Fojas Jr',4,'IL102',NULL,'24-1644','146132124121','2026-04-22','02:53:25 PM','00:00:00',0,NULL,'2026-04-22 14:53:25');

/*Table structure for table `class` */

CREATE TABLE `class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `classID` int(10) unsigned DEFAULT NULL,
  `sID` int(10) unsigned DEFAULT NULL,
  `card_uid` varchar(30) DEFAULT NULL,
  `status` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `classID` (`classID`),
  KEY `sID` (`sID`),
  CONSTRAINT `class_ibfk_1` FOREIGN KEY (`classID`) REFERENCES `classes` (`id`),
  CONSTRAINT `class_ibfk_2` FOREIGN KEY (`sID`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `class` */

insert  into `class`(`id`,`classID`,`sID`,`card_uid`,`status`) values (1,4,3,'146132124121','active'),(2,4,8,'112224234223','active'),(4,4,3,'146132124121','active'),(5,4,6,'1771719124','active'),(6,4,7,'1479863191','active'),(7,4,8,'112224234223','active'),(8,4,9,'17873136122','active'),(9,4,10,'44103235223','active'),(10,4,11,'460132170254122128','active'),(11,4,12,'28118236223','active'),(12,4,13,'9113062191','active'),(13,4,14,'1629358121','active'),(14,4,15,'1628980121','active'),(15,4,16,'7849239223','active'),(16,4,17,'17329159190','active'),(17,4,18,'6734172208','active'),(18,4,19,'14617199121','active'),(19,4,20,'1143794122','active'),(20,4,21,'226158142122','active'),(21,4,22,'13023663121','active'),(22,4,23,'21018060121','active'),(23,4,24,'16133238223','active'),(24,4,25,'18516869191','active'),(25,4,26,'46148234223','active'),(26,4,27,'22597242207','active'),(27,4,28,'49209234223','active'),(28,4,29,'6679102121','active');

/*Table structure for table `classes` */

CREATE TABLE `classes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tID` int(10) unsigned DEFAULT NULL,
  `course_code` varchar(16) DEFAULT NULL,
  `subject` text NOT NULL,
  `room` varchar(16) NOT NULL,
  `section` varchar(12) NOT NULL,
  `schedule` tinyint(1) DEFAULT NULL,
  `time_start` time DEFAULT NULL,
  `grace` int(4) DEFAULT NULL,
  `class_duration` int(4) DEFAULT NULL,
  `max_absentees` tinyint(2) DEFAULT NULL,
  `accepted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tID` (`tID`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`tID`) REFERENCES `teachers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `classes` */

insert  into `classes`(`id`,`tID`,`course_code`,`subject`,`room`,`section`,`schedule`,`time_start`,`grace`,`class_duration`,`max_absentees`,`accepted`) values (4,2,'SE101','Software Engineering','IL102','SBIT-2B',3,'14:30:00',15,180,3,1);

/*Table structure for table `devices` */

CREATE TABLE `devices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `device_uid` text DEFAULT NULL,
  `room` varchar(16) NOT NULL,
  `building` varchar(64) NOT NULL,
  `device_date` date DEFAULT NULL,
  `device_mode` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `devices` */

insert  into `devices`(`id`,`device_uid`,`room`,`building`,`device_date`,`device_mode`) values (2,'a1d2a7c6b7250505','IL102','acad','2026-03-29',1);

/*Table structure for table `intrusion_logs` */

CREATE TABLE `intrusion_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sID` int(10) unsigned DEFAULT NULL,
  `card_UID` varchar(30) DEFAULT NULL,
  `room` varchar(16) DEFAULT NULL,
  `event_type` varchar(30) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `intrusion_logs` */

/*Table structure for table `students` */

CREATE TABLE `students` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fname` varchar(64) NOT NULL DEFAULT 'none',
  `lname` varchar(64) NOT NULL DEFAULT 'none',
  `mname` varchar(64) DEFAULT NULL,
  `section` varchar(12) DEFAULT NULL,
  `year` enum('1st','2nd','3rd','4th','irreg') DEFAULT NULL,
  `gender` varchar(8) NOT NULL DEFAULT 'male',
  `stud_num` varchar(10) NOT NULL DEFAULT '0',
  `course` varchar(128) DEFAULT NULL,
  `card_uid` varchar(30) DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  `card_select` tinyint(1) DEFAULT 1,
  `add_card` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `students` */

insert  into `students`(`id`,`fname`,`lname`,`mname`,`section`,`year`,`gender`,`stud_num`,`course`,`card_uid`,`last_update`,`card_select`,`add_card`) values (3,'Jerry','Fojas Jr','O','SBIT-2B',NULL,'Male','24-1644','BSIT','146132124121','2026-04-11 15:19:54',0,1),(6,'Astan','Quinn','O','SBIT-2B',NULL,'Male','24-1645','BSIT','1771719124','2026-04-18 12:19:17',0,1),(7,'Tristan','Sebastian','Perniya','SBIT-2B',NULL,'Male','24-1550','BSIT','1479863191','2026-04-22 14:15:58',0,1),(8,'Guilberto','Ramos Jr.','Managuelod','SBIT-2B',NULL,'Male','24-1573','BSIT','112224234223','2026-04-22 14:17:55',0,1),(9,'Jomar Matthew','Cantere','Balictar','SBIT-2B',NULL,'Male','24-1514','BSIT','17873136122','2026-04-22 14:21:49',0,1),(10,'Chester Timothy','Pajoteya','Sioson','SBIT-2B',NULL,'Male','24-1436','BSIT','44103235223','2026-04-22 14:23:26',0,1),(11,'Khalil','Cambangay','T','SBIT-2B',NULL,'Male','24-1475','BSIT','460132170254122128','2026-04-22 14:26:23',0,1),(12,'Karl Shane','Cabalo','C','SBIT-2B',NULL,'Male','24-1515','BSIT','28118236223','2026-04-22 14:31:02',0,1),(13,'Aaron Vincent','Mapili','S','SBIT-2B',NULL,'Male','24-1640','BSIT','9113062191','2026-04-22 14:34:27',0,1),(14,'James Benedict','Vargas','V','SBIT-2B',NULL,'Male','24-1667','BSIT','1629358121','2026-04-22 14:36:22',0,1),(15,'Aaron James','Silastre','B','SBIT-2B',NULL,'Male','24-1656','BSIT','1628980121','2026-04-22 14:37:02',0,1),(16,'Jones Nhick','Bautista','C','SBIT-2B',NULL,'Male','24-1445','BSIT','7849239223','2026-04-22 14:37:49',0,1),(17,'Justin Marco','Bonaobra','N','SBIT-2B',NULL,'Male','24_1427','BSIT','17329159190','2026-04-22 14:38:27',0,1),(18,'Hanz Christian','Avila','O','SBIT-2B',NULL,'Male','22-2667','BSIT','6734172208','2026-04-22 14:39:52',0,1),(19,'Carl Kenneth','Clerigo','G','SBIT-2B',NULL,'Male','24-1689','BSIT','14617199121','2026-04-22 14:40:37',0,1),(20,'Roven Christopher','Burasca','A','SBIT-2B',NULL,'Male','24-1692','BSIT','1143794122','2026-04-22 14:41:21',0,1),(21,'Michael Jr.','Rojas','G','SBIT-2B',NULL,'Male','24-1527','BSIT','226158142122','2026-04-22 14:41:56',0,1),(22,'Daniel','Junio','D.V','SBIT-2B',NULL,'Male','24-1669','BSIT','13023663121','2026-04-22 14:42:37',0,1),(23,'Michael Philip','Celvio','C','SBIT-2B',NULL,'Male','24-1666','BSIT','21018060121','2026-04-22 14:43:17',0,1),(24,'Joefrey','Dionisio','T','SBIT-2B',NULL,'Male','24-1446','BSIT','16133238223','2026-04-22 14:44:02',0,1),(25,'Renmark','Tentoco','C','SBIT-2B',NULL,'Male','24-1463','BSIT','18516869191','2026-04-22 14:44:35',0,1),(26,'Duncan Kyenzo','Tajan','','SBIT-2B',NULL,'Male','24-1461','BSIT','46148234223','2026-04-22 14:47:42',0,1),(27,'Ron Mavin','Neri','M','SBIT-2B',NULL,'Male','24-1479','BSIT','22597242207','2026-04-22 14:48:11',0,1),(28,'Henry Jr.','Aragon','','SBIT-2B',NULL,'Male','24-1652','BSIT','49209234223','2026-04-22 14:49:04',0,1),(29,'Sean Terrence','Sequitin','T','SBIT-2B',NULL,'Male','24-3738','BSIT','6679102121','2026-04-22 14:49:47',0,1);

/*Table structure for table `teachers` */

CREATE TABLE `teachers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fname` varchar(64) NOT NULL,
  `mname` varchar(64) DEFAULT NULL,
  `lname` varchar(64) NOT NULL,
  `department` enum('it','is','cs') DEFAULT NULL,
  `subjects` text DEFAULT NULL,
  `userID` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userID` (`userID`),
  CONSTRAINT `teachers_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `teachers` */

insert  into `teachers`(`id`,`fname`,`mname`,`lname`,`department`,`subjects`,`userID`) values (2,'Bean',NULL,'Paculanan','it','cc101',1);

/*Table structure for table `users` */

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` longtext NOT NULL,
  `role` varchar(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`password`,`role`) values (1,'paculanan','$2y$10$4VE1w3AkNvB01u8hte44D.TPO6LaZmWoHYerFehJZN9SRYISwviq6','teacher'),(2,'admin','$2y$10$4VE1w3AkNvB01u8hte44D.TPO6LaZmWoHYerFehJZN9SRYISwviq6','admin');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
