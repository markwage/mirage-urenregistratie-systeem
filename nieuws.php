<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

if (isset($_GET['aktie'])) {
    $aktie = $_GET['aktie'];
} else {
    $aktie = "";
}

// Controleren of gebruiker admin-rechten heeft
if (! $aktie == "disp") {
    check_admin();
}

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

echo "<div id=\"main\"><h1>";
if ($_SESSION['admin']) {
    echo "Onderhoud ";
}
echo "Nieuwsartikelen</h1>";

// ------------------------------------------------------------------------------------------------------
// From here this code runs if the form has been submitted
// ------------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------------
// BUTTON Cancel
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
    header("location: nieuws.php?aktie=disp");
}
// ------------------------------------------------------------------------------------------------------
// BUTTON Delete
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['delete'])) {
    $delid = $_POST['ID'];
    $sql_code = "DELETE FROM nieuws
                 WHERE ID = '$delid'";
    $sql_out = mysqli_query($dbconn, $sql_code);
    
    writelog("nieuws", "INFO", "Het nieuwsbericht met id " . $delid . " is verwijderd uit tabel nieuws");
    
    header("location: nieuws.php?aktie=disp");
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Save
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['save'])) {
    $formerror = 0;
    
    if ((! $_POST['nieuwsheader'] || $_POST['nieuwsheader'] == "") && (! $formerror)) {
        echo '<p class="errmsg"> ERROR: Nieuwsheader is een verplicht veld</p>';
        $focus = 'nieuwsheader';
        $formerror = 1;
    }
    
    if ((! $_POST['nieuwsbericht'] || $_POST['nieuwsbericht'] == "") && (! $formerror)) {
        echo '<p class="errmsg"> ERROR: Nieuwsbericht is een verplicht veld</p>';
        $focus = 'nieuwsbericht';
        $formerror = 1;
    }
    
    if (! $formerror) {
        $sql_code = "UPDATE nieuws
                     SET datum = '" . $_POST['datum'] . "',
		                 nieuwsheader = '" . $_POST['nieuwsheader'] . "',
                         nieuwsbericht = '" . $_POST['nieuwsbericht'] . "'
                     WHERE ID = '" . $_POST['ID'] . "'";
        
        $sql_out = mysqli_query($dbconn, $sql_code);
        
        if ($sql_out) {
            writelog("nieuws", "INFO", "Het nieuwsbericht met id " . $_POST['ID'] . " is gewijzigd");
            
            $frm_datum = "";
            $frm_nieuwsheader = "";
            $frm_nieuwsbericht = "";
        } else {
            echo '<p class="errmsg">Er is een fout opgetreden bij het updaten van nieuwsbericht. Probeer het nogmaals.<br />
			Indien het probleem zich blijft voordoen neem dan contact op met de webmaster</p>';
        }
        header("location: nieuws.php?aktie=disp");
    }
}

// ------------------------------------------------------------------------------------------------------
// START Dit wordt uitgevoerd wanneer de user op Onderhoud nieuwsberichten heeft geklikt
// Er wordt een lijst met de uren getoond
// ------------------------------------------------------------------------------------------------------
if ($aktie == 'disp') {
    $sql_code = "SELECT * FROM nieuws
                 ORDER BY datum desc";
    $sql_out = mysqli_query($dbconn, $sql_code);
    
    echo "<center><table>";
    echo "<tr><th>Datum</th><th>Nieuwsheader</th><th colspan=\"3\" align=\"center\">Akties</th></tr>";
    
    //$rowcolor = 'row-a';
    
    while ($sql_rows = mysqli_fetch_array($sql_out)) {
        $id = $sql_rows['ID'];
        $datum = $sql_rows['datum'];
        $nieuwsheader = $sql_rows['nieuwsheader'];
        
        echo '<tr class="colored">
		<td>' . $datum . '</td><td>' . $nieuwsheader . '</td>';
        
        if (! isset($_SESSION['admin']) || (! $_SESSION['admin'])) {
            echo '<td><a href="nieuws.php?aktie=dispbericht&edtid=' . $id . '"><img class="button" src="./img/icons/view-48.png" alt="display nieuwsbericht" title="display volledig nieuwsbericht" /></a></td>';
        } else {
            echo '<td><a href="nieuws.php?aktie=edit&edtid=' . $id . '"><img class="button" src="./img/icons/edit-48.png" alt="wijzigen nieuwsbericht" title="wijzig nieuwsbericht" /></a></td>
			<td><a href="nieuws.php?aktie=delete&edtid=' . $id . '"><img class="button" src="./img/icons/trash-48.png" alt="delete nieuwsbericht" title="delete het nieuwsbericht" /></a></td>
			<td><a href="add_nieuws.php"><img class="button" src="./img/icons/add-48.png" alt="toevoegen nieuwsbericht" title="toevoegen nieuwsbericht" /></a></td>';
        }
        
        echo '</tr>';
    }
    echo "</table></center>";
}

