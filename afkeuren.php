<?php
session_start();
include ("config.php");
include ("db.php");
include ("mysqli_connect.php");
include ("function.php");
include ("autoload.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['username'])) {
    $username = convert_string('decrypt', $_GET['username']);
} else {
    $username = "";
}
if (isset($_GET['jaar'])) {
    $jaar = $_GET['jaar'];
} else {
    $jaar = "";
}
if (isset($_GET['maand'])) {
    $maand = $_GET['maand'];
} else {
    $maand = "";
}

check_admin(); // Controleren of gebruiker admin-rechten heeft
check_cookies(); // Controleren of cookie aanwezig is. Zo niet, login-scherm displayen

include ("header.php");

?>
<div id="main">
<h1>Afkeuren maandstaat</h1>
			
<?php
// -------------------------------------------------------------------------
// This code runs if the form has been submitted
// -------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------------
// BUTTON Cancel
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
    header("location: approve.php?aktie=disp");
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Submit
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['submit'])) {
    // Ophalen gegevens user
    $formerror = 0;
    if (! $_POST['reden'] && (! $formerror)) {
        echo '<p class="errmsg"> ERROR: Reden van afkeuring is een verplicht veld</p>';
        $focus = 'reden';
        $formerror = 1;
    }

    // Encrypt password en voeg eventueel slashes toe
    if (! $formerror) {
        $mail_to = $_POST['emailadres'];
        $mail_subject = 'AFGEKEURD! Je uren over '.$_POST['jaar'].' - '.$_POST['maand'].' zijn afgekeurd';

        // Aanmaken email headers
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: ' . $mail_from . "\r\n" . 'CC: ' . $_SESSION['emailadres'] . "\r\n" . 'Reply-To: ' . $_SESSION['emailadres'] . "\r\n" . 'X-Mailer: PHP/' . phpversion();

        // Creeeren van de email message
        mail_message_header();
        $message .= '<h1>Beste ' . $_POST['voornaam'] . '</h1>';
        $message .= '<p>Je uren betreffende maand <strong>'.$_POST['jaar'].' - '.$_POST['maand'].'</strong> zijn afgekeurd!</p>';
        $message .= '<p>De reden voor de afkeuring:<br />';
        $message .= nl2br($_POST['reden']).'</p>';
        mail_message_footer($message);

        // Versturen van de email
        if (mail($mail_to, $mail_subject, $message, $headers)) {
            echo '<blockquote>De mail is succesvol verstuurd.</blockquote>';
            writelog("add_user", "INFO", "Mail succesvol verstuurd naar " . $mail_to . " ivm afkeuren maandstaat");
        } else {
            echo '<blockquote class="errmsg">Het was niet mogelijk om de mail te versturen. Probeer het nogmaals.</blockquote>';
            writelog("add_user", "ERROR", "Het is niet gelukt om een mail te versturen naar " . $mail_to);
        }
    }
    
    $approveddatum = date('Y-m-d');
    $approved = 9;
    try {
        $stmt_upd = $mysqli->prepare("UPDATE uren SET approveddatum = ?, approvedbyuser = ?, approved = ? WHERE user = ? AND MONTH(datum) = ? AND YEAR(datum) = ?");
        $stmt_upd->bind_param("ssisii", $approveddatum, $_SESSION['username'], $approved, $_POST['username'], $_POST['maand'], $_POST['jaar']);
        $stmt_upd->execute();
    } catch(Exception $e) {
        writelog("afkeuren", "ERROR", $e);
        exit($MSGDB001E);
    }
    header("location: approve.php?aktie=disp");
}

// -------------------------------------------------------------------------
// Ophalen gegevens en display formulier
// -------------------------------------------------------------------------
try {
    $stmt_user = $mysqli->prepare("SELECT voornaam, tussenvoegsel, achternaam, emailadres FROM users WHERE username = ?");
    $stmt_user->bind_param("s", $username);
    $stmt_user->execute();
} catch(Exception $e) {
    writelog("afkeuren", "ERROR", $e);
    exit($MSGDB001E);
}
$stmt_user->bind_result($frm_voornaam, $frm_tussenvoegsel, $frm_achternaam, $frm_emailadres);
$stmt_user->fetch();
?>
 
<form name="afkeuren" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p style="text-align:center;">Geef een reden van afkeuren op. Er zal een mail naar de medewerker gestuurd worden dat zijn uren afgekeurd zijn en wat daar de reden van is.</p>
<p style="text-align:center;">Betreft maand<strong> <?php echo $jaar." - ".$maand; ?></strong> van <strong><?php echo $frm_voornaam." ".$frm_tussenvoegsel." ".$frm_achternaam;  ?></strong></p>
<table style="margin: 0px auto;">
	<tr>
		<td><strong>Reden van afkeuren</strong></td>
	</tr>
	<tr>
		<td><textarea id="reden" name="reden" rows="12" cols="80"></textarea></td>
	</tr>
</table>
<br /> 
<input class="button" type="submit" name="submit" value="submit"> 
<input class="button" type="submit" name="cancel" value="cancel"> 

<input type="hidden" name="maand" value="<?php if (isset($maand)) { echo $maand; } ?>"> 
<input type="hidden" name="jaar" value="<?php if (isset($jaar)) { echo $jaar; } ?>"> 
<input type="hidden" name="voornaam" value="<?php if (isset($frm_voornaam)) { echo $frm_voornaam; } ?>">
<input type="hidden" name="username" value="<?php if (isset($username)) { echo $username; } ?>"> 
<input type="hidden" name="emailadres" value="<?php if (isset($frm_emailadres)) { echo $frm_emailadres; } ?>"> 

</form>
<br />
<?php
if (! isset($focus)) 
{
	$focus='reden';
}
setfocus('afkeuren', $focus);
	
include ("footer.php");
?>		
