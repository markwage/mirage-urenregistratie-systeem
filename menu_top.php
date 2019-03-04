<?php 

echo '<ul>';
echo '<li><a href="index.php">Home</a></li>';
if (!isset($_SESSION['admin'])) {
	echo '<li><a href="login.php">Login</a></li>';
}
// Indien ingelogd is
if (isset($_COOKIE['ID_mus'])) {
	echo '<li><a href="logout.php">Logout</a></li>';
	echo '<li><a href="edit_users.php?aktie=edit&edtuser='.$_SESSION["username"].'">Profiel</a></li>';
	echo "<li><a href='edit_nieuws.php?aktie=disp'>Nieuws</a></li>";

	// Indien de user admin-rechten heeft
	// if ($_SESSION['admin']) {
	// 	echo '<li><a href="add_user.php">Add user</a></li>';
	// }
}
echo '<li><a href="contact.php">Contact</a></li>';
echo '</ul>';

?>
