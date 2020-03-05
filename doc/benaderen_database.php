<?php
try {
    $stmt_users = $mysqli->prepare("SELECT ID, username, fullname, beginsaldo FROM view_users_verlofuren WHERE jaar = ? ORDER BY fullname");
    $stmt_users->bind_param("i", $inputjaar);
    $stmt_users->execute();
} catch(Exception $e) {
    writelog("beginsaldo", "ERROR", $e);
    exit($MSGDB001E);
}

$stmt_users->bind_result($frm_ID, $frm_username, $frm_fullname, $frm_beginsaldo);
while($stmt_users->fetch()) {
    // Onderstaande is niet meer nodig omdat de variabelen al in de bind_result worden gevuld
    //$frm_ID            = $row['ID'];
    //$frm_username      = $row['username'];
    //$frm_fullname      = $row['fullname'];
    //$frm_beginsaldo    = $row['beginsaldo'];

    echo '<tr class="colored">';
    echo '<td><input style="display:none" type="text" name="ID[]" value="' . $frm_ID . '" readonly></td>';
    echo '<td style="display:none">' . $frm_username . '</td>';
    echo '<td style="height:1.2vw;">' . $frm_fullname . '</td>';
    echo '<td><input style="width:2.8vw; text-align:right" type="number" name="beginsaldo[]" value="' . $frm_beginsaldo . '"></td>';
    echo '</tr>';
}

//==========================================================================================
// Record inserten in de database
//=======================================================================================
        try {
            $stmt_ins = $mysqli->prepare("INSERT INTO nieuws (nieuwsheader, nieuwsbericht) VALUES (?, ?)");
            $stmt_ins->bind_param("ss", $_POST['nieuwsheader'], $_POST['nieuwsbericht']);
            $stmt_ins->execute();
        } catch(Exception $e) {
            writelog("add_nieuws", "ERROR", $e);
            exit($MSGDB001E);
        }

//==========================================================================================
// Indien je het aantal rijen moet hebben
//==========================================================================================
$stmt_users->store_result();
$aantal_rijen = $stmt_users->num_rows;

//==========================================================================================
// Indien het resultaat maar max. 1 rij is
//==========================================================================================
try {
    $stmt_sel = $mysqli->prepare("SELECT id, datum, nieuwsheader, nieuwsbericht FROM nieuws WHERE id = ?");
    $stmt_sel->bind_param("i", $_GET['edtid']);
    $stmt_sel->execute();
} catch(Exception $e) {
    writelog("login", "ERROR", $e);
    exit($MSGDB001E);
}
$stmt_sel->bind_result($frm_ID, $frm_datum, $frm_nieuwsheader, $frm_nieuwsbericht);
$stmt_sel->fetch();

//==========================================================================================
// Indien een query binnen een query uitgevoerd moet worden dient volgende statement na execute() uitgevoerd te worden
//==========================================================================================
$stmt_sel->store_result();

//==========================================================================================
// Indien een query niet goed gaat kun je hem als volgt debuggen
//==========================================================================================
// Zet deze twee regeles bovenaan in je prog, na <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// En ipv try/catch, execute de query als volgt
if($stmt_ins_user = $mysqli->prepare("INSERT INTO users (username, password, admin, voornaam, tussenvoegsel, achternaam, emailadres, indienst, approvenallowed, uren_invullen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
    $stmt_ins_user->bind_param("ssissssiii", $_POST['username'], $_POST['pass'], $_POST['admin'], $_POST['voornaam'], $_POST['tussenvoegsel'], $_POST['achternaam'], $_POST['email'], $_POST['indienst'], $_POST['approvenallowed'], $_POST['uren_invullen']);
    $stmt_ins_user->execute();
} else {
    $stmt_error = $mysqli->errno . ' ' . $mysqli->error;
    writelog("add_user", "ERROR", $stmt_error);
    exit($MSGDB001E);
  }

?>
