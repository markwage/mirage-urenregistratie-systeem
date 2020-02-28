<?php

// ------------------------------------------------------------------------
// functie om een error-message te displayen met standaard
// header en kleuren
// test git
// ------------------------------------------------------------------------
function errormessage($error_header, $error_message)
{
    echo '<div id="message">';
    echo "<h2>" . $error_header . "</h2>";
    echo $error_message;
    echo "</div>";
}

// ------------------------------------------------------------------------
// Controleren of cookies aanwezig zijn. Zo niet dan wordt het login-script
// uitgevoerd.
// ------------------------------------------------------------------------
function check_cookies()
{
    if ((isset($_COOKIE['ID_mus'])) && ($_SESSION['lastloggedin'] != "1970-01-01 00:00:00")) {
        // Indien aanwezig word je naar de volgende page ge-redirect
        include ("./db.php");
        $dbconn = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);
        $username = $_COOKIE['ID_mus'];
        $pass = $_COOKIE['Key_mus'];
        $sql_code = "SELECT * FROM users
                     WHERE username = '$username'";
        $check = mysqli_query($dbconn, $sql_code);
        if(!$sql_code) {
            writelog("function", "ERROR", "Selecteren van users gaat fout: " . $sql_code . " - " . mysqli_error($dbconn));
            exit("<br />MSGDB001E Fout opgetreden bij benaderen van de database. Probeer het nogmaals. Indien de fout zich blijft voordoen neem dan contact op met de beheerders.<br />");
        }

        while ($info = mysqli_fetch_array($check)) {
            if ($pass != $info['password']) {
                header("location: login.php");
            }
        }
    } else {
        header("location: login.php");
    }
}

// ------------------------------------------------------------------------
// Controleren of de user admin-rechten heeft. Zo niet een error-scherm displayen
// ------------------------------------------------------------------------
function check_admin()
{
    if (! isset($_SESSION['admin']) || (! $_SESSION['admin'])) {
        header("location: noadmin.php");
    }
}

// ------------------------------------------------------------------------
// Cursor op een bepaald veld in het formulier zetten
// ------------------------------------------------------------------------
function setfocus($formnaam, $veldnaam)
{
    echo '<script type="text/javascript">';
    echo 'document.' . $formnaam . '.' . $veldnaam . '.focus()';
    echo '</script>';
}

// ------------------------------------------------------------------------
// Displayen van de diverse gegevens van de user
// ------------------------------------------------------------------------
function displayUserGegevens()
{
    global $username, $user_id, $voornaam, $tussenvoegsel, $achternaam, $emailadres, $datum_laatste_mutatie, $weekNumber;
    echo "<p><table>";
    $username = $_SESSION['username'];
    include ("./db.php");

    $dbconn = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);
    $sql_code = "SELECT * FROM users
                 WHERE username = 'username'";
    $sql_out = mysqli_query($dbconn, $sql_code);

    while ($sql_row = mysqli_fetch_array($sql_out)) {
        $user_id = $sql_row['ID'];
        $username = $sql_row['username'];
        $voornaam = $sql_row['voornaam'];
        $tussenvoegsel = $sql_row['tussenvoegsel'];
        $achternaam = $sql_row['achternaam'];
        $emailadres = $sql_row['emailadres'];

        echo '<tr><td align="right"><strong>Medewerker: </strong></td><td>' . $voornaam . ' ' . $tussenvoegsel . ' ' . $achternaam . '</td></tr>';
        echo '<tr><td align="right"><strong>Emailadres: </strong></td><td>' . $emailadres . '</td></tr>';
    }

    echo "</table></p>";
}

