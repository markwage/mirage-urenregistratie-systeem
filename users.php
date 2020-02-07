<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

if (isset($_GET['aktie'])) {
    $aktie = $_GET['aktie'];
    $_SESSION['aktie'] = $_GET['aktie'];
} elseif (! isset($aktie)) {
    if (isset($_SESSION['aktie'])) {
        $aktie = $_SESSION['aktie'];
    } else {
        $aktie = "";
    }
}
if (isset($_GET['edtuser'])) {
    $edtuser = $_GET['edtuser'];
    $_SESSION['edtuser'] = $_GET['edtuser'];
} elseif (! isset($edtuser)) {
    if (isset($_SESSION['edtuser'])) {
        $edtuser = $_SESSION['edtuser'];
    } else {
        $edtuser = "";
    }
}

// Controleren of gebruiker admin-rechten heeft
// Indien het het wijzigen van het eigen profiel betreft hoeft hij geen admin-rechten te hebben
if ($aktie != "editprof") {
    check_admin();
}

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();
include ("header.php");

?>
<div id="main">
	<h1>Usermanagement</h1>
			
<?php
// ------------------------------------------------------------------------------------------------------
// From here this code runs if the form has been submitted
// ------------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------------
// BUTTON Cancel
// Wanneer het geen admin betreft wordt de hoofdpagina getoond. Indien wel adminrechten dan wordt de
// lijst met alle users getoond
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
    if (! isset($_SESSION['admin']) || (! $_SESSION['admin'])) {
        header("location: index.php");
    } else {
        header("location: users.php?aktie=disp");
    }
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Delete
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['delete'])) {
    $deluser = $_POST['username'];
    $sql_code = "DELETE FROM users
                 WHERE username = '$deluser'";
    $sql_out = mysqli_query($dbconn, $sql_code);
    writelog("users", "INFO", "User " . $deluser . " is succesvol verwijderd");

    header("location: users.php?aktie=disp");
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Save
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['save'])) {
    form_user_fill('save');

    // Checks wanneer password OF verificatiepassword niet leeg zijn
    if (($_POST['pass']) != "" || ($_POST['pass2']) != "") {
        if (! $_POST['pass'] && (! $formerror)) {
            echo '<blockquote class="error">ERROR: Wachtwoord is een verplicht veld</blockquote>';
            $focus = 'pass';
            $formerror = 1;
        }

        if (! $_POST['pass2'] && (! $formerror)) {
            echo '<blockquote class="error">ERROR: Wachtwoord ter verificatie is een verplicht veld</blockquote>';
            $focus = 'pass2';
            $formerror = 1;
        }

        // Check of de wachtwoorden gelijk zijn
        if (($_POST['pass'] != $_POST['pass2']) && (! $formerror)) {
            echo '<blockquote class="error">ERROR: De wachtwoorden zijn niet gelijk. Probeer het nogmaals.</blockquote>';
            $focus = 'pass';
            $formerror = 1;
        }
    }

    if ((! $_POST['voornaam'] || $_POST['voornaam'] == "") && (! $formerror)) {
        echo '<blockquote class="error">ERROR: Voornaam is een verplicht veld</blockquote>';
        $focus = 'voornaam';
        $formerror = 1;
    }

    if (! $_POST['achternaam'] && (! $formerror)) {
        echo '<blockquote class="error">ERROR: Achternaam is een verplicht veld</blockquote>';
        $focus = 'achternaam';
        $formerror = 1;
    }

    if (! $_POST['email'] && (! $formerror)) {
        echo '<blockquote class="error">ERROR: Emailadres is een verplicht veld</blockquote>';
        $focus = 'email';
        $formerror = 1;
    }

    if (! $formerror) {
        if ($_SESSION['admin']) {

            if (! isset($_POST['admin'])) {
                $_POST['admin'] = 0;
            } else {
                $_POST['admin'] = 1;
            }

            if (! isset($_POST['uren_invullen'])) {
                $_POST['uren_invullen'] = 0;
            } else {
                $_POST['uren_invullen'] = 1;
            }

            if (! isset($_POST['indienst'])) {
                $_POST['indienst'] = 0;
            } else {
                $_POST['indienst'] = 1;
            }

            if (! isset($_POST['approvenallowed'])) {
                $_POST['approvenallowed'] = 0;
            } else {
                $_POST['approvenallowed'] = 1;
            }
        } else {
            $_POST['admin'] = $frm_admin;
            $_POST['indienst'] = $frm_indienst;
            $_POST['approvenallowed'] = $frm_approvenallowed;
            $_POST['uren_invullen'] = $frm_uren_invullen;
        }
        $sql_code = "UPDATE users SET ";

        if (! $_POST['pass'] == "") {
            $_POST['pass'] = md5($_POST['pass']);

            if (! get_magic_quotes_gpc()) {
                $_POST['pass'] = addslashes($_POST['pass']);
                $_POST['username'] = addslashes($_POST['username']);
            }
            $sql_code .= "password='" . $_POST['pass'] . "',";
        }

        $sql_code .= "admin='" . $_POST['admin'] . "',
        uren_invullen='" . $_POST['uren_invullen'] . "',
		voornaam='" . $_POST['voornaam'] . "',
		tussenvoegsel='" . $_POST['tussenvoegsel'] . "',
		achternaam='" . $_POST['achternaam'] . "',
		emailadres='" . $_POST['email'] . "',
		indienst='" . $_POST['indienst'] . "',
        approvenallowed='" . $_POST['approvenallowed'] . "',
        wrong_password_count=0 
        WHERE username = '" . $_POST['username'] . "'";

        $sql_out = mysqli_query($dbconn, $sql_code);

        if ($sql_out) {
            writelog("users", "INFO", "De query voor updaten user is succesvol uitgevoerd voor user " . $frm_username);

            echo '<p class="infmsg">User <b>' . $_POST['username'] . '</b> is gewijzigd</p>.';
            $frm_username = "";
            $frm_pass = "";
            $frm_pass2 = "";
            $frm_voornaam = "";
            $frm_tussenvoegsel = "";
            $frm_achternaam = "";
            $frm_email = "";
        } else {
            writelog("users", "ERROR", "De query voor updaten user is fout gegaan - " . mysqli_error($dbconn));
            echo '<blockquote class="error">ERROR: Er is een fout opgetreden bij het toevoegen van de user. Probeer het nogmaals.<br />
			Indien het probleem zich blijft voordoen neem dan contact op met de webmaster</blockquote>';
        }
        if ($aktie == 'editprof') {
            header("location: users.php?aktie=editprof&edtuser=" . $_SESSION['edtuser']);
        } else {
            header("location: users.php?aktie=disp");
        }
    }
}