// ------------------------------------------------------------------------------------------------------
// Wordt uitgevoerd wanneer men op de button klikt om te wijzigen of te deleten of om het bericht
// te verwijderen
// ------------------------------------------------------------------------------------------------------
if ($aktie == 'edit' || $aktie == 'delete' || $aktie == 'dispbericht') {
    if ($aktie == 'edit' || $aktie == 'delete') {
        check_admin();
    }
    
    $edtid = $_GET['edtid'];
    $focus = "nieuwsheader";
    
    $sql_code = "SELECT * FROM nieuws
                 WHERE id = '$edtid'";
    $sql_out = mysqli_query($dbconn, $sql_code);
    
    while ($sql_rows = mysqli_fetch_array($sql_out)) {
        $frm_ID = $sql_rows['ID'];
        $frm_datum = $sql_rows['datum'];
        $frm_nieuwsheader = $sql_rows['nieuwsheader'];
        $frm_nieuwsbericht = $sql_rows['nieuwsbericht'];
    }
    ?>
<form name="nieuwsartikelen"
	action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<p>
	
	
	<table>
		<tr>
			<td>ID</td>
			<td><input type="text" readonly name="ID" size="4" maxlength="8"
				value="<?php if (isset($frm_ID)) { echo $frm_ID; } ?>"></td>
		</tr>
		<tr>
			<td>Datum</td>
			<td><input type="text" name="datum" size="20" maxlength="20"
				<?php if (!$_SESSION['admin']) { echo "readonly "; } ?>
				value="<?php if (isset($frm_datum)) { echo $frm_datum; } ?>"></td>
		</tr>
		<tr>
			<td>Nieuwsbericht</td>
			<td><input type="text" name="nieuwsheader" size="80" maxlength="128"
				<?php if (!$_SESSION['admin']) { echo "readonly "; } ?>
				value="<?php if (isset($frm_nieuwsheader)) { echo $frm_nieuwsheader; } ?>"></td>
		</tr>
		<?php
    if ($aktie == 'edit') {
        echo '<tr><td> </td><td><textarea id="area1" name="nieuwsbericht">' . $frm_nieuwsbericht . '</textarea></td></tr>';
    }
    if ($aktie == 'dispbericht') {
        echo '<tr><td> </td><td style="width:90%; height:90%;"><textarea readonly id="area1" name="nieuwsbericht">' . $frm_nieuwsbericht . '</textarea></td></tr>';
    }
    ?>
    </table>
	<br />
    <?php
    if ($aktie == 'edit') {
        echo '<input class="button" type="submit" name="save" value="save">';
    }

    if ($aktie == 'delete') {
        echo '<input class="button" type="submit" name="delete" value="delete" onClick="return confirmDelNieuwsbericht()">';
    }
    ?>
	<input class="button" type="submit" name="cancel" value="cancel">
	</p>
</form>
<br />

<?php
    if (! isset($focus)) {
        $focus = 'datum';
    }
    setfocus('nieuwsartikelen', $focus);
}

include ("footer.php");
?>		