// ------------------------------------------------------------------------
// encrypt/decrypt oa. usernames zodat dat niet leesbaar in de url te zien is
// ------------------------------------------------------------------------
function convert_string($action, $string)
{
    $output = '';
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'eaiYYkYTysia2lnHiw0N0vx7t7a3kEJVLfbTKoQIx5o=';
    $secret_iv = 'eaiYYkYTysia2lnHiw0N0';
    // hash
    $key = hash('sha256', $secret_key);
    $initialization_vector = substr(hash('sha256', $secret_iv), 0, 16);
    if ($string != '') {
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $initialization_vector);
            $output = base64_encode($output);
        }
        if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $initialization_vector);
        }
    }
    return $output;
}

// ------------------------------------------------------------------------
// Write logrecord to file
// ------------------------------------------------------------------------
function writelog($progname, $loglevel, $logrecord)
{
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else {
        $username = "onbekend";
    }

    $logfile_name = "logs/system.log";
    date_default_timezone_set('Europe/Amsterdam');
    $datumlog = date('Ymd H:i:s');
    file_put_contents($logfile_name, PHP_EOL . $datumlog . ";" . $progname . ";" . $username . ";" . $loglevel . ";" . $logrecord, FILE_APPEND);
}

// ------------------------------------------------------------------------
// Write debuglogrecords to file
// ------------------------------------------------------------------------
function writedebug($logrecord)
{
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else {
        $username = "onbekend";
    }

    $logfile_name = "logs/debug.log";
    date_default_timezone_set('Europe/Amsterdam');
    $datumlog = date('Ymd H:i:s');
    file_put_contents($logfile_name, PHP_EOL . $datumlog . ";" . $logrecord, FILE_APPEND);
}

// ------------------------------------------------------------------------
// Vullen van de frm_variabelen voor invullen van soort uren-scherm
// ------------------------------------------------------------------------
function form_soorturen_fill($aktie)
{
    if ($aktie == "save" || $aktie == "toevoegen") {
        global $frm_code, $frm_omschrijving, $frm_facturabel, $formerror;
        $formerror = 0;

        /**
         * Checken of $_POST['ID'] wel gevuld is.
         * Is nl niet het geval indien er een
         * code opgegeven wordt die al bestaat of het invoerveld is leeg
         */
        if (isset($_POST['ID'])) {
            $frm_ID = $_POST['ID'];
        }

        $frm_code = $_POST['code'];
        $frm_omschrijving = $_POST['omschrijving'];
    }
}

// ------------------------------------------------------------------------
// Vullen van de frm_variabelen voor invullen van soort uren-scherm
// ------------------------------------------------------------------------
function form_beginsaldo_fill($aktie)
{
    if ($aktie == "save") {
        global $frm_username, $frm_beginsaldo, $formerror;
        $formerror = 0;
                
        $frm_username = $_POST['username'];
        $frm_beginsaldo = $_POST['beginsaldo'];
    }
}

// ------------------------------------------------------------------------
// Stel de ingevulde gegevensin het scherm veilig zodat de velden gevuld worden
// met de al ingevulde waarden bij het optreden van een error
// ------------------------------------------------------------------------
function form_user_fill($btn_aktie)
{
    if ($btn_aktie == "save" || $btn_aktie == "toevoegen") {
        global $frm_username, $frm_pass, $frm_pass2, $frm_admin, $frm_voornaam, $frm_tussenvoegsel, $frm_achternaam, $frm_email, $frm_indienst, $frm_uren_invullen, $frm_approvenallowed, $formerror;

        $formerror = 0;
        $frm_username = $_POST['username'];
        $frm_pass = $_POST['pass'];
        $frm_pass2 = $_POST['pass2'];
        $frm_voornaam = $_POST['voornaam'];
        $frm_tussenvoegsel = $_POST['tussenvoegsel'];
        $frm_achternaam = $_POST['achternaam'];
        $frm_email = $_POST['email'];

        if (isset($_POST['admin'])) {
            $frm_admin = $_POST['admin'];
        } else {
            $frm_admin = 0;
        }

        if (isset($_POST['approvenallowed'])) {
            $frm_approvenallowed = $_POST['approvenallowed'];
        } else {
            $frm_approvenallowed = 0;
        }

        if (isset($_POST['indienst'])) {
            $frm_indienst = $_POST['indienst'];
        } else {
            $frm_indienst = 0;
        }

        if (isset($_POST['uren_invullen'])) {
            $frm_uren_invullen = $_POST['uren_invullen'];
        } else {
            $frm_uren_invullen = 0;
        }
    }
}

