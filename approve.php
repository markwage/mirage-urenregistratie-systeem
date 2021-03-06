<?php
session_start();

include ("config.php");
include ("db.php");
include ("mysqli_connect.php");
include ("function.php");
include ("autoload.php");

if (isset($_GET['aktie'])) {
    $aktie = $_GET['aktie'];
} else {
    $aktie = "";
}

check_admin(); // Controleren of gebruiker admin-rechten heeft
check_cookies(); // Controleren of cookie aanwezig is. Zo niet, login-scherm displayen

include ("header.php");

?>
<div id="main">
	<h1>Openstaande approvals afgelopen maand</h1>
			
<?php

// ------------------------------------------------------------------------------------------------------
//
// ******************* SUBMITTED *******************
//
// From here this code runs if the form has been submitted
// ------------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------------
// BUTTON Cancel
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
    header("location: approve.php?aktie=disp");
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Disapprove
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['afkeuren'])) {
    $encrypted_username = convert_string('encrypt', $_POST['username']);
    $maand = $_POST['maand'];
    $jaar = $_POST['jaar'];
    $emailadres = $_POST['emailadres'];
    header("location: afkeuren.php?username=".$encrypted_username."&jaar=".$_POST['jaar']."&maand=".$_POST['maand']);
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Approve
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['approve'])) {
    $approved_by_user = $_SESSION['username'];
    $username = $_POST['username'];
    $maand = $_POST['maand'];
    $jaar = $_POST['jaar'];
    $emailadres = $_POST['emailadres'];
    $approveddatum = date('Y-m-d');
    $approved = 1;
    
    try {
        $stmt_upd = $mysqli->prepare("UPDATE uren SET approveddatum = ?, approvedbyuser = ?, approved = ? WHERE user = ? AND MONTH(datum) = ? AND YEAR(datum) = ?");
        $stmt_upd->bind_param("ssisii", $approveddatum, $approved_by_user, $approved, $username, $maand, $jaar);
        $stmt_upd->execute();
    } catch(Exception $e) {
        writelog("approve", "ERROR", $e);
        exit($MSGDB001E);
    }

    writelog("approve", "INFO", "Voor user {$username} is maand {$jaar}-{$maand} approved");
    
    try {
        $stmt_ins = $mysqli->prepare("INSERT INTO approvals (maand, jaar, user) VALUES (?, ?, ?)");
        $stmt_ins->bind_param("iis", $maand, $jaar, $username);
        $stmt_ins->execute();
    } catch(Exception $e) {
        writelog("approve", "ERROR", $e);
        exit($MSGDB001E);
    }
    writelog("approve", "INFO", "Record succesvol toegevoegd in tabel approvals voor user {$username} periode {$jaar}-{$maand}");

    // Versturen mail naar user dat de maand approved is
    $mail_to = $emailadres;
    $mail_subject = 'Uren van ' . $jaar . '-' . $maand . ' zijn approved';

    // Aanmaken email headers
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: ' . $mail_from . "\r\n" . 'CC: ' . $mail_CC . "\r\n" . 'Reply-To: ' . $mail_from . "\r\n" . 'X-Mailer: PHP/' . phpversion();

    // Creeeren van de email message
    mail_message_header();
    $message .= '<p>De uren van gebruiker ' . $username . ' betreffende maand <strong>' . $jaar . '-' . $maand . '</strong> zijn approved in Mirage Urenregistratie Systeem<br />Approved door: ' . $approved_by_user . '</p>';

    // Display totaal per soortuur
    $message .= '<p><strong>Totaal per urensoort</strong></p>';
    $message .= '<table>';
    $message .= '<tr><th colspan="2" style="text-align:left">Soort uur</th><th style="text-align:right">Totaal</th></tr>';

    try {
        $stmt_sel_soort = $mysqli->prepare("SELECT sum(uren) AS toturen, soortuur, omschrijving FROM view_uren_soortuur WHERE user = ? AND approval_maand = ? AND approval_jaar = ? GROUP BY soortuur ORDER BY soortuur");
        $stmt_sel_soort->bind_param("sii", $username, $maand, $jaar);
        $stmt_sel_soort->execute();
    } catch(Exception $e) {
        writelog("beginsaldo", "ERROR", $e);
        exit($MSGDB001E);
    }
    $stmt_sel_soort->bind_result($totaal_uren, $soortuur, $omschrijving);
    while($stmt_sel_soort->fetch()) {
        $message .= '<tr class="colored"><td>' . $soortuur . '</td><td>' . $omschrijving . '</td><td style=\'text-align:right\'><strong>' . $totaal_uren . '</strong></td></tr>';
    }

    $message .= '</table>';
    mail_message_footer($message);
    // Versturen van de email
    if ($_SERVER['SERVER_NAME'] != 'localhost') {
        if (mail($mail_to, $mail_subject, $message, $headers)) {
            writelog("approve", "INFO", "Mail succesvol verstuurd naar " . $mail_to . " ivm approven uren user " . $_POST['username']);
        } else {
            echo '<blockquote class="errmsg">Het was niet mogelijk om de mail te versturen. Probeer het nogmaals.</blockquote>';
            writelog("approve", "ERROR", "Het is niet gelukt om een mail te versturen naar " . $mail_to . " ivm approven uren" . $_POST['username']);
        }
    }

    header("location: approve.php?aktie=disp");
}

