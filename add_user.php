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
	<h1>Toevoegen nieuwe user</h1>
			
<?php 
//This code runs if the form has been submitted
if (isset($_POST['cancel'])) 
{
	header("location: users.php?aktie=disp");
}

if (isset($_POST['submit'])) 
{ 
	form_user_fill('toevoegen');
	$formerror = 0;
	
	if (!$_POST['username']) 
	{
		echo '<p class="errmsg"> ERROR: Username is een verplicht veld</p>';
		$focus     = 'username';
		$formerror = 1;
	}
	
	if (!$_POST['pass'] && (!$formerror)) 
	{
		echo '<p class="errmsg"> ERROR: Password is een verplicht veld</p>';
		$focus     = 'pass';
		$formerror = 1;
	}
	
	if (!$_POST['pass2'] && (!$formerror)) 
	{
		echo '<p class="errmsg"> ERROR: Password voor verificatie is een verplicht veld</p>';
		$focus     = 'pass2';
		$formerror = 1;
	}
	
	if ($_SESSION['admin']) {
		if (!isset($_POST['admin'])) 
		{
		    $_POST['admin'] = 0;
		}
		else 
		{
		    $_POST['admin'] = 1;
		}
		
		if (!isset($_POST['approvenallowed'])) 
		{
		    $_POST['approvenallowed'] = 0;
		}
		else 
		{
		    $_POST['approvenallowed'] = 1;
		}
	}
	
	// Controleer of de username al in gebruik is
	if (!get_magic_quotes_gpc()) 
	{
		$_POST['username'] = addslashes($_POST['username']);
	}
	
	$usercheck = $_POST['username'];
	$sql_code = "SELECT username FROM users
                 WHERE username = '$usercheck'";
	$sql_out = mysqli_query($dbconn, $sql_code);
	$sql_num_rows = mysqli_num_rows($sql_out);

	//Als username al bestaat een error displayen
	if ($sql_num_rows != 0) 
	{
	    writelogrecord("add_user","ERROR Er is geprobeerd user ".$_POST['username']." aan te maken terwijl deze al bestaat");
		echo '<p class="errmsg"> ERROR: Username '.$_POST['username'].' is al aanwezig.</p>';
		$focus     = 'username';
		$formerror = 1;
	}
	
	// Controle of beide ingevoerde passwords gelijk zijn
	if (($_POST['pass'] != $_POST['pass2']) && (!$formerror)) 
	{
	    writelogrecord("add_user","ERROR De ingevoerde wachtwoorden zijn niet gelijk");
		echo '<p class="errmsg"> ERROR: De wachtwoorden zijn niet gelijk</p>';
		$focus     = 'pass2';
		$formerror = 1;
	}
	
	if (!$_POST['voornaam'] && (!$formerror)) 
	{
		echo '<p class="errmsg"> ERROR: Voornaam is een verplicht veld</p>';
		$focus     = 'voornaam';
		$formerror = 1;
	}
	
	if (!$_POST['achternaam'] && (!$formerror)) 
	{
		echo '<p class="errmsg"> ERROR: Achternaam is een verplicht veld</p>';
		$focus     = 'achternaam';
		$formerror = 1;
	}
	
	if (!$_POST['email'] && (!$formerror)) 
	{
		echo '<p class="errmsg"> ERROR: Email is een verplicht veld</p>';
		$focus     = 'email';
		$formerror = 1;
	}

	// Encrypt password en voeg eventueel slashes toe
	if (!$formerror) 
	{ 
		$_POST['indienst'] = 1;
		$_POST['pass'] = md5($_POST['pass']);
		
		if (!get_magic_quotes_gpc()) 
		{
			$_POST['pass'] = addslashes($_POST['pass']);
			$_POST['username'] = addslashes($_POST['username']);
		}

		// Record toevoegen in database
		$sql_code = "INSERT INTO users (username, password, admin, voornaam, tussenvoegsel, achternaam, emailadres, indienst, approvenallowed)
			       VALUES ('".$_POST['username']."', 
					       '".$_POST['pass']."', 
                           '".$_POST['admin']."',
					       '".$_POST['voornaam']."',
					       '".$_POST['tussenvoegsel']."',
					       '".$_POST['achternaam']."',
					       '".$_POST['email']."',
					       '".$_POST['indienst']."',
                           '".$_POST['approvenallowed']."')";
		            
		$sql_out = mysqli_query($dbconn, $sql_code);
	
		if ($sql_out) 
		{
		    writelogrecord("add_user","INFO User ".$_POST['username']." is succesvol aangemaakt");
			echo '<p class="infmsg">User <b>'.$_POST['username'].'</b> is opgenomen</p>.';
			$frm_username      = "";
			$frm_pass          = "";
			$frm_pass2         = "";
			$frm_voornaam      = "";
			$frm_tussenvoegsel = "";
			$frm_achternaam    = "";
			$frm_email         = "";
			header("location: users.php?aktie=disp"); 
		}
		else 
		{
			echo '<p class="errmsg">Er is een fout opgetreden bij het toevoegen van de user. Probeer het nogmaals.<br />
			Indien het probleem zich blijft voordoen neem dan contact op met de webmaster</p>';
		}
	}
} 

