-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 07 Apr 2011 om 15:12
-- Serverversie: 5.5.8
-- PHP-Versie: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mus`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `soorturen`
--

DROP TABLE IF EXISTS `soorturen`;
CREATE TABLE IF NOT EXISTS `soorturen` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(8) NOT NULL,
  `omschrijving` varchar(60) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Omschrijving` (`omschrijving`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Gegevens worden uitgevoerd voor tabel `soorturen`
--

INSERT INTO `soorturen` (`ID`, `code`, `omschrijving`) VALUES
(1, 'MIR000UU', 'Gewerkte uren'),
(2, 'MIR001VL', 'Opgenomen verlofuren'),
(3, 'MIR003BV', 'Bijzonder verlof'),
(4, 'MIR003FD', 'Erkende feestdag'),
(5, 'MIR010CU', 'Cursus'),
(6, 'MIR030IN', 'Intern'),
(7, 'MIR040BA', 'Bezoek arts'),
(8, 'MIR043ZK', 'Ziek'),
(9, 'MIR100OV', 'Overwerk tegen 100%'),
(10, 'MIR115OV', 'Overwerk tegen 115%'),
(11, 'MIR120OV', 'Overwerk tegen 120%'),
(12, 'MIR125OV', 'Overwerk tegen 125%'),
(13, 'MIR150OV', 'Overwerk tegen 150%'),
(14, 'MIR200OV', 'Overwerk tegen 200%'),
(15, 'MIR020Ll', 'Leegloop'),
(18, 'MIR999XX', 'Testcode voor soorten uren');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `uren`
--

DROP TABLE IF EXISTS `uren`;
CREATE TABLE IF NOT EXISTS `uren` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `weeknummer` int(2) unsigned NOT NULL,
  `soortuur` int(11) NOT NULL,
  `uren` time NOT NULL,
  `userID` int(11) NOT NULL,
  `terapprovalaangeboden` int(1) NOT NULL,
  `approved` int(1) NOT NULL,
  `approveddatum` date NOT NULL,
  `approvedbyID` int(11) NOT NULL,
  UNIQUE KEY `ID` (`ID`),
  KEY `datum` (`datum`,`userID`,`approvedbyID`),
  KEY `soortuur` (`soortuur`),
  KEY `weeknummer` (`weeknummer`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Gegevens worden uitgevoerd voor tabel `uren`
--

INSERT INTO `uren` (`ID`, `datum`, `weeknummer`, `soortuur`, `uren`, `userID`, `terapprovalaangeboden`, `approved`, `approveddatum`, `approvedbyID`) VALUES
(1, '2011-04-04', 14, 1, '08:00:00', 19, 0, 0, '0000-00-00', 0),
(2, '2011-03-28', 13, 1, '08:00:00', 19, 1, 0, '0000-00-00', 0),
(3, '2011-03-29', 13, 1, '08:00:00', 19, 1, 0, '0000-00-00', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `password` varchar(60) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `voornaam` varchar(24) NOT NULL,
  `tussenvoegsel` varchar(10) NOT NULL,
  `achternaam` varchar(60) NOT NULL,
  `emailadres` varchar(60) NOT NULL,
  `indienst` tinyint(1) NOT NULL DEFAULT '0',
  `lastloggedin` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `username` (`username`),
  KEY `achternaam` (`achternaam`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

--
-- Gegevens worden uitgevoerd voor tabel `users`
--

INSERT INTO `users` (`ID`, `username`, `password`, `admin`, `voornaam`, `tussenvoegsel`, `achternaam`, `emailadres`, `indienst`, `lastloggedin`) VALUES
(12, 'admin', 'd41d8cd98f00b204e9800998ecf8427e', 1, 'admini', '', 'strator', 'mark.wage@hotmail.com', 0, '0000-00-00 00:00:00'),
(19, 'mwage', '4c19e0f15b05949db9f467fef57b2e63', 1, 'Mark', '', 'Wage', 'mark.wage@hotmail.com', 1, '2011-04-07 12:04:58'),
(44, 'fbosman', 'd41d8cd98f00b204e9800998ecf8427e', 1, 'Frank', '', 'Bosman', 'fbosman@mirage.nl', 1, '0000-00-00 00:00:00'),
(45, 'escheffelaar', '73cb01ab94fd0f5475c6fa0a38b6f63d', 0, 'Erik', '', 'Scheffelaar', 'escheffelaar@mirage.nl', 1, '0000-00-00 00:00:00'),
(46, 'apaters', '44e32b0ff9a6af0108a1a06741b96d25', 0, 'Arjan', '', 'Paters', 'apaters@mirage.nl', 1, '2011-04-07 12:04:35'),
(43, 'abredze', 'd41d8cd98f00b204e9800998ecf8427e', 1, 'Arjan', '', 'Bredze', 'abredze@mirage.nl', 1, '0000-00-00 00:00:00');
