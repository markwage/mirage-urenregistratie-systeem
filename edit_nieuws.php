<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");
if (isset($_GET['aktie'])) {
    $aktie = $_GET['aktie'];
}
else {
    $aktie = "";
}

// Controleren of gebruiker admin-rechten heeft
if (!$aktie == "disp") {
    check_admin();
}

// Connectie met de database maken en database selecteren
$dbconn = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

//--------------

echo "<div id=\"main\"><h1>"; if ($_SESSION['admin']) { echo "Onderhoud "; } echo "Nieuwsartikelen</h1>";
//This code runs if the form has been submitted

if (isset($_POST['cancel'])) {
    header("location: edit_nieuws.php?aktie=disp");
}
if (isset($_POST['delete'])) {
    $delid = $_POST['id'];
    $sql_delnieuwsheader = mysqli_query($dbconn, "DELETE FROM nieuws WHERE ID = '$delid'");
    writeLogRecord("edit_nieuws","DELETE1 Het nieuwsbericht met id ".$delid." is verwijderd uit tabel nieuws");
    header("location: edit_nieuws.php?aktie=disp");
}

//
//>>>> Hierna code voor als er op save is geklikt <<<<<
//

if ($aktie == 'disp') {
    $sql_nieuwsheaders = mysqli_query($dbconn, "SELECT * FROM nieuws ORDER BY datum desc");
    echo "<center><table>";
    echo "<tr><th>Datum</th><th>Nieuwsheader</th><th colspan=\"3\" align=\"center\">Akties</th></tr>";
    $rowcolor = 'row-a';
    while($row_nieuwsheaders = mysqli_fetch_array($sql_nieuwsheaders)) {
        $id           = $row_nieuwsheaders['ID'];
        $datum        = $row_nieuwsheaders['datum'];
        $nieuwsheader = $row_nieuwsheaders['nieuwsheader'];
        echo '<tr class="'.$rowcolor.'">
			<td>'.$datum.'</td><td>'.$nieuwsheader.'</td>';
        if (!isset($_SESSION['admin']) || (!$_SESSION['admin'])) {
            writeLogRecord("edit_nieuws","BUTTONS Geen admin-sessie dus alleen de button bril wordt getoond");
            echo '<td><a href="edit_nieuws.php?aktie=dispbericht&edtid='.$id.'"><img src="./img/buttons/icons8-glasses-48.png" alt="display nieuwsbericht" title="display nieuwsbericht '.$id.'" /></a></td>';
        } else {
			echo '<td><a href="edit_nieuws.php?aktie=edit&edtid='.$id.'"><img src="./img/buttons/icons8-edit-48.png" alt="wijzigen nieuwsbericht" title="wijzig nieuwsbericht '.$id.'" /></a></td>
			<td><a href="edit_nieuws.php?aktie=delete&edtid='.$id.'"><img src="./img/buttons/icons8-trash-can-48.png" alt="delete nieuwsbericht" title="delete nieuwsbericht '.$id.'" /></a></td>
			<td><a href="add_nieuws.php"><img src="./img/buttons/icons8-plus-48.png" alt="toevoegen nieuwsbericht" title="toevoegen nieuwsbericht" /></a></td>'; 
			
        }
        echo '</tr>';
        if ($rowcolor == 'row-a') $rowcolor = 'row-b';
        else $rowcolor = 'row-a';
    }
    echo "</table></center>";
}

if ($aktie == 'edit' || $aktie == 'delete' || $aktie == 'dispbericht') {
    if ($aktie == 'edit' || $aktie == 'delete') {
        check_admin();
    }
    $edtid = $_GET['edtid'];
    $focus = "nieuwsheader";
    $sql_dspnieuws = mysqli_query($dbconn, "SELECT * FROM nieuws WHERE id = '$edtid'");
    while($row_dspnieuws = mysqli_fetch_array($sql_dspnieuws)) {
        $frm_ID            = $row_dspnieuws['ID'];
        $frm_datum         = $row_dspnieuws['datum'];
        $frm_nieuwsheader  = $row_dspnieuws['nieuwsheader'];
        $frm_nieuwsbericht = $row_dspnieuws['nieuwsbericht'];
    }
        
    ?>

	<form name="nieuwsartikelen" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<p>
	<table>
		<tr>
			<td>Datum</td>
			<td><input type="text" name="datum" size="20" maxlength="20" <?php if (!$_SESSION['admin']) { echo "readonly "; } ?> value="<?php if (isset($frm_datum)) { echo $frm_datum; } ?>"></td>
		</tr>
		<tr>
			<td>Nieuwsbericht</td>
			<td><input type="text" name="nieuwsheader" size="80" maxlength="128" <?php if (!$_SESSION['admin']) { echo "readonly "; } ?> value="<?php if (isset($frm_nieuwsheader)) { echo $frm_nieuwsheader; } ?>"></td>
		</tr>
		<?php 
        if ($aktie == 'edit') { 
            echo '<tr><td> </td><td><textarea id="area1" name="nieuwsbericht">'.$frm_nieuwsbericht.'</textarea></td></tr>'; 
        }
        if ($aktie == 'dispbericht') {
            echo '<tr><td> </td><td><textarea readonly id="area1" name="nieuwsbericht">'.$frm_nieuwsbericht.'</textarea></td></tr>'; 
        }
        echo '</table><br />';
        if ($aktie == 'edit') { 
            echo '<input class="button" type="submit" name="save" value="save">'; 
        }
        if ($aktie == 'delete') {
            echo '<input class="button" type="submit" name="delete" value="delete" onClick="return confirmDelNieuwsbericht()">'; 
        }
        ?>
		<input class="button" type="submit" name="cancel" value="cancel">
		</p>
	</form>
	<br />		

	<?php 
    if (!isset($focus)) {
    	$focus='datum';
    }
    setfocus('nieuwsartikelen', $focus);

// Einde van if aktie=edit
}
	
include ("footer.php");
?>		

