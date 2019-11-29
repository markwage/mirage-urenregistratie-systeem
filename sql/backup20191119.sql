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

/*Table structure for table `nieuws` */

CREATE TABLE `nieuws` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `datum` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nieuwsheader` varchar(128) NOT NULL,
  `nieuwsbericht` mediumtext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Data for the table `nieuws` */

insert  into `nieuws`(`ID`,`datum`,`nieuwsheader`,`nieuwsbericht`) values 
(13,'2019-03-08 14:52:22','test nieuwsbericht','Dit is weer een test'),
(3,'2019-03-06 22:05:03','Dit is een test met het aanmaken van een nieuw nieuwsbericht','Dit is het werkelijke bericht wat best wel een beetje\r\nuitgebreid kan zijn en meerdere\r\nregels kan bevatten. '),
(12,'2019-03-08 10:27:54','Dit is de header van het artikel. Nu nog het bericht er bij zien te krijgen. Maar dit bericht zou heel lang moeten zijn. Ik typ ','Test.');

/*Table structure for table `soorturen` */

CREATE TABLE `soorturen` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(8) NOT NULL,
  `omschrijving` varchar(60) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `code` (`code`),
  KEY `Omschrijving` (`omschrijving`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

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
(15,'MIR020LL','Leegloop');

/*Table structure for table `uren` */

CREATE TABLE `uren` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `jaar` int(4) NOT NULL,
  `week` int(2) unsigned NOT NULL,
  `dagnummer` tinyint(1) NOT NULL,
  `soortuur` char(8) NOT NULL,
  `datum` date NOT NULL,
  `uren` decimal(5,2) NOT NULL,
  `user` varchar(60) NOT NULL,
  `terapprovalaangeboden` tinyint(1) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `approveddatum` date DEFAULT NULL,
  `approvedbyuser` varchar(60) DEFAULT NULL,
  UNIQUE KEY `ID` (`ID`),
  KEY `datum` (`user`,`approvedbyuser`),
  KEY `soortuur` (`soortuur`),
  KEY `weeknummer` (`week`)
) ENGINE=MyISAM AUTO_INCREMENT=784 DEFAULT CHARSET=utf8;

/*Data for the table `uren` */

insert  into `uren`(`ID`,`jaar`,`week`,`dagnummer`,`soortuur`,`datum`,`uren`,`user`,`terapprovalaangeboden`,`approved`,`approveddatum`,`approvedbyuser`) values 
(752,2019,16,6,'MIR200OV','2019-04-21',3.00,'mwage',1,0,NULL,NULL),
(734,2019,12,5,'MIR200OV','2019-03-23',3.00,'mwage',1,0,NULL,NULL),
(733,2019,12,3,'MIR150OV','2019-03-21',8.00,'mwage',1,0,NULL,NULL),
(732,2019,12,1,'MIR150OV','2019-03-19',8.00,'mwage',1,0,NULL,NULL),
(731,2019,12,4,'MIR120OV','2019-03-22',2.00,'mwage',1,0,NULL,NULL),
(730,2019,12,3,'MIR120OV','2019-03-21',2.00,'mwage',1,0,NULL,NULL),
(552,2019,11,3,'MIR100OV','2019-03-14',2.00,'mwage',1,0,NULL,NULL),
(551,2019,11,1,'MIR100OV','2019-03-12',2.00,'mwage',1,0,NULL,NULL),
(550,2019,11,4,'MIR000UU','2019-03-15',8.00,'mwage',1,0,NULL,NULL),
(549,2019,11,3,'MIR000UU','2019-03-14',8.00,'mwage',1,0,NULL,NULL),
(548,2019,11,2,'MIR000UU','2019-03-13',8.00,'mwage',1,0,NULL,NULL),
(547,2019,11,1,'MIR000UU','2019-03-12',8.00,'mwage',1,0,NULL,NULL),
(546,2019,11,0,'MIR000UU','2019-03-11',8.00,'mwage',1,0,NULL,NULL),
(557,2019,10,2,'MIR001VL','2019-03-06',8.00,'mwage',1,1,'2019-05-06','mwage'),
(556,2019,10,4,'MIR000UU','2019-03-08',8.00,'mwage',1,1,'2019-05-06','mwage'),
(555,2019,10,3,'MIR000UU','2019-03-07',8.00,'mwage',1,1,'2019-05-06','mwage'),
(554,2019,10,1,'MIR000UU','2019-03-05',8.00,'mwage',1,1,'2019-05-06','mwage'),
(748,2019,16,4,'MIR000UU','2019-04-19',16.00,'mwage',1,0,NULL,NULL),
(553,2019,10,0,'MIR000UU','2019-03-04',8.00,'mwage',1,1,'2019-05-06','mwage'),
(751,2019,16,5,'MIR200OV','2019-04-20',3.00,'mwage',1,0,NULL,NULL),
(750,2019,16,6,'MIR000UU','2019-04-21',16.00,'mwage',1,0,NULL,NULL),
(749,2019,16,5,'MIR000UU','2019-04-20',16.00,'mwage',1,0,NULL,NULL),
(747,2019,16,3,'MIR000UU','2019-04-18',16.00,'mwage',1,0,NULL,NULL),
(729,2019,12,2,'MIR120OV','2019-03-20',2.00,'mwage',1,0,NULL,NULL),
(743,2019,14,6,'MIR150OV','2019-04-07',9.00,'mwage',1,0,NULL,NULL),
(742,2019,14,5,'MIR150OV','2019-04-06',9.00,'mwage',1,0,NULL,NULL),
(741,2019,14,4,'MIR150OV','2019-04-05',3.50,'mwage',1,0,NULL,NULL),
(610,2019,13,4,'MIR001VL','2019-03-29',8.00,'mwage',0,0,NULL,NULL),
(740,2019,14,3,'MIR150OV','2019-04-04',8.00,'mwage',1,0,NULL,NULL),
(609,2019,13,3,'MIR001VL','2019-03-28',8.00,'mwage',0,0,NULL,NULL),
(608,2019,13,2,'MIR001VL','2019-03-27',8.00,'mwage',0,0,NULL,NULL),
(728,2019,12,1,'MIR100OV','2019-03-19',3.00,'mwage',1,0,NULL,NULL),
(727,2019,12,0,'MIR100OV','2019-03-18',2.00,'mwage',1,0,NULL,NULL),
(726,2019,12,3,'MIR030IN','2019-03-21',5.00,'mwage',1,0,NULL,NULL),
(725,2019,12,2,'MIR030IN','2019-03-20',2.00,'mwage',1,0,NULL,NULL),
(724,2019,12,1,'MIR030IN','2019-03-19',3.00,'mwage',1,0,NULL,NULL),
(607,2019,13,1,'MIR001VL','2019-03-26',8.00,'mwage',0,0,NULL,NULL),
(606,2019,13,0,'MIR001VL','2019-03-25',6.00,'mwage',0,0,NULL,NULL),
(739,2019,14,1,'MIR150OV','2019-04-02',8.00,'mwage',1,0,NULL,NULL),
(738,2019,14,3,'MIR000UU','2019-04-04',3.50,'mwage',1,0,NULL,NULL),
(736,2019,14,0,'MIR000UU','2019-04-01',8.00,'mwage',1,0,NULL,NULL),
(737,2019,14,2,'MIR000UU','2019-04-03',8.00,'mwage',1,0,NULL,NULL),
(746,2019,16,2,'MIR000UU','2019-04-17',16.00,'mwage',1,0,NULL,NULL),
(745,2019,16,1,'MIR000UU','2019-04-16',16.00,'mwage',1,0,NULL,NULL),
(744,2019,16,0,'MIR000UU','2019-04-15',16.00,'mwage',1,0,NULL,NULL),
(685,2019,19,0,'MIR000UU','2019-05-06',8.00,'mwage',0,0,NULL,NULL),
(686,2019,19,1,'MIR000UU','2019-05-07',8.00,'mwage',0,0,NULL,NULL),
(687,2019,19,2,'MIR000UU','2019-05-08',8.00,'mwage',0,0,NULL,NULL),
(688,2019,19,3,'MIR000UU','2019-05-09',8.00,'mwage',0,0,NULL,NULL),
(689,2019,19,4,'MIR000UU','2019-05-10',8.00,'mwage',0,0,NULL,NULL),
(723,2019,12,0,'MIR030IN','2019-03-18',5.00,'mwage',1,0,NULL,NULL),
(722,2019,12,4,'MIR000UU','2019-03-22',8.00,'mwage',1,0,NULL,NULL),
(721,2019,12,2,'MIR000UU','2019-03-20',8.00,'mwage',1,0,NULL,NULL),
(720,2019,12,0,'MIR000UU','2019-03-18',8.00,'mwage',1,0,NULL,NULL),
(735,2019,12,6,'MIR200OV','2019-03-24',3.00,'mwage',1,0,NULL,NULL),
(754,2019,25,0,'MIR000UU','2019-06-17',8.00,'mwage',0,0,NULL,NULL),
(755,2019,25,1,'MIR000UU','2019-06-18',8.00,'mwage',0,0,NULL,NULL),
(756,2019,25,2,'MIR000UU','2019-06-19',8.00,'mwage',0,0,NULL,NULL),
(757,2019,25,3,'MIR000UU','2019-06-20',8.00,'mwage',0,0,NULL,NULL),
(758,2019,25,4,'MIR000UU','2019-06-21',8.00,'mwage',0,0,NULL,NULL),
(770,2019,25,4,'MIR003BV','2019-06-21',8.00,'demo',0,0,NULL,NULL),
(769,2019,25,2,'MIR001VL','2019-06-19',4.00,'demo',0,0,NULL,NULL),
(768,2019,25,3,'MIR000UU','2019-06-20',8.00,'demo',0,0,NULL,NULL),
(767,2019,25,2,'MIR000UU','2019-06-19',4.00,'demo',0,0,NULL,NULL),
(766,2019,25,1,'MIR000UU','2019-06-18',8.00,'demo',0,0,NULL,NULL),
(765,2019,25,0,'MIR000UU','2019-06-17',8.00,'demo',0,0,NULL,NULL),
(781,2019,31,2,'MIR001VL','2019-07-31',8.00,'mwage',0,0,NULL,NULL),
(780,2019,31,1,'MIR001VL','2019-07-30',8.00,'mwage',0,0,NULL,NULL),
(779,2019,31,0,'MIR001VL','2019-07-29',8.00,'mwage',0,0,NULL,NULL),
(774,2019,30,0,'MIR000UU','2019-07-22',8.00,'mwage',0,0,NULL,NULL),
(775,2019,30,1,'MIR000UU','2019-07-23',8.00,'mwage',0,0,NULL,NULL),
(776,2019,30,2,'MIR000UU','2019-07-24',8.00,'mwage',0,0,NULL,NULL),
(777,2019,30,3,'MIR000UU','2019-07-25',8.00,'mwage',0,0,NULL,NULL),
(778,2019,30,4,'MIR000UU','2019-07-26',8.00,'mwage',0,0,NULL,NULL),
(782,2019,31,3,'MIR001VL','2019-08-01',8.00,'mwage',0,0,NULL,NULL),
(783,2019,31,4,'MIR001VL','2019-08-02',8.00,'mwage',0,0,NULL,NULL);

/*Table structure for table `users` */

CREATE TABLE `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `password` varchar(60) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
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
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`ID`,`username`,`password`,`admin`,`voornaam`,`tussenvoegsel`,`achternaam`,`emailadres`,`indienst`,`lastloggedin`,`approvenallowed`) values 
(12,'admin','21232f297a57a5a743894a0e4a801fc3',1,'admini','','strator','mark.wage@hotmail.com',0,'2019-07-12 02:00:19',0),
(19,'mwage','4c19e0f15b05949db9f467fef57b2e63',1,'Mark','','Wage','mwage@mirage.nl',1,'2019-11-14 07:59:01',1),
(44,'fbosman','d41d8cd98f00b204e9800998ecf8427e',1,'Frank','','Bosman','fbosman@mirage.nl',1,'2018-04-07 12:04:58',0),
(59,'demo3','297e430d45e7bf6f65f5dc929d6b072b',1,'Demo','in','Demootje','demo@demo.com',0,'2019-03-06 14:37:23',0),
(46,'apaters','d41d8cd98f00b204e9800998ecf8427e',0,'Arjan','','Paters','apaters@mirage.nl',1,'2018-04-07 12:04:58',0),
(43,'abredze','d41d8cd98f00b204e9800998ecf8427e',1,'Arjan','de','Bredze','abredze@mirage.nl',1,'2018-04-07 12:04:58',1),
(58,'demo2','1066726e7160bd9c987c9968e0cc275a',0,'Demo','van het','Demo2','email@email.com',1,'2019-03-06 09:08:11',0),
(60,'rlans','4c19e0f15b05949db9f467fef57b2e63',1,'Robert','van der','Lans','robert.vander.lans@mirage.nl',1,'2019-05-01 15:47:21',1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
