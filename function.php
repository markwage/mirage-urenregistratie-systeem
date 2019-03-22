<?php

//------------------------------------------------------------------------
// functie om een connectie met de database te maken
//------------------------------------------------------------------------
function makedbconnection() {
    if (!isset($dbconn)) {
        include ("./db.php");
    }
    if (mysqli_connect_errno()) {
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
		echo '<tr><td align="right">Medewerker: </td><td><strong>'.$voornaam.' '.$tussenvoegsel.' '.$achternaam.'</strong></td></tr></table></p>' ;
	}
	//$sql_laatste = mysqli_query($dbconn, "SELECT * FROM uren WHERE userID = '$user_id' AND terapprovalaangeboden = 1 ORDER BY datum DESC LIMIT 1") or die ("Error in query: $sql_laatste. ".mysql_error());
	//$checknumrows = mysqli_num_rows($sql_laatste);
	//echo "<p><table>";
	//if ($checknumrows <> 0) {
	//    $row_laatste = mysqli_fetch_array($sql_laatste);
	//    $datum_laatste_mutatie = $row_laatste['datum'];
	//    echo '<tr><td align="right">Laatste week voor approval aangeboden: </td><td>week '.cnv_dateToWeek($datum_laatste_mutatie).'</td</tr>';
	//    $weekNumber = date("W");
	//    echo '<tr><td align="right">Huidige weeknummer: </td><td>'.$weekNumber.'</td</tr>';
	//    // echo "</table></p>";
	//} else {
	//    echo '<tr><td align="right">Er zijn nog geen weken ter approval aangeboden</td</tr>';
	//}
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

//------------------------------------------------------------------------
// Vullen van de frm_variabelen voor invullen van soort uren-scherm
//------------------------------------------------------------------------
function form_soorturen_fill($aktie) {
    if ($aktie == "save" || $aktie == "toevoegen") {
        global $frm_code, $frm_omschrijving, $formerror;
        $formerror = 0;
        $frm_ID            = $_POST['ID'];
        $frm_code          = $_POST['code'];
        $frm_omschrijving  = $_POST['omschrijving'];
    }
}

//------------------------------------------------------------------------
// Stel de ingevulde gegevensin het scherm veilig zodat de velden gevuld worden
// met de al ingevulde waarden bij het optreden van een error
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

//-------------------------------------------------------------------------
// Geef weeknummer en jaar door aan de functie
// Deze geeft de dagnaam (mon - sun) en de datum in dd-mm 
// Dit wordt gebruikt in de headers om de uren in te vullen
//-------------------------------------------------------------------------
//function getWeekdays($week, $year){
//    global $weekDatum, $weekDagNaam;
function getWeekdays($inputweeknr){
    global $weekDatum, $weekDagNaam, $week, $year;
    $week = substr($inputweeknr, 4, 2);
    $year = substr($inputweeknr, 0, 4);
    $weekDatum[0] = date("d-m", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT)));
    $weekDagNaam[0] = date("D", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT)));
    $weekDatum[1] = date("d-m", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +1 days'));
    $weekDagNaam[1] = date("D", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +1 days'));
    $weekDatum[2] = date("d-m", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +2 days'));
    $weekDagNaam[2] = date("D", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +2 days'));
    $weekDatum[3] = date("d-m", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +3 days'));
    $weekDagNaam[3] = date("D", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +3 days'));
    $weekDatum[4] = date("d-m", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +4 days'));
    $weekDagNaam[4] = date("D", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +4 days'));
    $weekDatum[5] = date("d-m", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +5 days'));
    $weekDagNaam[5] = date("D", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +5 days'));
    $weekDatum[6] = date("d-m", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +6 days'));
    $weekDagNaam[6] = date("D", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +6 days'));
}

?>



