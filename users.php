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

// Controleren of gebruiker admin-rechten heeft
// Indien het het wijzigen van het eigen profiel betreft hoeft hij geen admin-rechten te hebben
if (!$aktie == "editprof") 
{
    writelog("users","INFO","Aktie ".$aktie." is uitgevoerd");
    check_admin();
}

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

?>
<div id="main">		
	<h1>Usermanagement</h1>
			
<?php 
//------------------------------------------------------------------------------------------------------
// From here this code runs if the form has been submitted
//------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------
// BUTTON Cancel
// Wanneer het geen admin betreft wordt de hoofdpagina getoond. Indien wel adminrechten dan wordt de
// lijst met alle users getoond
//------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) 
{
    
    if (!isset($_SESSION['admin']) || (!$_SESSION['admin'])) 
    {
        header("location: index.php");
    } 
    else 
    {
        header("location: users.php?aktie=disp");
    }
}

//------------------------------------------------------------------------------------------------------
// BUTTON Delete
//------------------------------------------------------------------------------------------------------
if (isset($_POST['delete'])) 
{
	$deluser = $_POST['username'];
	$sql_code = "DELETE FROM users
                 WHERE username = '$deluser'";
	$sql_out = mysqli_query($dbconn, $sql_code);
	writelog("users","INFO","User ".$deluser." is succesvol verwijderd");
	
	header("location: users.php?aktie=disp");
}

//------------------------------------------------------------------------------------------------------
// BUTTON Save
//------------------------------------------------------------------------------------------------------
if (isset($_POST['save'])) 
{
    
    form_user_fill('save');
	
	// Checks wanneer password OF verificatiepassword niet leeg zijn
	if (($_POST['pass']) != "" || ($_POST['pass2']) != "") 
	{
	    
	    if (!$_POST['pass'] && (!$formerror)) 
	    {
			//echo '<p class="errmsg"> ERROR: Wachtwoord is een verplicht veld</p>';
			echo '<blockquote class="error">ERROR: Wachtwoord is een verplicht veld</blockquote>';
			$focus     = 'pass';
			$formerror = 1;
		}
		
		if (!$_POST['pass2'] && (!$formerror)) 
		{
			echo '<blockquote class="error">ERROR: Wachtwoord ter verificatie is een verplicht veld</blockquote>';
			$focus     = 'pass2';
			$formerror = 1;
		}
		
		// Check of de wachtwoorden gelijk zijn
		if (($_POST['pass'] != $_POST['pass2']) && (!$formerror)) 
		{
		    echo '<blockquote class="error">ERROR: De wachtwoorden zijn niet gelijk. Probeer het nogmaals.</blockquote>';
		    $focus     = 'pass';
		    $formerror = 1;
		}
	}
	
	if ((!$_POST['voornaam'] || $_POST['voornaam'] == "") && (!$formerror)) 
	{
	    echo '<blockquote class="error">ERROR: Voornaam is een verplicht veld</blockquote>';
		$focus     = 'voornaam';
		$formerror = 1;
	}
	
	if (!$_POST['achternaam'] && (!$formerror)) 
	{
	    echo '<blockquote class="error">ERROR: Achternaam is een verplicht veld</blockquote>';
		$focus     = 'achternaam';
		$formerror = 1;
	}
	
	if (!$_POST['email'] && (!$formerror)) 
	{
		echo '<blockquote class="error">ERROR: Emailadres is een verplicht veld</blockquote>';
		$focus     = 'email';
		$formerror = 1;
	}
	
	if (!$formerror)
	{
		if (!isset($_POST['admin'])) 
		{
		    $_POST['admin'] = 0;
		}
		else 
		{
		    $_POST['admin'] = 1;
		}
		
		if (!isset($_POST['ureninvullen']))
		{
		    $_POST['ureninvullen'] = 0;
		}
		else
		{
		    $_POST['ureninvullen'] = 1;
		}
		
		if (!isset($_POST['indienst'])) 
		{
		    $_POST['indienst'] = 0;
		}
		else 
		{
		    $_POST['indienst'] = 1;
		}
		
		if (!isset($_POST['approvenallowed'])) 
		{
		    $_POST['approvenallowed'] = 0;
		}
		else 
		{
		    $_POST['approvenallowed'] = 1;
		}
	//}
	
	// here we encrypt the password and add slashes if needed
	//if (!$formerror) 
	//{
	    $sql_code = "UPDATE users SET ";
	    
	    if (!$_POST['pass'] == "") 
	    {
	        $_POST['pass'] = md5($_POST['pass']);
	        
	        if (!get_magic_quotes_gpc()) 
	        {
	            $_POST['pass'] = addslashes($_POST['pass']);
	            $_POST['username'] = addslashes($_POST['username']);
	        }
	        $sql_code .= "password='".$_POST['pass']."',";
	    }
	    
	    $sql_code .= "admin='".$_POST['admin']."',
        uren_invullen='".$_POST['ureninvullen']."',
		voornaam='".$_POST['voornaam']."',
		tussenvoegsel='".$_POST['tussenvoegsel']."',
		achternaam='".$_POST['achternaam']."',
		emailadres='".$_POST['email']."',
		indienst='".$_POST['indienst']."',
        approvenallowed='".$_POST['approvenallowed']."' 
        WHERE username = '".$_POST['username']."'";
	    $sql_out = mysqli_query($dbconn, $sql_code);
	    
		if ($sql_out) 
		{ 
		    writelog("users","INFO","De query voor updaten user is succesvol uitgevoerd voor user ".$frm_username);
		    
			echo '<p class="infmsg">User <b>'.$_POST['username'].'</b> is gewijzigd</p>.';
			$frm_username      = "";
			$frm_pass          = "";
			$frm_pass2         = "";
			$frm_voornaam      = "";
			$frm_tussenvoegsel = "";
			$frm_achternaam    = "";
			$frm_email         = "";
		}
		else 
		{
		    writelog("users","ERROR","De query voor updaten user is fout gegaan - ".mysqli_error($dbconn));
			echo '<blockquote class="error">ERROR: Er is een fout opgetreden bij het toevoegen van de user. Probeer het nogmaals.<br />
			Indien het probleem zich blijft voordoen neem dan contact op met de webmaster</blockquote>';
		}
		if ($aktie == 'editprof')
		{
		    header("location: users.php?aktie=editprof&user=".$_SESSION['username']);
		}
		header("location: users.php?aktie=disp"); 
	}
}

