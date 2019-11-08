<?php
session_start();

include ("./config.php");
include ("./db.php");
include ("./function.php");

include ("header.php");

?>
<div id="main">		
	<h1>Urenregistratie</h1>
	<p>Dit is de urenregistratie van Mirage Automatisering BV. 
	Heb je een account dan kun je inloggen. 
	Zo niet dan kun je een account aanvragen via het contactformulier.</p>
			
<?php
// Indien het Login form is submitted
if (isset($_POST['submit'])) {
	//controleer of het ingevuld is
	if (!$_POST['username'] || !$_POST['pass']) {
	    echo '<blockquote class="error">ERROR: Niet alle verplichte velden zijn ingevuld</blockquote>';
	}
	// Controleer het met de database
	if (!get_magic_quotes_gpc()) {
		$_POST['username'] = addslashes($_POST['username']);
	}
	$check = mysqli_query($dbconn, "SELECT * FROM users WHERE username = '".$_POST['username']."'") or die(mysqli_error($dbconn));
	// Error indien username wel ingevuld maar onbekend
	$check2 = mysqli_num_rows($check);
	if ($check2 == 0 && $_POST['username'] <> '') {
	    writeLogRecord("login","WARN Er werd geprobeerd om in te loggen met een niet bestaande username: ".$_POST['username']);
		echo '<blockquote class="error">ERROR: Username is onbekend</blockquote>';
	}
	while ($info = mysqli_fetch_array($check)) {
		$_POST['pass']     = stripslashes($_POST['pass']);
		$info['password']  = stripslashes($info['password']);
		$_POST['pass']     = md5($_POST['pass']);
		$_POST['admin']    = $info['admin'];
		$_POST['voornaam'] = $info['voornaam'];
		$_POST['approvenallowed'] = $info['approvenallowed'];
		//Error indien password fout
		if ($_POST['pass'] != $info['password']) {
			writeLogRecord("login","WARN User ".$_POST['username']." probeerde in te loggen met een foutief wachtwoord");
			echo '<blockquote class="error">ERROR: Foutief wachtwoord. Probeer het nogmaals</blockquote>';
		}
		else {
			// Toevoegen cookie indien username-password correct
			$_POST['username'] = stripslashes($_POST['username']);
			$hour = time() + 36000;
			setcookie('ID_mus', $_POST['username'], $hour);
			setcookie('Key_mus', $_POST['pass'], $hour);
			$_SESSION['username'] = $_POST['username'];
			$_SESSION['admin']    = $_POST['admin'];
			$_SESSION['approvenallowed'] = $_POST['approvenallowed'];
			$_SESSION['voornaam'] = $_POST['voornaam'];
			// update lastloggedin in de tabel
			// Haal eerst de lastloggedin op om dit in het scherm te tonen als user aangelogd is
			//$sql_getlastloggedin = "SELECT lastloggedin FROM USERS WHERE username = '".$_POST['username']."'";
			//$check_getlastloggedin = mysqli_query($dbconn, $sql_lastloggedin) or die ("Error in query: $sql_getlastloggedin. ".mysqli_error());
			$update = "UPDATE users SET 
				lastloggedin = '".date('Y-m-d h:i:s')."' WHERE username = '".$_POST['username']."'";
			$check_upd_users = mysqli_query($dbconn, $update) or die ("Error in query: $update. ".mysqli_error());
			header("location: index.php");
			writeLogRecord("login","User ".$_POST['username']." is succesvol ingelogd.");
		}
	}
}
// else {
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