<?php

// Database instellingen

$dbhost = "localhost";
$dbname = "mus";
$dbuser = "root";
$dbpassw = "";

$GLOBALS['dbconn'] = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname)
?>