<?php
session_start();

include ("config.php");
include ("db.php");
include ("mysqli_connect.php");
include ("function.php");
include ("autoload.php");

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();
include ("header.php");

global $username_decrypted, $fullname_decrypted;
if (isset($_GET['username'])) {
    // decrypt username
    $username_decrypted = convert_string('decrypt', $_GET['username']);
    if ($username_decrypted == '') {
        writelog("mijn_verlofuren", "ERROR", " Men heeft geprobeerd om username handmatig aan te passen in de url: " . $_GET['username']);
        exit("Je hebt geprobeerd om username handmatig aan te passen in de url");
    }
} else {
    $username_decrypted = "";
}

if (isset($_GET['fullname']) && ($_GET['fullname'] != "")) {
    $fullname_decrypted = "Medewerker: " . convert_string('decrypt', $_GET['fullname']);
} else {
    $fullname_decrypted = "";
}

?>
<div id="main">
	<h1>Geboekte uren per urensoort</h1>

<?php

// Bepalen jaartal (= huidig jaar)
$inputjaar = date('Y');

// ------------------------------------------------------------------------------------------------------
// Refresh butto changejaar is op geklikt om een andere week te muteren.
// Door getWeekdays worden de dagen en data van die nieuwe week berekend
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['change_jaar'])) {
    $inputjaar = $_POST["jaartal"];
    $fullname_decrypted = $_POST['fullname_decrypted'];
    $username_decrypted = $_POST['username_decrypted'];
}

// ---------------------------------------------------------------------------------
// Begin van het formulier
// ---------------------------------------------------------------------------------

?>
<div id="form_div">
		<form name="uren_per_urensoort"
			action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<?php
// Display invoerveld om jaartal in te voeren
echo "<table>";
echo "<tr>";
echo "<td><strong>Jaar</strong></td>";
echo "<td><input type='number' style='width:3.2vw' name='jaartal' min='2019' max='2300' value='" . $inputjaar . "'>";
echo "<td><input class='button' type='submit' name='change_jaar' value='refresh'></td>";
echo "<td style='width:17vw; text-align:right'><strong>" . $fullname_decrypted . "</strong></td>";
// volgende twee regels zijn nodig. Ze worden niet op het scherm getoond maar de variabelen zijn nodig bij refresh van het scherm
echo "<td><input type='text' style='display:none' name='username_decrypted' value='" . $username_decrypted . "'></td>";
echo "<td><input type='text' style='display:none' name='fullname_decrypted' value='" . $fullname_decrypted . "'></td>";
echo "</tr>";
echo "</table>";

echo "<center><table id='uren_soortuur'>";
echo "<tr>";
echo "<th colspan='2'>Soortuur</th>
      <th style='width:3.1vw; text-align:right'>Jan</th>
      <th style='width:3.1vw; text-align:right'>Feb</th>
      <th style='width:3.1vw; text-align:right'>Maa</th>
      <th style='width:3.1vw; text-align:right'>Apr</th>
      <th style='width:3.1vw; text-align:right'>Mei</th>
      <th style='width:3.1vw; text-align:right'>Jun</th>
      <th style='width:3.1vw; text-align:right'>Jul</th>
      <th style='width:3.1vw; text-align:right'>Aug</th>
      <th style='width:3.1vw; text-align:right'>Sep</th>
      <th style='width:3.1vw; text-align:right'>Okt</th>
      <th style='width:3.1vw; text-align:right'>Nov</th>
      <th style='width:3.1vw; text-align:right'>Dec</th>
      <th style='width:4.85vw; text-align:right'>Totaal</th>";
echo "</tr>";

$frm_soortuur = "dummy";
$frm_omschrijving = 'dummy';

for ($ix_init = 0; $ix_init < 12; $ix_init ++) {
    $frm_maand[$ix_init] = ' ';
    $maand_approved[$ix_init] = ' ';
}

