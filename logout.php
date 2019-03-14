<?php

session_start();

include ("./config.php");
include ("./db.php");
include ("./function.php");

// make the time in the past to destroy the cookies
$past = time() - 100;

setcookie('ID_mus', gone, $past);
setcookie('Key_mus', gone, $past);
writeLogRecord("logout","User is succesvol uitgelogd");
header("location: login.php");
?>