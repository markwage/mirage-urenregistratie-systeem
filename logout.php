<?php

session_start();

include ("./config.php");
include ("./db.php");
include ("./function.php");
include ("autoload.php");

// make the time in the past to destroy the cookies
$past = time() - 100;

setcookie('ID_mus', gone, $past);
setcookie('Key_mus', gone, $past);

$log_record = new Writelog();
$log_record->progname = $_SERVER['PHP_SELF'];
$log_record->message_text  = 'User is succesvol uitgelogd';
$log_record->write_record();

//writeLogRecord("logout","User is succesvol uitgelogd");
header("location: login.php");
?>