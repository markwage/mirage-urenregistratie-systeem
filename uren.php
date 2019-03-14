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
//------------------------------------------------------------------------------------------------------
// From here this code runs if the form has been submitted
//------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------
// BUTTON Cancel
//------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) {
    header("location: index.php");
}

if (isset($_POST['save'])) {
    echo "Hier komt de check op invoer en wegschrijven van de data naar de tabel<br />";
    echo "Dat staat nu in user_add.php";
}
?>

<div id="form_div">
<form name="add_uren" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<table id="employee_table">
   		<tr id="row1">
   		    <!-- Het aantal td's moet in functions.js net zoveel zijn! -->
    		<td><input type="text" name="name[]" placeholder="Enter Name"></td>
    		<td><input type="text" name="age[]" placeholder="Enter Age"></td>
    		<td><input type="text" name="job[]" placeholder="Enter Job"></td>
    		<td><img src="./img/buttons/icons8-plus-48.png" alt="toevoegen soort uur" title="toevoegen soort uur" onclick="add_row();" /></td>
    		<td></td>
   		</tr>
  	</table>
  	<input class="button" type="submit" name="cancel" value="cancel">
  	<input class="button" type="submit" name="save" value="save">
</form>
</div>	
<?php 
include ("footer.php");
?>		
