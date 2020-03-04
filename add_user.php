<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include ("config.php");
include ("mysqli_connect.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

check_admin(); // Controleren of gebruiker admin-rechten heeft
check_cookies(); // Controleren of cookie aanwezig is. Zo niet, login-scherm displayen

include ("header.php");

?>
<div id="main">
	<h1>Toevoegen nieuwe user</h1>
			
<?php
// -------------------------------------------------------------------------
// This code runs if the form has been submitted
// -------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------------
// BUTTON Cancel
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
    header("location: users.php?aktie=disp");
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Submit
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['submit'])) {
    form_user_fill('toevoegen');
    $formerror = 0;

    if (! $_POST['username']) {
        echo '<p class="errmsg"> ERROR: Username is een verplicht veld</p>';
        $focus = 'username';
        $formerror = 1;
    }

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

    if ($_SESSION['admin']) {
        if (! isset($_POST['admin'])) {
            $_POST['admin'] = 0;
        } else {
            $_POST['admin'] = 1;
        }

        if (! isset($_POST['approvenallowed'])) {
            $_POST['approvenallowed'] = 0;
        } else {
            $_POST['approvenallowed'] = 1;
        }

        if (! isset($_POST['indienst'])) {
            $_POST['indienst'] = 0;
        } else {
            $_POST['indienst'] = 1;
        }

        if (! isset($_POST['uren_invullen'])) {
            $_POST['uren_invullen'] = 0;
        } else {
            $_POST['uren_invullen'] = 1;
        }
    }

    // Controleer of de username al in gebruik is
    if (! get_magic_quotes_gpc()) {
        $_POST['username'] = addslashes($_POST['username']);
    }

    $usercheck = $_POST['username'];
    $sql_code = "SELECT username FROM users
                 WHERE username = '$usercheck'";
    $sql_out = mysqli_query($dbconn, $sql_code);
    $sql_num_rows = mysqli_num_rows($sql_out);

    // Als username al bestaat een error displayen
    if ($sql_num_rows != 0) {
        writelog("add_user", "WARN", "Er is geprobeerd user {$_POST['username']} aan te maken terwijl deze al bestaat");

        echo '<p class="errmsg"> ERROR: Username ' . $_POST['username'] . ' is al aanwezig.</p>';
        $focus = 'username';
        $formerror = 1;
    }

    // Controle of beide ingevoerde passwords gelijk zijn
    if (($_POST['pass'] != $_POST['pass2']) && (! $formerror)) {
        writelog("add_user", "WARN", "De ingevoerde wachtwoorden zijn niet gelijk");

        echo '<p class="errmsg"> ERROR: De wachtwoorden zijn niet gelijk</p>';
        $focus = 'pass2';
        $formerror = 1;
    }

    if (! $_POST['voornaam'] && (! $formerror)) {
        echo '<p class="errmsg"> ERROR: Voornaam is een verplicht veld</p>';
        $focus = 'voornaam';
        $formerror = 1;
    }

    if (! $_POST['achternaam'] && (! $formerror)) {
        echo '<p class="errmsg"> ERROR: Achternaam is een verplicht veld</p>';
        $focus = 'achternaam';
        $formerror = 1;
    }

    if (! $_POST['email'] && (! $formerror)) {
        echo '<p class="errmsg"> ERROR: Email is een verplicht veld</p>';
        $focus = 'email';
        $formerror = 1;
    }
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
        echo '<blockquote class="error">ERROR: Dit is geen geldig emailadres</blockquote>';
        $focus = 'email';
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
       
        
        $jaar = date('Y');
        try {
            $stmt_ins_saldo = $mysqli->prepare("INSERT INTO beginsaldo (username, jaar, beginsaldo) VALUES (?, ?, ?)");
            $stmt_ins_saldo->bind_param("ssi", $_POST['username'], $jaar, $_POST['beginsaldo']);
            $stmt_ins_saldo->execute();
        } catch(Exception $e) {
            writelog("add_user", "ERROR", $e);
            exit($MSGDB001E);
        }

        // Record toevoegen in database

        try {
            $stmt_ins_user = $mysqli->prepare("INSERT INTO users (username, password, admin, voornaam, tussenvoegsel, achternaam, emailadres, indienst, approvenallowed, uren_invullen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_ins_user->bind_param("ssissssiii", $_POST['username'], $_POST['pass'], $_POST['admin'], $_POST['voornaam'], $_POST['tussenvoegsel'], $_POST['achternaam'], $_POST['email'], $_POST['indienst'], $_POST['approvenallowed'], $_POST['uren_invullen']);
            $stmt_ins_user->execute();
        } catch(Exception $e) {
            writelog("add_user", "ERROR", $e);
            exit($MSGDB001E);
        }
                    
        writelog("add_user", "INFO", "User {$_POST['username']} is succesvol aangemaakt");
        header("location: users.php?aktie=disp");

        $mail_to = $frm_email;
        $mail_subject = 'Welkom op Mirage Urenregistratie Systeem';

        // Aanmaken email headers
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: ' . $mail_from . "\r\n" . 'CC: ' . $mail_CC . "\r\n" . 'Reply-To: ' . $mail_from . "\r\n" . 'X-Mailer: PHP/' . phpversion();

        // Creeeren van de email message
        mail_message_header();
        $message .= '<h1>Welkom ' . $frm_voornaam . '</h1>';
        $message .= '<p>Er is voor jou een userid opgenomen in Mirage Urenregistratie Systeem<br />';
        $message .= 'Deze urenregistratie dien je vanaf nu te gebruiken voor het doorgeven van je uren.<br />';
        $message .= 'Met deze urenregistratie heb je zelf ook meer inzicht in je geboekte uren en je opgenomen verlofuren.<br />';
        $message .= 'Ga naar http://' . $_SERVER['SERVER_NAME'] . ' om in te loggen met onderstaande gegevens</p>';
        $message .= '<table id="mail">';
        $message .= '<tr><td>Username<br />Wachtwoord<br />Volledige naam<br />Emailadres</td><td>' . $frm_username . '<br />'. $decrypted_pass . '<br />'. $frm_voornaam . ' ' . $frm_tussenvoegsel . ' ' . $frm_achternaam .'<br />'. $frm_email .'</td></tr>';
        $message .= '</table>';
        $message .= '<br /><lu>';
        $message .= 'Het wachtwoord dient aan de volgende eisen te voldoen:';
        $message .= '<li>Moet minimaal 8 characters lang zijn.</li>';
        $message .= '<li>Moet minimaal 1 lower case character bevatten</li>';
        $message .= '<li>Moet minimaal 1 upper case character bevatten</li>';
        $message .= '<li>Moet minimaal 1 special character bevatten</li></lu>';
        $message .= '<p>Heb je nog vragen en/of opmerkingen laat het ons weten.</p>';
        mail_message_footer($message);

        // Versturen van de email
        if (mail($mail_to, $mail_subject, $message, $headers)) {
            echo '<blockquote>De mail is succesvol verstuurd.</blockquote>';
            writelog("add_user", "INFO", "Mail succesvol verstuurd naar " . $mail_to . " ivm aanmaken userid " . $frm_username);
        } else {
            echo '<blockquote class="errmsg">Het was niet mogelijk om de mail te versturen. Probeer het nogmaals.</blockquote>';
            writelog("add_user", "ERROR", "Het is niet gelukt om een mail te versturen naar " . $mail_to . " ivm aanmaken userid " . $frm_username);
        }
    }
}

