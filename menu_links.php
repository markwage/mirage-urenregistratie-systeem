<?php

if (isset($_COOKIE['ID_mus'])) 
{
    
	?>
    <div class="sidebox">
        <h1 class="clear">Welkom</h1>
        <p>
        <?php
        echo 'Gebruikersnaam: '.$_SESSION['username'];
        echo '<br />Medewerker: '.$_SESSION['voornaam'].' '.$_SESSION['tussenvoegsel'].' '.$_SESSION['achternaam'];
        echo '<br />Email: '.$_SESSION['emailadres'];
        echo '<br />Laatste login: '.$_SESSION['lastloggedin'];
        ?>
        </p>
    </div>
		
	<div class="sidebox">
	    <h1 class="clear">Urenadministratie</h1>
	    <ul class="sidemenu">
	        <li><a href="uren.php?aktie=toevoegen">Uren</a></li>
	        <li><a href="users.php?aktie=editprof&edtuser=<?php echo $_SESSION["username_encrypted"] ?>">Mijn profiel</a></li>
	    </ul>
	</div>
	
	<?php 
	// menu alleen voor gebruiker met adminrechten
	if ($_SESSION['admin']) 
	{
	    ?>
		<div class="sidebox">
		    <h1 class="clear">Admin menu</h1>
		    <ul class="sidemenu">
		        <li><a href="approve.php?aktie=disp">Openstaande approvals</a></li>
		        <li><a href="users.php?aktie=disp">Usermanagement</a></li>
		        <li><a href="soorturen.php?aktie=disp">Onderhoud soort uren</a></li>
	    	    <li><a href="nieuws.php?aktie=disp">Onderhoud nieuwsartikelen</a></li>
	    	</ul>
		</div>
		<div class="sidebox">
	    <h1 class="clear">Rapportage</h1>
	    <ul class="sidemenu">
	        <li><a href="rpt_uren_urensoort.php">Totaal uren per urensoort</a></li>
	        <li><a href="rpt_verlofuren_medewerker.php">Opgenomen verlofuren per mdw</a></li>
	    </ul>
	</div>
		<?php 
	}
}		

?>