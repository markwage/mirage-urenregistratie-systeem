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
displayUserGegevens();

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
echo "<th>Maand / dag</th>";
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
    $frm_approval_maand = '';
    $frm_approval_dag  = '';
    $frm_uren          = '';

    for($ix1=0; $ix1<32; $ix1++) {

        $uren_jan[$ix1] = ' ';
        $uren_jan[0] = 'Januari';
        $uren_feb[$ix1] = ' ';
        $uren_feb[0] = 'Februari';
        $uren_maa[$ix1] = ' ';
        $uren_maa[0] = 'Maart';
        $uren_apr[$ix1] = ' ';
        $uren_apr[0] = 'April';
        $uren_mei[$ix1] = ' ';
        $uren_mei[0] = 'Mei';
        $uren_jun[$ix1] = ' ';
        $uren_jun[0] = 'Juni';
        $uren_jul[$ix1] = ' ';
        $uren_jul[0] = 'Juli';
        $uren_aug[$ix1] = ' ';
        $uren_aug[0] = 'Augustus';
        $uren_sep[$ix1] = ' ';
        $uren_sep[0] = 'September';
        $uren_okt[$ix1] = ' ';
        $uren_okt[0] = 'Oktober';
        $uren_nov[$ix1] = ' ';
        $uren_nov[0] = 'November';
        $uren_dec[$ix1] = ' ';
        $uren_dec[0] = 'December';
    }



    $arr_months[0][0]='Januari';
    $arr_months[1][0]='Februari';
    $arr_months[2][0]='Maart';
    $arr_months[3][0]='April';
    $arr_months[4][0]='Mei';
    $arr_months[5][0]='Juni';
    $arr_months[6][0]='Juli';
    $arr_months[7][0]='Augustus';
    $arr_months[8][0]='September';
    $arr_months[9][0]='Oktober';
    $arr_months[10][0]='November';
    $arr_months[11][0]='December';
    for($ix1=0; $ix1<12; $ix1++) {
        for($ix2=1; $ix2<32; $ix2++) {
            $arr_months[$ix1][$ix2] = ' ';
        }
    }
    writedebug("array jan:".$arr_months[0][0]." - ".$arr_months[1][0]);

    //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // >>>tellen rijen. Indien 0 dan alleen de maanden op het scherm tonen.
    //    anders onderstaande while uitvoeren
    while ($sql_rows = mysqli_fetch_array($sql_out)) {
        $frm_approval_maand = $sql_rows['approval_maand'];
        $frm_approval_dag   = $sql_rows['approval_dag'];
        $frm_uren           = $sql_rows['uren'];

        if($frm_approval_maand == 1) {
            $uren_jan[$frm_approval_dag] = $frm_uren;
        }
        if($frm_approval_maand == 2) {
            $uren_feb[$frm_approval_dag] = $frm_uren;
        }
    }
    for($ix2=0; $ix2<12; $ix2++) {
        for($ix3=0; $ix3<32; $ix3++) {
            if($ix2 == 0) {
                if($ix3 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $uren_jan[$ix3] . '</td>';
                if($ix3 == 31) {
                    echo '</tr>';
                }
            }
            if($ix2 == 1) {
                if($ix3 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $uren_feb[$ix3] . '</td>';
                if($ix3 == 31) {
                    echo '</tr>';
                }
            }
            if($ix2 == 2) {
                if($ix3 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $uren_maa[$ix3] . '</td>';
                if($ix3 == 31) {
                    echo '</tr>';
                }
            }
            if($ix2 == 3) {
                if($ix3 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $uren_apr[$ix3] . '</td>';
                if($ix3 == 31) {
                    echo '</tr>';
                }
            }
            if($ix2 == 4) {
                if($ix3 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $uren_mei[$ix3] . '</td>';
                if($ix3 == 31) {
                    echo '</tr>';
                }
            }
            if($ix2 == 5) {
                if($ix3 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $uren_jun[$ix3] . '</td>';
                if($ix3 == 31) {
                    echo '</tr>';
                }
            }
            if($ix2 == 6) {
                if($ix3 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $uren_jul[$ix3] . '</td>';
                if($ix3 == 31) {
                    echo '</tr>';
                }
            }
            if($ix2 == 7) {
                if($ix3 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $uren_aug[$ix3] . '</td>';
                if($ix3 == 31) {
                    echo '</tr>';
                }
            }
            if($ix2 == 8) {
                if($ix3 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $uren_sep[$ix3] . '</td>';
                if($ix3 == 31) {
                    echo '</tr>';
                }
            }
            if($ix2 == 9) {
                if($ix3 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $uren_okt[$ix3] . '</td>';
                if($ix3 == 31) {
                    echo '</tr>';
                }
            }
            if($ix2 == 10) {
                if($ix3 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $uren_nov[$ix3] . '</td>';
                if($ix3 == 31) {
                    echo '</tr>';
                }
            }
            if($ix2 == 11) {
                if($ix3 == 0) {
                    echo '<tr class="colored">';
                }
                echo '<td style="width:1.45vw">' . $uren_dec[$ix3] . '</td>';
                if($ix3 == 31) {
                    echo '</tr>';
                }
            }
        }
    }

    //echo "</tr>";
    writelog("mijn_verlofuren", "INFO", "Overzicht opgenomen verlofuren per medewerker in een jaar is uitgevoerd");
}
echo "</div>"; // end id=verlofuren
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
