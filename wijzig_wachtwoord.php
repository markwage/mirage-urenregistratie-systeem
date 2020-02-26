<?php
session_start();
include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

//check_admin(); // Controleren of gebruiker admin-rechten heeft
//check_cookies(); // Controleren of cookie aanwezig is. Zo niet, login-scherm displayen
include ("header_first_logon.php");

?>
<div id="main">
<h1>Wijzigen wachtwoord</h1>
			
<?php
// -------------------------------------------------------------------------
// This code runs if the form has been submitted
// -------------------------------------------------------------------------
if (isset($_POST['submit'])) {
    $formerror = 0;

    if (! $_POST['pass'] && (! $formerror)) {
        echo '<p class="errmsg"> ERROR: Password is een verplicht veld</p>';
        $focus = 'pass';
        $formerror = 1;
    }

    if (! $_POST['pass2'] && (! $formerror)) {
        echo '<p class="errmsg"> ERROR: Password voor verificatie is een verplicht veld</p>';
        $focus = 'pass2';
        $formerror = 1;
    }

    // Controle of beide ingevoerde passwords gelijk zijn
    if (($_POST['pass'] != $_POST['pass2']) && (! $formerror)) {
        writelog("add_user", "WARN", "De ingevoerde wachtwoorden zijn niet gelijk");
        echo '<p class="errmsg"> ERROR: De wachtwoorden zijn niet gelijk</p>';
        $focus = 'pass2';
        $formerror = 1;
    }
    
    // Validate password strength
    $uppercase = preg_match('@[A-Z]@', $_POST['pass']);
    $lowercase = preg_match('@[a-z]@', $_POST['pass']);
    $number    = preg_match('@[0-9]@', $_POST['pass']);
    $specialChars = preg_match('@[^\w]@', $_POST['pass']);
    
    if((!$uppercase || !$lowercase || !$number || !$specialChars || strlen($_POST['pass']) < 8) && (!$formerror)) {
        echo '<blockquote class="error">ERROR: Het wachtwoord voldoet niet aan de volgende eisen:<br />
                  - Moet minimaal 8 characters lang zijn.<br />
                  - Moet minimaal 1 lower case character bevatten.<br />
                  - Moet minimaal 1 upper case character bevatten.<br />
                  - Moet minimaal 1 special character bevatten</blockquote>';
        $focus = 'pass';
        $formerror = 1;
    }

    // Encrypt password en voeg eventueel slashes toe
    if (! $formerror) {
        $decrypted_pass = $_POST['pass'];
        $_POST['pass'] = md5($_POST['pass']);

        if (! get_magic_quotes_gpc()) {
            $_POST['pass'] = addslashes($_POST['pass']);
            $_POST['username'] = addslashes($_POST['username']);
        }
        
        // Wijzig wachtwoord
        $sql_code = "UPDATE users SET password = '" . $_POST['pass'] . "' WHERE username = '".$_SESSION['username'] . "'";
        $sql_out = mysqli_query($dbconn, $sql_code);

        if ($sql_out) {
            writelog("wijzig_wachtwoord", "INFO", "User {$_POST['username']} heeft succesvol zijn wachtwoord gewijzigd");
            // update lastloggedin in de tabel
            date_default_timezone_set('Europe/Amsterdam');
            $sql_code = "UPDATE users SET lastloggedin = '" . date('Y-m-d H:i:s') . "',
                         wrong_password_count = 0
				         WHERE username = '" . $_POST['username'] . "'";
            $sql_out = mysqli_query($dbconn, $sql_code);
            if (!$sql_out) {
                writelog("login", "ERROR", "Update users gaat fout: " . $sql_code . " - " . mysqli_error($dbconn));
                exit($MSGDB001E);
            }
            header("location: logout.php");
        } else {
            writelog("wijzig_wachtwoord", "ERROR", "Er is een fout opgetreden bij het wijzigen van het wachtwoord - " . mysqli_error($dbconn));
            exit($MSGDB001E);
        }
    }
}
writedebug("Net voor het begin van het formulier");
?>
 
<form name="wijzigWachtwoord" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>
Omdat je wachtwoord niet door jezelf is ingevoerd dien je het wachtwoord te wijzigen.<br />
Wanneer je je wachtwoord hebt gewijzigd dien je opnieuw in te loggen met het nieuwe wachtwoord.<br /><br />
Het wachtwoord dient aan de volgende eisen te voldoen:
<ul>
<li>Moet minimaal 8 characters lang zijn</li>
<li>Moet minimaal 1 lower case character bevatten</li>
<li>Moet minimaal 1 upper case character bevatten</li>
<li>Moet minimaal 1 special character bevatten</li>
</ul>
<br />
<table>
	<tr>
		<td><b>Username:</b></td>
		<td><input type="text" readonly name="username" maxlength="40" value="<?php echo $_SESSION['username']; ?>"></td>
	</tr>
	<tr>
		<td>Wachtwoord</td>
		<td><input type="password" name="pass" maxlength="32" value=""></td>
		<td>Confirm</td>
		<td><input type="password" name="pass2" maxlength="32" value=""></td>
	</tr>
</table>
<br /><input class="button" type="submit" name="submit" value="wijzig"> 
<input type="hidden" name="indienst">
</p>
</form>
<br />		
<?php
if (! isset($focus)) 
{
	$focus='pass';
}
setfocus('wijzigWachtwoord', $focus);
	
include ("footer.php");
?>		
