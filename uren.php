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
// This code runs before the form is displayed
//------------------------------------------------------------------------------------------------------

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
    $aantalRijen = count($_POST["dag1"]);
    for($ix1=0; $ix1<$aantalRijen; $ix1++) {
        if(trim($_POST["soortuur"][$ix1] != '')) {
            //001 Check de ingevulde velden op correctheid
            checkIngevuldeUrenPerSoort($ix1);
            //002 Check of de week al voorkomt in de database Indien ja EN al approved dan kunnen de gegevens niet gewijzigd worden
            for($ix2=0; $ix2<7; $ix2++) {
                if ($urenarray[$ix2] > 0) {
                    $datum = date("Y-m-d", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +'.$ix2.' days'));
                    $dagnummer = $ix2;
                    $sql_insert_uren = "INSERT INTO uren (jaar, week, dagnummer, soortuur, datum, uren, user)
                        values('".$year."', '".$week."', '".$dagnummer."', '".$_POST['soortuur'][$ix1]."', '".$datum."', '".$urenarray[$ix2]."', '".$username."')";
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
	<?php 
	echo "<table id='uren_table'>";
        echo "<tr>";
            echo "<th>Soortuur</th>";
            for($ix6=0; $ix6<7; $ix6++) {
                echo "<th><center>".$weekDatum[$ix6]."<br>".$weekDagNaam[$ix6]."</center></th>";
            }
	    echo "</tr>";
	    //------------------------------------------------------------------------------------------------------
	    // Bekijk huidige week of er al uren ingevuld zijn.
	    //------------------------------------------------------------------------------------------------------
	    for($ix3=0; $ix3<7; $ix3++) {
	        ${"frm_valueDag$ix3"} = '';
	    }
	    $tmp_soortuur = 'eersteloop';
	    
	    $sql_uren = "SELECT * FROM uren where user='".$username."' AND week='".$week."' AND jaar='".$year."' ORDER BY soortuur, dagnummer";
        // Om regels per soortuur te krijgen HOE??
        if($sql_result_uren = mysqli_query($dbconn, $sql_uren)) {
	        if(mysqli_num_rows($sql_result_uren) > 0) {
	            while($row_uren = mysqli_fetch_array($sql_result_uren)) {
	                if($row_uren['approved'] == 1) { 
	                    $frm_readonly = "readonly";
	                    $frm_selectdisabled = "disabled";
	                } else { 
	                    $frm_readonly = "";
	                    $frm_selectdisabled = "";
	                }
	                if(($tmp_soortuur <> $row_uren['soortuur']) && ($tmp_soortuur <> 'eersteloop')) {
	                    $sql_soorturen = mysqli_query($dbconn, "SELECT * FROM soorturen ORDER BY code");
	                    $option = "";
	                    while($row_soorturen = mysqli_fetch_array($sql_soorturen)) {
	                        if ($tmp_soortuur == $row_soorturen['code']) {
	                            $option_selected = 'selected';
	                        } else {
	                            $option_selected = '';
	                        }
	                        $option .= "<option ".$option_selected." value='".$row_soorturen['code']."'>".$row_soorturen['code']." - ".$row_soorturen['omschrijving']."</option>";
	                    }
	                    echo "<tr id='row1'>";
	                        echo '<div id="dropdownSoortUren" data-options="'.$option.'"></div>';
	                        echo "<td><select name='soortuur[]' ".$frm_selectdisabled." ".$frm_readonly.">".$option."</select></td>";
	                        //echo "<td><select name='soortuur[]' disabled >".$option."</select></td>";
	                        for($ix5=0; $ix5<7; $ix5++) {
	                            $frm_value = ${"frm_valueDag$ix5"};
	                            $ix5b = $ix5 + 1;
	                            echo "<td><input ".$frm_readonly." style='width:50px' type='number' name='dag".$ix5b."[]' min='0' max='24' step='0.25' size='2' value='".$frm_value."'></td>";
	                        }
	                        if($row_uren['approved'] == 0) echo "<td><img src='./img/buttons/icons8-plus-48.png' alt='toevoegen soort uur' title='toevoegen soort uur' onclick='add_row();' /></td>";
	                        else echo "<td></td>";
	                        echo "<td></td>";
	                    echo "</tr>";
	                    for($ix4=0; $ix4<7; $ix4++) {
	                        ${"frm_valueDag$ix4"} = '';
	                    }
	                }
	                for($ix5=0; $ix5<7; $ix5++) {
	                    if($row_uren['dagnummer'] == $ix5) ${"frm_valueDag$ix5"} = $row_uren['uren'];
	                }
	                $tmp_soortuur = $row_uren['soortuur'];
	            }
	        }
   		    echo "<tr id='row1'>";
   	            $sql_soorturen = mysqli_query($dbconn, "SELECT * FROM soorturen ORDER BY code");
   	            $option = "";
   	            while($row_soorturen = mysqli_fetch_array($sql_soorturen)) {
   	                if ($tmp_soortuur == $row_soorturen['code']) {
   	                    $option_selected = 'selected';
   	                } else {
   	                   $option_selected = '';
   	                }
   	                $option .= "<option ".$option_selected." value='".$row_soorturen['code']."'>".$row_soorturen['code']." - ".$row_soorturen['omschrijving']."</option>";
   	            }
   	            echo '<div id="dropdownSoortUren" data-options="'.$option.'"></div>';
   	            //echo "<td><select ".$frm_readonly." name='soortuur[]'>".$option."</select></td>";
   	            echo "<td><select name='soortuur[]' ".$frm_selectdisabled." ".$frm_readonly.">".$option."</select></td>";
   	            for($ix7=0; $ix7<7; $ix7++) {
   	                $frm_value = ${"frm_valueDag$ix7"};
   	                $ix7b = $ix7 + 1;
   	                echo "<td><input ".$frm_readonly." style='width:50px' type='number' name='dag".$ix7b."[]' min='0' max='24' step='0.25' size='2' value='".$frm_value."'></td>";
   	            }
   	            if($row_uren['approved'] == 0) echo "<td><img src='./img/buttons/icons8-plus-48.png' alt='toevoegen soort uur' title='toevoegen soort uur' onclick='add_row();' /></td>";
   	            else echo "<td></td>";
    		    //echo "<td><img src='./img/buttons/icons8-plus-48.png' alt='toevoegen soort uur' title='toevoegen soort uur' onclick='add_row();' /></td>";
    		    echo "<td></td>";
   		    echo "</tr>";
        } else {
            echo "ERROR: Kan geen connectie met de database maken. Query:". $sql_select. " failed.". mysqli_error($dbconn);
        }
   	
  	echo "</table>";
  	?>
  	<input class="button" type="submit" name="save" value="save">
  	<input class="button" type="submit" name="cancel" value="cancel">
</form>
</div>	

<?php 
include ("footer.php");
?>		
