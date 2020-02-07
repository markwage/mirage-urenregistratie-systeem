<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_admin();
check_cookies();

include ("header.php");

/**
if (isset($_GET['username'])) {
    // decrypt username
    $username_decrypted = convert_string('decrypt', $_GET['username']);
    if ($username_decrypted == '') {
        writelog("rpt_uren_urensoort", "ERROR", " Men heeft geprobeerd om username handmatig aan te passen in de url: " . $_GET['username']);
        exit("Je hebt geprobeerd om username handmatig aan te passen in de url");
    }
} else {
    $username_decrypted = "";
} */

?>
<div id="main">
	<h1>Overzicht beginsaldi verlofuren</h1>

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
    writedebug("inputjaar: " . $inputjaar);
}

// ---------------------------------------------------------------------------------
// Begin van het formulier Worden eerst alle medewerkers met hun beginsaldi getoond
// ---------------------------------------------------------------------------------
//if($aktie = 'disp') {
    ?>
    <div id="form_div">
		<form name="beginsaldo" action="<?php echo $_SERVER['PHP_SELF']; ?>"
			method="post">
    <?php 
    echo "<table>";
    echo "<tr>";
    echo "<td><strong>Jaar</strong></td>";
    echo "<td><input type='number' style='width:3.2vw' name='jaartal' min='2019' max='2300' value='" . $inputjaar . "'>";
    echo "<td><input class='button' type='submit' name='change_jaar' value='refresh'></td>";

    // echo "<td><strong>Medewerker: ".$_SESSION['voornaam']." ".$_SESSION['tussenvoegsel']." ".$_SESSION['achternaam']."</strong></td>";
    echo "</tr>";
    echo "</table>";

    echo "<center><table id='beginsaldo'>";
    echo "<tr>";
    echo "<th>ID</th><th>Username</th><th>Volledige naam medewerker</th><th style='width:3.33vw; text-align:right'>Beginsaldo</th><th>aktie</th>";
    echo "</tr>";
    $rowcolor = 'row-a';

    // Hier komt het ophalen en optellen van de uren
    $sql_code = "SELECT * FROM view_users_verlofuren
                 WHERE jaar = " . $inputjaar;
    writedebug("beginsaldo-query: " . $sql_code);

    $sql_out = mysqli_query($dbconn, $sql_code);
    if (! $sql_out) {
        writelog("beginsaldo", "ERROR", "Ophalen van de beginsaldi gaat fout: " . $sql_code . " - " . mysqli_error($dbconn));
    } else {

        while ($sql_rows = mysqli_fetch_array($sql_out)) {
            $frm_ID         = $sql_rows['ID'];
            $frm_username   = $sql_rows['username'];
            $frm_medewerker = $sql_rows['achternaam'] . ", " . $sql_rows['tussenvoegsel'] . " " . $sql_rows['voornaam'];
            $frm_beginsaldo = $sql_rows['beginsaldo'];
            $encrypted_username = convert_string('encrypt', $frm_username);

            echo '<tr class="' . $rowcolor . '">';
            echo "<td>" . $frm_ID . "</td><td>" . $frm_username. "</td><td>" . $frm_medewerker . "</td><td style='width:3.33vw; text-align:right'>" . $frm_beginsaldo . "</td>";
            echo '<td><a href="beginsaldo.php?aktie=edit&username=' . $encrypted_username . '"><img class="button" src="./img/icons/edit-48.png" alt="wijzigen beginsaldo" title="wijzig beginsaldo ' . $frm_medewerker . '" /></a></td>';
            echo "</tr>";
            check_row_color($rowcolor);
        }
    }
//}
 
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
