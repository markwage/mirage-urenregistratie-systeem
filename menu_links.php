<?php
if (isset($_SESSION['username'])) {
    if (isset($_COOKIE['ID_mus'])) {
    
        ?>
        <div class="sidebox">
    	<h1 class="clear">Welkom</h1>
    	<p>
        <?php
        echo 'Gebruikersnaam: ' . $_SESSION['username'];
        echo '<br />Medewerker: ' . $_SESSION['voornaam'] . ' ' . $_SESSION['tussenvoegsel'] . ' ' . $_SESSION['achternaam'];
        echo '<br />Email: ' . $_SESSION['emailadres'];
        echo '<br />Laatste login: ' . $_SESSION['lastloggedin'];
        ?>
        </p>
        </div>
    
        <div class="sidebox">
    	<h1 class="clear">Urenadministratie</h1>
    	<ul class="sidemenu">
    		<li><a href="uren.php?aktie=toevoegen">Uren boeken</a></li>
    		<li><a href="mijn_verlofuren.php">Mijn verlofuren</a></li>
    		<li><a href="rpt_uren_urensoort.php?username=<?php echo $_SESSION["username_encrypted"] ?>">Mijn geboekte uren per maand</a></li>
    		<li><a href="users.php?aktie=editprof&edtuser=<?php echo $_SESSION["username_encrypted"] ?>">Mijn profiel</a></li>
    	</ul>
        </div>
    
        <?php
        // menu alleen voor gebruiker met adminrechten
        if ($_SESSION['admin']) {
            ?>
            <div class="sidebox">
    	    <h1 class="clear">Admin menu</h1>
    	    <ul class="sidemenu">
    		    <li><a href="approve.php?aktie=disp">Openstaande approvals</a></li>
    		    <li><a href="users.php?aktie=disp">Onderhoud medewerkers</a></li>
    		    <li><a href="soorturen.php?aktie=disp">Onderhoud soort uren</a></li>
    		    <li><a href="beginsaldo.php">Beginsaldi verlofuren</a></li>
    	    </ul>
            </div>
            <div class="sidebox">
    	    <h1 class="clear">Rapportage</h1>
    	    <ul class="sidemenu">
    		    <li><a href="rpt_uren_urensoort.php">Totaal per urensoort</a></li>
    		    <li><a href="verlofuren_medewerkers.php">Overzicht verlofuren per mdw</a></li>
    	    </ul>
            </div>
            <?php
        }
    }
}
?>