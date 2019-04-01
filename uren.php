<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");


// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

?>
<div id="main">		
	<h1>Urenadministratie</h1>
	
<?php 
displayUserGegevens();
$inputweeknr=date('Y').date('W');
getWeekdays($inputweeknr);

//------------------------------------------------------------------------------------------------------
// Haal alle soorturen op uit de database zodat deze in de dorpdown getoond worden
//------------------------------------------------------------------------------------------------------
$sql_soorturen = mysqli_query($dbconn, "SELECT * FROM soorturen ORDER BY code");
$option = "";
while($row_soorturen = mysqli_fetch_array($sql_soorturen)) {
    $option .= "<option value='".$row_soorturen['code']."'>".$row_soorturen['code']." - ".$row_soorturen['omschrijving']."</option>";
}

?>
	
<?php 
//------------------------------------------------------------------------------------------------------
// From here this code runs if the form has been submitted (clicked save or cancel)
//------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------
// BUTTON changeWeeknr is op geklikt om een andere week te muteren.
// Door getWeekdays worden de dagen en data van die nieuwe week berekend
//------------------------------------------------------------------------------------------------------
if (isset($_POST['updateweeknr'])) {
    writelogrecord("uren","UPDWEEK 001 Op refresh gedrukt POST(week): ".$_POST['week']);
    $inputweeknr = $_POST["week"];
    getWeekdays($_POST['week']);
    // Controleer of deze week bestaat. Zo ja dan gegevens ophalen en tonen op scherm
    // Indien week al approved kan deze niet gewijzigd worden
}

//------------------------------------------------------------------------------------------------------
// BUTTON Cancel
//------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
    header("location: index.php");
}

//------------------------------------------------------------------------------------------------------
// BUTTON Save
//------------------------------------------------------------------------------------------------------
if (isset($_POST['save'])) {
    getWeekdays($_POST['week']);
    $sql_select_uren = "SELECT * FROM uren where user='".$username."' AND week='".$week."' AND jaar='".$year."'";
    writelogrecord("uren","BTNSAVE Controle of er al gegevens van jaar ".$year." en week ".$week." zijn");
    writelogrecord("uren","BTNSAVE Query: ".$sql_select_uren);
    $check_select_uren = mysqli_query($dbconn, $sql_select_uren);
    if (mysqli_num_rows($check_select_uren) > 0) {
        $sql_delete_uren = "DELETE FROM uren where user='".$username."' AND week='".$week."' AND jaar='".$year."'";
        $check_insert_uren = mysqli_query($dbconn, $sql_delete_uren);
        writelogrecord("uren","BTNSAVE Records worden verwijderd van jaar ".$year." en week ".$week." voor het updaten van de betreffende week");
        writelogrecord("uren","BTNSAVE Query: ".$sql_delete_uren);
    }
    $inputweeknr = $_POST['week'];
    //getWeekdays($_POST['week']);
    $aantalRijen = count($_POST["dag1"]);
    for($ix1=0; $ix1<$aantalRijen; $ix1++) {
        if(trim($_POST["soortuur"][$ix1] != '')) {
            //001 Check de ingevulde velden op correctheid
            checkIngevuldeUrenPerSoort($ix1);
            //002 Check of de week al voorkomt in de database Indien ja EN al approved dan kunnen de gegevens niet gewijzigd worden
            for($ix2=0; $ix2<7; $ix2++) {
                if ($urenarray[$ix2] > 0) {
                    $datum = date("Y-m-d", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +'.$ix2.' days'));
                    $sql_insert_uren = "INSERT INTO uren (jaar, week, soortuur, datum, uren, user)
                        values('".$year."', '".$week."', '".$_POST['soortuur'][$ix1]."', '".$datum."', '".$urenarray[$ix2]."', '".$username."')";
                    $check_insert_uren = mysqli_query($dbconn, $sql_insert_uren);
                    writelogrecord("uren","BTNSAVE Records worden toegevoegd van jaar ".$year." en week ".$week." voor het updaten van de betreffende week");
                    writelogrecord("uren","BTNSAVE Query: ".$sql_insert_uren);
                }
            }
        }
    }
}
?>

<div id="form_div">
<form name="add_uren" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<table>
		<tr>
			<td>Weeknummer</td>
			<td><input style="width:70px" type="number" name="week" id="camp-week" value="<?php echo $inputweeknr; ?>" required></td>
			<td><input class="button" type="submit" name="updateweeknr" value="refresh"></td>
		</tr>
	</table>
	<table id="uren_table">
		<tr>
			<th>Soortuur</th>
			<th><?php echo "<center>".$weekDatum[0]."<br>".$weekDagNaam[0]."</center>"; ?></th>
			<th><?php echo "<center>".$weekDatum[1]."<br>".$weekDagNaam[1]."</center>"; ?></th>
			<th><?php echo "<center>".$weekDatum[2]."<br>".$weekDagNaam[2]."</center>"; ?></th>
			<th><?php echo "<center>".$weekDatum[3]."<br>".$weekDagNaam[3]."</center>"; ?></th>
			<th><?php echo "<center>".$weekDatum[4]."<br>".$weekDagNaam[4]."</center>"; ?></th>
			<th><?php echo "<center>".$weekDatum[5]."<br>".$weekDagNaam[5]."</center>"; ?></th>
			<th><?php echo "<center>".$weekDatum[6]."<br>".$weekDagNaam[6]."</center>"; ?></th>
			<th colspan="2"></th>
		</tr>
   		<tr id="row1">
   			<div id="dropdownSoortUren" data-options="<?php echo $option; ?>"></div>
   		    <!-- Het aantal td's moet in functions.js net zoveel zijn! -->
   		    <td><select name="soortuur[]"><?php echo $option; ?></select></td>
    		<td><input style="width:50px" type="number" name="dag1[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><input style="width:50px" type="number" name="dag2[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><input style="width:50px" type="number" name="dag3[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><input style="width:50px" type="number" name="dag4[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><input style="width:50px" type="number" name="dag5[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><input style="width:50px" type="number" name="dag6[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><input style="width:50px" type="number" name="dag7[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><img src="./img/buttons/icons8-plus-48.png" alt="toevoegen soort uur" title="toevoegen soort uur" onclick="add_row();" /></td>
    		<td></td>
   		</tr>
  	</table>
  	<input class="button" type="submit" name="save" value="save">
  	<input class="button" type="submit" name="cancel" value="cancel">
</form>
</div>	

<?php 
include ("footer.php");
?>		
