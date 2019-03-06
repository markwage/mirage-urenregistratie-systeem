<?php
session_start();
include ("config.php");
include ("db.php");
include ("function.php");

// Controleren of gebruiker admin-rechten heeft
// check_admin();

// Connectie met de database maken en database selecteren
$dbconn = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

?>
<div id="main">		
	<h1>Toevoegen nieuws</h1>
			
<?php 
//This code runs if the form has been submitted
if (isset($_POST['cancel'])) {
	header("location: edit_nieuws.php?aktie=disp");
}
if (isset($_POST['submit'])) { 
	//form_nieuws_fill('toevoegen');
    $formerror = 0;
		
	if (!$_POST['nieuwsheader']) {
		echo '<p class="errmsg"> ERROR: Nieuwsheader is een verplicht veld</p>';
		$focus     = 'nieuwsheader';
		$formerror = 1;
	}
	if (!$_POST['nieuwsbericht'] && (!$formerror)) {
		echo '<p class="errmsg"> ERROR: Nieuwsbericht is een verplicht veld</p>';
		$focus     = 'nieuwsbericht';
		$formerror = 1;
	}
	// checks if the soort uur is in use
	if (!get_magic_quotes_gpc()) {
		$_POST['code'] = addslashes($_POST['code']);
	}
	$soortuurcheck = $_POST['code'];
	$check = mysqli_query($dbconn, "SELECT code FROM soorturen WHERE code = '$soortuurcheck'") or die(mysql_error());
	$check2 = mysqli_num_rows($check);

	//if the name exists it gives an error
	if ($check2 != 0) {
		echo '<p class="errmsg"> ERROR: Code '.$_POST['code'].' is al aanwezig.</p>';
		$focus     = 'code';
		$formerror = 1;
	}

	// here we encrypt the password and add slashes if needed
	if (!$formerror) { 
		//$_POST['code'] = strtoupper($_POST['code']);
		if (!get_magic_quotes_gpc()) {
			$_POST['code'] = addslashes($_POST['code']);
		}
		
		// Record toevoegen in database
		$insert = "INSERT INTO soorturen (code, omschrijving)
			VALUES ('".$_POST['code']."', 
					'".$_POST['omschrijving']."')";
		$check_add_member = mysqli_query($dbconn, $insert);

		if ($check_add_member) { 
			echo '<p class="infmsg">Code <b>'.$_POST['code'].'</b> is opgenomen</p>.';
			$frm_code         = "";
			$frm_omschrijving = "";
			header("location: edit_soorturen.php?aktie=disp"); 
		}
		else {
			echo '<p class="errmsg">Er is een fout opgetreden bij het toevoegen van de code. Probeer het nogmaals.<br />
			Indien het probleem zich blijft voordoen neem dan contact op met de webmaster</p>';
		}
	}
} 

?>
 
<form name="nieuws" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 	<p>
	<table>
		<tr>
			<td>header</td>
			<td><input type="text" name="nieuwsheader" size="128" maxlength="128" value="<?php if (isset($frm_nieuwsheader)) { echo $frm_nieuwsheader; } ?>"></td>
		</tr>
		<tr>
			<td>Bericht</td>
			<td><input type="text" name="nieuwsbericht" size="60" maxlength="60" value="<?php if (isset($frm_nieuwsbericht)) { echo $frm_nieuwsbericht; } ?>"></td>
	</table>
	<br />
	<input class="button" type="submit" name="submit" value="add nieuwsbericht">
	<input class="button" type="submit" name="cancel" value="cancel">
	</p>
</form>
<br />		
<?php 
if (!isset($focus)) {
	$focus='code';
}
setfocus('soorturen', $focus);
	
include ("footer.php");
?>		
