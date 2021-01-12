<?php

include ("config.php");
echo $_SERVER['SERVER_NAME'];
// --------------------------------------------------------
// Instellingen voor localhost of onedrivehost
// --------------------------------------------------------
if (($_SERVER['SERVER_NAME'] == 'localhost') || ($_SERVER['SERVER_NAME'] == 'onedrivehost') || ($_SERVER['SERVER_NAME'] == 'mus')) {
    $mysqli = new mysqli("localhost", "root", "", "mus");
}

// --------------------------------------------------------
// Instellingen voor remote - kermistriathlon
// --------------------------------------------------------
if ($_SERVER['SERVER_NAME'] == 'mus.kermistriathlonbeusichem.nl') {
    $mysqli = new mysqli("mysql-c6.argewebhosting.nl", "kermistri", "g%nUCGVu", "kermistri");
}

// --------------------------------------------------------
// Instellingen voor remote - mirage-urenregistratie
// --------------------------------------------------------
if ($_SERVER['SERVER_NAME'] == 'www.mirage-urenregistratie.nl') {
    $mysqli = new mysqli("mysql-c3.mirage-urenregistratie.nl", "mirageure", "c@PKT8f3x_3A7QsF", "mirageure");
}

if($mysqli->connect_error || $mysqli->error) {
    exit($MSGDB001E);
}

$mysqli->set_charset("utf8mb4");

// Voor een betere error reporting
//$driver = new mysqli_driver();
//$driver->report_mode = MYSQLI_REPORT_STRICT;
// Plaats in de code dan na de try de volgende catch
// } catch (mysqli_sql_exception $e) {
?>