?>
 
<form name="AddUser" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>
<table>
<tr>
	<td><b>Username:</b></td>
	<td><input type="text" name="username" maxlength="40" value="<?php if (isset($frm_username)) { echo $frm_username; } ?>"></td>
</tr>
<tr>
	<td>Wachtwoord</td>
	<td><input type="password" name="pass" maxlength="32" value="<?php if (isset($frm_pass)) { echo $frm_pass; } ?>"></td>
	<td>Confirm</td>
	<td><input type="password" name="pass2" maxlength="32" value="<?php if (isset($frm_pass2)) { echo $frm_pass2; } ?>"></td>
</tr>
<tr>
	<td>Admin</td>
	<td><input type="checkbox" name="admin"></td>

</tr>
<tr>
	<td>Approven</td>
	<td><input type="checkbox" name="approvenallowed"></td>
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
	<td colspan="2"><input type="email" name="email" size="40" maxlength="60" value="<?php if (isset($frm_email)) { echo $frm_email; } ?>"></td>
</tr>
<tr>
	<td>In dienst</td>
	<td><input type="checkbox" name="indienst"></td>
</tr>
<tr>
	<td>Uren invullen</td>
	<td><input type="checkbox" name="uren_invullen"></td>
</tr>
<tr>
	<td>Beginsaldo verlofuren</td>
	<td><input style="width:2.3vw; text-align:right" type="number" name="beginsaldo" value=240></td>
</tr>
</table>
<br /> 
<input class="button" type="submit" name="submit" value="add user"> 
<input class="button" type="submit" name="cancel" value="cancel"> 
<input type="hidden" name="indienst">
</p>
</form>
<br />		
<?php
if (! isset($focus)) 
{
	$focus='username';
}
setfocus('AddUser', $focus);
	
include ("footer.php");
?>		
