<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();
include ("header.php");

?>
<div id="main">
	<h1>Urenadministratie</h1>

<?php

// Indien weeknr en jaar is doorgegeven via url dan dit de inputweeknr maken
// Anders is vandaag de inputdatum
// edtweek wordt doorgegeven via het home-scherm als user op edit-button klikt
//
if (isset($_GET['edtweek'])) {
    //$inputweeknr = $_GET['edtweek'];
    $inputweeknr = convert_string('decrypt', $_GET['edtweek']);
} else {
    $inputweeknr = date('Y') . "-W" . date('W');
}
getWeekdays($inputweeknr);

// ------------------------------------------------------------------------------------------------------
// Haal alle soorturen op uit de database en zet deze in de variabele $option zodat deze in de 
// dropdown getoond worden
// ------------------------------------------------------------------------------------------------------
$option = "";

$sql_code1 = "SELECT * FROM soorturen
             ORDER BY code";
$sql_out1 = mysqli_query($dbconn, $sql_code1);
if(!$sql_out1) {
    writelog("uren", "ERROR", "Select soorturen fout gegaan" . mysqli_error($dbconn));
    exit($MSGDB001E);
}

while ($sql_rows1 = mysqli_fetch_array($sql_out1)) {
    $option .= "<option value='" . $sql_rows1['code'] . "'>" . $sql_rows1['code'] . " - " . $sql_rows1['omschrijving'] . "</option>";
}

