<?php
session_start();

include ("./config.php");
include ("./db.php");
include ("./function.php");
include ("autoload.php");

include ("header.php");

?>
<div id="main">		
	<h1>Urenregistratie</h1>
			
<?php
// Indien het Login form is submitted
if (isset($_POST['submit'])) 
{
	//controleer of username en wachtwoord zijn ingevuld
	if (!$_POST['username'] || !$_POST['pass']) 
	{
	    echo '<blockquote class="error">ERROR: Niet alle verplichte velden zijn ingevuld</blockquote>';
	}
	
	// Controleer het met de database
	if (!get_magic_quotes_gpc()) 
	{
		$_POST['username'] = addslashes($_POST['username']);
	}
	
	$sql_code = "SELECT * FROM users
                 WHERE username = '".$_POST['username']."'";
	
	$sql_out = mysqli_query($dbconn, $sql_code);
	// Error indien username wel ingevuld maar onbekend
	$sql_num_rows = mysqli_num_rows($sql_out);
	
	if ($sql_num_rows == 0 && $_POST['username'] <> '') 
	{
	    writelog("login","WARN","Er werd geprobeerd om in te loggen met een niet bestaande username: ".$_POST['username']);
		echo '<blockquote class="error">ERROR: Username is onbekend</blockquote>';
	}
	
	while ($sql_rows = mysqli_fetch_array($sql_out)) 
	{
		$_POST['pass']            = stripslashes($_POST['pass']);
		$sql_rows['password']     = stripslashes($sql_rows['password']);
		$_POST['pass']            = md5($_POST['pass']);
		$_POST['admin']           = $sql_rows['admin'];
		$_POST['approvenallowed'] = $sql_rows['approvenallowed'];
		$_POST['voornaam']        = $sql_rows['voornaam'];
		$_POST['tussenvoegsel']   = $sql_rows['tussenvoegsel'];
		$_POST['achternaam']      = $sql_rows['achternaam'];
		$_POST['emailadres']      = $sql_rows['emailadres'];
		$_POST['indienst']        = $sql_rows['indienst'];
		$_POST['lastloggedin']    = $sql_rows['lastloggedin'];
		$_POST['uren_invullen']   = $sql_rows['uren_invullen'];
		
		
		//Error indien user niet meer in dienst is
		if (!$_POST['indienst'])
		{
		    writelog("login","WARN","User ".$_POST['username']." probeerde in te loggen terwijl deze niet meer in dienst is");
		    echo '<blockquote class="error">ERROR: User is niet meer in dienst</blockquote>';
		}
		
		//Error indien password fout
		elseif ($_POST['pass'] != $sql_rows['password']) 
		{
		    writelog("login","DEBUG","User ".$_POST['username']." probeerde in te loggen met een foutief wachtwoord - ".$_POST['pass']);
		    writelog("login","DEBUG","Het wachtwoord in de database: - ".$sql_rows['password']);
		    
			echo '<blockquote class="error">ERROR: Foutief wachtwoord. Probeer het nogmaals</blockquote>';
		}
		else 
		{
			// Toevoegen cookie indien username-password correct
			$_POST['username'] = stripslashes($_POST['username']);
			//$hour = time() + 1800;  // cookie is 30 minuten geldig
			$hour = time() + 86400; // Cookie is 24 uur geldig
			setcookie('ID_mus', $_POST['username'], $hour);
			setcookie('Key_mus', $_POST['pass'], $hour);
			
			$_SESSION['username']        = $_POST['username'];
			$_SESSION['admin']           = $_POST['admin'];
			$_SESSION['approvenallowed'] = $_POST['approvenallowed'];
			$_SESSION['voornaam']        = $_POST['voornaam'];
			$_SESSION['tussenvoegsel']   = $_POST['tussenvoegsel'];
			$_SESSION['achternaam']      = $_POST['achternaam'];
			$_SESSION['emailadres']      = $_POST['emailadres'];
			$_SESSION['lastloggedin']    = $_POST['lastloggedin'];
			$_SESSION['indienst']        = $_POST['indienst'];
			$_SESSION['uren_invullen']   = $_POST['uren_invullen'];
			
			$_SESSION['username_encrypted'] = convert_string('encrypt', $_SESSION['username']);
			writelog("login","INFO","User is succesvol encrypted");
			// update lastloggedin in de tabel
			date_default_timezone_set('Europe/Amsterdam');
			$sql_code = "UPDATE users SET lastloggedin = '".date('Y-m-d H:i:s')."' 
				       WHERE username = '".$_POST['username']."'";
			$sql_out = mysqli_query($dbconn, $sql_code);
			header("location: index.php");
			
			writelog("login","INFO","User ".$_POST['username']." is succesvol ingelogd");
		}
	}
}
	// indien niet ingelogd het inlogform displayen
	?>
	
			<h3>Login</h3>
			<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">			
				<p>			
				<label>Name</label>
				<input name="username" type="text" maxlength="40" />
				<label>Password</label>
				<input name="pass" type="password" maxlength="32"/>
				<br /><br />	
				<input class="button" name="submit" type="submit" value="login" />		
				</p>		
			</form>	
			<br />				
		</div>					
		
		<!-- content-wrap ends here -->		
		</div></div>


<?php 
	include ("footer.php");
?>