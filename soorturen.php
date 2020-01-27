<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

if (isset($_GET['aktie'])) 
{
	$aktie = $_GET['aktie'];
}
else 
{
	$aktie = "";
}

check_admin();     // Controleren of gebruiker admin-rechten heeft
check_cookies();   // Controleren of cookie aanwezig is. Zo niet, login-scherm displayen

include ("header.php");

?>
<div id="main">		
	<h1>Onderhoud soort uren</h1>
			
<?php 

/**
 * Dit is het begin van de code wat uitgevoerd wordt indien het formulier is gesubmit
 * Welk gedeelte van de code is afhankelijk van de button waarop geclickt is. 
 */

//------------------------------------------------------------------------------------------------------
// BUTTON Cancel
//------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) 
{
	header("location: soorturen.php?aktie=disp");
}

//------------------------------------------------------------------------------------------------------
// BUTTON Delete
//------------------------------------------------------------------------------------------------------
if (isset($_POST['delete'])) 
{
	$delcode = $_POST['code'];
	$sql_code = "SELECT * FROM uren
                 WHERE soortuur='".$delcode."'";
	
	if($sql_out = mysqli_query($dbconn, $sql_code)) 
	{
	    if(mysqli_num_rows($sql_out) > 0) 
	    {
	        writelog("soortuur","WARN","De code ".$_POST['code']." kan niet verwijderd worden omdat er nog uren aan gekoppeld zijn");
	        
	        echo '<p class="errmsg"> ERROR: Code kan niet verwijderd worden. Er zijn nog uren gekoppeld aan deze code</p>';
	        $focus     = 'code';
	        $formerror = 1;
	    } 
	    else 
	    {
	        $sql_code = "DELETE FROM soorturen
                         WHERE code = '$delcode'";
	        $sql_out = mysqli_query($dbconn, $sql_code);
	        
	        writelog("soortuur","INFO","De code ".$_POST['code']." is succesvol verwijderd");
	        
	        header("location: soorturen.php?aktie=disp");
	    }
	}
}

//------------------------------------------------------------------------------------------------------
// BUTTON Save
//------------------------------------------------------------------------------------------------------
if (isset($_POST['save'])) 
{
    form_soorturen_fill('save');
    
	if ((!$_POST['code'] || $_POST['code'] == "") && (!$formerror)) 
	{
		echo '<p class="errmsg"> ERROR: Code is een verplicht veld</p>';
		$focus     = 'code';
		$formerror = 1;
	}
	
	if ((!$_POST['omschrijving'] || $_POST['omschrijving'] == "") && (!$formerror)) 
	{
		echo '<p class="errmsg"> ERROR: Omschrijving is een verplicht veld</p>';
		$focus     = 'omschrijving';
		$formerror = 1;
	}

	// Update record indien er geen errors zijn
	if (!$formerror) 
	{ 
		$_POST['code'] = strtoupper($_POST['code']);
		$sql_code = "UPDATE soorturen
		           SET code = '".$_POST['code']."', 
		               omschrijving = '".$_POST['omschrijving']."' 
                   WHERE ID = '".$_POST['ID']."'";
		$sql_out = mysqli_query($dbconn, $sql_code) or die ("Error in query: $sql_code. ".mysqli_error($dbconn));
		
		if ($sql_out) { 
		    writelog("soortuur","INFO","De code ".$_POST['code']." is succesvol ge-update");
		    
			echo '<p class="infmsg">Soort uur <b>'.$_POST['cude'].'</b> is gewijzigd</p>.';
			$frm_code          = "";
			$frm_omschrijving  = "";
		}
		else 
		{
		    writelog("soortuur","ERROR","Database fout opgetreden bij update van  ".$_POST['code']);
		    
		    echo '<p class="errmsg">Er is een fout opgetreden bij het updaten van soort uur. Probeer het nogmaals.<br />
			Indien het probleem zich blijft voordoen neem dan contact op met de webmaster</p>';
		}
		header("location: soorturen.php?aktie=disp"); 
	}
}

//------------------------------------------------------------------------------------------------------
//
//       *******************   START   *******************
//
// Dit wordt uitgevoerd wanneer de user op de link Onderhoud soort uren heeft geklikt
// Er wordt een lijst met de soorten uren getoond waarop uren geboekt kunnen worden 
//------------------------------------------------------------------------------------------------------
if ($aktie == 'disp') 
{
    $sql_code = "SELECT * FROM soorturen
                 ORDER BY code";
	$sql_out = mysqli_query($dbconn, $sql_code);
	
	echo "<center><table>";
	echo "<tr><th>Code</th><th>Omschrijving</th><th colspan=\"3\" align=\"center\">Akties</th></tr>";
	$rowcolor = 'row-a';
	
	while($sql_row = mysqli_fetch_array($sql_out)) 
	{
	    $id           = $sql_row['ID'];
	    $code         = $sql_row['code'];
	    $omschrijving = $sql_row['omschrijving'];
	    
		echo '<tr class="'.$rowcolor.'">
			<td><b>'.$code.'</b></td><td>'.$omschrijving.'</td>
			<td><a href="soorturen.php?aktie=edit&edtcode='.$code.'"><img class="button" src="./img/buttons/icons8-edit-48.png" alt="wijzigen soort uur" title="wijzig soort uur '.$code.'" /></a></td>
			<td><a href="soorturen.php?aktie=delete&edtcode='.$code.'"><img class="button" src="./img/buttons/icons8-trash-can-48.png" alt="delete soort uur" title="delete soort uur '.$code.'" /></a></td>
			<td><a href="add_soortuur.php"><img class="button" src="./img/buttons/icons8-plus-48.png" alt="toevoegen soort uur" title="toevoegen soort uur" /></a></td>
			</tr>';
		check_row_color($rowcolor);
	}
	echo "</table></center>";
}

//------------------------------------------------------------------------------------------------------
// Wordt uitgevoerd wanneer men op de button klikt om te wijzigen of te deleten
//------------------------------------------------------------------------------------------------------
if ($aktie == 'edit' || $aktie == 'delete') 
{
	$edtcode = $_GET['edtcode'];
	$focus = "code";
	$sql_code = "SELECT * FROM soorturen
                 WHERE code = '$edtcode'";
	$sql_out = mysqli_query($dbconn, $sql_code);
	
	while($sql_row = mysqli_fetch_array($sql_out)) 
	{
	    global $frm_code, $frm_omschrijving, $formerror;
	    $formerror = 0;
		$frm_ID           = $sql_row['ID'];
		$frm_code         = $sql_row['code'];
		$frm_omschrijving = $sql_row['omschrijving'];
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
		</table>
		<br />
		<?php 
		if ($aktie == 'edit') 
		{
		    echo '<input class="button" type="submit" name="save" value="save">';
		}
		elseif ($aktie == 'delete') 
		{
		    echo '<input class="button" type="submit" name="delete" value="delete" onClick="return confirmDelSoortuur()">';
		}
		?>
		
		<input class="button" type="submit" name="cancel" value="cancel" formnovalidate>
		</p>
	</form>
	<br />		
	<?php 
    if (!isset($focus)) 
    {
    	$focus='code';
    }
    setfocus('soorturen', $focus);
}
	
include ("footer.php");
?>		


