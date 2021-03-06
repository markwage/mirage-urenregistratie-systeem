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

check_admin(); // Controleren of gebruiker admin-rechten heeft
check_cookies(); // Controleren of cookie aanwezig is. Zo niet, login-scherm displayen

include ("header.php");

?>
<div id="main">
	<h1>Onderhoud soort uren</h1>
			
<?php

/**
 * Dit is het begin van de code wat uitgevoerd wordt indien het formulier is gesubmit
 * Welk gedeelte van de code is afhankelijk van de button waarop geclickt is.
 */

// ------------------------------------------------------------------------------------------------------
// BUTTON Cancel
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
    header("location: soorturen.php?aktie=disp");
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Nieuw
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['nieuw'])) {
    header("location: add_soortuur.php");
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Delete
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['delete'])) {
    $delcode = $_POST['code'];
    $sql_code = "SELECT * FROM uren
                 WHERE soortuur='" . $delcode . "'";

    if ($sql_out = mysqli_query($dbconn, $sql_code)) {
        if (mysqli_num_rows($sql_out) > 0) {
            writelog("soortuur", "WARN", "De code " . $_POST['code'] . " kan niet verwijderd worden omdat er nog uren aan gekoppeld zijn");

            echo '<p class="errmsg"> ERROR: Code kan niet verwijderd worden. Er zijn nog uren gekoppeld aan deze code</p>';
            $focus = 'code';
            $formerror = 1;
        } else {
            $sql_code = "DELETE FROM soorturen
                         WHERE code = '$delcode'";
            $sql_out = mysqli_query($dbconn, $sql_code);

            writelog("soortuur", "INFO", "De code " . $_POST['code'] . " is succesvol verwijderd");

            header("location: soorturen.php?aktie=disp");
        }
    }
}

// ------------------------------------------------------------------------------------------------------
// BUTTON Save
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['save'])) {
    form_soorturen_fill('save');

    if ((! $_POST['code'] || $_POST['code'] == "") && (! $formerror)) {
        echo '<p class="errmsg"> ERROR: Code is een verplicht veld</p>';
        $focus = 'code';
        $formerror = 1;
    }

    if ((! $_POST['omschrijving'] || $_POST['omschrijving'] == "") && (! $formerror)) {
        echo '<p class="errmsg"> ERROR: Omschrijving is een verplicht veld</p>';
        $focus = 'omschrijving';
        $formerror = 1;
    }

    // Update record indien er geen errors zijn
    if (! $formerror) {
        if (! isset($_POST['facturabel'])) {
            $_POST['facturabel'] = 0;
        } else {
            $_POST['facturabel'] = 1;
        }

        $_POST['code'] = strtoupper($_POST['code']);
        $sql_code = "UPDATE soorturen
		             SET code = '" . $_POST['code'] . "', 
		             omschrijving = '" . $_POST['omschrijving'] . "',
                     facturabel = '" . $_POST['facturabel'] . "'
                     WHERE ID = '" . $_POST['ID'] . "'";
        $sql_out = mysqli_query($dbconn, $sql_code);
        if (!$sql_out) {
            writelog("soorturen", "ERROR", "Update van soorturen gaat fout: " . $sql_code . " - " . mysqli_error($dbconn));
            exit($MSGDB001E);
        } else {
            writelog("soortuur", "INFO", "De code " . $_POST['code'] . " is succesvol ge-update");
         
            echo '<p class="infmsg">Soort uur <b>' . $_POST['code'] . '</b> is gewijzigd</p>.';
            $frm_code = "";
            $frm_omschrijving = "";
            $frm_facturabel = "";
        }
        header("location: soorturen.php?aktie=disp");
    }
}