// ------------------------------------------------------------------------------------------------------
//
// ******************* START *******************
//
// Dit wordt uitgevoerd wanneer de gebruiker in linkermenu op "Openstaande approvals" klikt
// Alles wordt van de laatste maand getoond behalve wat nog openstaat. Daarvan wordt alles getoond
// ------------------------------------------------------------------------------------------------------
if ($aktie == 'disp') {
    ?> 
    <form name="disp" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"> 
    <?php 
    
    echo "<center><table>";
    echo "<tr><th>Medewerker</th><th>Maand</th><th></th></tr>";
    
    $vorig_jaar = date('Y', strtotime('-1 month', time()));
    $vorige_maand = date('m', strtotime('-1 month', time()));
    $huidige_maand = date('m');

    try {
        $stmt_maanden = $mysqli->prepare("SELECT approved, username, approval_maand, approval_jaar, fullname FROM view_uren_get_full_username WHERE uren_invullen = 1 GROUP BY username, approval_jaar, approval_maand ORDER BY fullname, approval_jaar, approval_maand");
        $stmt_maanden->execute();
    } catch(Exception $e) {
        writelog("approve", "ERROR", $e);
        exit($MSGDB001E);
    }
    
    $stmt_maanden->bind_result($approved, $username, $maand, $jaar, $fullname);
    while($stmt_maanden->fetch()) { 
        $encrypted_username = convert_string('encrypt', $username);

        // Maanden die nog niet approved zijn
        if (($jaar != '') && ($approved == 0) && ($maand != $huidige_maand)) {
            echo '<tr class="colored" style="font-size:0.8vw">
            <td style="height:1.2vw;"><b>' . $fullname . '</b></td><td style=\'text-align:center\'>' . $jaar . ' ' . $maand . '</td>
			<td><a href="approve.php?aktie=dspuren&user=' . $encrypted_username . '&jaar=' . $jaar . '&maand=' . $maand . '"><img class="button" src="./img/icons/view-48.png" alt="Toon week" title="Toon/approve de uren van deze maand" /></a></td>
			</tr>';
        // Indien voor afgelopen maand geen uren ingeleverd
        } elseif ($jaar == '') {
            echo '<tr class="colored" style="font-size:0.8vw">
            <td style="height:1.2vw;"><b>' . $fullname . '</b></td><td style=\'text-align:center\'>' . $jaar . ' ' . $maand . '</td>
			<td>Nog geen gegevens over afgelopen maand aanwezig</td>
			</tr>';
        // Uren van afgelopen maand die approved zijn
        } elseif (($approved == 1) && ($maand == $vorige_maand) && ($jaar == $vorig_jaar)) {
            echo '<tr class="colored" style="font-size:0.8vw">
            <td style="height:1.2vw;"><b>' . $fullname . '</b></td><td style=\'text-align:center\'>' . $jaar . ' ' . $maand . '</td>
			<td>Approved</td>
			</tr>';
        // Uren welke afgekeurd zijn ongeacht de maand
        } elseif ($approved == 9) {
            echo '<tr class="colored" style="font-size:0.8vw">
            <td style="height:1.2vw;"><b>' . $fullname . '</b></td><td style=\'text-align:center\'>' . $jaar . ' ' . $maand . '</td>
			<td style="color:red"><a href="approve.php?aktie=dspuren&user=' . $encrypted_username . '&jaar=' . $jaar . '&maand=' . $maand . '"><img class="button" src="./img/icons/view-48.png" alt="Toon week" title="Toon/approve de uren van deze maand" /></a> AFGEKEURD! </td>
			</tr>';
        }
    }
    echo "</table></center>";
    echo "</form>";
}

