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

//$aktie = $_GET['aktie'];

// Connectie met de database maken en database selecteren
//$dbconn = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

?>
<div id="main">		
	<h1>Urenadministratie</h1>

<?php 
//This code runs if the form has been submitted
if (isset($_POST['go'])) {
	//header("location: uren.php?aktie=disp&datumin=$datumin");
	echo "datumin is ".$_POST['date'];
}
if (isset($_POST['cancel'])) {
	header("location: uren.php?aktie=disp");
}
if (isset($_POST['delete'])) {
	$deluser = $_POST['username'];
	$sql_deluser = mysql_query("DELETE FROM users WHERE username = '$deluser'");
	header("location: edit_users.php?aktie=disp");
}
if (isset($_POST['update'])) {
	form_user_fill('update');
	if (($_POST['pass']) != "" || ($_POST['pass2']) != "") {
		if (($_POST['pass'] != $_POST['pass2']) && (!$formerror)) {
			echo '<p class="errmsg"> ERROR: De wachtwoorden zijn niet gelijk</p>';
			$focus     = 'pass2';
			$formerror = 1;
		}
		if (!$_POST['pass'] && (!$formerror)) {
			echo '<p class="errmsg"> ERROR: Password is een verplicht veld</p>';
			$focus     = 'pass';
			$formerror = 1;
		}
		if (!$_POST['pass2'] && (!$formerror)) {
			echo '<p class="errmsg"> ERROR: Password voor verificatie is een verplicht veld</p>';
			$focus     = 'pass2';
			$formerror = 1;
		}
	}
	if ((!$_POST['voornaam'] || $_POST['voornaam'] == "") && (!$formerror)) {
		echo '<p class="errmsg"> ERROR: Voornaam is een verplicht veld</p>';
		$focus     = 'voornaam';
		$formerror = 1;
	}
	if (!$_POST['achternaam'] && (!$formerror)) {
		echo '<p class="errmsg"> ERROR: Achternaam is een verplicht veld</p>';
		$focus     = 'achternaam';
		$formerror = 1;
	}
	if (!$_POST['email'] && (!$formerror)) {
		echo '<p class="errmsg"> ERROR: Email is een verplicht veld</p>';
		$focus     = 'email';
		$formerror = 1;
	}
	if ($_SESSION['admin']) {
		if (!isset($_POST['admin'])) {
			$_POST['admin'] = 0;
		}
		else {
			$_POST['admin'] = 1;
		}
		if (!isset($_POST['indienst'])) {
			$_POST['indienst'] = 0;
		}
		else {
			$_POST['indienst'] = 1;
		}
	}
	
	// here we encrypt the password and add slashes if needed
	if (!$formerror) { 
		// $_POST['indienst'] = 1;
		$_POST['pass'] = md5($_POST['pass']);
		if (!get_magic_quotes_gpc()) {
			$_POST['pass'] = addslashes($_POST['pass']);
			$_POST['username'] = addslashes($_POST['username']);
		}
		$update = "UPDATE users SET 
		password='".$_POST['pass']."', 
		admin='".$_POST['admin']."',
		voornaam='".$_POST['voornaam']."',
		tussenvoegsel='".$_POST['tussenvoegsel']."',
		achternaam='".$_POST['achternaam']."',
		emailadres='".$_POST['email']."',
		indienst='".$_POST['indienst']."' WHERE username = '".$_POST['username']."'";
		$check_upd_user = mysql_query($update);
		if ($check_upd_user) { 
			echo '<p class="infmsg">User <b>'.$_POST['username'].'</b> is gewijzigd</p>.';
			$frm_username      = "";
			$frm_pass          = "";
			$frm_pass2         = "";
			$frm_voornaam      = "";
			$frm_tussenvoegsel = "";
			$frm_achternaam    = "";
			$frm_email         = "";
		}
		else {
			echo '<p class="errmsg">Er is een fout opgetreden bij het toevoegen van de user. Probeer het nogmaals.<br />
			Indien het probleem zich blijft voordoen neem dan contact op met de webmaster</p>';
		}
		header("location: edit_users.php?aktie=disp"); 
	}
}

