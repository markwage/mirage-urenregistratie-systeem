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
	<h1>Overzicht opgenomen verlofuren</h1>

<?php
//displayUserGegevens();

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

echo '<div id="verlof">';
echo "<center><table id='verlofuren_mdw'>";
echo "<tr>";
echo '<th style="width:5.45vw;">Maand / dag</th>';
for($ix=1; $ix<32; $ix++) {
    echo '<th style="width:1.45vw; text-align:right">' . $ix . '</th>';
}


echo "</tr>";

$sql_code = "SELECT username, approval_maand, approval_dag, uren
             FROM view_verlofuren
             WHERE approval_jaar = " . $inputjaar . " 
             AND username = '". $_SESSION['username'] . "'
             ORDER BY approval_maand, approval_dag";

$sql_out = mysqli_query($dbconn, $sql_code);
if (! $sql_out) {
    writelog("mijn_verlofuren", "ERROR", "Ophalen van de verlofuren per medewerker gaat fout: " . $sql_code . " - " . mysqli_error($dbconn));
} else {
    //$maandnr = '';
    //$dagnr   = '';
    //$uren    = '';
    $totaal_opgenomen = 0;
    
    $arr_uren[0][0]='Januari';
    $arr_uren[1][0]='Februari';
    $arr_uren[2][0]='Maart';
    $arr_uren[3][0]='April';
    $arr_uren[4][0]='Mei';
    $arr_uren[5][0]='Juni';
    $arr_uren[6][0]='Juli';
    $arr_uren[7][0]='Augustus';
    $arr_uren[8][0]='September';
    $arr_uren[9][0]='Oktober';
    $arr_uren[10][0]='November';
    $arr_uren[11][0]='December';
    
    for($ix1=0; $ix1<12; $ix1++) {
        for($ix2=1; $ix2<32; $ix2++) {
            $arr_uren[$ix1][$ix2] = ' ';
        }
    }
    
    //
    while ($sql_rows = mysqli_fetch_array($sql_out)) {
        $maandnr = $sql_rows['approval_maand'];
        $dagnr   = $sql_rows['approval_dag'];
        $uren    = $sql_rows['uren'];
        
        $arr_uren[$maandnr - 1][$dagnr] = $uren;
        $totaal_opgenomen = $totaal_opgenomen + $uren;
        
    }
    for($ix3=0; $ix3<12; $ix3++) {
        for($ix4=0; $ix4<32; $ix4++) {            
            
                if($ix4 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $arr_uren[$ix3][$ix4] . '</td>';
                if($ix4 == 32) {
                    echo '</tr>';
                }
            
           
        }
    }
    
    //echo "</tr>";
    writelog("mijn_verlofuren", "INFO", "Overzicht opgenomen verlofuren per medewerker in een jaar is uitgevoerd");
}

echo "</table></center>";


// Display overzicht beginsaldo en huidige saldo
$sql_beginsaldo_qry = "SELECT beginsaldo
                           FROM beginsaldo
                           WHERE username = '" . $_SESSION['username'] . "'
                           AND jaar = " . $inputjaar . ";";
$sql_beginsaldo_out = mysqli_query($dbconn, $sql_beginsaldo_qry);
if (! $sql_beginsaldo_out) {
    writelog("mijn_verlofuren", "ERROR", "Ophalen van de beginsaldi van de medewerker gaat fout: " . $sql_code . " - " . mysqli_error($dbconn));
}

while ($sql_beginsaldo_rows = mysqli_fetch_array($sql_beginsaldo_out)) {
    $frm_beginsaldo = $sql_beginsaldo_rows['beginsaldo'];
    echo "<table>";
    echo '<tr>';
    echo "<td>Beginsaldo:</td>";
    echo "<td style='text-align:right'>" . number_format($frm_beginsaldo, 2) . "</td>";
    echo "</tr>";
    echo '<tr>';
    echo "<td>Totaal opgenomen:</td>";
    echo "<td style='text-align:right'>" . number_format($totaal_opgenomen, 2) . "</td>";
    
    echo "</tr>";
    echo "</table>";
}
echo "</div>"; // end id=verlofuren

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
