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
$currentWeek = date("W");
$currentJaar = date("Y");
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
// BUTTON changeWeeknr
//------------------------------------------------------------------------------------------------------
if (isset($_POST['updateweeknr'])) {
    $inputweeknr = $_POST["week"];
    getWeekdays($inputweeknr);
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
    $number = count($_POST["dag1"]);
    if($number > 0) {
        for($i=0; $i<$number; $i++) {
            if(trim($_POST["soortuur"][$i] != '')) {
                //$sql = "INSERT INTO tbl_name(name) VALUES('".mysqli_real_escape_string($dbconn, $_POST["name"][$i])."') or die(mysqli_error($db))";
                //mysqli_query($connect, $sql);
                echo "Urensoort: ".$_POST["soortuur"][$i]."<br />";
            }
        }
    } else {
        echo "Please Enter Name";
    }  
}
?>

<div id="form_div">
<form name="add_uren" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<table>
		<tr>
			<td>Weeknummer</td>
			<td><input type="number" name="week" id="camp-week" value="<?php echo $inputweeknr; ?>" size="4" required></td>
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
    		<td><input type="number" name="dag1[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><input type="number" name="dag2[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><input type="number" name="dag3[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><input type="number" name="dag4[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><input type="number" name="dag5[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><input type="number" name="dag6[]" min="0" max="24" step="0.25" size="2"></td>
    		<td><input type="number" name="dag7[]" min="0" max="24" step="0.25" size="2"></td>
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
