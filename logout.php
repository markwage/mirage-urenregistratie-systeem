<?php

// make the time in the past to destroy the cookies
$past = time() - 100;

setcookie('ID_mus', gone, $past);
setcookie('Key_mus', gone, $past);
header("location: login.php");
?>