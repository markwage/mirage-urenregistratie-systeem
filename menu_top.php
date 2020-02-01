<?php 
//include ("function.php");

echo '<ul>';
echo '<li><a href="index.php">Home</a></li>';

if (!isset($_SESSION['admin'])) 
{
	echo '<li><a href="login.php">Login</a></li>';
}

// Indien ingelogd is
if (isset($_COOKIE['ID_mus'])) 
{
	echo '<li><a href="logout.php">Uitloggen</a></li>';
	//echo ">>> ".$username_encrypted;
	//echo '<li><a href="users.php?aktie=editprof&edtuser='.$_SESSION["username"].'">Profiel</a></li>';
	echo '<li><a href="users.php?aktie=editprof&edtuser='.$_SESSION["username_encrypted"].'">Profiel</a></li>';
	echo "<li><a href='nieuws.php?aktie=disp'>Nieuws</a></li>";
	//echo '<li><a href="contact.php">Contact</a></li>';
}

echo '</ul>';

?>