// ------------------------------------------------------------------------------------------------------
// START Dit wordt uitgevoerd wanneer de user op Usermanagement heeft geklikt
// Er wordt een lijst met de users getoond
// ------------------------------------------------------------------------------------------------------
if ($aktie == 'disp') {
    $sql_code = "SELECT * FROM users
                 ORDER BY achternaam";
    $sql_out = mysqli_query($dbconn, $sql_code);
    echo '<center><table>';
    echo '<tr><th>Naam medewerker</th>
          <th>Username</th>
          <th>Emailadres</th>
          <th>last logged in</th>
          <th>Admin</th>
          <th>In dienst</th>
          <th><center>Mag<br />approven</center></th>
          <th><center>Uren<br />invullen</center></th>
          <th colspan="4"><center>Akties</center></th></tr>';
    $rowcolor = 'row-a';

    while ($sql_rows = mysqli_fetch_array($sql_out)) {
        $id = $sql_rows['ID'];
        $username = $sql_rows['username'];
        $voornaam = $sql_rows['voornaam'];
        $tussenvoegsel = $sql_rows['tussenvoegsel'];
        $achternaam = $sql_rows['achternaam'];
        $emailadres = $sql_rows['emailadres'];
        $lastloggedin = $sql_rows['lastloggedin'];
        $admin = $sql_rows['admin'];
        $uren_invullen = $sql_rows['uren_invullen'];
        $indienst = $sql_rows['indienst'];
        $approvenallowed = $sql_rows['approvenallowed'];

        echo '<tr class="' . $rowcolor . '">
			<td>' . $achternaam . ', ' . $voornaam . ' ' . $tussenvoegsel . '</td>
            <td>' . $username . '</td>
			<td>' . $emailadres . '</td>
            <td>' . $lastloggedin . '</td>';
        if ($admin == 1) {
            echo '<td style="text-align:center;"><img class="button" src="./img/icons/checkmark-32.png" alt="1" title="heeft adminrechten" /></td>';
        } else {
            echo '<td style="text-align:center;"></td>';
        }

        if ($indienst == 1) {
            echo '<td style="text-align:center;"><img class="button" src="./img/icons/checkmark-32.png" alt="1" title="medewerker is nog in dienst" /></td>';
        } else {
            echo '<td style="text-align:center;"></td>';
        }

        if ($approvenallowed == 1) {
            echo '<td style="text-align:center;"><img class="button" src="./img/icons/checkmark-32.png" alt="1" title="medewerker heeft rechten om uren te approven" /></td>';
        } else {
            echo '<td style="text-align:center;"></td>';
        }

        if ($uren_invullen == 1) {
            echo '<td style="text-align:center;"><img class="button" src="./img/icons/checkmark-32.png" alt="1" title="medewerker is verplicht uren in te invullen" /></td>';
        } else {
            echo '<td style="text-align:center;"></td>';
        }

        $fullname = $voornaam . ' ' . $tussenvoegsel . ' ' . $achternaam;
        $username_encrypted = convert_string('encrypt', $username);
        $fullname_encrypted = convert_string('encrypt', $fullname);

        echo '<td><a href="users.php?aktie=edit&edtuser=' . $username_encrypted . '"><img class="button" src="./img/icons/edit-48.png" alt="wijzigen user" title="wijzig user ' . $username . '" /></a></td>';
        echo '<td><a href="users.php?aktie=delete&edtuser=' . $username_encrypted . '"><img class="button" src="./img/icons/trash-48.png" alt="delete user" title="delete user ' . $username . '" /></a></td>';
        echo '<td><a href="rpt_uren_urensoort.php?username=' . $username_encrypted . '&fullname=' . $fullname_encrypted . '"><img class="button" src="./img/icons/stopwatch-48.png" alt="jaaroverzicht" title="display jaaroverzicht per urensoort voor ' . $username . '" /></a></td>';
        echo '<td><a href="add_user.php"><img class="button" src="./img/icons/add-48.png" alt="toevoegen nieuwe user" title="toevoegen nieuwe user" /></a></td>';
        echo '</tr>';

        check_row_color($rowcolor);
    }
    echo "</table></center>";
}