// ------------------------------------------------------------------------
// Bepalen of soortuur enabled of disabled moet zijn
// ------------------------------------------------------------------------
function enablesoortuur()
{
    global $dbconn, $option, $tmp_soortuur, $dag_readonly;
    $option = "";
    $sql_soorturen = "SELECT * FROM soorturen
              ORDER BY code";
    $sql_out_soorturen = mysqli_query($dbconn, $sql_soorturen);

    while ($row_soorturen = mysqli_fetch_array($sql_out_soorturen)) {
        if ($tmp_soortuur == $row_soorturen['code']) {
            $option_selected = 'selected';
            $option_disabled = 'enabled';
        } else {
            $option_selected = '';
            if (($dag_readonly[0] == 'readonly') || ($dag_readonly[6] == 'readonly')) {
                $option_disabled = 'disabled';
            } else {
                $option_disabled = 'enabled';
            }
        }
        $option .= "<option " . $option_selected . " " . $option_disabled . " value='" . $row_soorturen['code'] . "'>" . $row_soorturen['code'] . " - " . $row_soorturen['omschrijving'] . "</option>";
    }
}

// ------------------------------------------------------------------------
// Controleer de ingevulde uren per Soortuur
// ------------------------------------------------------------------------
function berekendagenreadonly()
{
    global $dag_readonly, $js_aantal_dagen_readonly;
    if ($dag_readonly[6] == '') {
        if (isset($js_aantal_dagen_readonly)) {
            $aantal_dagen_readonly = $js_aantal_dagen_readonly;
        } else {
            $aantal_dagen_readonly = '';
        }
        echo "<td><img class='button' src='./img/icons/add-48.png' alt='toevoegen nieuwe regel' title='toevoegen nieuwe regel' onclick='add_row(" . $aantal_dagen_readonly . ");' /></td>";
    } else {
        echo "<td></td>";
    }
    echo "<td></td>";
    echo "</tr>";
}

// ------------------------------------------------------------------------
// Controleer de ingevulde uren per Soortuur
// ------------------------------------------------------------------------
function berekenurenpersoort()
{
    global $option, $totaal_uren_per_soort, $frm_valueDag, $frm_value, $dag_readonly, $js_readonly, $js_aantal_dagen_readonly;

    echo '<div id="dropdownSoortUren" data-options="' . $option . '"></div>';
    $totaal_uren_per_soort = 0;
    for ($ix = 0; $ix < 7; $ix ++) {
        $frm_value = $frm_valueDag[$ix];
        if ($dag_readonly[$ix] == 'readonly') {
            $js_readonly = 'readonly';
            $js_aantal_dagen_readonly = $ix;
        }
        if ($ix == 0) {
            echo "<td><select name='soortuur[]' selected>" . $option . "</select></td>";
        }

        $ixb = $ix + 1;
        echo "<td title='Geef waarde in decimalen. Hierbij is een kwartier 0.25, half uur 0.5 en 45 minuten is 0.75'><input " . $dag_readonly[$ix] . " style='width:3.33vw; text-align:right' type='number' name='dag" . $ixb . "[]' min='0' max='24' step='0.25' size='2' value='" . $frm_value . "'></td>";

        $totaal_uren_per_soort = number_format($totaal_uren_per_soort + floatval($frm_value), 2);

        if ($ixb == 7) {
            echo "<td class='totaalkolom'><input readonly style='width:3.33vw; text-align:right' type='number' name='totaalpersoort' min='0' max='24' step='0.25' size='2' value='" . $totaal_uren_per_soort . "'></td>";
        }
    }
}

