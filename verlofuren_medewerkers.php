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
	<h1>Overzicht verlofuren medewerkers</h1>

<?php

// Bepalen jaartal (= huidig jaar)
$inputjaar = date('Y');

// ------------------------------------------------------------------------------------------------------
// Refresh butto changejaar is op geklikt om een andere week te muteren.
// Door getWeekdays worden de dagen en data van die nieuwe week berekend
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['change_jaar'])) {
    $inputjaar = $_POST["jaartal"];
}

// ---------------------------------------------------------------------------------
// Begin van het formulier
// ---------------------------------------------------------------------------------
?>
<div id="form_div">
<form name="verlofuren_per_medewerker" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<?php

echo "<table>";
echo "<tr>";
echo "<td><strong>Jaar</strong></td>";
echo "<td><input type='number' style='width:3.2vw' name='jaartal' min='2019' max='2300' value='" . $inputjaar . "'>";
echo "<td><input class='button' type='submit' name='change_jaar' value='refresh'></td>";
echo "</tr>";
echo "</table>";

echo "<center><table id='verlofuren_mdw'>";
echo "<tr>";
echo "<th>Medewerker</th>
      <th style='width:2.85vw; text-align:right'>Begin<br />saldo</th>
      <th style='width:2.85vw; text-align:right'>Jan</th>
      <th style='width:2.85vw; text-align:right'>Feb</th>
      <th style='width:2.85vw; text-align:right'>Maa</th>
      <th style='width:2.85vw; text-align:right'>Apr</th>
      <th style='width:2.85vw; text-align:right'>Mei</th>
      <th style='width:2.85vw; text-align:right'>Jun</th>
      <th style='width:2.85vw; text-align:right'>Jul</th>
      <th style='width:2.85vw; text-align:right'>Aug</th>
      <th style='width:2.85vw; text-align:right'>Sep</th>
      <th style='width:2.85vw; text-align:right'>Okt</th>
      <th style='width:2.85vw; text-align:right'>Nov</th>
      <th style='width:2.85vw; text-align:right'>Dec</th>
      <th style='width:4.2vw; text-align:right'>Totaal</th>
      <th style='width:4.2vw; text-align:right'>Huidig<br />saldo</th>
      <th style='width:2.85vw; text-align:right'>Aktie</th>";
echo "</tr>";

// In onderstaande query WHERE (approval_jaar = ".$inputjaar." OR approval_jaar IS NULL)
// vervangen door WHERE (approval_jaar = ".$inputjaar.")

$sql_code = "SELECT username, fullname, SUM(uren) AS totaal_uren, beginsaldo,
             SUM(CASE WHEN approval_maand = '1' THEN uren END) AS `jan`,
             SUM(CASE WHEN approval_maand = '2' THEN uren END) AS `feb`,
             SUM(CASE WHEN approval_maand = '3' THEN uren END) AS `maa`,
             SUM(CASE WHEN approval_maand = '4' THEN uren END) AS `apr`,
             SUM(CASE WHEN approval_maand = '5' THEN uren END) AS `mei`,
             SUM(CASE WHEN approval_maand = '6' THEN uren END) AS `jun`,
             SUM(CASE WHEN approval_maand = '7' THEN uren END) AS `jul`,
             SUM(CASE WHEN approval_maand = '8' THEN uren END) AS `aug`,
             SUM(CASE WHEN approval_maand = '9' THEN uren END) AS `sep`,
             SUM(CASE WHEN approval_maand = '10' THEN uren END) AS `okt`,
             SUM(CASE WHEN approval_maand = '11' THEN uren END) AS `nov`,
             SUM(CASE WHEN approval_maand = '12' THEN uren END) AS `dec`
             FROM view_verlofuren
             WHERE (approval_jaar = ".$inputjaar.")
             GROUP BY username
             ORDER BY fullname, approval_maand";

