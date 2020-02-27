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

?>