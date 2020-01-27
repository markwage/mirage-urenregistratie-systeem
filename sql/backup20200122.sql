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
CREATE DATABASE /*!32312 IF NOT EXISTS*/`mus` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `mus`;

/*Table structure for table `approvals` */

CREATE TABLE `approvals` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `maand` int(2) DEFAULT NULL,
  `jaar` int(4) DEFAULT NULL,
  `user` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `datum` (`maand`,`jaar`),
  KEY `user` (`user`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `approvals` */

insert  into `approvals`(`ID`,`maand`,`jaar`,`user`) values 
(1,11,2019,'mwage'),
(5,12,2019,'mwage'),
(4,12,2019,'demo');

/*Table structure for table `nieuws` */

CREATE TABLE `nieuws` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `datum` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nieuwsheader` varchar(128) NOT NULL,
  `nieuwsbericht` mediumtext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

/*Data for the table `nieuws` */

insert  into `nieuws`(`ID`,`datum`,`nieuwsheader`,`nieuwsbericht`) values 
(12,'2019-03-08 10:27:54','Dit is de header van het artikel. Nu nog het bericht er bij zien te krijgen. Maar dit bericht zou heel lang moeten zijn. Ik typ ','Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui.');

/*Table structure for table `soorturen` */

CREATE TABLE `soorturen` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(8) NOT NULL,
  `omschrijving` varchar(60) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `code` (`code`),
  KEY `Omschrijving` (`omschrijving`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

/*Data for the table `soorturen` */

insert  into `soorturen`(`ID`,`code`,`omschrijving`) values 
(1,'MIR000UU','Gewerkte uren'),
(2,'MIR001VL','Opgenomen verlofuren'),
(3,'MIR003BV','Bijzonder verlof'),
(4,'MIR003FD','Erkende feestdag'),
(5,'MIR010CU','Cursus'),
(6,'MIR030IN','Intern'),
(7,'MIR040BA','Bezoek arts'),
(8,'MIR043ZK','Ziek'),
(9,'MIR100OV','Overwerk tegen 100%'),
(10,'MIR115OV','Overwerk tegen 115%'),
(11,'MIR120OV','Overwerk tegen 120%'),
(12,'MIR125OV','Overwerk tegen 125%'),
(13,'MIR150OV','Overwerk tegen 150%'),
(14,'MIR200OV','Overwerk tegen 200%'),
(15,'MIR020LL','Leegloop'),
(38,'RABSBYHG','Rabo Standby Hoog'),
(39,'RABSBYLG','Rabo Standby Laag');

/*Table structure for table `uren` */

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
  KEY `weeknummer` (`week`)
) ENGINE=MyISAM AUTO_INCREMENT=1382 DEFAULT CHARSET=utf8;

/*Data for the table `uren` */

insert  into `uren`(`ID`,`jaar`,`maand`,`week`,`dagnummer`,`soortuur`,`datum`,`uren`,`user`,`approved`,`approveddatum`,`approvedbyuser`) values 
(1331,2020,12,1,1,'MIR001VL','2019-12-31',8.00,'demo',1,'2020-01-20','mwage'),
(1330,2020,12,1,0,'MIR001VL','2019-12-30',8.00,'demo',1,'2020-01-20','mwage'),
(1329,2019,12,52,3,'MIR003FD','2019-12-26',8.00,'demo',1,'2020-01-20','mwage'),
(1328,2019,12,52,2,'MIR003FD','2019-12-25',8.00,'demo',1,'2020-01-20','mwage'),
(1327,2019,12,52,4,'MIR000UU','2019-12-27',8.00,'demo',1,'2020-01-20','mwage'),
(1326,2019,12,52,1,'MIR000UU','2019-12-24',8.00,'demo',1,'2020-01-20','mwage'),
(1325,2019,12,52,0,'MIR000UU','2019-12-23',8.00,'demo',1,'2020-01-20','mwage'),
(1324,2019,12,51,4,'MIR000UU','2019-12-20',8.00,'demo',1,'2020-01-20','mwage'),
(1323,2019,12,51,3,'MIR000UU','2019-12-19',8.00,'demo',1,'2020-01-20','mwage'),
(1322,2019,12,51,2,'MIR000UU','2019-12-18',8.00,'demo',1,'2020-01-20','mwage'),
(1321,2019,12,51,1,'MIR000UU','2019-12-17',8.00,'demo',1,'2020-01-20','mwage'),
(1320,2019,12,51,0,'MIR000UU','2019-12-16',8.00,'demo',1,'2020-01-20','mwage'),
(1319,2019,12,50,4,'MIR000UU','2019-12-13',8.00,'demo',1,'2020-01-20','mwage'),
(1318,2019,12,50,3,'MIR000UU','2019-12-12',8.00,'demo',1,'2020-01-20','mwage'),
(1317,2019,12,50,2,'MIR000UU','2019-12-11',8.00,'demo',1,'2020-01-20','mwage'),
(1316,2019,12,50,1,'MIR000UU','2019-12-10',8.00,'demo',1,'2020-01-20','mwage'),
(1131,2019,11,45,2,'MIR001VL','2019-11-06',8.00,'mwage',1,'2020-01-10','mwage'),
(1132,2019,11,46,0,'MIR000UU','2019-11-11',8.00,'mwage',1,'2020-01-10','mwage'),
(1133,2019,11,46,1,'MIR000UU','2019-11-12',8.00,'mwage',1,'2020-01-10','mwage'),
(1134,2019,11,46,2,'MIR000UU','2019-11-13',8.00,'mwage',1,'2020-01-10','mwage'),
(1129,2019,11,45,3,'MIR000UU','2019-11-07',8.00,'mwage',1,'2020-01-10','mwage'),
(1128,2019,11,45,1,'MIR000UU','2019-11-05',8.00,'mwage',1,'2020-01-10','mwage'),
(1127,2019,11,45,0,'MIR000UU','2019-11-04',8.00,'mwage',1,'2020-01-10','mwage'),
(1381,2020,1,4,4,'MIR020LL','2020-01-24',8.00,'mwage',0,NULL,NULL),
(1380,2020,1,4,3,'MIR020LL','2020-01-23',8.00,'mwage',0,NULL,NULL),
(1379,2020,1,4,2,'MIR020LL','2020-01-22',8.00,'mwage',0,NULL,NULL),
(1374,2019,12,48,6,'MIR200OV','2019-12-01',3.00,'mwage',1,'2020-01-22','mwage'),
(1378,2020,1,4,1,'MIR020LL','2020-01-21',8.00,'mwage',0,NULL,NULL),
(1293,2020,1,3,4,'MIR020LL','2020-01-17',8.00,'mwage',0,NULL,NULL),
(1292,2020,1,3,3,'MIR020LL','2020-01-16',8.00,'mwage',0,NULL,NULL),
(1349,2020,1,1,4,'MIR020LL','2020-01-03',8.00,'mwage',0,NULL,NULL),
(1348,2020,1,1,3,'MIR020LL','2020-01-02',8.00,'mwage',0,NULL,NULL),
(1283,2019,12,52,4,'MIR020LL','2019-12-27',8.00,'mwage',1,'2020-01-22','mwage'),
(1282,2019,12,52,3,'MIR003FD','2019-12-26',8.00,'mwage',1,'2020-01-22','mwage'),
(1281,2019,12,52,2,'MIR003FD','2019-12-25',8.00,'mwage',1,'2020-01-22','mwage'),
(1280,2019,12,52,1,'MIR001VL','2019-12-24',8.00,'mwage',1,'2020-01-22','mwage'),
(1279,2019,12,52,0,'MIR001VL','2019-12-23',8.00,'mwage',1,'2020-01-22','mwage'),
(1278,2019,12,51,3,'MIR001VL','2019-12-19',4.00,'mwage',1,'2020-01-22','mwage'),
(1277,2019,12,51,4,'MIR000UU','2019-12-20',8.00,'mwage',1,'2020-01-22','mwage'),
(1276,2019,12,51,3,'MIR000UU','2019-12-19',4.00,'mwage',1,'2020-01-22','mwage'),
(1275,2019,12,51,2,'MIR000UU','2019-12-18',8.00,'mwage',1,'2020-01-22','mwage'),
(1274,2019,12,51,1,'MIR000UU','2019-12-17',8.00,'mwage',1,'2020-01-22','mwage'),
(1273,2019,12,51,0,'MIR000UU','2019-12-16',8.00,'mwage',1,'2020-01-22','mwage'),
(1343,2020,1,2,1,'MIR001VL','2020-01-07',8.00,'mwage',0,NULL,NULL),
(1342,2020,1,2,2,'MIR000UU','2020-01-08',8.00,'mwage',0,NULL,NULL),
(1341,2020,1,2,0,'MIR000UU','2020-01-06',8.00,'mwage',0,NULL,NULL),
(1345,2020,1,1,2,'MIR003FD','2020-01-01',8.00,'mwage',0,NULL,NULL),
(1272,2019,12,50,4,'MIR000UU','2019-12-13',8.00,'mwage',1,'2020-01-22','mwage'),
(1269,2019,12,50,1,'MIR000UU','2019-12-10',8.00,'mwage',1,'2020-01-22','mwage'),
(1270,2019,12,50,2,'MIR000UU','2019-12-11',8.00,'mwage',1,'2020-01-22','mwage'),
(1271,2019,12,50,3,'MIR000UU','2019-12-12',8.00,'mwage',1,'2020-01-22','mwage'),
(1268,2019,12,50,0,'MIR000UU','2019-12-09',8.00,'mwage',1,'2020-01-22','mwage'),
(1267,2019,12,49,4,'MIR000UU','2019-12-06',8.00,'mwage',1,'2020-01-22','mwage'),
(1291,2020,1,3,2,'MIR003FD','2020-01-15',8.00,'mwage',0,NULL,NULL),
(1290,2020,1,3,1,'MIR001VL','2020-01-14',8.00,'mwage',0,NULL,NULL),
(1289,2020,1,3,0,'MIR000UU','2020-01-13',8.00,'mwage',0,NULL,NULL),
(1266,2019,12,49,3,'MIR000UU','2019-12-05',8.00,'mwage',1,'2020-01-22','mwage'),
(1263,2019,12,49,0,'MIR000UU','2019-12-02',8.00,'mwage',1,'2020-01-22','mwage'),
(1264,2019,12,49,1,'MIR000UU','2019-12-03',8.00,'mwage',1,'2020-01-22','mwage'),
(1265,2019,12,49,2,'MIR000UU','2019-12-04',8.00,'mwage',1,'2020-01-22','mwage'),
(1130,2019,11,45,4,'MIR000UU','2019-11-08',8.00,'mwage',1,'2020-01-10','mwage'),
(1315,2019,12,50,0,'MIR000UU','2019-12-09',8.00,'demo',1,'2020-01-20','mwage'),
(1314,2019,12,49,4,'MIR000UU','2019-12-06',8.00,'demo',1,'2020-01-20','mwage'),
(1313,2019,12,49,3,'MIR000UU','2019-12-05',8.00,'demo',1,'2020-01-20','mwage'),
(1312,2019,12,49,2,'MIR000UU','2019-12-04',8.00,'demo',1,'2020-01-20','mwage'),
(1311,2019,12,49,1,'MIR000UU','2019-12-03',8.00,'demo',1,'2020-01-20','mwage'),
(1310,2019,12,49,0,'MIR000UU','2019-12-02',8.00,'demo',1,'2020-01-20','mwage'),
(1309,2020,1,3,4,'MIR043ZK','2020-01-17',8.00,'demo',0,NULL,NULL),
(1308,2020,1,3,3,'MIR043ZK','2020-01-16',8.00,'demo',0,NULL,NULL),
(1307,2020,1,3,2,'MIR043ZK','2020-01-15',8.00,'demo',0,NULL,NULL),
(1306,2020,1,3,1,'MIR043ZK','2020-01-14',8.00,'demo',0,NULL,NULL),
(1305,2020,1,3,0,'MIR020LL','2020-01-13',8.00,'demo',0,NULL,NULL),
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
(1347,2020,12,1,1,'MIR020LL','2019-12-31',8.00,'mwage',1,'2020-01-22','mwage'),
(1346,2020,12,1,0,'MIR020LL','2019-12-30',8.00,'mwage',1,'2020-01-22','mwage'),
(1375,2019,12,48,6,'RABSBYLG','2019-12-01',6.00,'mwage',1,'2020-01-22','mwage'),
(1333,2020,1,1,3,'MIR001VL','2020-01-02',8.00,'demo',0,NULL,NULL),
(1334,2020,1,1,4,'MIR001VL','2020-01-03',8.00,'demo',0,NULL,NULL),
(1335,2020,1,1,2,'MIR003FD','2020-01-01',8.00,'demo',0,NULL,NULL),
(1336,2020,1,2,0,'MIR000UU','2020-01-06',8.00,'demo',0,NULL,NULL),
(1337,2020,1,2,1,'MIR000UU','2020-01-07',8.00,'demo',0,NULL,NULL),
(1338,2020,1,2,2,'MIR000UU','2020-01-08',8.00,'demo',0,NULL,NULL),
(1339,2020,1,2,3,'MIR000UU','2020-01-09',8.00,'demo',0,NULL,NULL),
(1340,2020,1,2,4,'MIR000UU','2020-01-10',8.00,'demo',0,NULL,NULL),
(1344,2020,1,2,3,'MIR003BV','2020-01-09',8.00,'mwage',0,NULL,NULL),
(1350,2020,1,1,5,'MIR150OV','2020-01-04',5.00,'mwage',0,NULL,NULL),
(1351,2020,1,1,6,'MIR150OV','2020-01-05',5.00,'mwage',0,NULL,NULL),
(1373,2019,12,48,6,'MIR115OV','2019-12-01',5.00,'mwage',1,'2020-01-22','mwage'),
(1377,2020,1,4,0,'MIR020LL','2020-01-20',8.00,'mwage',0,NULL,NULL),
(1376,2019,12,48,6,'RABSBYHG','2019-12-01',14.00,'mwage',1,'2020-01-22','mwage');

/*Table structure for table `users` */

CREATE TABLE `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `password` varchar(60) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `uren_invullen` tinyint(1) NOT NULL DEFAULT '1',
  `voornaam` varchar(24) NOT NULL,
  `tussenvoegsel` varchar(10) NOT NULL,
  `achternaam` varchar(60) NOT NULL,
  `emailadres` varchar(60) NOT NULL,
  `indienst` tinyint(1) NOT NULL DEFAULT '0',
  `lastloggedin` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approvenallowed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `username` (`username`),
  KEY `achternaam` (`achternaam`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`ID`,`username`,`password`,`admin`,`uren_invullen`,`voornaam`,`tussenvoegsel`,`achternaam`,`emailadres`,`indienst`,`lastloggedin`,`approvenallowed`) values 
(19,'mwage','4c19e0f15b05949db9f467fef57b2e63',1,1,'Mark','','Wage','mark.wage@mirage.nl',1,'2020-01-22 17:51:44',1),
(44,'fbosman','d41d8cd98f00b204e9800998ecf8427e',1,1,'Frank','','Bosman','fbosman@mirage.nl',1,'2018-04-07 12:04:58',1),
(46,'apaters','d41d8cd98f00b204e9800998ecf8427e',0,0,'Arjan','','Paters','arjan.paters@mirage.nl',1,'2018-04-07 12:04:58',0),
(43,'abredze','d7598d13a5a968add88f27dd3f6aa457',1,0,'Arjan','de','Bredze','abredze@mirage.nl',1,'2018-04-07 12:04:58',1),
(58,'demo2','1066726e7160bd9c987c9968e0cc275a',0,1,'Demo','van het','Demo2','alleen.invoeren@email.com',1,'2020-01-20 22:42:02',0),
(60,'rlans','4c19e0f15b05949db9f467fef57b2e63',1,0,'Robert','van der','Lans','robert.vanderlans@mirage.nl',1,'2019-05-01 15:47:21',1),
(64,'demo','fe01ce2a7fbac8fafaed7c982a04e229',0,1,'Demo','van der','Demootje','niet.in.dienst@mirage.nl',0,'2020-01-20 21:38:46',0),
(66,'joosterom','7120c49703ba7fb5733b2aa930f12e6d',0,1,'Jeroen','van','Oosterom','jeroen.vanoosterom@mirage.nl',1,'2020-01-03 21:54:25',0);

/*Table structure for table `view_uren_get_full_username` */

DROP TABLE IF EXISTS `view_uren_get_full_username`;

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
 `omschrijving` varchar(60) 
)*/;

