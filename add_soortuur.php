<?php
session_start();
include ("config.php");
include ("mysqli_connect.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

check_admin(); // Controleren of gebruiker admin-rechten heeft
check_cookies(); // Controleren of cookie aanwezig is. Zo niet, login-scherm displayen

include ("header.php");

?>
<div id="main">
	<h1>Toevoegen nieuw soort uur</h1>
			
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
// BUTTON Add om record toe te voegen
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['add_record'])) {
    form_soorturen_fill('toevoegen');

    $_POST['code'] = strtoupper($_POST['code']);

    if (! $_POST['code']) {
        echo '<p class="errmsg"> ERROR: Code is een verplicht veld</p>';
        $focus = 'code';
        $formerror = 1;
    }

    if (! $_POST['omschrijving'] && (! $formerror)) {
        echo '<p class="errmsg"> ERROR: Omschrijving is een verplicht veld</p>';
        $focus = 'omschrijving';
        $formerror = 1;
    }
    
    try {
        $stmt_code = $mysqli->prepare("SELECT code FROM soorturen WHERE code = ?");
        $stmt_code->bind_param("s", $_POST['code']);
        $stmt_code->execute();
    } catch(Exception $e) {
        writelog("beginsaldo", "ERROR", $e);
        exit($MSGDB001E);
    }
    $stmt_code->store_result();
    $aantal_rijen = $stmt_code->num_rows;
    // Als de soort uur bestaat een error displayen
    if ($aantal_rijen != 0) {
        writelog("add_soortuur", "WARN", "Er is geprobeerd soortuur {$_POST['code']} aan te maken terwijl deze al bestaat");
        echo "<p class='errmsg'> ERROR: Code {$_POST['code']} is al aanwezig.</p>";
        $focus = 'code';
        $formerror = 1;
    }

    // 
    if (! $formerror) {
        if (! isset($_POST['facturabel'])) {
            $_POST['facturabel'] = 0;
        } else {
            $_POST['facturabel'] = 1;
        }

        try {
            $stmt_ins = $mysqli->prepare("INSERT INTO soorturen (code, omschrijving, facturabel) VALUES (?, ?, ?)");
            $stmt_ins->bind_param("ssi", $_POST['code'], $_POST['omschrijving'], $_POST['facturabel']);
            $stmt_ins->execute();
        } catch(Exception $e) {
            writelog("add_soortuur", "ERROR", $e);
            exit($MSGDB001E);
        }
        $frm_code = "";
        $frm_omschrijving = "";
        writelog("add_soortuur", "INFO", "Soortuur {$_POST['code']} ({$_POST['omschrijving']}) is succesvol gecreeerd");
        header("location: soorturen.php?aktie=disp");
    }
}

?>
 
<form name="soorturen" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>
<table>
	<tr>
		<td>Code</td>
		<td><input style="text-transform: uppercase" type="text" name="code"
			size="10" maxlength="8"
			value="<?php if (isset($frm_code)) { echo $frm_code; } ?>"></td>
	</tr>
	<tr>
		<td>Omschrijving</td>
		<td><input type="text" name="omschrijving" size="60" maxlength="60"
			value="<?php if (isset($frm_omschrijving)) { echo $frm_omschrijving; } ?>"></td>
	</tr>
	<tr>
		<td>Facturabel</td>
		<td><input type="checkbox" id="facturabel" name="facturabel"></td>
	</tr>
</table>
<br /> 
<input class="button" type="submit" name="add_record" value="add"> <input class="button" type="submit" name="cancel" value="cancel">
</p>
</form>
<br />	
	
<?php
if (! isset($focus)) {
    $focus = 'code';
}
setfocus('soorturen', $focus);
	
include ("footer.php");
?>		
