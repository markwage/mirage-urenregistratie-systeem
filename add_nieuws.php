<?php
session_start();
include ("config.php");
include ("mysqli_connewct.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_admin();
check_cookies();

include ("header.php");

?>
<div id="main">
	<h1>Toevoegen nieuwsbericht</h1>
			
<?php
// This code runs if the form has been submitted
if (isset($_POST['cancel'])) {
    header("location: nieuws.php?aktie=disp");
}

if (isset($_POST['submit'])) {
    $formerror = 0;

    if (isset($frm_nieuwsheader)) {
        $nieuwsheader = $frm_nieuwsheader;
    }

    if (! $_POST['nieuwsheader']) {
        echo '<p class="errmsg"> ERROR: Nieuwsheader is een verplicht veld</p>';
        $focus = 'nieuwsheader';
        $formerror = 1;
    } else {
        $frm_nieuwsheader = $_POST['nieuwsheader'];
    }

    if ((! $_POST['nieuwsbericht']) && (! $formerror)) {
        echo '<p class="errmsg"> ERROR: Nieuwsbericht is een verplicht veld</p>';
        $focus = 'nieuwsbericht';
        $formerror = 1;
    }

    // Normaal wordt hier gechecked of de ingevulde velden al bestaan in de database maar dat is voor nieuwsberichten niet nodig

    // Toevoegen nieuwsbericht in de database
    if (! $formerror) {
        // Record toevoegen in database
        try {
            $stmt_ins = $mysqli->prepare("INSERT INTO nieuws (nieuwsheader, nieuwsbericht) VALUES (?, ?)");
            $stmt_ins->bind_param("ss", $_POST['nieuwsheader'], $_POST['nieuwsbericht']);
            $stmt_ins->execute();
        } catch(Exception $e) {
            writelog("add_nieuws", "ERROR", $e);
            exit($MSGDB001E);
        }
        $frm_nieuwsheader = "";
        $frm_nieuwsbericht = "";
        writelog("add_nieuws", "INFO", "Nieuwsbericht is succesvol toegevoegd aan de database");
        header("location: nieuws.php?aktie=disp");
    }
}

?>
 
<form name="nieuws" action="<?php echo $_SERVER['PHP_SELF']; ?>"
		method="post">
		<p>
		
		
		<table>
			<tr>
				<td>Nieuwsheader</td>
				<td><input type="text" name="nieuwsheader" size="80" maxlength="128"
					value="<?php if (isset($frm_nieuwsheader)) { echo $frm_nieuwsheader; } ?>"></td>
			</tr>
			<tr>
				<td>Nieuwsericht</td>
				<td><textarea id="area1" name="nieuwsbericht"><?php if (isset($frm_nieuwsbericht)) { echo $frm_nieuwsbericht; } ?></textarea></td>
		
		</table>
		<br /> <input class="button" type="submit" name="submit" value="add nieuwsbericht"> 
		<input class="button" type="submit" name="cancel" value="cancel">
		</p>
	</form>
	<br />		
<?php
if (! isset($focus)) {
    $focus = 'nieuwsheader';
}
setfocus('nieuws', $focus);
	
include ("footer.php");
?>		