// ------------------------------------------------------------------------
// Controleer de ingevulde uren per Soortuur
// ------------------------------------------------------------------------
function checkIngevuldeUrenPerSoort($ix1)
{
    global $urenarray, $frm_soortuur, $frm_urendag1, $frm_urendag2, $frm_urendag3, $frm_urendag4, $frm_urendag5, $frm_urendag6, $frm_urendag7;
    $frm_soortuur = $_POST["soortuur"][$ix1];

    if (! isset($_POST["dag1"][$ix1]) || $_POST["dag1"][$ix1] == '') {
        $frm_urendag1 = 0;
    } else {
        $frm_urendag1 = $_POST["dag1"][$ix1];
    }

    if (! isset($_POST["dag2"][$ix1]) || $_POST["dag2"][$ix1] == '') {
        $frm_urendag2 = 0;
    } else {
        $frm_urendag2 = $_POST["dag2"][$ix1];
    }

    if (! isset($_POST["dag3"][$ix1]) || $_POST["dag3"][$ix1] == '') {
        $frm_urendag3 = 0;
    } else {
        $frm_urendag3 = $_POST["dag3"][$ix1];
    }

    if (! isset($_POST["dag4"][$ix1]) || $_POST["dag4"][$ix1] == '') {
        $frm_urendag4 = 0;
    } else {
        $frm_urendag4 = $_POST["dag4"][$ix1];
    }

    if (! isset($_POST["dag5"][$ix1]) || $_POST["dag5"][$ix1] == '') {
        $frm_urendag5 = 0;
    } else {
        $frm_urendag5 = $_POST["dag5"][$ix1];
    }

    if (! isset($_POST["dag6"][$ix1]) || $_POST["dag6"][$ix1] == '') {
        $frm_urendag6 = 0;
    } else {
        $frm_urendag6 = $_POST["dag6"][$ix1];
    }

    if (! isset($_POST["dag7"][$ix1]) || $_POST["dag7"][$ix1] == '') {
        $frm_urendag7 = 0;
    } else {
        $frm_urendag7 = $_POST["dag7"][$ix1];
    }

    $urenarray[0] = $frm_urendag1;
    $urenarray[1] = $frm_urendag2;
    $urenarray[2] = $frm_urendag3;
    $urenarray[3] = $frm_urendag4;
    $urenarray[4] = $frm_urendag5;
    $urenarray[5] = $frm_urendag6;
    $urenarray[6] = $frm_urendag7;
}

// ------------------------------------------------------------------------
// Converteren datum (JJJJ-MM-DD) naar een weeknummer
// ------------------------------------------------------------------------
function cnv_dateToWeek($datum)
{
    $dat_jaar = substr($datum, 0, 4); // jaren (Y)
    $dat_maand = substr($datum, 5, 2); // maanden (m)
    $dat_dag = substr($datum, 8, 2); // dagen (d)
    $buildDatum = mktime(0, 0, 0, $dat_maand, $dat_dag, $dat_jaar);
    $weekNumber = date('W', $buildDatum);
    return $weekNumber;
}

// ------------------------------------------------------------------------
// Converteren huidige dag naar currentWeeknummer
// ------------------------------------------------------------------------
function jaarWeek()
{
    global $week_nr, $jaar_nr;
    // Datum van vandaag
    $datum = date("d-m-Y");
    $dagen = 0;

    for ($ix1 = 0; $ix1 < 10; $ix1 ++) {
        $dagen = - 7 * $ix1; // Negatief omdat we 10 weken terug moeten kijken

        // "o" laat het correcte jaar zien aan het einde van het jaar of het begin
        // 31-12-2019: Y > 2019
        //             o > 2020 (wat correct is omdat die dag in eerste week van nieuwe jaar valt)
        $jaar_nr[$ix1] = date("o", strtotime("+$dagen day " . $datum));
        $week_nr[$ix1] = date("W", strtotime("+$dagen day " . $datum));
    }
}