// ------------------------------------------------------------------------------------------------------
// Wordt uitgevoerd wanneer men op de button klikt om uren van die user / maand te displayen
// ------------------------------------------------------------------------------------------------------
if ($aktie == 'dspuren') {
    ?> 
    <form name="disp" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"> 
    <?php
    $username = convert_string('decrypt',$_GET['user']);
    $maand = $_GET['maand'];
    $jaar = $_GET['jaar'];
    try {
        $stmt_user = $mysqli->prepare("SELECT voornaam, tussenvoegsel, achternaam, emailadres FROM users WHERE username = ?");
        $stmt_user->bind_param("s", $username);
        $stmt_user->execute();
        $stmt_user->store_result();
    } catch(Exception $e) {
        writelog("approve", "ERROR", $e);
        exit($MSGDB001E);
    }
    $stmt_user->bind_result($voornaam, $tussenvoegsel, $achternaam, $emailadres);
    $stmt_user->fetch();

    echo "<center><b>Maand: </b>" . $jaar . " " . $maand . "<br /><b>Medewerker: </b>" . $voornaam . " " . $tussenvoegsel . " " . $achternaam . " ";
    echo "<h3>Overzicht per dag</h3>";
    
    echo '<div id="approve">';
    echo "<center><table>";
    echo "<tr style='font-size:0.8vw'>";
    echo '<th style="width:10.25vw;">Soortuur</th>';
    for($ix=1; $ix<32; $ix++) {
        echo '<th style="width:1.25vw; text-align:right">' . $ix . '</th>';
    }

    // Creeeren van het SQL statement.
    // De variabele wordt in meerdere regels uitgebreid middels .=
    // Dit is nodig om voor elke dag een SUM(CASE te creeeren. De laatste dag is apart omdat deze niet met een komma moet eindigen 
    $sql_code = "SELECT *,";
    for($ix=1; $ix<31; $ix++) {
        $sql_code .= "SUM(CASE WHEN approval_dag = '".$ix."' THEN uren END) AS `".$ix."`,";
    }
    
    $sql_code .= "SUM(CASE WHEN approval_dag = '31' THEN uren END) AS `31` 
                  FROM view_uren_soortuur
                  WHERE USER = '".$username."' AND uren > 0 AND approval_maand = '".$maand."' AND approval_jaar = '".$jaar."'
                  GROUP BY soortuur 
                  ORDER BY soortuur, datum"; 
    
    $sql_out = mysqli_query($dbconn, $sql_code);
    if (!$sql_out) {
        writelog("approve", "ERROR", "Select view_uren_soortuur gaat fout: " . $sql_code . " - " . mysqli_error($dbconn));
        exit($MSGDB001E);
    }

    while ($sql_rows = mysqli_fetch_array($sql_out)) {
        $soortuur              = $sql_rows['soortuur'];
        $omschrijving_soortuur = $sql_rows['omschrijving'];

        echo '<tr class="colored" style="font-size:0.8vw">';
		echo "<td>".$soortuur." - ".$omschrijving_soortuur."</td>";
		for($ix=1; $ix<32; $ix++) {
		    echo "<td style='text-align:right'>".$sql_rows[$ix]."</td>";
        }
        echo '</tr>';
    }
    echo "</table></center>";
    echo "</div>";  // Einde div approve

    // Display totaal per soortuur
    echo "<h3>Totaal per urensoort</h3>";
    echo "<center><table>";
    echo "<tr style='font-size:0.8vw'><th>Soort uur</th><th>Totaal</th></tr>";
    /**
    $sql_code = "SELECT SUM(uren) AS totaal_uren, soortuur, omschrijving, approved FROM view_uren_soortuur
                WHERE user = '$username'
                AND approval_maand = '$maand'
                AND approval_jaar = '$jaar'
                GROUP BY soortuur
                ORDER BY soortuur";
    $sql_out = mysqli_query($dbconn, $sql_code);
    if (!$sql_out) {
        writelog("approve", "ERROR", "Benaderen database gaat fout: " . $sql_code . " - " . mysqli_error($dbconn));
        exit($MSGDB001E);
    }
    
    while ($sql_rows = mysqli_fetch_array($sql_out)) {
    */  
    //$driver = new mysqli_driver();
    //$driver->report_mode = MYSQLI_REPORT_ALL;
    try {
        $stmt_uren = $mysqli->prepare("SELECT SUM(uren) AS totaal_uren, soortuur, omschrijving, approved FROM view_uren_soortuur WHERE user = ? AND approval_maand = ? AND approval_jaar = ? AND uren > 0 GROUP BY soortuur ORDER BY soortuur");
        $stmt_uren->bind_param("sii", $username, $maand, $jaar);
        $stmt_uren->execute();
    } catch(Exception $e) {
        writelog("approve", "ERROR", $e);
        exit($MSGDB001E);
    }
    $stmt_uren->bind_result($totaal_uren, $soortuur, $omschrijving, $approved);
    while($stmt_uren->fetch()) {
        //$approved     = $sql_rows['approved'];
        //$totaal_uren  = $sql_rows['totaal_uren'];
        //$soortuur     = $sql_rows['soortuur'];
        //$omschrijving = $sql_rows['omschrijving'];
        echo '<tr class="colored" style="font-size:0.8vw">
			<td>' . $soortuur . ' - ' . $omschrijving . '</td><td style=\'text-align:right\'>' . $totaal_uren . '</td>
            </tr>';
    }
    echo "</table></center>";

    // Display buttons. Met behulp van een formulier
	//Volgende velden zijn hidden om toch de waarden door te geven voor de update-query
	?>
	
	<input type="hidden" name="maand" value="<?php if (isset($maand)) { echo $maand; } ?>"> 
	<input type="hidden" name="jaar" value="<?php if (isset($jaar)) { echo $jaar; } ?>"> 
	<input type="hidden" name="username" value="<?php if (isset($username)) { echo $username; } ?>"> 
	<input type="hidden" name="emailadres" value="<?php if (isset($emailadres)) { echo $emailadres; } ?>"> 
	<!--  <input type="hidden" name="jaar" value="<?php //if (isset($datum)) { echo substr($datum,0,4); } ?>"> -->
	
	<input class="button" type="submit" name="cancel" value="cancel" formnovalidate>
    <?php
    if (! isset($_SESSION['approvenallowed']) || (! $_SESSION['approvenallowed'])) {
        echo '<blockquote>Je hebt geen rechten om te approven</blockquote>';
    } else {
        if ($approved == 0 || $approved == 9) {
            echo '<input class="button" type="submit" name="approve" value="approve">';
            echo '<input class="button" type="submit" name="afkeuren" value="afkeuren">';
        }
    }
    ?>
    </form>
    <?php
}

include ("footer.php");
?>
