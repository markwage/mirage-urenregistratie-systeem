<?php
session_start();
include ("config.php");
include ("db.php");
include ("function.php");

// Controleren of gebruiker admin-rechten heeft
check_admin();

// Connectie met de database maken en database selecteren
mysql_connect($dbhost, $dbuser, $dbpassw) or die ("Kan de connectie met de database niet maken");
mysql_select_db($dbname) or die ("Kan de database niet openen");

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

?>
<div id="main">		
	<h1>Toevoegen nieuw soort uur</h1>
			
<?php 
//This code runs if the form has been submitted
if (isset($_POST['cancel'])) {
	header("location: edit_soorturen.php?aktie=disp");
}
if (isset($_POST['submit'])) { 
	form_soorturen_fill('toevoegen');
	$_POST['code'] = strtoupper($_POST['code']);
	
	if (!$_POST['code']) {
		echo '<p class="errmsg"> ERROR: Code is een verplicht veld</p>';
		$focus     = 'username';
		$formerror = 1;
	}
	if (!$_POST['omschrijving'] && (!$formerror)) {
		echo '<p class="errmsg"> ERROR: Omschrijving is een verplicht veld</p>';
		$focus     = 'pass';
		$formerror = 1;
	}
	// checks if the soort uur is in use
	if (!get_magic_quotes_gpc()) {
		$_POST['code'] = addslashes($_POST['code']);
	}
	$soortuurcheck = $_POST['code'];
	$check = mysql_query("SELECT code FROM soorturen WHERE code = '$soortuurcheck'") or die(mysql_error());
	$check2 = mysql_num_rows($check);

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
		$check_add_member = mysql_query($insert);

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
 
<form name="soorturen" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 	<p>
	<table>
		<tr>
			<td>Code</td>
			<td><input style="text-transform: uppercase" type="text" name="code" size="10" maxlength="8" value="<?php if (isset($frm_code)) { echo $frm_code; } ?>"></td>
		</tr>
		<tr>
			<td>Omschrijving</td>
			<td><input type="text" name="omschrijving" size="60" maxlength="60" value="<?php if (isset($frm_omschrijving)) { echo $frm_omschrijving; } ?>"></td>
	</table>
	<br />
	<input class="button" type="submit" name="submit" value="add soort uur">
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