//------------------------------------------------------------------------------------------------------
// START Dit wordt uitgevoerd wanneer de user op Usermanagement heeft geklikt
// Er wordt een lijst met de users getoond
//------------------------------------------------------------------------------------------------------
if ($aktie == 'disp') 
{
    $sql_code = "SELECT * FROM users
                 ORDER BY achternaam";
	$sql_out = mysqli_query($dbconn, $sql_code);
	echo "<center><table>";
	echo "<tr><th>Naam medewerker</th><th>Username</th><th>Emailadres</th><th>last<br />logged in</th><th>Admin</th><th>In dienst</th><th>Mag<br />approven</th><th>Uren<br />invullen</th><th colspan=\"3\" align=\"center\">Akties</th></tr>";
	$rowcolor = 'row-a';
	
	while($sql_rows = mysqli_fetch_array($sql_out)) 
	{
		$id              = $sql_rows['ID'];
		$username        = $sql_rows['username'];
		$voornaam        = $sql_rows['voornaam'];
		$tussenvoegsel   = $sql_rows['tussenvoegsel'];
		$achternaam      = $sql_rows['achternaam'];
		$emailadres      = $sql_rows['emailadres'];
		$lastloggedin    = $sql_rows['lastloggedin'];
		$admin           = $sql_rows['admin'];
		$uren_invullen   = $sql_rows['uren_invullen'];
		$indienst        = $sql_rows['indienst'];
		$approvenallowed = $sql_rows['approvenallowed'];
		echo '<tr class="'.$rowcolor.'">
			<td><strong>'.$achternaam.', '.$voornaam.' '.$tussenvoegsel.'</strong></td>
            <td>'.$username.'</td>
			<td>'.$emailadres.'</td>
            <td>'.$lastloggedin.'</td>';
		if ($admin == 1) 
		{
		    echo '<td style="text-align:center;"><img class="button" src="./img/buttons/icons8-thumbs-up-48.png" alt="1" title="heeft adminrechten" /></td>';
		}
		else 
		{
		    echo '<td style="text-align:center;"><img class="button" src="./img/buttons/icons8-thumbs-down-48.png" alt="0" title="heeft geen adminrechten" /></td>';
		}
		
		if ($indienst == 1) 
		{
		    echo '<td style="text-align:center;"><img class="button" src="./img/buttons/icons8-thumbs-up-48.png" alt="1" title="medewerker is nog in dienst" /></td>';
		}
		else 
		{
		    echo '<td style="text-align:center;"><img class="button" src="./img/buttons/icons8-thumbs-down-48.png" alt="0" title="medewerker is niet meer in dienst" /></td>';
		}
		
		if ($approvenallowed == 1) 
		{
		    echo '<td style="text-align:center;"><img class="button" src="./img/buttons/icons8-thumbs-up-48.png" alt="1" title="medewerker heeft rechten om uren te approven" /></td>';
		}
		else 
		{
		    echo '<td style="text-align:center;"><img class="button" src="./img/buttons/icons8-thumbs-down-48.png" alt="0" title="medewerker heeft geen rechten om uren te approven" /></td>';
		}
		
		if ($uren_invullen == 1)
		{
		    echo '<td style="text-align:center;"><img class="button" src="./img/buttons/icons8-thumbs-up-48.png" alt="1" title="verplicht uren invullen" /></td>';
		}
		else
		{
		    echo '<td style="text-align:center;"><img class="button" src="./img/buttons/icons8-thumbs-down-48.png" alt="0" title="hoeft geen uren in te vullen" /></td>';
		}
		$username_encrypted = convert_string('encrypt', $username);
		echo '<td><a href="users.php?aktie=edit&edtuser='.$username_encrypted.'"><img class="button" src="./img/buttons/icons8-edit-48.png" alt="wijzigen user" title="wijzig user '.$username.'" /></a></td>
		<td><a href="users.php?aktie=delete&edtuser='.$username_encrypted.'"><img class="button" src="./img/buttons/icons8-trash-can-48.png" alt="delete user" title="delete user '.$username.'" /></a></td>
		<td><a href="add_user.php"><img class="button" src="./img/buttons/icons8-plus-48.png" alt="toevoegen nieuwe user" title="toevoegen nieuwe user" /></a></td>
		</tr>';
		
		check_row_color($rowcolor);
	}
	echo "</table></center>";
}

