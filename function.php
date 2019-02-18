<?php
//------------------------------------------------------------------------
// functie om een error-message te displayen met standaard
// header en kleuren
//------------------------------------------------------------------------
function errormessage($error_header, $error_message) {
	echo '<div id="message">';
	echo "<h2>".$error_header."</h2>";
	echo $error_message;
	echo "</div>";
}

//------------------------------------------------------------------------
// Controleren of cookies aanwezig zijn. Zo niet dan wordt het login-script
// uitgevoerd.
//------------------------------------------------------------------------
function check_cookies() {
	if(isset($_COOKIE['ID_mus'])) {
		// Indien aanwezig word je naar de volgende page ge-redirect
		$username = $_COOKIE['ID_mus'];
		$pass = $_COOKIE['Key_mus'];
		$check = mysql_query("SELECT * FROM users WHERE username = '$username'") or die(mysql_error());
		while ($info = mysql_fetch_array($check)) {
			if ($pass != $info['password']) {
				header("location: login.php");
			}
		}
	}
	else {
		header("location: login.php");
	}
}

//------------------------------------------------------------------------
// Controleren of de user admin-rechten heeft. Zo niet een error-scherm displayen
//------------------------------------------------------------------------
function check_admin() {
	if (!isset($_SESSION['admin']) || (!$_SESSION['admin'])) {
		header("location: noadmin.php");
	}
}

//------------------------------------------------------------------------
// Cursor op een bepaald veld in het formulier zetten
//------------------------------------------------------------------------
function setfocus($formnaam, $veldnaam) {
	echo '<script type="text/javascript">';
	echo 'document.'.$formnaam.'.'.$veldnaam.'.focus()';
	echo '</script>';
}

//------------------------------------------------------------------------
// Vullen van de frm_variabelen voor invullen van usermanagement-scherm
//------------------------------------------------------------------------
function form_user_fill($aktie) {
	if ($aktie == "save" || $aktie == "toevoegen") {
		global $frm_username, $frm_pass, $frm_pass2, $frm_voornaam, $frm_tussenvoegsel, $frm_achternaam, 
			$frm_email, $frm_indienst, $formerror;
		$formerror = 0;
		$frm_username      = $_POST['username'];
		$frm_pass          = $_POST['pass'];
		$frm_pass2         = $_POST['pass2'];
		$frm_voornaam      = $_POST['voornaam'];
		$frm_tussenvoegsel = $_POST['tussenvoegsel'];
		$frm_achternaam    = $_POST['achternaam'];
		$frm_email         = $_POST['email'];
		$frm_indienst      = $_POST['indienst'];
	}
}
//------------------------------------------------------------------------
// Vullen van de frm_variabelen voor invullen van soort uren-scherm
//------------------------------------------------------------------------
function form_soorturen_fill($aktie) {
	if ($aktie == "save" || $aktie == "toevoegen") {
		global $frm_code, $frm_omschrijving, $formerror;
		$formerror = 0;
		$frm_code          = $_POST['code'];
		$frm_omschrijving  = $_POST['omschrijving'];
	}
}

//------------------------------------------------------------------------
// Displayen van de diverse gegevens van de user
//------------------------------------------------------------------------
function displayUserGegevens() {
	global $username, $user_id, $voornaam, $tussenvoegsel, $achternaam, $emailadres, $datum_laatste_mutatie, $weekNumber;
	echo "<p><table>";
	$username = $_SESSION['username'];
	$sql_user = mysql_query("SELECT * FROM users WHERE username = '$username'");
	while($row_user = mysql_fetch_array($sql_user)) {
		$user_id       = $row_user['ID'];
		$username      = $row_user['username'];
		$voornaam      = $row_user['voornaam'];
		$tussenvoegsel = $row_user['tussenvoegsel'];
		$achternaam    = $row_user['achternaam'];
		$emailadres    = $row_user['emailadres'];
		echo '<tr><td align="right">Gebruikersnaam: </td><td>'.$username.'</td></tr>
			<tr><td align="right">Medewerker: </td><td>'.$voornaam.' '.$tussenvoegsel.' '.$achternaam.'</td></tr>' ;
	}
	$sql_laatste = mysql_query("SELECT * FROM uren WHERE userID = '$user_id' AND terapprovalaangeboden = 1 ORDER BY datum DESC LIMIT 1") or die ("Error in query: $sql_laatste. ".mysql_error());
	$row_laatste = mysql_fetch_array($sql_laatste);
	$datum_laatste_mutatie = $row_laatste['datum'];
	echo '<tr><td align="right">Laatste week voor approval aangeboden: </td><td>week '.cnv_dateToWeek($datum_laatste_mutatie).'</td</tr>';
	$weekNumber = date("W");
	echo '<tr><td align="right">Huidige weeknummer: </td><td>'.$weekNumber.'</td</tr>';
	echo "</table></p>";
}

//------------------------------------------------------------------------
// Converteren datum (JJJJ-MM-DD) naar een weeknummer
//------------------------------------------------------------------------
function cnv_dateToWeek($datum) {
	$dat_jaar  = substr($datum, 0, 4); // jaren     (Y)
    $dat_maand = substr($datum, 5, 2); // maanden   (m)
    $dat_dag   = substr($datum, 8, 2); // dagen     (d)
    $buildDatum = mktime(0, 0, 0, $dat_maand, $dat_dag, $dat_jaar);
	$weekNumber = date('W', $buildDatum); 
	return $weekNumber;
}

?>



