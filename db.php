<?php

// Database instellingen

if (($_SERVER['SERVER_NAME'] == 'localhost') || ($_SERVER['SERVER_NAME'] == 'onedrivehost')) {
    $mysqli = new mysqli("localhost", "root", "", "mus");
    $dbhost = "localhost";
    $dbname = "mus";
    $dbuser = "root";
    $dbpassw = "";
}

// --------------------------------------------------------
// Instellingen voor remote - kermistriathlon
// --------------------------------------------------------
if ($_SERVER['SERVER_NAME'] == 'mus.kermistriathlonbeusichem.nl') {
    $dbhost = "mysql-c6.argewebhosting.nl";
    $dbname = "kermistri";
    $dbuser = "kermistri";
    $dbpassw = "g%nUCGVu";
}

// --------------------------------------------------------
// Instellingen voor remote - tvdebongerd
// --------------------------------------------------------
if ($_SERVER['SERVER_NAME'] == 'www.tvdebongerd.nl') {
    $mysqli = new mysqli("localhost", "mwage", "utreg01", "wp_prod");
    $dbhost = "localhost";
    $dbname = "wp_prod";
    $dbuser = "mwage";
    $dbpassw = "utreg01";
}

//global $dbconn;
$GLOBALS['dbconn'] = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);
if($GLOBALS['dbconn'] === false) {
    die("ERROR: Kan geen connectie met de database maken. " . mysqli_connect_error());
}
?>
