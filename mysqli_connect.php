<?php

include ("config.php");

// --------------------------------------------------------
// Instellingen voor localhost of onedrivehost
// --------------------------------------------------------
if (($_SERVER['SERVER_NAME'] == 'localhost') || ($_SERVER['SERVER_NAME'] == 'onedrivehost')) {
    $mysqli = new mysqli("localhost", "root", "", "mus");
}

// --------------------------------------------------------
// Instellingen voor remote - kermistriathlon
// --------------------------------------------------------
if ($_SERVER['SERVER_NAME'] == 'mus.kermistriathlonbeusichem.nl') {
    $mysqli = new mysqli("mysql-c6.argewebhosting.nl", "kermistri", "g%nUCGVu", "kermistri");
}

if($mysqli->connect_error || $mysqli->error) {
    exit($MSGDB001E);
}

$mysqli->set_charset("utf8mb4");

?>
