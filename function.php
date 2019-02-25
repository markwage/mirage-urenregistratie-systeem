<?php

//------------------------------------------------------------------------
// functie om een connectie met de database te maken
//------------------------------------------------------------------------
function makedbconnection() {
    if (!isset($dbconn))
        include ("./db.php");
    //include ("./db.php");
    
    //$dbconn = mysqli_connect($dbhost, $dbuser, $dbpassw);
    //$dbconn = mysqli_connect("localhost","root","","mus");
    
    if (mysqli_connect_errno()) {
    //if (!$dbconn) {
        die("Kan de connectie met de database niet maken");
    }
    $dbselect = mysqli_select_db($dbconn, $dbname);
    if (!$dbselect) {
        die("Kan de database niet openen : " . mysqli_error());
    }
}

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
		include ("./db.php");
	    $dbconn = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);
		$username = $_COOKIE['ID_mus'];
		$pass = $_COOKIE['Key_mus'];
		$check = mysqli_query($dbconn, "SELECT * FROM users WHERE username = '$username'") or die(mysqli_error($dbconn));
		while ($info = mysqli_fetch_array($check)) {
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
function form_user_fill($btn_aktie) {
	if ($btn_aktie == "save" || $btn_aktie == "toevoegen") {
		global $frm_username, $frm_pass, $frm_pass2, $frm_admin, $frm_voornaam, $frm_tussenvoegsel, $frm_achternaam, 
			$frm_email, $frm_indienst, $formerror;
		$formerror = 0;
		$frm_username      = $_POST['username'];
		$frm_pass          = $_POST['pass'];
		$frm_pass2         = $_POST['pass2'];
		if (isset($_POST['admin'])) $frm_admin = $_POST['admin'];
		else $frm_admin = "";
		$frm_voornaam      = $_POST['voornaam'];
		$frm_tussenvoegsel = $_POST['tussenvoegsel'];
		$frm_achternaam    = $_POST['achternaam'];
		$frm_email         = $_POST['email'];
		if (isset($_POST['indienst'])) $frm_indienst = $_POST['indienst'];
		else $frm_indienst = "";
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
	include ("./db.php");
	$dbconn = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);
	$sql_user = mysqli_query($dbconn, "SELECT * FROM users WHERE username = '$username'");
	while($row_user = mysqli_fetch_array($sql_user)) {
		$user_id       = $row_user['ID'];
		$username      = $row_user['username'];
		$voornaam      = $row_user['voornaam'];
		$tussenvoegsel = $row_user['tussenvoegsel'];
		$achternaam    = $row_user['achternaam'];
		$emailadres    = $row_user['emailadres'];
		echo '<tr><td align="right">Gebruikersnaam: </td><td>'.$username.'</td></tr>
			<tr><td align="right">Medewerker: </td><td>'.$voornaam.' '.$tussenvoegsel.' '.$achternaam.'</td></tr>' ;
	}
	$sql_laatste = mysqli_query($dbconn, "SELECT * FROM uren WHERE userID = '$user_id' AND terapprovalaangeboden = 1 ORDER BY datum DESC LIMIT 1") or die ("Error in query: $sql_laatste. ".mysql_error());
	$checknumrows = mysqli_num_rows($sql_laatste);
	if ($checknumrows <> 0) {
	    $row_laatste = mysqli_fetch_array($sql_laatste);
	    $datum_laatste_mutatie = $row_laatste['datum'];
	    echo '<tr><td align="right">Laatste week voor approval aangeboden: </td><td>week '.cnv_dateToWeek($datum_laatste_mutatie).'</td</tr>';
	    $weekNumber = date("W");
	    echo '<tr><td align="right">Huidige weeknummer: </td><td>'.$weekNumber.'</td</tr>';
	    echo "</table></p>";
	} else {
	    echo '<tr><td align="right"><br>Er zijn nog geen weken ter approval aangeboden</td</tr>';
	    echo "</table></p>";
	}
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

//------------------------------------------------------------------------
// Write logrecord to file 
//------------------------------------------------------------------------
function writeLogRecord($phpProg, $logRecord) {
    if (isset($_SESSION['username'])) $username = $_SESSION['username'];
    else $username = "";
    $fileName = "C:\\wamp64\\www\\mirage-urenregistratie-systeem\\logs\\systemlogMUS.log";
    $datumlog = date('Ymd H:i:s');
    file_put_contents($fileName, PHP_EOL.$datumlog.";".$phpProg.";".$username.";".$logRecord, FILE_APPEND);
}

?>



