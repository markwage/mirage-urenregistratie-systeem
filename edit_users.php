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

// Controleren of gebruiker admin-rechten heeft
// Indien het het wijzigen van het eigen profiel betreft hoeft hij geen admin-rechten te hebben
if (!$aktie == "editprof") {
    check_admin();
}

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

?>
<div id="main">		
	<h1>Usermanagement</h1>
			
<?php 
//------------------------------------------------------------------------------------------------------
// From here this code runs if the form has been submitted
//------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------
// BUTTON Cancel
// Wanneer het geen admin betreft wordt de hoofdpagina getoond. Indien wel adminrechten dan wordt de
// lijst met alle users getoond
//------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
    if (!isset($_SESSION['admin']) || (!$_SESSION['admin'])) {
        header("location: index.php");
    } else {
        header("location: edit_users.php?aktie=disp");
    }
}

//------------------------------------------------------------------------------------------------------
// BUTTON Delete
//------------------------------------------------------------------------------------------------------
if (isset($_POST['delete'])) {
	$deluser = $_POST['username'];
	$sql_deluser = mysqli_query($dbconn, "DELETE FROM users WHERE username = '$deluser'");
	writeLogRecord("edit_users","User ".$deluser." is succesvol verwijderd.");
	header("location: edit_users.php?aktie=disp");
}

