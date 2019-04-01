<?php

// Database instellingen

$dbhost = "localhost";
$dbname = "mus";
$dbuser = "root";
$dbpassw = "";

//global $dbconn;
$GLOBALS['dbconn'] = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);
if($GLOBALS['dbconn'] === false) {
    die("ERROR: Kan geen connectie met de database maken. " . mysqli_connect_error());
}
?>