//------------------------------------------------------------------------------------------------------
// Wordt uitgevoerd wanneer men op de button klikt om te wijzigen of te deleten of om het eigen
// profiel aan te passen
//------------------------------------------------------------------------------------------------------
if ($aktie == 'edit' || $aktie == 'delete' || $aktie == 'editprof') 
{
	//$edtuser = $_GET['edtuser'];
    $edtuser = convert_string('decrypt', $_GET['edtuser']);
	$focus = "pass";
	$sql_code = "SELECT * FROM users
                 WHERE username = '$edtuser'";
	$sql_out = mysqli_query($dbconn, $sql_code);
	
	while($sql_rows = mysqli_fetch_array($sql_out)) 
	{
		$frm_username = $sql_rows['username'];
		$frm_voornaam = $sql_rows['voornaam'];
		$frm_tussenvoegsel = $sql_rows['tussenvoegsel'];
		$frm_achternaam = $sql_rows['achternaam'];
		$frm_email = $sql_rows['emailadres'];
		
		if ($sql_rows['admin'] == 1) 
		{
		    $frm_admin = "checked";
		}
		else 
		{
		    $frm_admin = "";
		}
		
		if ($sql_rows['uren_invullen'] == 1)
		{
		    $frm_uren_invullen = "checked";
		}
		else
		{
		    $frm_uren_invullen = "";
		}
		
		if ($sql_rows['indienst'] == 1) 
		{
		    $frm_indienst = "checked";
		}
		else 
		{
		    $frm_indienst = "";
		}
		
		if ($sql_rows['approvenallowed'] == 1) 
		{
		    $frm_approvenallowed = "checked";
		}
		else 
		{
		    $frm_approvenallowed = "";
		}
	}
    ?>
	<form name="AddUser" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 		<p>
		<table>
			<tr>
				<td><b>Username</b></td>
				<td><input <?php if ($aktie == "edit" || $aktie == "editprof") { echo "readonly"; } ?> type="text" name="username" maxlength="40" value="<?php if (isset($frm_username)) { echo $frm_username; } ?>"></td>
			</tr>
			<tr>
				<td>Wachtwoord</td>
				<td><input type="password" name="pass" maxlength="10" value="<?php if (isset($frm_pass)) { echo $frm_pass; } ?>"></td>
				<td>Confirm</td>
				<td><input type="password" name="pass2" maxlength="10" value="<?php if (isset($frm_pass2)) { echo $frm_pass2; } ?>"></td>
			</tr>
			<tr>
				<td>Admin</td>
				<!--  <td><input type="checkbox" <?php if (!$_SESSION['admin']) { echo "checked disabled "; } ?>name="admin" <?php { echo $frm_admin; } ?>></td> -->
				<td><div class="onoffswitch">
    			<input type="checkbox" name="admin" class="onoffswitch-checkbox" id="myonoffswitch" <?php if (!$_SESSION['admin']) { echo "disabled "; } ?><?php { echo $frm_admin; } ?>>
    			<label class="onoffswitch-label" for="myonoffswitch">
        			<span class="onoffswitch-inner"></span>
        			<span class="onoffswitch-switch"></span>
    			</label>
				</div></td>
			</tr>
			<tr>
				<td>Approven</td>
				<!--  <td><input type="checkbox" <?php if (!$_SESSION['admin']) { echo "checked disabled "; } ?>name="admin" <?php { echo $frm_admin; } ?>></td> -->
				<td><div class="onoffswitch">
    			<input type="checkbox" name="approvenallowed" class="onoffswitch-checkbox" id="myonoffswitch3" <?php if (!$_SESSION['admin']) { echo "disabled "; } ?><?php { echo $frm_approvenallowed; } ?>>
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
			<tr>
				<td>In dienst</td>
				<!--  <td><input type="checkbox" <?php //if (!$_SESSION['admin']) { echo "disabled "; } ?>name="indienst" <?php //{ echo $frm_indienst; } ?>></td> -->
				<td><div class="onoffswitch">
    			<input type="checkbox" name="indienst" class="onoffswitch-checkbox" id="myonoffswitch2" <?php if (!$_SESSION['admin']) { echo "disabled "; } ?><?php { echo $frm_indienst; } ?>>
    			<label class="onoffswitch-label" for="myonoffswitch2">
        			<span class="onoffswitch-inner"></span>
        			<span class="onoffswitch-switch"></span>
    			</label>
				</div></td>
			</tr>
			<tr>
				<td>Uren invullen</td>
				<!--  <td><input type="checkbox" <?php //if (!$_SESSION['admin']) { echo "disabled "; } ?>name="indienst" <?php //{ echo $frm_indienst; } ?>></td> -->
				<td><div class="onoffswitch">
    			<input type="checkbox" name="ureninvullen" class="onoffswitch-checkbox" id="myonoffswitch4" <?php if (!$_SESSION['admin']) { echo "disabled "; } ?><?php { echo $frm_uren_invullen; } ?>>
    			<label class="onoffswitch-label" for="myonoffswitch4">
        			<span class="onoffswitch-inner"></span>
        			<span class="onoffswitch-switch"></span>
    			</label>
				</div></td>
			</tr>
		</table>
		<br />
		<?php 
		if ($aktie == 'edit' || $aktie == 'editprof') 
		{
		    echo '<input class="button" type="submit" name="save" value="save">'; 
		}
		if ($aktie == 'delete') 
		{
		    echo '<input class="button" type="submit" name="delete" value="delete" onClick="return confirmDelUser()">'; 
		}
		?>
		<input class="button" type="submit" name="cancel" value="cancel">
		</p>
	</form>
	<br />		
	<?php 
    if (!isset($focus)) 
    {
    	$focus='username';
    }
    setfocus('AddUser', $focus);
}
	
include ("footer.php");
?>		

