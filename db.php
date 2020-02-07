<?php

// --------------------------------------------------------
// Instellingen voor localhost
// --------------------------------------------------------
$dbhost = "localhost";
$dbname = "mus";
$dbuser = "root";
$dbpassw = "";
// --------------------------------------------------------
// Instellingen voor remote
// --------------------------------------------------------
// $dbhost = "mysql-c6.argewebhosting.nl";
// $dbname = "kermistri";
// $dbuser = "kermistri";
// $dbpassw = "g%nUCGVu";

$GLOBALS['dbconn'] = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);
if ($GLOBALS['dbconn'] === false) {
    die("ERROR: Kan geen connectie met de database maken. " . mysqli_connect_error());
}
?>