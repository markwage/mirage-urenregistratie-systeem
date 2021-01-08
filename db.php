<?php

// Database instellingen
echo $_SERVER['SERVER_NAME'];
echo "=========================";
if (($_SERVER['SERVER_NAME'] == 'localhost') || ($_SERVER['SERVER_NAME'] == 'onedrivehost') || ($_SERVER['SERVER_NAME'] == 'mus')) {
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
if ($_SERVER['SERVER_NAME'] == 'www.mirage-urenregistratie.nl') {
    $mysqli = new mysqli("mysql-c3.mirage-urenregistratie.nl", "mirageure", "c@PKT8f3x_3A7QsF", "mirageure");
    $dbhost = "mysql-c3.mirage-urenregistratie.nl";
    $dbname = "mirageure";
    $dbuser = "mirageure";
    $dbpassw = "c@PKT8f3x_3A7QsF";
}

//global $dbconn;
$GLOBALS['dbconn'] = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);
if($GLOBALS['dbconn'] === false) {
    die("ERROR: Kan geen connectie met de database maken. " . mysqli_connect_error());
}
?>
