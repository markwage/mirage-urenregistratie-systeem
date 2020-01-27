<?php
session_start();
include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

// Controleren of gebruiker admin-rechten heeft
// check_admin();

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

?>
<div id="main">		
	<h1>Toevoegen nieuwsbericht</h1>
			
<?php 
//This code runs if the form has been submitted
if (isset($_POST['cancel'])) 
{
	header("location: nieuws.php?aktie=disp");
}

if (isset($_POST['submit'])) 
{ 
	//form_nieuws_fill('toevoegen');
    $formerror = 0;
    
    if (isset($frm_nieuwsheader)) 
    {
        $nieuwsheader = $frm_nieuwsheader;
    }
    
	if (!$_POST['nieuwsheader']) 
	{	    
	    //writeLogRecord("add_nieuws","ERROR Verplicht inputveld nieuwsheader is niet ingevuld");
		echo '<p class="errmsg"> ERROR: Nieuwsheader is een verplicht veld</p>';
		$focus     = 'nieuwsheader';
		$formerror = 1;
	} 
	else 
	{
	    $frm_nieuwsheader = $_POST['nieuwsheader'];
	}
	
	if ((!$_POST['nieuwsbericht'])  && (!$formerror)) 
	{
		echo '<p class="errmsg"> ERROR: Nieuwsbericht is een verplicht veld</p>';
		$focus     = 'nieuwsbericht';
		$formerror = 1;
	}
	
	// Normaal wordt hier gechecked of de ingevulde velden al bestaan in de database maar dat is voor nieuwsberichten niet nodig

	// Toevoegen nieuwsbericht in de database
	if (!$formerror) 
	{ 
		// Record toevoegen in database
		$sql_code = "INSERT INTO nieuws (nieuwsheader, nieuwsbericht)
			      VALUES ('".$_POST['nieuwsheader']."', 
                          '".$_POST['nieuwsbericht']."')";
		$sql_out = mysqli_query($dbconn, $sql_code);

		if ($sql_out) 
		{
		    writelog("add_nieuws","INFO","Nieuwsbericht is succesvol toegevoegd aan de database");
		    
			echo '<p class="infmsg">Het nieuwsbericht is opgenomen</p>.';
			$frm_nieuwsheader  = "";
			$frm_nieuwsbericht = "";
			header("location: nieuws.php?aktie=disp"); 
		}
		else 
		{
			echo '<p class="errmsg">Er is een fout opgetreden bij het toevoegen van nieuwsbericht. Probeer het nogmaals.<br />
			Indien het probleem zich blijft voordoen neem dan contact op met de webmaster</p>';
		}
	}
} 

?>
 
<form name="nieuws" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 	<p>
	<table>
		<tr>
			<td>Nieuwsheader</td>
			<td><input type="text" name="nieuwsheader" size="80" maxlength="128" value="<?php if (isset($frm_nieuwsheader)) { echo $frm_nieuwsheader; } ?>"></td>
		</tr>
		<tr>
			<td>Nieuwsericht</td>
			<td><textarea id="area1" name="nieuwsbericht"><?php if (isset($frm_nieuwsbericht)) { echo $frm_nieuwsbericht; } ?></textarea></td>
		</table>
	<br />
	<input class="button" type="submit" name="submit" value="add nieuwsbericht">
	<input class="button" type="submit" name="cancel" value="cancel">
	</p>
</form>
<br />		
<?php 
if (!isset($focus)) 
{
	$focus='nieuwsheader';
}
setfocus('nieuws', $focus);
	
include ("footer.php");
?>		