?>
 
<form name="AddUser" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 	<p>
	<table>
		<tr>
			<td><b>Username:</b></td>
			<td><input type="text" name="username" maxlength="40" value="<?php if (isset($frm_username)) { echo $frm_username; } ?>"></td>
		</tr>
		<tr>
			<td>Wachtwoord</td>
			<td><input type="password" name="pass" maxlength="10" value="<?php if (isset($frm_pass)) { echo $frm_pass; } ?>"></td>
			<td>Confirm</td>
			<td><input type="password" name="pass2" maxlength="10" value="<?php if (isset($frm_pass2)) { echo $frm_pass2; } ?>"></td>
		</tr>
		<tr>
			<td>Admin</td>
			<!-- <td><input type="checkbox" name="admin"></td> -->
			<td><div class="onoffswitch">
    			<input type="checkbox" name="admin" class="onoffswitch-checkbox" id="myonoffswitch">
    			<label class="onoffswitch-label" for="myonoffswitch">
        			<span class="onoffswitch-inner"></span>
        			<span class="onoffswitch-switch"></span>
    			</label>
			</div></td>
		</tr>
		<tr>
			<td>Approven</td>
			<!-- <td><input type="checkbox" name="admin"></td> -->
			<td><div class="onoffswitch">
    			<input type="checkbox" name="approvenallowed" class="onoffswitch-checkbox" id="myonoffswitch3">
    			<label class="onoffswitch-label" for="myonoffswitch3">
        			<span class="onoffswitch-inner"></span>
        			<span class="onoffswitch-switch"></span>
    			</label>
			</div></td>
		</tr>
		<tr>
			<td>Voornaam</td>
			<td><input type="text" name="voornaam" maxlength="24" value="<?php if (isset($frm_voornaam)) { echo $frm_voornaam; } ?>"></td>
		</tr>
		<tr>
			<td>Tussenv.</td>
			<td><input type="text" name="tussenvoegsel" maxlength="10" value="<?php if (isset($frm_tussenvoegsel)) { echo $frm_tussenvoegsel; } ?>"></td>
			<td>Achternaam</td>
			<td><input type="text" name="achternaam" maxlength="40" value="<?php if (isset($frm_achternaam)) { echo $frm_achternaam; } ?>"></td>
		</tr>
		<tr>
			<td>Email</td>
			<td colspan="2"><input type="text" name="email" size="40" maxlength="60" value="<?php if (isset($frm_email)) { echo $frm_email; } ?>"></td>
		</tr>
	</table>
	<br />
	<input class="button" type="submit" name="submit" value="add user">
	<input class="button" type="submit" name="cancel" value="cancel">
	<input type="hidden" name="indienst">
	</p>
</form>
<br />		
<?php 
if (!isset($focus)) 
{
	$focus='username';
}
setfocus('AddUser', $focus);
	
include ("footer.php");
?>		