//------------------------------------------------------------------------------------------------------
// BUTTON Save
//------------------------------------------------------------------------------------------------------
if (isset($_POST['save'])) {
    form_user_fill('save');
	//$formerror = 0;
	writelogrecord("edit_users", "SAVEBUTTON - Wachtwoorden worden gecontroleerd");
	// Checks wanneer password OF verificatiepassword niet leeg zijn
	if (($_POST['pass']) != "" || ($_POST['pass2']) != "") {
	    if (!$_POST['pass'] && (!$formerror)) {
			echo '<p class="errmsg"> ERROR: Wachtwoord is een verplicht veld</p>';
			$focus     = 'pass';
			$formerror = 1;
		}
		if (!$_POST['pass2'] && (!$formerror)) {
			echo '<p class="errmsg"> ERROR: Wachtwoord voor verificatie is een verplicht veld</p>';
			$focus     = 'pass2';
			$formerror = 1;
		}
		// Check of de wachtwoorden gelijk zijn
		if (($_POST['pass'] != $_POST['pass2']) && (!$formerror)) {
		    echo '<p class="errmsg"> ERROR: De wachtwoorden zijn niet gelijk</p>';
		    $focus     = 'pass';
		    $formerror = 1;
		}
	}
	writelogrecord("edit_users", "CHECKFIELDS - Overige velden worden gecontroleerd");
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
	if ($_SESSION['admin'] && (!$formerror)) {
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
	    writelogrecord("edit_users", "CREATEQRY1 - Beginnen met het aanmaken van de UPDATE query om user ".$_POST['username']."te updaten");
	    $update = "UPDATE users SET ";
	    if (!$_POST['pass'] == "") {
	        $_POST['pass'] = md5($_POST['pass']);
	        writelogrecord("edit_users", "PASS_MD5 - Wachtwoord is middels md5 encrypted");
	        if (!get_magic_quotes_gpc()) {
	            $_POST['pass'] = addslashes($_POST['pass']);
	            $_POST['username'] = addslashes($_POST['username']);
	        }
	        $update .= "password='".$_POST['pass']."',";
	    }
	    
	    $update .= "admin='".$_POST['admin']."',
		voornaam='".$_POST['voornaam']."',
		tussenvoegsel='".$_POST['tussenvoegsel']."',
		achternaam='".$_POST['achternaam']."',
		emailadres='".$_POST['email']."',
		indienst='".$_POST['indienst']."' WHERE username = '".$_POST['username']."'";
	    writeLogRecord("edit_users","UPDQUERY De UPDATE-query wordt nu uitgevoerd op de database voor user".$frm_username);
	    $check_upd_user = mysqli_query($dbconn, $update);
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

//------------------------------------------------------------------------------------------------------
// START Dit wordt uitgevoerd wanneer de user op Usermanagement heeft geklikt
// Er wordt een lijst met de users getoond
//------------------------------------------------------------------------------------------------------
if ($aktie == 'disp') {
	$sql_allusers = mysqli_query($dbconn, "SELECT * FROM users ORDER BY achternaam");
	echo "<center><table>";
	//echo "<tr><th>ID</th><th>username</th><th>naam</th><th>Emailadres</th><th>Admin</th><th>InDienst</th><th colspan=\"3\" align=\"center\">Akties</th></tr>";
	echo "<tr><th>username</th><th>naam</th><th>Emailadres</th><th>Admin</th><th>InDienst</th><th colspan=\"3\" align=\"center\">Akties</th></tr>";
	$rowcolor = 'row-a';
	while($row_allusers = mysqli_fetch_array($sql_allusers)) {
		$id            = $row_allusers['ID'];
		$username      = $row_allusers['username'];
		$voornaam      = $row_allusers['voornaam'];
		$tussenvoegsel = $row_allusers['tussenvoegsel'];
		$achternaam    = $row_allusers['achternaam'];
		$emailadres    = $row_allusers['emailadres'];
		$admin         = $row_allusers['admin'];
		$indienst      = $row_allusers['indienst'];
		echo '<tr class="'.$rowcolor.'">
			<td><b>'.$username.'</b></td>
			<td>'.$achternaam.', '.$voornaam.' '.$tussenvoegsel. '</td>
			<td>'.$emailadres.'</td>
			<td align="center">'.$admin.'</td>
			<td align="center">'.$indienst.'</td>
			<td><a href="edit_users.php?aktie=edit&edtuser='.$username.'"><img src="./img/buttons/icons8-edit-48.png" alt="wijzigen user" title="wijzig user '.$username.'" /></a></td>
			<td><a href="edit_users.php?aktie=delete&edtuser='.$username.'"><img src="./img/buttons/icons8-trash-can-48.png" alt="delete user" title="delete user '.$username.'" /></a></td>
			<td><a href="add_user.php"><img src="./img/buttons/icons8-plus-48.png" alt="toevoegen nieuwe user" title="toevoegen nieuwe user" /></a></td>
			</tr>';
		if ($rowcolor == 'row-a') $rowcolor = 'row-b';
		else $rowcolor = 'row-a';
	}
	echo "</table></center>";
}

//------------------------------------------------------------------------------------------------------
// Wordt uitgevoerd wanneer men op de button klikt om te wijzigen of te deleten of om het eigen
// profiel aan te passen
//------------------------------------------------------------------------------------------------------
if ($aktie == 'edit' || $aktie == 'delete' || $aktie == 'editprof') {
	$edtuser = $_GET['edtuser'];
	$focus = "pass";
	$sql_dspuser = mysqli_query($dbconn, "SELECT * FROM users WHERE username = '$edtuser'");
	while($row_dspuser = mysqli_fetch_array($sql_dspuser)) {
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
				<td><input <?php if ($aktie == "edit" || $aktie == "editprof") { echo "readonly"; } ?> type="text" name="username" maxlength="40" value="<?php if (isset($frm_username)) { echo $frm_username; } ?>"></td>
			</tr>
			<tr>
				<td>Wachtwoord</td>
				<td><input type="password" name="pass" maxlength="10" value="<?php if (isset($frm_pass)) { echo $frm_pass; } ?>"></td>
				<td>Confirm</td>
				<td><input type="password" name="pass2" maxlength="10" value="<?php if (isset($frm_pass2)) { echo $frm_pass2; } ?>"></td>
			</tr>
			<tr>
				<td>Admin</td>
				<td><input type="checkbox" <?php if (!$_SESSION['admin']) { echo "checked disabled "; } ?>name="admin" <?php { echo $frm_admin; } ?>></td>
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
				<td><input type="checkbox" <?php if (!$_SESSION['admin']) { echo "checked disabled "; } ?>name="indienst" <?php { echo $frm_indienst; } ?>></td>
			</tr>
		</table>
		<br />
		<?php if ($aktie == 'edit' || $aktie == 'editprof') echo '<input class="button" type="submit" name="save" value="save">'; ?>
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
}
	
include ("footer.php");
?>		