try {
    if ($username_decrypted == "") {
        $stmt_uren = $mysqli->prepare("SELECT soortuur, omschrijving, approval_maand, approved, SUM(uren) AS totaal_uren FROM view_uren_soortuur  WHERE approval_jaar = ? GROUP BY approval_maand, soortuur ORDER BY soortuur, approval_maand");
        $stmt_uren->bind_param("i", $inputjaar);
    } else { 
        $stmt_uren = $mysqli->prepare("SELECT soortuur, omschrijving, approval_maand, approved, SUM(uren) AS totaal_uren FROM view_uren_soortuur  WHERE approval_jaar = ? AND user = ? GROUP BY approval_maand, soortuur ORDER BY soortuur, approval_maand");
        $stmt_uren->bind_param("is", $inputjaar, $username_decrypted);
    }
    $stmt_uren->execute();
} catch(Exception $e) {
    writelog("rpt_uren_urensoort", "ERROR", $e);
    exit($MSGDB001E);
}
$stmt_uren->bind_result($row_soortuur, $row_omschrijving, $row_maandnr, $row_approved, $totaal_uren);

$frm_soortuur = "dummy";
$frm_omschrijving = 'dummy';

while($stmt_uren->fetch()) {
    $row_maandnr = $row_maandnr - 1; // maandnr - 1 omdat array bij 0 begint
    $frm_maand_totaal = 0;
    if ($row_soortuur != $frm_soortuur) {
        if ($frm_soortuur != 'dummy') {
            echo '<tr class="colored">';
            echo "<td style='height:1.2vw;'>" . $frm_soortuur . "</td><td>" . $frm_omschrijving . "</td>";
            for ($ix = 0; $ix < 12; $ix ++) {
                if($frm_maand[$ix] > 0) {
                    $frm_maand_totaal = $frm_maand_totaal + $frm_maand[$ix];
                }
                if($maand_approved[$ix] == 1) {
                    echo "<td style='width:3.1vw; text-align:right;'>" . $frm_maand[$ix] . "</td>";
                } else {
                    echo "<td style='width:3.1vw; text-align:right; font-style:italic'>" . $frm_maand[$ix] . "</td>";
                }
                $frm_maand[$ix] = ' ';
                $maand_approved[$ix] = ' ';
            }
            echo "<td style='width:4.85vw; text-align:right;'><strong>" . number_format($frm_maand_totaal, 2) . "</strong></td>";
            echo "</tr>";
        }
    }

    $frm_soortuur = $row_soortuur;
    $frm_omschrijving = $row_omschrijving;
    $frm_maand[$row_maandnr] = $totaal_uren;
    $maand_approved[$row_maandnr] = $row_approved;
}
// laatste rij uit de query dus wegschrijven naar formulier
// Indien $row_soortuur niet is gevuld dan zijn er geen gegevens van het jaar
if (! isset($row_soortuur)) {
    echo '</table></center><blockquote>INFO: Er zijn geen gegevens aanwezig over dit jaar</blockquote>';
} else {
    $frm_maand_totaal = 0;
    echo '<tr class="colored">';
    echo "<td>" . $row_soortuur . "</td><td>" . $row_omschrijving . "</td>";
    for ($ix = 0; $ix < 12; $ix ++) {
        if($frm_maand[$ix] > 0) {
            $frm_maand_totaal = $frm_maand_totaal + $frm_maand[$ix];
        }
        if($maand_approved[$ix] == 1) {
            echo "<td style='width:3.1vw; text-align:right'>" . $frm_maand[$ix] . "</td>";
        } else {
            echo "<td style='width:3.1vw; text-align:right; font-style:italic'>" . $frm_maand[$ix] . "</td>";
        }
    }
    echo "<td style='width:4.85vw; text-align:right;'><strong>" . number_format($frm_maand_totaal, 2) . "</strong></td>";
}
echo "</tr>";
echo "</table></center>";
// This button is needed for when user pushes the ENTER button when changing the yearnumber. Button is not displayed
echo "<input type='submit' name='dummy' value='None' style='display: none'>";
writelog("rpt_uren_urensoort", "INFO", "Overzicht totaal aantal uren per urensoort in een jaar is uitgevoerd");
?>

</form>
	</div>
	<!-- ------------------------------------------------------------------------------
  Einde van het formulier
------------------------------------------------------------------------------  -->

<?php
include ("footer.php");
?>
