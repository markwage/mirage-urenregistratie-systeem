<?php
session_start();
include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

check_admin();     // Controleren of gebruiker admin-rechten heeft
check_cookies();   // Controleren of cookie aanwezig is. Zo niet, login-scherm displayen

include ("header.php");

?>
<div id="main">		
	<h1>Toevoegen nieuw soort uur</h1>
			
<?php 
/**
 * Dit is het begin van de code wat uitgevoerd wordt indien het formulier is gesubmit
 * Welk gedeelte van de code is afhankelijk van de button waarop geclickt is.
 */
if (isset($_POST['cancel'])) 
{
	header("location: soorturen.php?aktie=disp");
}

if (isset($_POST['submit'])) 
{
	form_soorturen_fill('toevoegen');
	
	$_POST['code'] = strtoupper($_POST['code']);
	
	if (!$_POST['code']) 
	{
		echo '<p class="errmsg"> ERROR: Code is een verplicht veld</p>';
		$focus     = 'code';
		$formerror = 1;
	} 
	
	if (!$_POST['omschrijving'] && (!$formerror)) 
	{
		echo '<p class="errmsg"> ERROR: Omschrijving is een verplicht veld</p>';
		$focus     = 'omschrijving';
		$formerror = 1;
	}
	
	// Controle of het soort uur al in gebruik is
	if (!get_magic_quotes_gpc()) 
	{
		$_POST['code'] = addslashes($_POST['code']);
	}
	
	$check_soortuur = $_POST['code'];
	$sql_code = "SELECT code FROM soorturen 
                 WHERE code = '$check_soortuur'";
	$sql_out = mysqli_query($dbconn, $sql_code);
	
	if (!$sql_out) 
	{
	    writelogrecord("add_soortuur","ERROR Er is een fout opgetreden bij het uitvoeren van een query -> ".mysqli_error($dbconn));
	    echo '<p class="errmsg">Er is een fout opgetreden bij het toevoegen van de code. Probeer het nogmaals.<br />
			Indien het probleem zich blijft voordoen neem dan contact op met de webmaster</p>';
	    $focus     = 'code';
	    $formerror = 1;
	    // Proberen om een functie te maken (sqlerror() )en dan een error-screen te displayen.
	    //   Meegeven scriptnaam, 
	    // Uitvoeren middels header("location: sql_error.php?message='een message'?errorcode='errorcode'");
	    // Als dat lukt kan de else weg
	} 
	else 
	{
	    $sql_rows = mysqli_num_rows($sql_out);

	    // Als de soort uur bestaat een error displayen
	    if ($sql_rows != 0) 
	    {
	        writelogrecord("add_soortuur","ERROR Er is geprobeerd soortuur ".$_POST['code']." aan te maken terwijl deze al bestaat");
		    //echo '<p class="errmsg"> ERROR: Code '.$_POST['code'].' is al aanwezig.</p>';
	        echo "<p class='errmsg'> ERROR: Code {$_POST['code']} is al aanwezig.</p>";
		    $focus     = 'code';
		    $formerror = 1;
	    }
    }

	// Encrypt het wachtwoord en voeg eventueel slashes toe
	if (!$formerror) 
	{ 
		if (!get_magic_quotes_gpc()) 
		{
			$_POST['code'] = addslashes($_POST['code']);
		}
		
		// Record toevoegen in database
		$sql_code = "INSERT INTO soorturen (code, omschrijving)
			         VALUES ('".$_POST['code']."', 
					         '".$_POST['omschrijving']."')";
		$sql_out = mysqli_query($dbconn, $sql_code);

		if ($sql_out) 
		{ 
		    writelogrecord("add_soortuur","INFO Soortuur {$_POST['code']} ({$_POST['omschrijving']}) is succesvol gecreeerd");
		    echo "<p class='infmsg'>Code <b>{$_POST['code']}</b> is opgenomen</p>.";
			$frm_code         = "";
			$frm_omschrijving = "";
			header("location: soorturen.php?aktie=disp"); 
		} 
		else 
		{
			echo '<p class="errmsg">Er is een fout opgetreden bij het toevoegen van de code. Probeer het nogmaals.<br />
			Indien het probleem zich blijft voordoen neem dan contact op met de webmaster</p>';
		}
	}
} 

?>
 
<form name="soorturen" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 	<p>
	<table>
		<tr>
			<td>Code</td>
			<td><input style="text-transform: uppercase" type="text" name="code" size="10" maxlength="8" value="<?php if (isset($frm_code)) { echo $frm_code; } ?>"></td>
		</tr>
		<tr>
			<td>Omschrijving</td>
			<td><input type="text" name="omschrijving" size="60" maxlength="60" value="<?php if (isset($frm_omschrijving)) { echo $frm_omschrijving; } ?>"></td>
	</table>
	<br />
	<input class="button" type="submit" name="submit" value="add soort uur">
	<input class="button" type="submit" name="cancel" value="cancel">
	</p>
</form>
<br />	
	
<?php 
if (!isset($focus)) 
{
	$focus='code';
}
setfocus('soorturen', $focus);
	
include ("footer.php");
?>		
