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
	<h1>Beginsaldi verlofuren</h1>

<?php
displayUserGegevens();

// Indien weeknr en jaar is doorgegeven via url dan dit de inputweeknr maken
// Anders is vandaag de inputdatum
// edtweek wordt doorgegeven via het home-scherm als user op edit-button klikt
//
if (isset($_GET['edtjaar'])) {
    $inputjaar = $_GET['edtjaar'];
} else {
    $inputjaar = date('Y');
}

/**
 * Dit is het begin van de code wat uitgevoerd wordt indien het formulier is gesubmit
 * Welk gedeelte van de code is afhankelijk van de button waarop geclickt is.
 */

// ------------------------------------------------------------------------------------------------------
// BUTTON changeWeeknr is op geklikt om een andere week te muteren.
// Door getWeekdays worden de dagen en data van die nieuwe week berekend
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['change_jaar'])) {
    $inputjaar = $_POST["jaartal"];
    //getWeekdays($_POST['week_nummer']);
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
if (isset($_POST['save'])) {
    //writedebug("Aantal rijen uit de query en die worden getoond op het scherm: " . $_POST);
    $inputjaar = ($_POST['jaartal']);
    $aantalRijen = count($_POST['beginsaldo']);
    writedebug("Aantal rijen uit de query en die worden getoond op het scherm: " . $aantalRijen);

    for ($ix1 = 0; $ix1 < $aantalRijen; $ix1 ++) {
        $frm_ID = $_POST['ID'][$ix1];
        $frm_beginsaldo = $_POST['beginsaldo'][$ix1];
        
        $sql_code = "UPDATE beginsaldo
                     SET beginsaldo = " . $frm_beginsaldo . "
                     WHERE ID = " . $frm_ID;
        $sql_out = mysqli_query($dbconn, $sql_code);

        if (! $sql_out) {
            writelog("beginsaldo", "ERROR", "Er is een fout opgetreden bij het updaten van de beginsaldi verlofuren -> " . mysqli_error($dbconn));
        }
    }
}
writelog("beginsaldo", "INFO", "Records zijn ge-update over jaar " . $inputjaar);

?>

<!-- ------------------------------------------------------------------------------
  Begin van het formulier
------------------------------------------------------------------------------  -->
<div id="form_div">
<form name="beginsaldo" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<?php

echo "<table>";
echo "<tr>";
echo "<td><strong>Jaar</strong></td>";
echo "<td><input type='number' style='width:3.2vw' name='jaartal' min='2019' max='2300' value='" . $inputjaar . "'>";
echo "<td><input class='button' type='submit' name='change_jaar' value='refresh'></td>";
echo "</tr>";
echo "</table>";

echo "<center><table id='uren_beginsaldo'>";
echo "<tr>";
echo "<th>ID</th><th>Username</th><th>Naam medewerker</th><th style='width:2.8vw; text-align:right'>Beginsaldo</th>";
echo "</tr>";

$sql_code = "SELECT * FROM view_users_verlofuren
                 WHERE jaar = " . $inputjaar;
$sql_out = mysqli_query($dbconn, $sql_code);
if (! $sql_out) {
    writelog("beginsaldo", "ERROR", "Er is een fout opgetreden bij het selecteren van de beginsaldi verlofuren -> " . mysqli_error($dbconn));
}
$SQL_NUM_ROWS = mysqli_num_rows($sql_out);
$rowcolor = 'row-a';

while ($sql_row = mysqli_fetch_array($sql_out)) {
    $ID            = $sql_row['ID'];
    $username      = $sql_row['username'];
    $voornaam      = $sql_row['voornaam'];
    $tussenvoegsel = $sql_row['tussenvoegsel'];
    $achternaam    = $sql_row['achternaam'];
    $beginsaldo    = $sql_row['beginsaldo'];
    
    echo '<tr class="' . $rowcolor . '">';
    echo '<td name="ID">' . $ID . '</td>';
    echo '<td>' . $username . '</td>';
    echo '<td>' . $achternaam . ', ' . $voornaam . ' ' . $tussenvoegsel . '</td>';
    echo '<td><input style="width:2.8vw; text-align:right" type="number" name="beginsaldo[]" value="' . $beginsaldo . '"></td>';
    echo '</tr>';
    check_row_color($rowcolor);
}

echo "</table></center>";
// This button is needed for when user pushes the ENTER button when changing the weeknumber. Button is not displayed
echo "<input type='submit' name='dummy' value='None' style='display: none'>";
echo "<input class='button' type='submit' name='save' value='save'>";
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
