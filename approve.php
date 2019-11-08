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

check_admin();     // Controleren of gebruiker admin-rechten heeft
check_cookies();   // Controleren of cookie aanwezig is. Zo niet, login-scherm displayen

include ("header.php");

?>
<div id="main">		
	<h1>Openstaande approvals</h1>
			
<?php 

//------------------------------------------------------------------------------------------------------
//
//       *******************   SUBMITTED   *******************
//
// From here this code runs if the form has been submitted
//------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------
// BUTTON Cancel
//------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
	header("location: approve.php?aktie=disp");
}

//------------------------------------------------------------------------------------------------------
// BUTTON Cancel
//------------------------------------------------------------------------------------------------------
if (isset($_POST['approve'])) {
    $approvedbyuser = $_SESSION['username'];
    $user           = $_POST['username'];
    $week           = $_POST['week'];
    $jaar           = $_POST['jaar'];
    $update = "UPDATE uren SET 
			approveddatum = '".date('Y-m-d')."', approvedbyuser = '".$approvedbyuser."', approved = '1'  
            WHERE user = '".$user."' AND week = '".$week."' AND jaar = '".$jaar."'";
    $check_approve_uren = mysqli_query($dbconn, $update) or die ("Error in query: $update. ".mysqli_error($dbconn));

    header("location: approve.php?aktie=disp");
}

//------------------------------------------------------------------------------------------------------
//
//       *******************   START   *******************
//
// Dit wordt uitgevoerd wanneer de gebruiker in linkermenu op "Openastaande approvals" klikt
// Er wordt een lijst met de te approven weken getoond
//------------------------------------------------------------------------------------------------------
if ($aktie == 'disp') {
	$sql_approvals = mysqli_query($dbconn, "SELECT user, week FROM uren WHERE terapprovalaangeboden = 1 AND approved = 0 GROUP BY user, week ORDER BY user, week;");
	echo "<center><table>";
	echo "<tr><th>Medewerker</th><th>Week</th><th></th></tr>";
	$rowcolor = 'row-a';
	while($row_approvals = mysqli_fetch_array($sql_approvals)) {
		$username = $row_approvals['user'];
		$week     = $row_approvals['week'];
		// ophalen volledige naam
		$sql_user = mysqli_query($dbconn, "SELECT voornaam, tussenvoegsel, achternaam FROM users WHERE username = '$username'");
		$row_user = mysqli_fetch_array($sql_user);
		$voornaam      = $row_user['voornaam'];
		$tussenvoegsel = $row_user['tussenvoegsel'];
		$achternaam    = $row_user['achternaam'];
		echo '<tr class="'.$rowcolor.'">
			<td><b>'.$voornaam.' '.$tussenvoegsel.' '.$achternaam.'</b></td><td style=\'text-align:center\'>'.$week.'</td>

			<td><a href="approve.php?aktie=dspuren&user='.$username.'&week='.$week.'"><img src="./img/buttons/icons8-glasses-48.png" alt="wijzigen soort uur" title="Toon de uren voor deze week voor deze user" /></a></td>
			</tr>';
		    //<td><a href="add_soortuur.php"><img src="./img/buttons/plus-green.gif" alt="toevoegen soort uur" title="toevoegen soort uur" /></a></td>
		if ($rowcolor == 'row-a') $rowcolor = 'row-b';
		else $rowcolor = 'row-a';
	}
	echo "</table></center>";
}

