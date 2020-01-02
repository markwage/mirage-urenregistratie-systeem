<?php

if (isset($_COOKIE['ID_mus'])) {
    
	?>
    <div class="sidebox">
        <h1>Welkom op MUS</h1>
        <p>Welkom <b><?php echo $_SESSION['voornaam']?></b>.<br /> Je bent nu ingelogd op MUS, het Mirage Urenregistratie Systeem<br /></p>
        <p>Je kunt nu je uren inzien en je urenverantwoording invullen
    </div>
		
	<div class="sidebox">
	    <h1 class="clear">Urenadministratie</h1>
	    <ul class="sidemenu">
	        <li><a href="uren.php?aktie=toevoegen">Uren</a></li>
	        <li><a href="uren.php?aktie=disp">Vakantie-uren per kalenderjaar</a></li>
	        <li><a href="rapportage.php">Overzicht periode</a></li>
	        <li><a href="users.php?aktie=editprof&edtuser=<?php echo $_SESSION["username"] ?>">Mijn profiel</a></li>
	    </ul>
	</div>
	
	<?php 
	// menu alleen voor gebruiker met adminrechten
	if ($_SESSION['admin']) 
	{
	    ?>
		<div class="sidebox">
		    <h1>Admin menu</h1>
		    <ul class="sidemenu">
		        <li><a href="approve.php?aktie=disp">Openstaande approvals</a></li>
		        <li><a href="users.php?aktie=disp">Usermanagement</a></li>
		        <li><a href="soorturen.php?aktie=disp">Onderhoud soort uren</a></li>
	    	    <li><a href="nieuws.php?aktie=disp">Onderhoud nieuwsartikelen</a></li>
	    	</ul>
		</div>
		<?php 
	}
}		

?>