/*View structure for view view_uren_get_full_username */

/*!50001 DROP TABLE IF EXISTS `view_uren_get_full_username` */;
/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_uren_get_full_username` AS (select `uren`.`ID` AS `ID`,`uren`.`jaar` AS `jaar`,`uren`.`maand` AS `maand`,year(`uren`.`datum`) AS `approval_jaar`,month(`uren`.`datum`) AS `approval_maand`,`uren`.`week` AS `week`,`uren`.`dagnummer` AS `dagnummer`,`uren`.`soortuur` AS `soortuur`,`uren`.`datum` AS `datum`,`uren`.`uren` AS `uren`,`uren`.`approved` AS `approved`,`uren`.`approveddatum` AS `approveddatum`,`uren`.`approvedbyuser` AS `approvedbyuser`,`usr`.`username` AS `user`,`usr`.`voornaam` AS `voornaam`,`usr`.`tussenvoegsel` AS `tussenvoegsel`,`usr`.`achternaam` AS `achternaam`,`usr`.`uren_invullen` AS `uren_invullen` from (`users` `usr` left join `uren` on((`usr`.`username` = `uren`.`user`)))) */;

/*View structure for view view_uren_soortuur */

/*!50001 DROP TABLE IF EXISTS `view_uren_soortuur` */;
/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_uren_soortuur` AS (select `u`.`ID` AS `ID`,year(`u`.`datum`) AS `approval_jaar`,month(`u`.`datum`) AS `approval_maand`,`u`.`maand` AS `maand`,`u`.`jaar` AS `jaar`,`u`.`week` AS `week`,`u`.`dagnummer` AS `dagnummer`,`u`.`soortuur` AS `soortuur`,`u`.`datum` AS `datum`,`u`.`uren` AS `uren`,`u`.`user` AS `user`,`u`.`approved` AS `approved`,`u`.`approveddatum` AS `approveddatum`,`u`.`approvedbyuser` AS `approvedbyuser`,`s`.`omschrijving` AS `omschrijving` from (`uren` `u` left join `soorturen` `s` on((`u`.`soortuur` = `s`.`code`)))) */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