//------------------------------------------------------------------------------------------------------
// Wordt uitgevoerd wanneer men op de button klikt om uren van die user / week te displayen
//------------------------------------------------------------------------------------------------------
if ($aktie == 'dspuren') {
    $username = $_GET['user'];
    $week     = $_GET['week'];
    $sql_user = mysqli_query($dbconn, "SELECT * FROM users WHERE username = '$username'");
    $row_user = mysqli_fetch_array($sql_user);
    $voornaam      = $row_user['voornaam'];
    $tussenvoegsel = $row_user['tussenvoegsel'];
    $achternaam    = $row_user['achternaam'];
    $emailadres    = $row_user['emailadres'];
    echo "<center><b>Weeknummer: </b>".$week."<br /><b>Medewerker: </b>".$voornaam." ".$tussenvoegsel." ".$achternaam." ";
    
    $sql_uren = mysqli_query($dbconn, "SELECT * FROM uren WHERE user = '$username' AND week = '$week' ORDER BY datum, soortuur");
    echo "<h3>Overzicht per dag</h3>";
    echo "<center><table>";
    echo "<tr><th>Datum</th><th>Soortuur</th><th>Uren</th></tr>";
    $rowcolor = 'row-a';
    while ($row_uren = mysqli_fetch_array($sql_uren)) {
        $datum     = $row_uren['datum'];
        $soortuur  = $row_uren['soortuur'];
        $uren      = $row_uren['uren'];
        $sql_soortuur = mysqli_query($dbconn, "SELECT omschrijving FROM soorturen WHERE code = '$soortuur'");
        $row_soortuur = mysqli_fetch_array($sql_soortuur);
        $omschrijving = $row_soortuur['omschrijving'];
        echo '<tr class="'.$rowcolor.'">
			<td><b>'.$datum.'</b></td><td>'.$soortuur.' - '.$omschrijving.'</td><td style=\'text-align:right\'>'.$uren.'</td>
            </tr>';
        //<td><a href="add_soortuur.php"><img src="./img/buttons/plus-green.gif" alt="toevoegen soort uur" title="toevoegen soort uur" /></a></td>
        if ($rowcolor == 'row-a') $rowcolor = 'row-b';
        else $rowcolor = 'row-a';
    }
    echo "</table></center>";
    
    // Display totaal per soortuur
    //SELECT SUM(uren), soortuur FROM uren WHERE WEEK = 10 AND USER = 'mwage' GROUP BY soortuur;
    $sql_totaalurenpersoort = mysqli_query($dbconn, "SELECT SUM(uren) as toturen, soortuur FROM uren WHERE user = '$username' AND week = '$week' GROUP BY soortuur ORDER BY soortuur");
    echo "<h3>Totaal per urensoort</h3>";
    echo "<center><table>";
    echo "<tr><th>Soort uur</th><th>Totaal</th></tr>";
    $rowcolor = 'row-a';
    while ($row_totaalurenpersoort = mysqli_fetch_array($sql_totaalurenpersoort)) {
        $toturen    = $row_totaalurenpersoort['toturen'];
        $soortuur   = $row_totaalurenpersoort['soortuur'];
        $sql_soortuur = mysqli_query($dbconn, "SELECT omschrijving FROM soorturen WHERE code = '$soortuur'");
        $row_soortuur = mysqli_fetch_array($sql_soortuur);
        $omschrijving = $row_soortuur['omschrijving'];
        echo '<tr class="'.$rowcolor.'">
			<td>'.$soortuur.' - '.$omschrijving.'</td><td style=\'text-align:right\'>'.$toturen.'</td>
            </tr>';
        //<td><a href="add_soortuur.php"><img src="./img/buttons/plus-green.gif" alt="toevoegen soort uur" title="toevoegen soort uur" /></a></td>
        if ($rowcolor == 'row-a') $rowcolor = 'row-b';
        else $rowcolor = 'row-a';
    }
    echo "</table></center>";
    
    // Display buttons. Met behulp van een formulier
    ?>
    <form style='background-color:#FFF;' name="approve" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"> 
    <!-- Volgende velden zijn hidden om toch de waarden door te geven voor de update-query -->
    <input type="hidden" name="week" value="<?php if (isset($week)) { echo $week; } ?>">
    <input type="hidden" name="username" value="<?php if (isset($username)) { echo $username; } ?>">
    <input type="hidden" name="jaar" value="<?php if (isset($datum)) { echo substr($datum,0,4); } ?>">
    <!--  Tot hier -->
    <input class="button" type="submit" name="cancel" value="cancel" formnovalidate>
    <?php 
    if (!isset($_SESSION['approvenallowed']) || (!$_SESSION['approvenallowed'])) echo '<blockquote>Je hebt geen rechten om te approven</blockquote>';
    else echo '<input class="button" type="submit" name="approve" value="approve">';
    ?>
    </form>
    <?php 
}
	
include ("footer.php");
?>		


