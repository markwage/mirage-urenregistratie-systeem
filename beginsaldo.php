<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// ------------------------------------------------------------------------------------------------------
// Bepalen welk jaar gedisplayed moet worden. 
// Indien het in de url is meegegeven zalt dat jaar getoond worden
// ------------------------------------------------------------------------------------------------------
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
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Cancel
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
    header("location: index.php");
}

// ------------------------------------------------------------------------------------------------------
// Initieren van de beginsaldi van de medewerkers voor een nieuw jaar
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['init_verlofuren_ja'])) {
    $inputjaar = $_POST["jaartal"];
    writelog("beginsaldo", "INFO", "De verlofuren worden geinitieerd voor het jaar " . $inputjaar);
    try {
        $stmt_user = $mysqli->prepare("SELECT username FROM users WHERE indienst = 1 AND uren_invullen = 1");
        //$stmt_user->bind_param("i", $inputjaar);
        $stmt_user->execute();
        $stmt_user->store_result();
    } catch(Exception $e) {
        writelog("beginsaldo", "ERROR", $e);
        exit($MSGDB001E);
    }
    
    $stmt_user->bind_result($username);
    while($stmt_user->fetch()) { 
        $saldo = 240;
        try {
            $stmt_ins = $mysqli->prepare("INSERT INTO beginsaldo (username, jaar, beginsaldo) VALUES (?, ?, ?)");
            $stmt_ins->bind_param("sii", $username, $inputjaar, $saldo);
            $stmt_ins->execute();
        } catch(Exception $e) {
            writelog("beginsaldo", "ERROR", $e);
            exit($MSGDB001E);
        }
    }
    header("location: beginsaldo.php?edtjaar=" . $inputjaar);
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Save
// ix1 = loop aantal rijen dat er uren ingevuld zijn
// ix2 = dagen binnen ix1 (ma t/m zo)
//
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['save'])) {
    $inputjaar = ($_POST['jaartal']);
    $aantal_rijen = count($_POST['beginsaldo']);
    
    for ($ix1 = 0; $ix1 < $aantal_rijen; $ix1 ++) {
        $frm_ID = $_POST['ID'][$ix1];
        $frm_beginsaldo = $_POST['beginsaldo'][$ix1];
        try {
            $stmt_upd = $mysqli->prepare("UPDATE beginsaldo SET beginsaldo = ? WHERE ID = ?");
            $stmt_upd->bind_param("ii", $frm_beginsaldo, $frm_ID);
            $stmt_upd->execute();
        } catch(Exception $e) {
            writelog("beginsaldo", "ERROR", $e);
            exit($MSGDB001E);
        }
    }
    writelog("beginsaldo", "INFO", "Records zijn ge-update over jaar " . $inputjaar);
    $frm_message = '<blockquote>INFO: De gegevens zijn succesvol gesaved</blockquote>';
}

?>

<!-- ------------------------------------------------------------------------------
  Begin van het formulier
------------------------------------------------------------------------------  -->
<div id="form_div">
<form name="beginsaldo" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<?php
if(isset($frm_message)) {
    echo $frm_message;
}

echo "<table>";
echo "<tr>";
echo "<td><strong>Jaar</strong></td>";
echo "<td><input type='number' style='width:3.2vw' name='jaartal' min='2019' max='2300' value='" . $inputjaar . "'>";
echo "<td><input class='button' type='submit' name='change_jaar' value='refresh'></td>";
echo "</tr>";
echo "</table>";

echo "<center><table id='uren_beginsaldo'>";

$sql_code = "SELECT * FROM view_users_verlofuren
                 WHERE jaar = " . $inputjaar . "
                 ORDER BY fullname";
$sql_out = mysqli_query($dbconn, $sql_code);
if (!$sql_out) {
    writelog("beginsaldo", "ERROR", "Er is een fout opgetreden bij het selecteren van de beginsaldi verlofuren -> " . mysqli_error($dbconn));
    exit($MSGDB001E);
}

$aantal_rijen = mysqli_num_rows($sql_out);
if($aantal_rijen == 0) {
    // Lege rijen wegschrijven voordat de vraag getoond wordt om het jaar te initieren
    echo '<tr><td>Voor dit jaar zijn er nog geen uren ingevoerd voor de medewerkers.</td></tr>';
    echo '<tr><td>Wil je voor alle medewerkers het beginsaldo voor dit jaar initieren?</td></tr>';
    echo '<tr><td>Het saldo zal dan voor iedereen op <strong>240</strong> uur gezet worden.</td></tr>';
    echo '<tr><td>Voor de medewerkers waarvan het beginsaldo anders moet zijn dient</td></tr>';
    echo '<tr><td>dit hierna handmatig aangepast te worden.</td></tr>';
    echo '<tr><td> </td></tr>';
    
    // Button dummy is nodig indien de user het jaartal wijzigt en dan op enter drukt ipv refresh-button
    echo '<tr>';
    echo "<td><input type='submit' name='dummy' value='None' style='display: none'>";
    echo "    <input class='button' type='submit' name='init_verlofuren_ja' value='Ja'>";
    echo "    <input class='button' type='submit' name='init_verlofuren_nee' value='Nee'></td>";
    echo '</tr>';
    echo "</table></center>";
    ?>
    </form>
	</div>
    <?php 	
} else {
    echo "<tr>";
    echo "<th></th><th colspan='1'>Naam medewerker</th><th style='width:2.8vw; text-align:right'>Beginsaldo</th>";
    echo "</tr>";

    while ($sql_row = mysqli_fetch_array($sql_out)) {
        $ID            = $sql_row['ID'];
        $username      = $sql_row['username'];
        $fullname      = $sql_row['fullname'];
        $beginsaldo    = $sql_row['beginsaldo'];
    
        echo '<tr class="colored">';
        echo '<td><input style="display:none" type="text" name="ID[]" value="' . $ID . '" readonly></td>';
        echo '<td style="display:none">' . $username . '</td>';
        echo '<td style="height:1.2vw;">' . $fullname . '</td>';
        echo '<td><input style="width:2.8vw; text-align:right" type="number" name="beginsaldo[]" value="' . $beginsaldo . '"></td>';
        echo '</tr>';
    }

    echo "</table></center>";
    // Deze button is nodig indien de user het jaartal wijzigt en dan op enter drukt ipv refresh-button
    echo "<input type='submit' name='dummy' value='None' style='display: none'>";
    echo "<input class='button' type='submit' name='save' value='save'>";
    echo "<input class='button' type='submit' name='cancel' value='cancel'>";
}

?>
</form>
</div>
<!-- ------------------------------------------------------------------------------
  Einde van het formulier
------------------------------------------------------------------------------  -->

<?php
include ("footer.php");
?>
