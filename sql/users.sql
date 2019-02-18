-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 16 Mar 2011 om 20:14
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
  PRIMARY KEY (`ID`),
  UNIQUE KEY `username` (`username`),
  KEY `achternaam` (`achternaam`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Gegevens worden uitgevoerd voor tabel `users`
--

INSERT INTO `users` (`ID`, `username`, `password`, `admin`, `voornaam`, `tussenvoegsel`, `achternaam`, `emailadres`, `indienst`) VALUES
(12, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, '', '', '', '', 0),
(11, 'demo', 'fe01ce2a7fbac8fafaed7c982a04e229', 0, '', '', '', '', 0),
(19, 'mwage', '4c19e0f15b05949db9f467fef57b2e63', 1, 'Mark', '', 'Wage', 'mark.wage@hotmail.com', 1);