// ------------------------------------------------------------------------------------------------------
// Wordt uitgevoerd wanneer men op de button klikt om te wijzigen of te deleten of om het eigen
// profiel aan te passen
// ------------------------------------------------------------------------------------------------------
if ($aktie == 'edit' || $aktie == 'delete' || $aktie == 'editprof') {
    $edtuser = convert_string('decrypt', $_SESSION['edtuser']);
    if ($edtuser == '') {
        writelog("users", "ERROR", " Men heeft geprobeerd om username handmatig aan te passen in de url: " . $_GET['edtuser']);
        exit("Je hebt geprobeerd om username handmatig aan te passen in de url");
    }
    $focus = "pass";
    $sql_code = "SELECT * FROM users
                 WHERE username = '$edtuser'";
    $sql_out = mysqli_query($dbconn, $sql_code);
    if (! $sql_out) {
        writelog("users", "ERROR", "Er is een fout opgetreden bij het selecteren van users -> " . mysqli_error($dbconn));
    }

    while ($sql_rows = mysqli_fetch_array($sql_out)) {
        $frm_username = $sql_rows['username'];
        $frm_voornaam = $sql_rows['voornaam'];
        $frm_tussenvoegsel = $sql_rows['tussenvoegsel'];
        $frm_achternaam = $sql_rows['achternaam'];
        $frm_email = $sql_rows['emailadres'];

        if ($sql_rows['admin'] == 1) {
            $frm_admin = $sql_rows['admin'];
            $frm_admin_checked = "checked";
        } else {
            $frm_admin = 0;
            $frm_admin_checked = "";
        }

        if ($sql_rows['uren_invullen'] == 1) {
            $frm_uren_invullen = $sql_rows['uren_invullen'];
            $frm_uren_invullen_checked = "checked";
        } else {
            $frm_uren_invullen = 0;
            $frm_uren_invullen_checked = "";
        }

        $frm_indienst = $sql_rows['indienst'];
        if ($sql_rows['indienst'] == 1) {
            $frm_indienst_checked = "checked";
        } else {
            $frm_indienst_checked = "";
        }

        $frm_approvenallowed = $sql_rows['approvenallowed'];
        if ($sql_rows['approvenallowed'] == 1) {
            $frm_approvenallowed_checked = "checked";
        } else {
            $frm_approvenallowed_checked = "";
        }
    }
    ?>
	<form name="AddUser" action="<?php echo $_SERVER['PHP_SELF']; ?>"
		method="post">
		<p>
		
		
		<table>
			<tr>
				<td><b>Username</b></td>
				<td><input
					<?php if ($aktie == "edit" || $aktie == "editprof") { echo "readonly"; } ?>
					type="text" name="username" maxlength="40"
					value="<?php if (isset($frm_username)) echo $frm_username;?>"></td>
			</tr>
			<tr>
				<td>Wachtwoord</td>
				<td><input type="password" name="pass" maxlength="32"
					value="<?php if (isset($frm_pass)) echo $frm_pass;?>"></td>
				<td>Confirm</td>
				<td><input type="password" name="pass2" maxlength="32"
					value="<?php if (isset($frm_pass2)) echo $frm_pass2;?>"></td>
			</tr>
			<tr>
				<td>Admin</td>
				<td><input type="checkbox"
					<?php if (!$_SESSION['admin']) { echo "readonly "; } ?>
					name="admin" value=<?php if (isset($frm_admin)) echo $frm_admin;?>
					<?php echo $frm_admin_checked;?>></td>

			</tr>
			<tr>
				<td>Approven</td>
				<td><input type="checkbox"
					<?php if (!$_SESSION['admin']) { echo "readonly "; } ?>
					name="approvenallowed"
					value=<?php if (isset($frm_approvenallowed)) echo $frm_approvenallowed;?>
					<?php echo $frm_approvenallowed_checked;?>></td>


			</tr>
			<tr>
				<td>Voornaam</td>
				<td><input type="text" name="voornaam" maxlength="24"
					value="<?php if (isset($frm_voornaam)) { echo $frm_voornaam; } ?>"></td>
			</tr>
			<tr>
				<td>Tussenv.</td>
				<td><input type="text" name="tussenvoegsel" maxlength="10"
					value="<?php if (isset($frm_tussenvoegsel)) echo $frm_tussenvoegsel;?>"></td>
				<td>Achternaam</td>
				<td><input type="text" name="achternaam" maxlength="40"
					value="<?php if (isset($frm_achternaam)) echo $frm_achternaam;?>"></td>
			</tr>
			<tr>
				<td>Email</td>
				<td colspan="2"><input type="text" name="email" size="40"
					maxlength="60"
					value="<?php if (isset($frm_email)) echo $frm_email;?>"></td>
			</tr>
			<tr>
				<td>In dienst</td>
				<td><input type="checkbox"
					<?php if (!$_SESSION['admin']) echo "readonly ";?> name="indienst"
					value=<?php if (isset($frm_indienst)) echo $frm_indienst;?>
					<?php echo $frm_indienst_checked;?>></td>
			</tr>
			<tr>
				<td>Uren invullen</td>
				<td><input type="checkbox"
					<?php if (!$_SESSION['admin']) echo "readonly "; ?>
					name="uren_invullen"
					value=<?php if (isset($frm_uren_invullen)) echo $frm_uren_invullen; ?>
					<?php echo $frm_uren_invullen_checked;?>></td>

			</tr>
		</table>
		<br />
		<?php
    if ($aktie == 'edit' || $aktie == 'editprof') {
        echo '<input class="button" type="submit" name="save" value="save">';
    }
    if ($aktie == 'delete') {
        echo '<input class="button" type="submit" name="delete" value="delete" onClick="return confirmDelUser()">';
    }
    ?>
		<input class="button" type="submit" name="cancel" value="cancel">
		</p>
	</form>
	<br />		
	<?php
    if (! isset($focus)) 
    {
    	$focus='username';
    }
    setfocus('AddUser', $focus);
}
	
include ("footer.php");
?>		