// ------------------------------------------------------------------------------------------------------
//
// ******************* START *******************
//
// Dit wordt uitgevoerd wanneer de user op de link Onderhoud soort uren heeft geklikt
// Er wordt een lijst met de soorten uren getoond waarop uren geboekt kunnen worden
// ------------------------------------------------------------------------------------------------------
if ($aktie == 'disp') {
    ?> <form name="disp" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"> <?php 
    $sql_code = "SELECT * FROM soorturen
                 ORDER BY code";
    $sql_out = mysqli_query($dbconn, $sql_code);

    echo "<center><table>";
    echo "<tr><th>Code</th><th>Omschrijving</th><th>Facturabel</th><th colspan=\"3\" align=\"center\">Akties</th></tr>";

    while ($sql_row = mysqli_fetch_array($sql_out)) {
        $id = $sql_row['ID'];
        $code = $sql_row['code'];
        $omschrijving = $sql_row['omschrijving'];
        $facturabel = $sql_row['facturabel'];

        if ($facturabel == 1) {
            $facturabel_checked = 'checked';
        } else {
            $facturabel_checked = '';
        }

        echo '<tr class="colored">
			<td><b>' . $code . '</b></td><td>' . $omschrijving . '</td><td><center><input type="checkbox" id="facturabel" name="facturabel" readonly ' . $facturabel_checked . '></center></td>
			<td><a href="soorturen.php?aktie=edit&edtcode=' . $code . '"><img class="button" src="./img/icons/edit-48.png" alt="wijzigen soort uur" title="wijzig soort uur ' . $code . '" /></a></td>
			<td><a href="soorturen.php?aktie=delete&edtcode=' . $code . '"><img class="button" src="./img/icons/trash-48.png" alt="delete soort uur" title="delete soort uur ' . $code . '" /></a></td>
			<td><a href="add_soortuur.php"><img class="button" src="./img/icons/add-48.png" alt="toevoegen soort uur" title="toevoegen soort uur" /></a></td>
			</tr>';
    }
    echo "</table></center>";
    echo '<input class="button" type="submit" name="nieuw" value="nieuw">';
    echo "</form>";
}

// ------------------------------------------------------------------------------------------------------
// Wordt uitgevoerd wanneer men op de button klikt om te wijzigen of te deleten
// ------------------------------------------------------------------------------------------------------
if ($aktie == 'edit' || $aktie == 'delete') {
    $edtcode = $_GET['edtcode'];
    $focus = "omschrijving";
    $sql_code = "SELECT * FROM soorturen
                 WHERE code = '$edtcode'";
    $sql_out = mysqli_query($dbconn, $sql_code);

    while ($sql_row = mysqli_fetch_array($sql_out)) {
        global $frm_code, $frm_omschrijving, $frm_facturabel, $formerror;
        $formerror = 0;
        $frm_ID = $sql_row['ID'];
        $frm_code = $sql_row['code'];
        $frm_omschrijving = $sql_row['omschrijving'];
        $frm_facturabel = $sql_row['facturabel'];
    }
    if ($frm_facturabel == 1) {
        $facturabel_checked = 'checked';
    } else {
        $facturabel_checked = '';
    }
    ?>
	<form name="soorturen" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<p>
		<table>
			<tr>
				<td>ID</td>
				<td><input type="text" readonly name="ID" size="4" maxlength="8" value="<?php if (isset($frm_ID)) { echo $frm_ID; } ?>"></td>
			</tr>
			<tr>
				<td>Code</td>
				<td><input type="text" readonly name="code" size="10" maxlength="10" value="<?php if (isset($frm_code)) { echo $frm_code; } ?>" ></td>
			</tr>
			<tr>
				<td>Omschrijving</td>
				<td><input type="text" name="omschrijving" size="60" maxlength="60" value="<?php if (isset($frm_omschrijving)) { echo $frm_omschrijving; } ?>" required></td>
			</tr>
			<tr>
				<td>Facturabel</td>
				<td><input type="checkbox" id="facturabel" name="facturabel" <?php echo $facturabel_checked; ?>></td>
			</tr>
		</table>
		<br />
		<?php
        if ($aktie == 'edit') {
            echo '<input class="button" type="submit" name="save" value="save">';
        } elseif ($aktie == 'delete') {
            echo '<input class="button" type="submit" name="delete" value="delete" onClick="return confirmDelSoortuur()">';
        }
        ?>
		<input class="button" type="submit" name="cancel" value="cancel" formnovalidate>
		</p>
	</form>
	<br />		
	<?php
    if (! isset($focus)) {
        $focus = 'omschrijving';
    }
    setfocus('soorturen', $focus);
}
	
include ("footer.php");
?>