if ($aktie == 'disp') {
	displayUserGegevens();
	echo "<p><table>";
	echo "<tr><th width=25%><strong>soort uur</th><th width=9%>Maa<br />28-03</th><th width=9%>Din<br />29-03</th><th width=9%>Woe<br />30-03</th><th width=9%>Don<br />31-03</th><th width=9%>Vrij<br />01-04</th><th width=9%>Zat<br />02-04</th><th width=9%>Zon<br />03-04</th><th width=12%>Totaal</th>";
	echo "</table></p>";
	?>
	<form name="uren" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<fieldset>
	<input type="text" name="date" id="date" size="11" /><a href="javascript:viewcalendar()"> <img src="./img/calendar.jpg" alt="display kalender" title="display kalendar" /></a>
	<input class="button" type="submit" name="go" value="go">
	</fieldset>
	</form>

<?php 
	
}

if ($aktie == 'edit' || $aktie == 'delete') {
	$edtuser = $_GET['edtuser'];
	$focus = "pass";
	$sql_dspuser = mysql_query("SELECT * FROM users WHERE username = '$edtuser'");
	while($row_dspuser = mysql_fetch_array($sql_dspuser)) {
		$frm_username = $row_dspuser['username'];
		$frm_voornaam = $row_dspuser['voornaam'];
		$frm_tussenvoegsel = $row_dspuser['tussenvoegsel'];
		$frm_achternaam = $row_dspuser['achternaam'];
		$frm_email = $row_dspuser['emailadres'];
		if ($row_dspuser['admin'] == 1) $frm_admin = "checked";
		else $frm_admin = "";
		if ($row_dspuser['indienst'] == 1) $frm_indienst = "checked";
		else $frm_indienst = "";
	}

?>

<form name="AddUser" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 	<p>
	<table>
		<tr>
			<td><b>Username</b></td>
			<td><input <?php if ($aktie == "edit") { echo "readonly"; } ?> type="text" name="username" maxlength="40" value="<?php if (isset($frm_username)) { echo $frm_username; } ?>"></td>
		</tr>
		<tr>
			<td>Wachtwoord</td>
			<td><input type="password" name="pass" maxlength="10" value="<?php if (isset($frm_pass)) { echo $frm_pass; } ?>"></td>
			<td>Confirm</td>
			<td><input type="password" name="pass2" maxlength="10" value="<?php if (isset($frm_pass2)) { echo $frm_pass2; } ?>"></td>
		</tr>
		<tr>
			<td>Admin</td>
			<td><input type="checkbox" <?php if (!$_SESSION['admin']) { echo "readonly"; } ?>name="admin" "<?php if (isset($frm_admin)) { echo $frm_admin; } ?>"></td>
		</tr>
		<tr>
			<td>Voornaam</td>
			<td><input type="text" name="voornaam" maxlength="24" value="<?php if (isset($frm_voornaam)) { echo $frm_voornaam; } ?>"></td>
		</tr>
		<tr>
			<td>Tussenv.</td>
			<td><input type="text" name="tussenvoegsel" maxlength="10" value="<?php if (isset($frm_tussenvoegsel)) { echo $frm_tussenvoegsel; } ?>"></td>
			<td>Achternaam</td>
			<td><input type="text" name="achternaam" maxlength="40" value="<?php if (isset($frm_achternaam)) { echo $frm_achternaam; } ?>"></td>
		</tr>
		<tr>
			<td>Email</td>
			<td colspan="2"><input type="text" name="email" size="40" maxlength="60" value="<?php if (isset($frm_email)) { echo $frm_email; } ?>"></td>
		</tr>
		<tr>
			<td>In dienst</td>
			<td><input type="checkbox" name="indienst" "<?php if (isset($frm_indienst)) { echo $frm_indienst; } ?>"></td>
		</tr>
	</table>
	<br />
	<?php if ($aktie == 'edit') echo '<input class="button" type="submit" name="update" value="update">'; ?>
	<?php if ($aktie == 'delete') echo '<input class="button" type="submit" name="delete" value="delete" onClick="return confirmDelUser()">'; ?>
	<input class="button" type="submit" name="cancel" value="cancel">
	</p>
</form>
<br />		
<?php 
if (!isset($focus)) {
	$focus='username';
}
setfocus('AddUser', $focus);

// Einde van if aktie=edit
}
	
include ("footer.php");
?>		
