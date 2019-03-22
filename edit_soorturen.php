<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");
if (isset($_GET['aktie'])) {
	$aktie = $_GET['aktie'];
}
else {
	$aktie = "";
}

// Controleren of gebruiker admin-rechten heeft
check_admin();

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

?>
<div id="main">		
	<h1>Onderhoud soort uren</h1>
			
<?php 

//------------------------------------------------------------------------------------------------------
// From here this code runs if the form has been submitted
//------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------
// BUTTON Cancel
//------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
	header("location: edit_soorturen.php?aktie=disp");
}

//------------------------------------------------------------------------------------------------------
// BUTTON Delete
//------------------------------------------------------------------------------------------------------
if (isset($_POST['delete'])) {
	$delcode = $_POST['code'];
	$sql_delsoortuur = mysqli_query($dbconn, "DELETE FROM soorturen WHERE code = '$delcode'");
	header("location: edit_soorturen.php?aktie=disp");
}

//------------------------------------------------------------------------------------------------------
// BUTTON Save
//------------------------------------------------------------------------------------------------------
if (isset($_POST['save'])) {
    form_soorturen_fill('save');
    writelogrecord("edit_soorturen","BTNSAVE Op save gedrukt om gewijzigd record op te slaan");
    //$formerror = 0;
	if ((!$_POST['code'] || $_POST['code'] == "") && (!$formerror)) {
	    writelogrecord("edit_soorturen","CHECK1A Het veld code is niet ingevuld");
		echo '<p class="errmsg"> ERROR: Code is een verplicht veld</p>';
		$focus     = 'code';
		$formerror = 1;
	}
	if ((!$_POST['omschrijving'] || $_POST['omschrijving'] == "") && (!$formerror)) {
	    writelogrecord("edit_soorturen","CHECK1B Het veld omschrijving is niet ingevuld");
		echo '<p class="errmsg"> ERROR: Omschrijving is een verplicht veld</p>';
		$focus     = 'omschrijving';
		$formerror = 1;
	}

	// Update record indien er geen errors zijn
	if (!$formerror) { 
		$_POST['code'] = strtoupper($_POST['code']);
		$update = "UPDATE soorturen SET 
		code = '".$_POST['code']."', 
		omschrijving = '".$_POST['omschrijving']."' WHERE ID = '".$_POST['ID']."'";
		$check_upd_soorturen = mysqli_query($dbconn, $update) or die ("Error in query: $update. ".mysqli_error($dbconn));
		if ($check_upd_soorturen) { 
		    writelogrecord("edit_soorturen","UPDATE Soortuur ".$_POST['code']." is succesvol ge-update");
			echo '<p class="infmsg">Soort uur <b>'.$_POST['cude'].'</b> is gewijzigd</p>.';
			$frm_code          = "";
			$frm_omschrijving  = "";
		}
		else {
			echo '<p class="errmsg">Er is een fout opgetreden bij het updaten van soort uur. Probeer het nogmaals.<br />
			Indien het probleem zich blijft voordoen neem dan contact op met de webmaster</p>';
		}
		header("location: edit_soorturen.php?aktie=disp"); 
	}
}

//------------------------------------------------------------------------------------------------------
// START Dit wordt uitgevoerd wanneer de user op Onderhoud soort uren heeft geklikt
// Er wordt een lijst met de uren getoond
//------------------------------------------------------------------------------------------------------
if ($aktie == 'disp') {
	$sql_soorturen = mysqli_query($dbconn, "SELECT * FROM soorturen ORDER BY code");
	echo "<center><table>";
	echo "<tr><th>Code</th><th>Omschrijving</th><th colspan=\"3\" align=\"center\">Akties</th></tr>";
	$rowcolor = 'row-a';
	while($row_soorturen = mysqli_fetch_array($sql_soorturen)) {
		$id           = $row_soorturen['ID'];
		$code         = $row_soorturen['code'];
		$omschrijving = $row_soorturen['omschrijving'];
		echo '<tr class="'.$rowcolor.'">
			<td><b>'.$code.'</b></td><td>'.$omschrijving.'</td>

			<td><a href="edit_soorturen.php?aktie=edit&edtcode='.$code.'"><img src="./img/buttons/icons8-edit-48.png" alt="wijzigen soort uur" title="wijzig soort uur '.$code.'" /></a></td>
			<td><a href="edit_soorturen.php?aktie=delete&edtcode='.$code.'"><img src="./img/buttons/icons8-trash-can-48.png" alt="delete soort uur" title="delete soort uur '.$code.'" /></a></td>
			<td><a href="add_soortuur.php"><img src="./img/buttons/icons8-plus-48.png" alt="toevoegen soort uur" title="toevoegen soort uur" /></a></td>
			</tr>';
		    //<td><a href="add_soortuur.php"><img src="./img/buttons/plus-green.gif" alt="toevoegen soort uur" title="toevoegen soort uur" /></a></td>
		if ($rowcolor == 'row-a') $rowcolor = 'row-b';
		else $rowcolor = 'row-a';
	}
	echo "</table></center>";
}

//------------------------------------------------------------------------------------------------------
// Wordt uitgevoerd wanneer men op de button klikt om te wijzigen of te deleten
//------------------------------------------------------------------------------------------------------
if ($aktie == 'edit' || $aktie == 'delete') {
	$edtcode = $_GET['edtcode'];
	$focus = "code";
	$sql_dspsoorturen = mysqli_query($dbconn, "SELECT * FROM soorturen WHERE code = '$edtcode'");
	while($row_dspsoorturen = mysqli_fetch_array($sql_dspsoorturen)) {
	    global $frm_code, $frm_omschrijving, $formerror;
	    $formerror = 0;
		$frm_ID   = $row_dspsoorturen['ID'];
		$frm_code = $row_dspsoorturen['code'];
		$frm_omschrijving = $row_dspsoorturen['omschrijving'];
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
				<td><input style="text-transform: uppercase" type="text" name="code" size="10" maxlength="8" value="<?php if (isset($frm_code)) { echo $frm_code; } ?>" required></td>
			</tr>
			<tr>
				<td>Omschrijving</td>
				<td><input type="text" name="omschrijving" size="60" maxlength="60" value="<?php if (isset($frm_omschrijving)) { echo $frm_omschrijving; } ?>" required></td>
			</tr>
			<!-- 
			<tr>
				<td>Weeknummer</td>
				<td><input type="week" name="week" id="camp-week" min="2019-W1" max="2019-W26" required></td>
			</tr-->
		</table>
		<br />
		<?php if ($aktie == 'edit') echo '<input class="button" type="submit" name="save" value="save">'; ?>
		<?php if ($aktie == 'delete') echo '<input class="button" type="submit" name="delete" value="delete" onClick="return confirmDelSoortuur()">'; ?>
		<input class="button" type="submit" name="cancel" value="cancel" formnovalidate>
		</p>
	</form>
	<br />		
	<?php 
    if (!isset($focus)) {
    	$focus='code';
    }
    setfocus('soorturen', $focus);
}
	
include ("footer.php");
?>		


