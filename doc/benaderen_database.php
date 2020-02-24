try {
    $stmt_users = $mysqli->prepare("SELECT * FROM view_users_verlofuren WHERE jaar = ? ORDER BY fullname");
    $stmt_users->bind_param("i", $inputjaar);
    $stmt_users->execute();
} catch(Exception $e) {
    writelog("beginsaldo", "ERROR", "" . $e);
    exit($MSGDB001E);
}

$result = $stmt_users->get_result();
$aantal_rijen = $result->num_rows;

while($row = $result->fetch_assoc()) {
        $ID            = $row['ID'];
        $username      = $row['username'];
        $fullname      = $row['fullname'];
        $beginsaldo    = $row['beginsaldo'];

        echo '<tr class="colored">';
        echo '<td><input style="display:none" type="text" name="ID[]" value="' . $ID . '" readonly></td>';
        echo '<td style="display:none">' . $username . '</td>';
        echo '<td style="height:1.2vw;">' . $fullname . '</td>';
        echo '<td><input style="width:2.8vw; text-align:right" type="number" name="beginsaldo[]" value="' . $beginsaldo . '"></td>';
        echo '</tr>';
}