// -----------------------------------------------------------------------------
// Bepalen de startdatum en einddatum van een week
// -----------------------------------------------------------------------------
function getStartAndEndDate($week, $jaar)
{
    $dto = new DateTime();
    $dto->setISODate($jaar, $week);
    $ret['week_start'] = $dto->format('d-m-Y');
    $dto->modify('+6 days');
    $ret['week_end'] = $dto->format('d-m-Y');
    return $ret;
}

// -------------------------------------------------------------------------
// Geef weeknummer en jaar door aan de functie
// Deze geeft de dagnaam (mon - sun) en de datum in dd-mm
// Dit wordt gebruikt in de headers om de uren in te vullen
// -------------------------------------------------------------------------
function getWeekdays($weeknr)
{
    global $weekDatum, $weekDagNaam, $weekMaand, $weekJaar, $week, $year, $inputweeknr;
    $inputweeknr = $weeknr;
    $week = substr($inputweeknr, 6, 2);
    $year = substr($inputweeknr, 0, 4);

    for ($ixweek = 0; $ixweek < 7; $ixweek ++) {
        $weekDatum[$ixweek] = date("d-m", strtotime($year . 'W' . str_pad($week, 2, 0, STR_PAD_LEFT) . ' +' . $ixweek . ' days'));
        $weekMaand[$ixweek] = date("m", strtotime($year . 'W' . str_pad($week, 2, 0, STR_PAD_LEFT) . ' +' . $ixweek . ' days'));
        $weekJaar[$ixweek] = date("Y", strtotime($year . 'W' . str_pad($week, 2, 0, STR_PAD_LEFT) . ' +' . $ixweek . ' days'));
    }

    $weekDagNaam[0] = "Maa";
    $weekDagNaam[1] = "Din";
    $weekDagNaam[2] = "Woe";
    $weekDagNaam[3] = "Don";
    $weekDagNaam[4] = "Vrij";
    $weekDagNaam[5] = "Zat";
    $weekDagNaam[6] = "Zon";
}

// -----------------------------------------------------------------------------
// Style voor de verstuurde mails - header incl styling
// -----------------------------------------------------------------------------
function mail_message_header()
{
    global $message;
    $message = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>

<style type="text/css">
body {
    font: normal 16px calibri, arial, sans-serif;
}
h1 {
    font: bold 20px calibri, arial, sans-serif;
}
table {
    border: 1px solid black;
	border-collapse: collapse;
	/* margin: 10px 15px; */	
}
th {
	background: #13C3B7;
	padding-left: 5px;
	padding-right: 5px;
	color: white;
	text-align: left;
}
td {
	padding-left: 5px;
	padding-right: 5px;
    padding-top: 0px;
    padding-bottom: 0px;
}
tr {
    padding-top: 0px;
    padding-bottom: 0px;
}
/*
table#mail tr.colored:nth-child(odd) {
      background-color: #d0fffb;
}
table#mail tr.colored:nth-child(even) {
      background-color: white;
}
*/
</style>
</head>

<body>';
}

// -----------------------------------------------------------------------------
// Style voor de verstuurde mails footer
// -----------------------------------------------------------------------------
function mail_message_footer($message)
{
    global $message;
    $message .= '<br />Met vriendelijke groet,<br />Beheerders van <strong>mus</strong><br /><br /><img src="http://'.$_SERVER['SERVER_NAME'].'/img/logo_mail_footer.png" width="600" height="115"><br /><br />';
    $message .= '<p style="font-size:10px;">&copy; copyright 2020 <strong><a href="http://www.mirage.nl">Mirage Automatisering BV</a></strong><br /> ';
    $message .= 'Dit is een geautomatiseerd bericht vanuit Mirage Urenregistratie Systeem</p>';
    $message .= '</body></html>';
}

?>