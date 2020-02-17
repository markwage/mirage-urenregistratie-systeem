/*
SQLyog Community v13.1.2 (64 bit)
MySQL - 5.7.24 : Database - mus
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `approvals` */

DROP TABLE IF EXISTS `approvals`;

CREATE TABLE `approvals` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `maand` int(2) DEFAULT NULL,
  `jaar` int(4) DEFAULT NULL,
  `user` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `datum` (`maand`,`jaar`),
  KEY `user` (`user`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

/*Data for the table `approvals` */

insert  into `approvals`(`ID`,`maand`,`jaar`,`user`) values 
(1,11,2019,'mwage'),
(7,12,2019,'mwage'),
(17,1,2020,'mwage2');

/*Table structure for table `beginsaldo` */

DROP TABLE IF EXISTS `beginsaldo`;

CREATE TABLE `beginsaldo` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `jaar` int(4) NOT NULL,
  `username` varchar(60) NOT NULL,
  `beginsaldo` int(4) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

/*Data for the table `beginsaldo` */

insert  into `beginsaldo`(`ID`,`jaar`,`username`,`beginsaldo`) values 
(12,2019,'fbosman',240),
(11,2019,'mwage',240),
(4,2020,'mwage',280),
(5,2020,'fbosman',240),
(6,2020,'apaters',220),
(7,2020,'abredze',220),
(8,2020,'rlans',240),
(9,2020,'mjansen',240),
(10,2020,'mklinkenberg',240),
(13,2019,'apaters',240),
(14,2019,'abredze',240),
(15,2019,'rlans',240),
(16,2019,'mjansen',200),
(17,2019,'mklinkenberg',210),
(23,2020,'mwage2',240),
(22,2020,'demo',175),
(24,2019,'mwage2',240),
(25,2020,'demo2',240);

/*Table structure for table `nieuws` */

DROP TABLE IF EXISTS `nieuws`;

CREATE TABLE `nieuws` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `datum` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nieuwsheader` varchar(128) NOT NULL,
  `nieuwsbericht` mediumtext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

/*Data for the table `nieuws` */

insert  into `nieuws`(`ID`,`datum`,`nieuwsheader`,`nieuwsbericht`) values 
(29,'2020-02-04 15:11:09','Nieuw Mirage Urenregistratie Systeem','Er is een nieuwe urenregistratie welke de excel sheets vervangt.\r\nZorg dat je je uren nauwkeurig invult, zowel de facturabele als de niet facturabele uren.\r\nHeb je vragen en/of opmerkingen of zit er een bepaalde urensoort niet tussen die jij wel verwacht, stuur dan even een mail naar Robert van der Lans en Mark Wage.');

/*Table structure for table `soorturen` */

DROP TABLE IF EXISTS `soorturen`;

CREATE TABLE `soorturen` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(8) NOT NULL,
  `omschrijving` varchar(60) NOT NULL,
  `facturabel` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `code` (`code`),
  KEY `Omschrijving` (`omschrijving`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

/*Data for the table `soorturen` */

insert  into `soorturen`(`ID`,`code`,`omschrijving`,`facturabel`) values 
(1,'MIR000UU','Gewerkte uren',1),
(2,'MIR001VL','Opgenomen verlofuren',0),
(3,'MIR003BV','Bijzonder verlof',0),
(4,'MIR003FD','Erkende feestdag',0),
(5,'MIR010CU','Cursus',0),
(6,'MIR030IN','Intern',0),
(7,'MIR040BA','Bezoek arts',0),
(8,'MIR043ZK','Ziek',0),
(9,'MIR100OV','Overwerk tegen 100%',1),
(10,'MIR115OV','Overwerk tegen 115%',1),
(11,'MIR120OV','Overwerk tegen 120%',1),
(12,'MIR125OV','Overwerk tegen 125%',1),
(13,'MIR150OV','Overwerk tegen 150%',1),
(14,'MIR200OV','Overwerk tegen 200%',1),
(15,'MIR020LL','Leegloop',0),
(38,'RABSBYHG','Rabo Standby Hoog',1),
(39,'RABSBYLG','Rabo Standby Laag',1);

/*Table structure for table `uren` */

DROP TABLE IF EXISTS `uren`;

CREATE TABLE `uren` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `jaar` int(4) NOT NULL,
  `maand` int(2) DEFAULT NULL,
  `week` int(2) unsigned NOT NULL,
  `dagnummer` tinyint(1) NOT NULL,
  `soortuur` char(8) NOT NULL,
  `datum` date NOT NULL,
  `uren` decimal(5,2) NOT NULL,
  `user` varchar(60) NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `approveddatum` date DEFAULT NULL,
  `approvedbyuser` varchar(60) DEFAULT NULL,
  UNIQUE KEY `ID` (`ID`),
  KEY `datum` (`user`,`approvedbyuser`),
  KEY `soortuur` (`soortuur`),
  KEY `weeknummer` (`week`),
  KEY `user` (`user`)
) ENGINE=MyISAM AUTO_INCREMENT=1569 DEFAULT CHARSET=utf8;

/*Data for the table `uren` */

insert  into `uren`(`ID`,`jaar`,`maand`,`week`,`dagnummer`,`soortuur`,`datum`,`uren`,`user`,`approved`,`approveddatum`,`approvedbyuser`) values 
(1131,2019,11,45,2,'MIR001VL','2019-11-06',8.00,'mwage',1,'2020-01-10','mwage'),
(1132,2019,11,46,0,'MIR000UU','2019-11-11',8.00,'mwage',1,'2020-01-10','mwage'),
(1133,2019,11,46,1,'MIR000UU','2019-11-12',8.00,'mwage',1,'2020-01-10','mwage'),
(1134,2019,11,46,2,'MIR000UU','2019-11-13',8.00,'mwage',1,'2020-01-10','mwage'),
(1129,2019,11,45,3,'MIR000UU','2019-11-07',8.00,'mwage',1,'2020-01-10','mwage'),
(1128,2019,11,45,1,'MIR000UU','2019-11-05',8.00,'mwage',1,'2020-01-10','mwage'),
(1127,2019,11,45,0,'MIR000UU','2019-11-04',8.00,'mwage',1,'2020-01-10','mwage'),
(1480,2020,1,4,2,'MIR200OV','2020-01-22',9.00,'mwage',0,NULL,NULL),
(1478,2020,1,4,6,'MIR100OV','2020-01-26',5.00,'mwage',0,NULL,NULL),
(1374,2019,12,48,6,'MIR200OV','2019-12-01',3.00,'mwage',1,'2020-01-27','mwage'),
(1479,2020,1,4,6,'MIR150OV','2020-01-26',5.00,'mwage',0,NULL,NULL),
(1293,2020,1,3,4,'MIR020LL','2020-01-17',8.00,'mwage',0,NULL,NULL),
(1292,2020,1,3,3,'MIR020LL','2020-01-16',8.00,'mwage',0,NULL,NULL),
(1484,2020,1,1,5,'MIR150OV','2020-01-04',5.00,'mwage',0,NULL,NULL),
(1483,2020,1,1,4,'MIR150OV','2020-01-03',5.00,'mwage',0,NULL,NULL),
(1283,2019,12,52,4,'MIR020LL','2019-12-27',8.00,'mwage',1,'2020-01-27','mwage'),
(1282,2019,12,52,3,'MIR003FD','2019-12-26',8.00,'mwage',1,'2020-01-27','mwage'),
(1281,2019,12,52,2,'MIR003FD','2019-12-25',8.00,'mwage',1,'2020-01-27','mwage'),
(1280,2019,12,52,1,'MIR001VL','2019-12-24',8.00,'mwage',1,'2020-01-27','mwage'),
(1279,2019,12,52,0,'MIR001VL','2019-12-23',8.00,'mwage',1,'2020-01-27','mwage'),
(1278,2019,12,51,3,'MIR001VL','2019-12-19',4.00,'mwage',1,'2020-01-27','mwage'),
(1277,2019,12,51,4,'MIR000UU','2019-12-20',8.00,'mwage',1,'2020-01-27','mwage'),
(1276,2019,12,51,3,'MIR000UU','2019-12-19',4.00,'mwage',1,'2020-01-27','mwage'),
(1275,2019,12,51,2,'MIR000UU','2019-12-18',8.00,'mwage',1,'2020-01-27','mwage'),
(1274,2019,12,51,1,'MIR000UU','2019-12-17',8.00,'mwage',1,'2020-01-27','mwage'),
(1273,2019,12,51,0,'MIR000UU','2019-12-16',8.00,'mwage',1,'2020-01-27','mwage'),
(1413,2020,1,2,1,'MIR003BV','2020-01-07',8.00,'mwage',0,NULL,NULL),
(1482,2020,1,1,3,'MIR020LL','2020-01-02',8.00,'mwage',0,NULL,NULL),
(1272,2019,12,50,4,'MIR000UU','2019-12-13',8.00,'mwage',1,'2020-01-27','mwage'),
(1269,2019,12,50,1,'MIR000UU','2019-12-10',8.00,'mwage',1,'2020-01-27','mwage'),
(1270,2019,12,50,2,'MIR000UU','2019-12-11',8.00,'mwage',1,'2020-01-27','mwage'),
(1271,2019,12,50,3,'MIR000UU','2019-12-12',8.00,'mwage',1,'2020-01-27','mwage'),
(1268,2019,12,50,0,'MIR000UU','2019-12-09',8.00,'mwage',1,'2020-01-27','mwage'),
(1267,2019,12,49,4,'MIR000UU','2019-12-06',8.00,'mwage',1,'2020-01-27','mwage'),
(1291,2020,1,3,2,'MIR003FD','2020-01-15',8.00,'mwage',0,NULL,NULL),
(1290,2020,1,3,1,'MIR001VL','2020-01-14',8.00,'mwage',0,NULL,NULL),
(1289,2020,1,3,0,'MIR000UU','2020-01-13',8.00,'mwage',0,NULL,NULL),
(1266,2019,12,49,3,'MIR000UU','2019-12-05',8.00,'mwage',1,'2020-01-27','mwage'),
(1263,2019,12,49,0,'MIR000UU','2019-12-02',8.00,'mwage',1,'2020-01-27','mwage'),
(1264,2019,12,49,1,'MIR000UU','2019-12-03',8.00,'mwage',1,'2020-01-27','mwage'),
(1265,2019,12,49,2,'MIR000UU','2019-12-04',8.00,'mwage',1,'2020-01-27','mwage'),
(1130,2019,11,45,4,'MIR000UU','2019-11-08',8.00,'mwage',1,'2020-01-10','mwage'),
(1148,2019,11,48,4,'MIR003BV','2019-11-29',4.00,'mwage',1,'2020-01-10','mwage'),
(1147,2019,11,48,4,'MIR010CU','2019-11-29',4.00,'mwage',1,'2020-01-10','mwage'),
(1146,2019,11,48,3,'MIR003BV','2019-11-28',8.00,'mwage',1,'2020-01-10','mwage'),
(1145,2019,11,48,2,'MIR003BV','2019-11-27',8.00,'mwage',1,'2020-01-10','mwage'),
(1144,2019,11,48,1,'MIR000UU','2019-11-26',8.00,'mwage',1,'2020-01-10','mwage'),
(1143,2019,11,48,0,'MIR000UU','2019-11-25',8.00,'mwage',1,'2020-01-10','mwage'),
(1142,2019,11,47,4,'MIR000UU','2019-11-22',8.00,'mwage',1,'2020-01-10','mwage'),
(1141,2019,11,47,3,'MIR000UU','2019-11-21',8.00,'mwage',1,'2020-01-10','mwage'),
(1140,2019,11,47,2,'MIR000UU','2019-11-20',8.00,'mwage',1,'2020-01-10','mwage'),
(1139,2019,11,47,1,'MIR010CU','2019-11-19',8.00,'mwage',1,'2020-01-10','mwage'),
(1138,2019,11,47,0,'MIR010CU','2019-11-18',8.00,'mwage',1,'2020-01-10','mwage'),
(1137,2019,11,46,5,'MIR115OV','2019-11-16',4.00,'mwage',1,'2020-01-10','mwage'),
(1136,2019,11,46,4,'MIR000UU','2019-11-15',8.00,'mwage',1,'2020-01-10','mwage'),
(1135,2019,11,46,3,'MIR000UU','2019-11-14',8.00,'mwage',1,'2020-01-10','mwage'),
(1126,2019,11,44,4,'MIR000UU','2019-11-01',8.00,'mwage',1,'2020-01-10','mwage'),
(1347,2020,12,1,1,'MIR020LL','2019-12-31',8.00,'mwage',1,'2020-01-27','mwage'),
(1346,2020,12,1,0,'MIR020LL','2019-12-30',8.00,'mwage',1,'2020-01-27','mwage'),
(1375,2019,12,48,6,'RABSBYLG','2019-12-01',6.00,'mwage',1,'2020-01-27','mwage'),
(1412,2020,1,2,0,'MIR000UU','2020-01-06',8.00,'mwage',0,NULL,NULL),
(1481,2020,1,1,2,'MIR020LL','2020-01-01',8.00,'mwage',0,NULL,NULL),
(1404,2020,12,1,1,'MIR003FD','2019-12-31',8.00,'mwage',1,'2020-01-27','mwage'),
(1373,2019,12,48,6,'MIR115OV','2019-12-01',5.00,'mwage',1,'2020-01-27','mwage'),
(1477,2020,1,4,1,'MIR100OV','2020-01-21',4.00,'mwage',0,NULL,NULL),
(1376,2019,12,48,6,'RABSBYHG','2019-12-01',14.00,'mwage',1,'2020-01-27','mwage'),
(1469,2020,2,5,6,'MIR001VL','2020-02-02',8.00,'mwage',0,NULL,NULL),
(1468,2020,1,5,3,'MIR000UU','2020-01-30',8.00,'mwage',0,NULL,NULL),
(1470,2020,1,5,0,'MIR020LL','2020-01-27',8.00,'mwage',0,NULL,NULL),
(1476,2020,1,4,1,'MIR020LL','2020-01-21',5.00,'mwage',0,NULL,NULL),
(1485,2020,1,5,0,'MIR000UU','2020-01-27',8.00,'apaters',0,NULL,NULL),
(1486,2020,1,5,1,'MIR000UU','2020-01-28',8.00,'apaters',0,NULL,NULL),
(1487,2020,1,5,4,'MIR000UU','2020-01-31',8.00,'apaters',0,NULL,NULL),
(1488,2020,1,5,2,'MIR001VL','2020-01-29',8.00,'apaters',0,NULL,NULL),
(1489,2020,1,5,3,'MIR001VL','2020-01-30',8.00,'apaters',0,NULL,NULL),
(1493,2020,2,6,1,'MIR000UU','2020-02-04',8.00,'mwage',0,NULL,NULL),
(1492,2020,2,6,0,'MIR000UU','2020-02-03',8.00,'mwage',0,NULL,NULL),
(1494,2020,2,6,2,'MIR001VL','2020-02-05',8.00,'mwage',0,NULL,NULL),
(1495,2020,2,6,3,'MIR020LL','2020-02-06',8.00,'mwage',0,NULL,NULL),
(1543,2020,1,5,4,'MIR000UU','2020-01-31',8.00,'mwage2',1,'2020-02-13','mwage'),
(1542,2020,1,5,3,'MIR000UU','2020-01-30',8.00,'mwage2',1,'2020-02-13','mwage'),
(1541,2020,1,5,2,'MIR000UU','2020-01-29',8.00,'mwage2',1,'2020-02-13','mwage'),
(1540,2020,1,5,1,'MIR000UU','2020-01-28',8.00,'mwage2',1,'2020-02-13','mwage'),
(1539,2020,1,5,0,'MIR000UU','2020-01-27',8.00,'mwage2',1,'2020-02-13','mwage'),
(1538,2020,1,4,2,'MIR043ZK','2020-01-22',8.00,'mwage2',1,'2020-02-13','mwage'),
(1537,2020,1,4,1,'MIR043ZK','2020-01-21',8.00,'mwage2',1,'2020-02-13','mwage'),
(1536,2020,1,4,0,'MIR001VL','2020-01-20',4.00,'mwage2',1,'2020-02-13','mwage'),
(1535,2020,1,4,4,'MIR000UU','2020-01-24',8.00,'mwage2',1,'2020-02-13','mwage'),
(1534,2020,1,4,3,'MIR000UU','2020-01-23',8.00,'mwage2',1,'2020-02-13','mwage'),
(1533,2020,1,4,0,'MIR000UU','2020-01-20',4.00,'mwage2',1,'2020-02-13','mwage'),
(1532,2020,1,3,4,'MIR000UU','2020-01-17',8.00,'mwage2',1,'2020-02-13','mwage'),
(1531,2020,1,3,3,'MIR000UU','2020-01-16',8.00,'mwage2',1,'2020-02-13','mwage'),
(1530,2020,1,3,2,'MIR000UU','2020-01-15',8.00,'mwage2',1,'2020-02-13','mwage'),
(1529,2020,1,3,1,'MIR000UU','2020-01-14',8.00,'mwage2',1,'2020-02-13','mwage'),
(1528,2020,1,3,0,'MIR000UU','2020-01-13',8.00,'mwage2',1,'2020-02-13','mwage'),
(1527,2020,1,2,4,'MIR000UU','2020-01-10',8.00,'mwage2',1,'2020-02-13','mwage'),
(1526,2020,1,2,3,'MIR000UU','2020-01-09',8.00,'mwage2',1,'2020-02-13','mwage'),
(1525,2020,1,2,2,'MIR000UU','2020-01-08',8.00,'mwage2',1,'2020-02-13','mwage'),
(1524,2020,1,2,1,'MIR000UU','2020-01-07',8.00,'mwage2',1,'2020-02-13','mwage'),
(1523,2020,1,2,0,'MIR000UU','2020-01-06',8.00,'mwage2',1,'2020-02-13','mwage'),
(1522,2020,1,1,4,'MIR001VL','2020-01-03',8.00,'mwage2',1,'2020-02-13','mwage'),
(1521,2020,1,1,3,'MIR001VL','2020-01-02',8.00,'mwage2',1,'2020-02-13','mwage'),
(1520,2020,1,1,2,'MIR003FD','2020-01-01',8.00,'mwage2',1,'2020-02-13','mwage'),
(1544,2020,2,6,0,'MIR001VL','2020-02-03',8.00,'mwage2',0,NULL,NULL),
(1545,2020,2,6,1,'MIR001VL','2020-02-04',8.00,'mwage2',0,NULL,NULL),
(1546,2020,2,6,2,'MIR001VL','2020-02-05',8.00,'mwage2',0,NULL,NULL),
(1547,2020,2,6,3,'MIR001VL','2020-02-06',8.00,'mwage2',0,NULL,NULL),
(1548,2020,2,6,4,'MIR001VL','2020-02-07',8.00,'mwage2',0,NULL,NULL),
(1568,2020,2,7,2,'MIR001VL','2020-02-12',4.00,'mwage2',0,NULL,NULL),
(1567,2020,2,7,0,'MIR001VL','2020-02-10',8.00,'mwage2',0,NULL,NULL),
(1566,2020,2,7,4,'MIR000UU','2020-02-14',8.00,'mwage2',0,NULL,NULL),
(1565,2020,2,7,3,'MIR000UU','2020-02-13',8.00,'mwage2',0,NULL,NULL),
(1564,2020,2,7,1,'MIR000UU','2020-02-11',8.00,'mwage2',0,NULL,NULL);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `uren_invullen` tinyint(1) NOT NULL DEFAULT '1',
  `voornaam` varchar(24) NOT NULL,
  `tussenvoegsel` varchar(10) NOT NULL,
  `achternaam` varchar(60) NOT NULL,
  `emailadres` varchar(60) NOT NULL,
  `indienst` tinyint(1) NOT NULL DEFAULT '1',
  `lastloggedin` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `approvenallowed` tinyint(1) NOT NULL DEFAULT '0',
  `wrong_password_count` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `username` (`username`),
  KEY `achternaam` (`achternaam`)
) ENGINE=MyISAM AUTO_INCREMENT=84 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`ID`,`username`,`password`,`admin`,`uren_invullen`,`voornaam`,`tussenvoegsel`,`achternaam`,`emailadres`,`indienst`,`lastloggedin`,`approvenallowed`,`wrong_password_count`) values 
(19,'mwage','4c19e0f15b05949db9f467fef57b2e63',1,1,'Mark','','Wage','mark.wage@mirage.nl',1,'2020-02-14 09:10:06',1,0),
(44,'fbosman','d41d8cd98f00b204e9800998ecf8427e',1,1,'Frank','','Bosman','fbosman@mirage.nl',1,'2018-04-07 12:04:58',0,0),
(46,'apaters','44e32b0ff9a6af0108a1a06741b96d25',0,1,'Arjan','','Paters','arjan.paters@mirage.nl',1,'2020-02-01 23:15:36',0,0),
(43,'abredze','d7598d13a5a968add88f27dd3f6aa457',1,1,'Arjan','','Bredze','abredze@mirage.nl',1,'2018-04-07 12:04:58',1,0),
(81,'mwage2','ffa2246051d262db0673a3f6eea6870e',0,1,'Mark','demo','Wage','mark.wage@hotmail.com',1,'2020-02-13 23:31:04',0,0),
(60,'rlans','0bb18b9c134e4fb82aa13655f095aa67',1,1,'Robert','van der','Lans','robert.vanderlans@mirage.nl',1,'2020-02-03 20:01:45',1,0),
(69,'mjansen','d70650118e3425dd7108f2231708a66b',0,1,'Mario','','Jansen','mario.jansen@mirage.nl',1,'2020-01-27 17:21:21',0,0),
(72,'mklinkenberg','b30cd6f49a0394cf92499f09953341fd',0,1,'Marco','','Klinkenberg','marco.klinkenberg@mirage.nl',1,'2020-02-04 15:34:57',0,0),
(80,'demo','fe01ce2a7fbac8fafaed7c982a04e229',0,1,'demo','','demo','demo@asd.nl',1,'1970-01-01 00:00:00',0,0),
(82,'demo2','1066726e7160bd9c987c9968e0cc275a',0,1,'demo','','demo','demo@asd.nl',1,'1970-01-01 00:00:00',0,0);

/*Table structure for table `view_uren_get_full_username` */

DROP TABLE IF EXISTS `view_uren_get_full_username`;

/*!50001 DROP VIEW IF EXISTS `view_uren_get_full_username` */;
/*!50001 DROP TABLE IF EXISTS `view_uren_get_full_username` */;

/*!50001 CREATE TABLE  `view_uren_get_full_username`(
 `ID` int(11) ,
 `jaar` int(4) ,
 `maand` int(2) ,
 `approval_jaar` int(4) ,
 `approval_maand` int(2) ,
 `week` int(2) unsigned ,
 `dagnummer` tinyint(1) ,
 `soortuur` char(8) ,
 `datum` date ,
 `uren` decimal(5,2) ,
 `approved` tinyint(1) ,
 `approveddatum` date ,
 `approvedbyuser` varchar(60) ,
 `user` varchar(60) ,
 `voornaam` varchar(24) ,
 `tussenvoegsel` varchar(10) ,
 `achternaam` varchar(60) ,
 `uren_invullen` tinyint(1) 
)*/;

/*Table structure for table `view_uren_soortuur` */

DROP TABLE IF EXISTS `view_uren_soortuur`;

/*!50001 DROP VIEW IF EXISTS `view_uren_soortuur` */;
/*!50001 DROP TABLE IF EXISTS `view_uren_soortuur` */;

/*!50001 CREATE TABLE  `view_uren_soortuur`(
 `ID` int(11) ,
 `approval_jaar` int(4) ,
 `approval_maand` int(2) ,
 `maand` int(2) ,
 `jaar` int(4) ,
 `week` int(2) unsigned ,
 `dagnummer` tinyint(1) ,
 `soortuur` char(8) ,
 `datum` date ,
 `uren` decimal(5,2) ,
 `user` varchar(60) ,
 `approved` tinyint(1) ,
 `approveddatum` date ,
 `approvedbyuser` varchar(60) ,
 `omschrijving` varchar(60) ,
 `facturabel` tinyint(1) 
)*/;

/*Table structure for table `view_users_verlofuren` */

DROP TABLE IF EXISTS `view_users_verlofuren`;

/*!50001 DROP VIEW IF EXISTS `view_users_verlofuren` */;
/*!50001 DROP TABLE IF EXISTS `view_users_verlofuren` */;

/*!50001 CREATE TABLE  `view_users_verlofuren`(
 `ID` int(11) ,
 `beginsaldo` int(4) ,
 `jaar` int(4) ,
 `username` varchar(60) ,
 `voornaam` varchar(24) ,
 `tussenvoegsel` varchar(10) ,
 `achternaam` varchar(60) 
)*/;

/*Table structure for table `view_verlofuren` */

DROP TABLE IF EXISTS `view_verlofuren`;

/*!50001 DROP VIEW IF EXISTS `view_verlofuren` */;
/*!50001 DROP TABLE IF EXISTS `view_verlofuren` */;

/*!50001 CREATE TABLE  `view_verlofuren`(
 `ID` int(11) ,
 `approval_jaar` int(4) ,
 `approval_maand` int(2) ,
 `approval_dag` int(2) ,
 `uren` decimal(5,2) ,
 `approved` tinyint(1) ,
 `username` varchar(60) ,
 `beginsaldo` int(4) ,
 `fullname` varchar(97) 
)*/;

/*View structure for view view_uren_get_full_username */

/*!50001 DROP TABLE IF EXISTS `view_uren_get_full_username` */;
/*!50001 DROP VIEW IF EXISTS `view_uren_get_full_username` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_uren_get_full_username` AS (select `uren`.`ID` AS `ID`,`uren`.`jaar` AS `jaar`,`uren`.`maand` AS `maand`,year(`uren`.`datum`) AS `approval_jaar`,month(`uren`.`datum`) AS `approval_maand`,`uren`.`week` AS `week`,`uren`.`dagnummer` AS `dagnummer`,`uren`.`soortuur` AS `soortuur`,`uren`.`datum` AS `datum`,`uren`.`uren` AS `uren`,`uren`.`approved` AS `approved`,`uren`.`approveddatum` AS `approveddatum`,`uren`.`approvedbyuser` AS `approvedbyuser`,`usr`.`username` AS `user`,`usr`.`voornaam` AS `voornaam`,`usr`.`tussenvoegsel` AS `tussenvoegsel`,`usr`.`achternaam` AS `achternaam`,`usr`.`uren_invullen` AS `uren_invullen` from (`users` `usr` left join `uren` on((`usr`.`username` = `uren`.`user`)))) */;

/*View structure for view view_uren_soortuur */

/*!50001 DROP TABLE IF EXISTS `view_uren_soortuur` */;
/*!50001 DROP VIEW IF EXISTS `view_uren_soortuur` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_uren_soortuur` AS (select `u`.`ID` AS `ID`,year(`u`.`datum`) AS `approval_jaar`,month(`u`.`datum`) AS `approval_maand`,`u`.`maand` AS `maand`,`u`.`jaar` AS `jaar`,`u`.`week` AS `week`,`u`.`dagnummer` AS `dagnummer`,`u`.`soortuur` AS `soortuur`,`u`.`datum` AS `datum`,`u`.`uren` AS `uren`,`u`.`user` AS `user`,`u`.`approved` AS `approved`,`u`.`approveddatum` AS `approveddatum`,`u`.`approvedbyuser` AS `approvedbyuser`,`s`.`omschrijving` AS `omschrijving`,`s`.`facturabel` AS `facturabel` from (`uren` `u` left join `soorturen` `s` on((`u`.`soortuur` = `s`.`code`)))) */;

/*View structure for view view_users_verlofuren */

/*!50001 DROP TABLE IF EXISTS `view_users_verlofuren` */;
/*!50001 DROP VIEW IF EXISTS `view_users_verlofuren` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_users_verlofuren` AS (select `beg`.`ID` AS `ID`,`beg`.`beginsaldo` AS `beginsaldo`,`beg`.`jaar` AS `jaar`,`usr`.`username` AS `username`,`usr`.`voornaam` AS `voornaam`,`usr`.`tussenvoegsel` AS `tussenvoegsel`,`usr`.`achternaam` AS `achternaam` from (`users` `usr` left join `beginsaldo` `beg` on((`usr`.`username` = `beg`.`username`)))) */;

/*View structure for view view_verlofuren */

/*!50001 DROP TABLE IF EXISTS `view_verlofuren` */;
/*!50001 DROP VIEW IF EXISTS `view_verlofuren` */;

/*!50001 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_verlofuren` AS (select `uren`.`ID` AS `ID`,year(`uren`.`datum`) AS `approval_jaar`,month(`uren`.`datum`) AS `approval_maand`,dayofmonth(`uren`.`datum`) AS `approval_dag`,`uren`.`uren` AS `uren`,`uren`.`approved` AS `approved`,`usr`.`username` AS `username`,`beg`.`beginsaldo` AS `beginsaldo`,concat(`usr`.`achternaam`,', ',`usr`.`voornaam`,' ',`usr`.`tussenvoegsel`) AS `fullname` from ((`users` `usr` left join `uren` on(((`usr`.`username` = `uren`.`user`) and (`uren`.`soortuur` = 'MIR001VL')))) left join `beginsaldo` `beg` on(((`usr`.`username` = `beg`.`username`) and (`beg`.`jaar` = year(`uren`.`datum`)))))) */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
