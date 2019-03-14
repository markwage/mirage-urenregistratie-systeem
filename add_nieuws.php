<?php
session_start();
include ("config.php");
include ("db.php");
include ("function.php");

// Controleren of gebruiker admin-rechten heeft
// check_admin();

// Connectie met de database maken en database selecteren
//$dbconn = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

?>
<div id="main">		
	<h1>Toevoegen nieuwsbericht</h1>
			
<?php 
//This code runs if the form has been submitted
if (isset($_POST['cancel'])) {
	header("location: edit_nieuws.php?aktie=disp");
}

if (isset($_POST['submit'])) { 
	//form_nieuws_fill('toevoegen');
    $formerror = 0;
    if (isset($frm_nieuwsheader)) {
        $nieuwsheader = $frm_nieuwsheader;
    }
    writeLogRecord("add_nieuws","CHECK01A De inputveld nieuwsheader wordt gecontroleerd");
	if (!$_POST['nieuwsheader']) {
	    writeLogRecord("add_nieuws","CHECK02 ERROR Verplicht inputveld nieuwsheader is niet gevuld");
		echo '<p class="errmsg"> ERROR: Nieuwsheader is een verplicht veld</p>';
		$focus     = 'nieuwsheader';
		$formerror = 1;
	} else {
	    $frm_nieuwsheader = $_POST['nieuwsheader'];
	}
	writeLogRecord("add_nieuws","CHECK01B De inputveld nieuwsbericht wordt gecontroleerd");
	if ((!$_POST['nieuwsbericht'])  && (!$formerror)) {
	    writeLogRecord("add_nieuws","CHECK02 ERROR Verplicht inputveld nieuwsheader is niet gevuld");
		echo '<p class="errmsg"> ERROR: Nieuwsbericht is een verplicht veld</p>';
		$focus     = 'nieuwsbericht';
		$formerror = 1;
	}
	
	// Normaal wordt hier gechecked of de ingevulde velden al bestaan in de database maar dat is voor nieuwsberichten niet nodig

	// here we encrypt the password and add slashes if needed
	if (!$formerror) { 
	    writeLogRecord("add_nieuws","ADD01 Nieuwsbericht wordt toegevoegd aan de database");
		// Record toevoegen in database
		$insert = "INSERT INTO nieuws (nieuwsheader, nieuwsbericht)
			VALUES ('".$_POST['nieuwsheader']."', 
					'".$_POST['nieuwsbericht']."')";
		$check_add_nieuws = mysqli_query($dbconn, $insert);

		if ($check_add_nieuws) { 
			echo '<p class="infmsg">Het nieuwsbericht is opgenomen</p>.';
			$frm_nieuwsheader  = "";
			$frm_nieuwsbericht = "";
			header("location: edit_nieuws.php?aktie=disp"); 
		}
		else {
			echo '<p class="errmsg">Er is een fout opgetreden bij het toevoegen van nieuwsbericht. Probeer het nogmaals.<br />
			Indien het probleem zich blijft voordoen neem dan contact op met de webmaster</p>';
		}
	}
} 

?>
 
<form name="nieuws" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 	<p>
	<table>
		<tr>
			<td>Nieuwsheader</td>
			<td><input type="text" name="nieuwsheader" size="80" maxlength="128" value="<?php if (isset($frm_nieuwsheader)) { echo $frm_nieuwsheader; } ?>"></td>
		</tr>
		<tr>
			<td>Nieuwsericht</td>
			<td><textarea id="area1" name="nieuwsbericht"><?php if (isset($frm_nieuwsbericht)) { echo $frm_nieuwsbericht; } ?></textarea></td>
		</table>
	<br />
	<input class="button" type="submit" name="submit" value="add nieuwsbericht">
	<input class="button" type="submit" name="cancel" value="cancel">
	</p>
</form>
<br />		
<?php 
if (!isset($focus)) {
	$focus='nieuwsheader';
}
setfocus('nieuws', $focus);
	
include ("footer.php");
?>		