// ------------------------------------------------------------------------------------------------------
// Dit is het begin van de code wat uitgevoerd wordt indien het formulier is gesubmit
// Welk gedeelte van de code is afhankelijk van de button waarop geclickt is.
// ------------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------------
// BUTTON changeWeeknr is op geklikt om een andere week te muteren.
// Door getWeekdays worden de dagen en data van die nieuwe week berekend
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['updateweeknr']) || isset($_POST['week']) || isset($_POST['week_nummer'])) {
    $inputweeknr = $_POST["week_nummer"];
    getWeekdays($_POST['week_nummer']);
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Cancel
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
    header("location: index.php");
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Save
// ix1 = loop aantal rijen dat er uren ingevuld zijn
// ix2 = dagen binnen ix1 (ma t/m zo)
//
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['save']) || isset($_POST['approval'])) {
    getWeekdays($_POST['week_nummer']);

    $sql_select_uren = "SELECT * FROM uren 
                        WHERE user='" . $_SESSION['username'] . "' 
                        AND week='" . $week . "' 
                        AND jaar='" . $year . "'";
    $check_select_uren = mysqli_query($dbconn, $sql_select_uren);
    if(!$check_select_uren) {
        writelog("uren", "ERROR", "Select from uren fout gegaan" . mysqli_error($dbconn));
        exit($MSGDB001E);
    }

    // niet de uren verwijderen die al approved zijn in de vorige maand
    // Dit komt voor omdat maandovergang in de week valt
    writedebug("Hier worden de uren verwijderd");
    if (mysqli_num_rows($check_select_uren) > 0) {
        $sql_delete_uren = "DELETE FROM uren 
                            WHERE user='" . $_SESSION['username'] . "' 
                            AND week='" . $week . "' 
                            AND jaar='" . $year . "'
                            AND (approved = 0 OR approved = 9)";
        writedebug("SQL: ".$sql_delete_uren);
        $check_delete_uren = mysqli_query($dbconn, $sql_delete_uren);
        if(!$check_delete_uren) {
            writelog("uren", "ERROR", "Delete from uren fout gegaan" . mysqli_error($dbconn));
            exit($MSGDB001E);
        }
        writelog("uren", "INFO", "Records zijn verwijderd van week " . $year . "-" . $week . " ivm het updaten van de betreffende week");
    }

    $inputweeknr = $_POST['week_nummer'];
    $aantalRijen = count($_POST["dag1"]);

    for ($ix1 = 0; $ix1 < $aantalRijen; $ix1 ++) {
        if (trim($_POST["soortuur"][$ix1] != '')) {
            // Check de ingevulde velden op correctheid
            checkIngevuldeUrenPerSoort($ix1);

            // Check of de week al voorkomt in de database Indien ja EN al approved dan kunnen de gegevens niet gewijzigd worden
            for ($ix2 = 0; $ix2 < 7; $ix2 ++) {
                if ($urenarray[$ix2] > 0) {
                    $datum = date("Y-m-d", strtotime($year . 'W' . str_pad($week, 2, 0, STR_PAD_LEFT) . ' +' . $ix2 . ' days'));
                    $str_datum = strtotime($datum);
                    $maand = date("m", $str_datum);
                    $dagnummer = $ix2;

                    // niet de uren inserten die al approved waren en dus in vorige step niet verwijderd zijn
                    // Controleren of de datum van die user/week al in de database aanwezig is. Zou niet
                    // moeten zijn omdat de week verwijderd is. Indien wel dan was deze dus al approved
                    $sql_check_datum_approved = "SELECT * FROM uren
                                                 WHERE user='" . $_SESSION['username'] . "' 
                                                 AND datum='" . $datum . "'
                                                 AND soortuur='" . $_POST['soortuur'][$ix1] . "'";
                    $check_check_datum_approved = mysqli_query($dbconn, $sql_check_datum_approved);
                    if (! $check_check_datum_approved) {
                        writelog("uren", "ERROR", "Er is een fout opgetreden bij het selecteren van uren -> " . mysqli_error($dbconn));
                        exit($MSGDB001E);
                    }

                    $rows_check_datum_approved = mysqli_num_rows($check_check_datum_approved);
                    // Make string numeric First change the , in a . (because of decimals)
                    $uren_temp = str_replace(",",".",$urenarray[$ix2]);
                    $uren_insert = number_format($uren_temp, 2);
                    if ($rows_check_datum_approved == 0) {
                        $sql_insert_uren = "INSERT INTO uren (jaar, maand, week, dagnummer, soortuur, datum, uren, user)
                                            VALUES('" . $year . "', 
                                                   '" . $maand . "', 
                                                   '" . $week . "', 
                                                   '" . $dagnummer . "', 
                                                   '" . $_POST['soortuur'][$ix1] . "', 
                                                   '" . $datum . "', 
                                                   '" . $uren_insert . "', 
                                                   '" . $_SESSION['username'] . "')";
                        $check_insert_uren = mysqli_query($dbconn, $sql_insert_uren);

                        if (! $check_insert_uren) {
                            writelog("uren", "ERROR", "Er is een fout opgetreden bij het selecteren van uren -> " . mysqli_error($dbconn));
                            exit($MSGDB001E);
                        }
                    }
                }
            }
        }
    }
    writelog("uren", "INFO", "Records zijn toegevoegd voor week " . $year . "-" . $week . " ivm updaten van de betreffende week");
}
?>

<!-- ------------------------------------------------------------------------------
  Begin van het formulier
------------------------------------------------------------------------------  -->
	<div id="form_div">
		<form name="add_uren" action="<?php echo $_SERVER['PHP_SELF']; ?>"
			method="post">

<?php

echo "<table>";
echo "<tr>";
echo "<td><strong>Weeknummer</strong></td>";
echo "<td><input type='week' style='width:9.8vw' name='week_nummer' value='" . $inputweeknr . "' required></td>";
echo "<td><input class='button' type='submit' name='change_week' value='refresh'></td>";
echo "</tr>";
echo "</table>";

echo "<center><table id='uren_table'>";
echo "<tr>";
echo "<th>Soortuur</th>";

// Tabelheaders aanmaken met datum/afkorting dagnaam
// En controleren of de maand waarin de dag valt al approved is
// De variabelem weekDatum em WeekDagNaam worden aangemaakt in de function getWeekDays
for ($ix6 = 0; $ix6 < 7; $ix6 ++) {
    echo "<th><center>" . $weekDatum[$ix6] . "<br>" . $weekDagNaam[$ix6] . "</center></th>";

    $sql_check_approved = "SELECT * FROM approvals
                           WHERE user='" . $_SESSION['username'] . "'
                           AND maand='" . $weekMaand[$ix6] . "'
                           AND jaar='" . $weekJaar[$ix6] . "'";
    $check_check_approved = mysqli_query($dbconn, $sql_check_approved);

    if (! $check_check_approved) {
        writelog("uren", "ERROR", "Select from approvals gaat fout: " . mysqli_error($dbconn));
        exit($MSGDB001E);
    }

    $rows_check_approved = mysqli_num_rows($check_check_approved);

    if ($rows_check_approved > 0) {
        $dag_readonly[$ix6] = 'readonly';
    } else {
        $dag_readonly[$ix6] = '';
    }
}