$sql_out = mysqli_query($dbconn, $sql_code);
if (! $sql_out) {
    writelog("verlofuren_mdw", "ERROR", "Ophalen van de verlofuren per medewerker gaat fout: " . $sql_code . " - " . mysqli_error($dbconn));
    exit($MSGDB001E);
} else {
    
    while ($sql_rows = mysqli_fetch_array($sql_out)) {
        $frm_username    = $sql_rows['username'];
        $frm_fullname    = $sql_rows['fullname'];
        $frm_beginsaldo  = $sql_rows['beginsaldo'];
        $frm_totaal_uren = $sql_rows['totaal_uren'];
        //
        if(!($sql_rows['jan'] > 0)) {
            $sql_rows['jan'] = " ";
        }
        if(!($sql_rows['feb'] > 0)) {
            $sql_rows['feb'] = " ";
        }
        if(!($sql_rows['maa'] > 0)) {
            $sql_rows['maa'] = " ";
        }
        if(!($sql_rows['apr'] > 0)) {
            $sql_rows['apr'] = " ";
        }
        if(!($sql_rows['mei'] > 0)) {
            $sql_rows['mei'] = " ";
        }
        if(!($sql_rows['jun'] > 0)) {
            $sql_rows['jun'] = " ";
        }
        if(!($sql_rows['jul'] > 0)) {
            $sql_rows['jul'] = " ";
        }
        if(!($sql_rows['aug'] > 0)) {
            $sql_rows['aug'] = " ";
        }
        if(!($sql_rows['sep'] > 0)) {
            $sql_rows['sep'] = " ";
        }
        if(!($sql_rows['okt'] > 0)) {
            $sql_rows['okt'] = " ";
        }
        if(!($sql_rows['nov'] > 0)) {
            $sql_rows['nov'] = " ";
        }
        if(!($sql_rows['dec'] > 0)) {
            $sql_rows['dec'] = " ";
        }
        $frm_eindsaldo   = $frm_beginsaldo - $frm_totaal_uren;
               
        echo '<tr class="colored">';
        echo "<td style='height:1.2vw;'>" . $frm_fullname . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . number_format($frm_beginsaldo, 2) . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . $sql_rows['jan'] . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . $sql_rows['feb'] . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . $sql_rows['maa'] . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . $sql_rows['apr'] . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . $sql_rows['mei'] . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . $sql_rows['jun'] . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . $sql_rows['jul'] . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . $sql_rows['aug'] . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . $sql_rows['sep'] . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . $sql_rows['okt'] . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . $sql_rows['nov'] . "</td>";
        echo "<td style='width:2.85vw; text-align:right'>" . $sql_rows['dec'] . "</td>";
        echo "<td style='width:4.2vw; text-align:right'><strong>" . number_format($frm_totaal_uren, 2) . "</strong></td>";
        echo "<td style='width:4.2vw; text-align:right'><strong>" . number_format($frm_eindsaldo, 2) . "</strong></td>";
        $encrypted_username = convert_string('encrypt', $frm_username);
        if($frm_totaal_uren > 0) {
            echo '<td><center><a href="mijn_verlofuren.php?aktie=dspmdw&user=' . $encrypted_username . '&jaar=' . $inputjaar . '"><img class="button" src="./img/icons/view-48.png" alt="Toon medewerker" title="Toon de verlofuren van deze medewerker" /></a></center></td>';
        } else {
            echo '<td> </td>';
        }
        echo "</tr>";
        $frm_totaal_uren = 0;
        $frm_eindsaldo = 0;
    }
    writelog("verlofuren_medewerker", "INFO", "Overzicht opgenomen verlofuren per medewerker in een jaar is uitgevoerd");
}
echo "</table></center>";
// This button is needed for when user pushes the ENTER button when changing the yearnumber. Button is not displayed
echo "<input type='submit' name='dummy' value='None' style='display: none'>";
?>

</form>
</div>
<!-- ------------------------------------------------------------------------------
  Einde van het formulier
------------------------------------------------------------------------------  -->

<?php
include ("footer.php");
?>
