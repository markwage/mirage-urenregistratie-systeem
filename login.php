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
	    writeLogRecord("login","WARN Er werd geprobeerd om in te loggen met een niet bestaande username: ".$_POST['username']);
		echo '<blockquote class="error">ERROR: Username is onbekend</blockquote>';
	}
	
	while ($sql_rows = mysqli_fetch_array($sql_out)) 
	{
		$_POST['pass']            = stripslashes($_POST['pass']);
		$sql_rows['password']     = stripslashes($sql_rows['password']);
		$_POST['pass']            = md5($_POST['pass']);
		$_POST['admin']           = $sql_rows['admin'];
		$_POST['voornaam']        = $sql_rows['voornaam'];
		$_POST['tussenvoegsel']   = $sql_rows['tussenvoegsel'];
		$_POST['achternaam']      = $sql_rows['achternaam'];
		$_POST['emailadres']      = $sql_rows['emailadres'];
		$_POST['lastloggedin']    = $sql_rows['lastloggedin'];
		
		//Error indien password fout
		if ($_POST['pass'] != $sql_rows['password']) 
		{
		    $log_record = new Writelog();
		    $log_record->progname = $_SERVER['PHP_SELF'];
		    $log_record->loglevel = 'WARN';
		    $log_record->message_text  = 'User '.$_POST['username'].' probeerde in te loggen met een foutief wachtwoord';
		    $log_record->write_record();
			echo '<blockquote class="error">ERROR: Foutief wachtwoord. Probeer het nogmaals</blockquote>';
		}
		else 
		{
			// Toevoegen cookie indien username-password correct
			$_POST['username'] = stripslashes($_POST['username']);
			$hour = time() + 3600; //cookie is 60 minuten geldig
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
			
			// update lastloggedin in de tabel
			date_default_timezone_set('Europe/Amsterdam');
			$sql_code = "UPDATE users SET lastloggedin = '".date('Y-m-d H:i:s')."' 
				       WHERE username = '".$_POST['username']."'";
			$sql_out = mysqli_query($dbconn, $sql_code);
			header("location: index.php");
			
			$log_record = new Writelog();
			$log_record->progname = $_SERVER['PHP_SELF'];
			$log_record->message_text  = 'User '.$_POST['username'].' is succesvol ingelogd';
			$log_record->write_record();
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
				<input name="pass" type="password" maxlength="50" />
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