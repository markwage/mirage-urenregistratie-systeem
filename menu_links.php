<?php

if (isset($_COOKIE['ID_mus'])) {
	echo '<div class="sidebox">';
	echo '<h1>Welkom op MUS</h1>';
	echo "<p>Welkom <b>".$_SESSION['voornaam']."</b>.<br /> Je bent nu ingelogd op MUS, het Mirage Urenregistratie Systeem<br /></p>";	
	echo "<p>Je kunt nu je uren inzien en je urenverantwoording invulling";
	echo '</div>';
	
	// Menu voor updaten/onderhouden van de gewerkte uren
	echo '<div class="sidebox">';	
	echo '<h1 class="clear">Urenadministratie</h1>';
	echo '<ul class="sidemenu">';
	echo '<li><a href="uren.php?aktie=toevoegen">Uren</a></li>';
	echo '<li><a href="uren.php?aktie=disp">Vakantie-uren per kalenderjaar</a></li>';
	echo '<li><a href="rapportage.php">Overzicht periode</a></li>';
	echo '<li><a href="users.php?aktie=editprof&edtuser='.$_SESSION["username"].'">Mijn profiel</a></li>';
	echo '</ul>';	
	echo '</div>';	
	
	// menu alleen voor gebruiker met adminrechten
	if ($_SESSION['admin']) {
		echo '<div class="sidebox">';
		echo '<h1>Admin menu</h1>';
		echo '<ul class="sidemenu">';
		echo '<li><a href="approve.php?aktie=disp">Openstaande approvals</a></li>';
		echo '<li><a href="users.php?aktie=disp">Usermanagement</a></li>';
		echo '<li><a href="soorturen.php?aktie=disp">Onderhoud soort uren</a></li>';
		echo '<li><a href="nieuws.php?aktie=disp">Onderhoud nieuwsartikelen</a></li>';
		echo '</ul>';	
		echo '</div>';
	}
}		

?>