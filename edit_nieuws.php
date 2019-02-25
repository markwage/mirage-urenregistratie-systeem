<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");
if (isset($_GET['aktie'])) {
    $aktie = $_GET['aktie'];
}
else {
    $aktie = "";
}

// Controleren of gebruiker admin-rechten heeft
check_admin();

// Connectie met de database maken en database selecteren
$dbconn = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

?>
<div id="main">		
	<h1>Onderhoud nieuwsartikelen</h1>
			
<?php 
//This code runs if the form has been submitted