echo "<th style='text-align:right'>Totaal</th><th style='width:1.5vw'></th>";
echo "</tr>";

// ------------------------------------------------------------------------------------------------------
// Bekijk huidige week of er al uren ingevuld zijn.
// ------------------------------------------------------------------------------------------------------
for ($ix3 = 0; $ix3 < 7; $ix3 ++) {
    $frm_valueDag[$ix3] = '';
}

// Om regels per soortuur te krijgen
//
// Er dient ook gechecked te worden of alle dagen van deze week approved zijn
// Zo niet dan moet de +-button achteraan gewoon getoond worden
// Options veld van soortuur moet wel readonly zijn indien er minimaal ��n dag approved is

$tmp_soortuur = 'eersteloop';

$sql_code2 = "SELECT * FROM uren 
             WHERE user='" . $_SESSION['username'] . "' 
             AND week='" . $week . "' 
             AND jaar='" . $year . "' 
             ORDER BY soortuur, dagnummer";
if(!$sql_code2) {
    writelog("uren", "ERROR", "Select from uren fout gegaan" . mysqli_error($dbconn));
    exit($MSGDB001E);
}

if ($sql_out2 = mysqli_query($dbconn, $sql_code2)) {
    $sql_rows2 = mysqli_num_rows($sql_out2);
    if (mysqli_num_rows($sql_out2) > 0) {

        while ($row_uren2 = mysqli_fetch_array($sql_out2)) {
            // Onderstaande if uitvoeren als het soortuur anders is dan laatste gelezen record. Dit betekent dat er
            // een nieuwe regel gedisplayed moet worden omdat per soortuur een regel op scherm komt
            // 'eersteloop' wordt gebruikt om het eerst gelezen record niet meteen op het scherm te displayen

            if (($tmp_soortuur != $row_uren2['soortuur']) && ($tmp_soortuur != 'eersteloop')) {
                // Loop om de dropdown met soorten uren op te bouwen
                // En om te bepalen of de betreffende soortuur voor de regel geldt waarvoor uren zijn ingevuld

                enablesoortuur();

                echo "<tr id='row1'>";

                berekenurenpersoort();
                berekendagenreadonly();

                for ($ix4 = 0; $ix4 < 7; $ix4 ++) {
                    $frm_valueDag[$ix4] = '';
                }
            }

            for ($ix8 = 0; $ix8 < 7; $ix8 ++) {
                if ($row_uren2['dagnummer'] == $ix8) {
                    $frm_valueDag[$ix8] = $row_uren2['uren'];
                }
            }
            $tmp_soortuur = $row_uren2['soortuur'];
        }
    } else {
        $frm_select_disabled = "";
    }

    echo "<tr id='row1'>";

    // Loop om de dropdown met soorten uren op te bouwen
    // En om te bepalen of de betreffende soortuur voor de regel geldt waarvoor uren zijn ingevuld
    enablesoortuur();
    berekenurenpersoort();
    berekendagenreadonly();
} else {
    echo "ERROR: Kan geen connectie met de database maken. " . mysqli_error($dbconn);
}

echo "</table></center>";
// This button is needed for when user pushes the ENTER button when changing the weeknumber. Button is not displayed
echo "<input type='submit' name='dummy' value='None' style='display: none'>";

if ($dag_readonly[6] == '') {
    echo "<input class='button' type='submit' name='save' value='save'>";
}

echo "<input class='button' type='submit' name='cancel' value='cancel'>";
?>
</form>
	</div>
	<!-- ------------------------------------------------------------------------------
  Einde van het formulier
------------------------------------------------------------------------------  -->

<?php
include ("footer.php");
?>
