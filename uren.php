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

$inputweeknr = date('Y').date('W');
getWeekdays($inputweeknr);

//------------------------------------------------------------------------------------------------------
// Haal alle soorturen op uit de database zodat deze in de dorpdown getoond worden
//------------------------------------------------------------------------------------------------------
$sql_soorturen = mysqli_query($dbconn, "SELECT * FROM soorturen ORDER BY code");
$option = "";

while($row_soorturen = mysqli_fetch_array($sql_soorturen)) 
{
    $option .= "<option value='".$row_soorturen['code']."'>".$row_soorturen['code']." - ".$row_soorturen['omschrijving']."</option>";
}

//------------------------------------------------------------------------------------------------------
// This code runs before the form is displayed
//------------------------------------------------------------------------------------------------------

/**
 * Dit is het begin van de code wat uitgevoerd wordt indien het formulier is gesubmit
 * Welk gedeelte van de code is afhankelijk van de button waarop geclickt is.
 */

//------------------------------------------------------------------------------------------------------
// BUTTON changeWeeknr is op geklikt om een andere week te muteren.
// Door getWeekdays worden de dagen en data van die nieuwe week berekend
//------------------------------------------------------------------------------------------------------
if (isset($_POST['updateweeknr']) || (isset($_POST['week']))) 
{
    $inputweeknr = $_POST["week"];
    getWeekdays($_POST['week']);
    // Controleer of deze week bestaat. Zo ja dan gegevens ophalen en tonen op scherm
    // Indien week al approved kan deze niet gewijzigd worden
}

//------------------------------------------------------------------------------------------------------
// BUTTON Cancel
//------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) 
{
    header("location: index.php");
}

//------------------------------------------------------------------------------------------------------
// BUTTON Save
//------------------------------------------------------------------------------------------------------
if (isset($_POST['save']) || isset($_POST['approval'])) 
{
    getWeekdays($_POST['week']);
    
    $sql_select_uren = "SELECT * FROM uren where user='".$username."' AND week='".$week."' AND jaar='".$year."'";
    $check_select_uren = mysqli_query($dbconn, $sql_select_uren);
    
    if (mysqli_num_rows($check_select_uren) > 0) {
        $sql_delete_uren = "DELETE FROM uren where user='".$username."' AND week='".$week."' AND jaar='".$year."'";
        $check_delete_uren = mysqli_query($dbconn, $sql_delete_uren);
        writelogrecord("uren","INFO Records worden verwijderd van jaar ".$year." en week ".$week." voor het updaten van de betreffende week");
    }
    
    $inputweeknr = $_POST['week'];
    $aantalRijen = count($_POST["dag1"]);
    
    for($ix1=0; $ix1<$aantalRijen; $ix1++) 
    {
        if(trim($_POST["soortuur"][$ix1] != '')) 
        {
            //001 Check de ingevulde velden op correctheid
            checkIngevuldeUrenPerSoort($ix1);
            
            //002 Check of de week al voorkomt in de database Indien ja EN al approved dan kunnen de gegevens niet gewijzigd worden
            for($ix2=0; $ix2<7; $ix2++) 
            {
                if ($urenarray[$ix2] > 0) 
                {
                    $datum = date("Y-m-d", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +'.$ix2.' days'));
                    $dagnummer = $ix2;
                    
                    $sql_insert_uren = "INSERT INTO uren (jaar, week, dagnummer, soortuur, datum, uren, user)
                        values('".$year."', '".$week."', '".$dagnummer."', '".$_POST['soortuur'][$ix1]."', '".$datum."', '".$urenarray[$ix2]."', '".$username."')";
                    $check_insert_uren = mysqli_query($dbconn, $sql_insert_uren);
                    writelogrecord("uren","INFO Records worden toegevoegd van jaar ".$year." en week ".$week." voor het updaten van de betreffende week");
                }
            }
        }
    }
    
    if (isset($_POST['approval'])) 
    {
        //getWeekdays($_POST['week']);
        $sql_update_uren = "UPDATE uren SET terapprovalaangeboden='1' where user='".$username."' AND week='".$week."' AND jaar='".$year."'";
        
        if($sql_result = mysqli_query($dbconn, $sql_update_uren)) 
        {
            writelogrecord("uren","INFO Week ".$year." /  ".$week." is aangeboden om approved te worden");
        } 
        else 
        {
            writelogrecord("uren","ERROR Week ".$year." /  ".$week." is aangeboden maar update is mislukt");
            writelogrecord("uren","ERROR sql error: ".mysqli_error($dbconn));
            echo "ERROR: Problemen in uitvoeren van query ".mysqli_error($dbconn);
        }
        header("location: index.php");
    }
}
?>

<div id="form_div">
<form name="add_uren" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<?php
$sql_approval = "SELECT approved, terapprovalaangeboden FROM uren where user='".$username."' AND week='".$week."' AND jaar='".$year."' ORDER BY soortuur, dagnummer";

if($sql_result_approval = mysqli_query($dbconn, $sql_approval)) 
{
    if(mysqli_num_rows($sql_result_approval) > 0) 
    {
        while($row_approved = mysqli_fetch_array($sql_result_approval)) 
        {
            $frm_approved = $row_approved['approved'];
            $frm_terapprovalaangeboden = $row_approved['terapprovalaangeboden'];
            
            if($frm_approved == 1) 
            {
                $status = "Deze week is al approved en kan derhalve niet meer gewijzigd worden.";
            }
            
            if($frm_approved == 0) 
            {
                if($frm_terapprovalaangeboden == 1 ) 
                {
                    $status = "Deze week is al ter approval aangeboden maar kan nog gewijzigd worden";
                }
                if($frm_terapprovalaangeboden == 0 ) 
                {
                    $status = "Deze week is nog niet ter approval aangeboden.";
                }
            }
        }
    } 
    else 
    {
        $status = "Dit is een nieuwe week en derhalve nog niet ter approval aangeboden";
    }
}

echo "<table>";
echo "<tr>";
echo "<td><strong>Weeknummer</strong></td>";
echo "<td><input style='width:4.66vw' type='number' name='week' id='camp-week' value='".$inputweeknr."' required onchange='this.form.submit()'></td>";
echo "<td><img class='button' src=\"./img/buttons/icons8-info-48.png\" alt=\"informatie\" title=\"informatie\" /></td><td>".$status."</td";
echo "</tr>";

echo "</table>";
echo "<table id='uren_table'>";
echo "<tr>";
echo "<th>Soortuur</th>";

for($ix6=0; $ix6<7; $ix6++) 
{
    echo "<th><center>".$weekDatum[$ix6]."<br>".$weekDagNaam[$ix6]."</center></th>";
}

echo "<th style='text-align:right'>Totaal</th><th style='width:1.5vw'></th>";
echo "</tr>";

//------------------------------------------------------------------------------------------------------
// Bekijk huidige week of er al uren ingevuld zijn.
//------------------------------------------------------------------------------------------------------
for($ix3=0; $ix3<7; $ix3++) 
{
    ${"frm_valueDag$ix3"} = '';
}

$tmp_soortuur = 'eersteloop';

$sql_code = "SELECT * FROM uren 
             WHERE user='".$username."' 
             AND week='".$week."' 
             AND jaar='".$year."' 
             ORDER BY soortuur, dagnummer";
// Om regels per soortuur te krijgen
if($sql_out = mysqli_query($dbconn, $sql_code)) 
{
    if(mysqli_num_rows($sql_out) > 0) 
    {
        while($row_uren = mysqli_fetch_array($sql_out)) 
        {
            $frm_approved = $row_uren['approved'];
            if($frm_approved == 1) 
            {
                $frm_readonly = "readonly";
                $frm_select_disabled = "disabled";
            } 
            else 
            {
                $frm_readonly = "";
                $frm_select_disabled = "";
            }
            
            if(($tmp_soortuur <> $row_uren['soortuur']) && ($tmp_soortuur <> 'eersteloop')) 
            {
                $sql_soorturen = mysqli_query($dbconn, "SELECT * FROM soorturen ORDER BY code");
                $option = "";
                while($row_soorturen = mysqli_fetch_array($sql_soorturen)) 
                {
                    if ($tmp_soortuur == $row_soorturen['code']) 
                    {
                        $option_selected = 'selected';
                    } 
                    else 
                    {
                        $option_selected = '';
                    }
                    $option .= "<option ".$option_selected." value='".$row_soorturen['code']."'>".$row_soorturen['code']." - ".$row_soorturen['omschrijving']."</option>";
                }

                echo "<tr id='row1'>";
                echo '<div id="dropdownSoortUren" data-options="'.$option.'"></div>';
                echo "<td><select name='soortuur[]' ".$frm_select_disabled." ".$frm_readonly.">".$option."</select></td>";
               
                $totaal_uren_per_soort = 0;
                
                for($ix5=0; $ix5<7; $ix5++) 
                {
                    $frm_value = ${"frm_valueDag$ix5"};
                    $ix5b = $ix5 + 1;
                    
                    echo "<td><input ".$frm_readonly." style='width:3.33vw; text-align:right' type='number' name='dag".$ix5b."[]' min='0' max='24' step='0.25' size='2' value='".$frm_value."'></td>";
                    
                    $totaal_uren_per_soort = number_format($totaal_uren_per_soort + floatval($frm_value), 2);
                    
                    if($ix5b == 7) 
                    {
                        echo "<td class='totaalkolom'><input readonly style='width:3.33vw; text-align:right' type='number' name='totaalpersoort' min='0' max='24' step='0.25' size='2' value='".$totaal_uren_per_soort."'></td>";
                    }
                }
                if($frm_approved == 0) 
                {
                    echo "<td><img class='button' src='./img/buttons/icons8-plus-48.png' alt='toevoegen soort uur' title='toevoegen soort uur' onclick='add_row();' /></td>";
                }
                else 
                {
                    echo "<td></td>";
                }
                
                echo "<td></td>";
                echo "</tr>";

                for($ix4=0; $ix4<7; $ix4++) 
                {
                    ${"frm_valueDag$ix4"} = '';
                }
            }
            for($ix5=0; $ix5<7; $ix5++) 
            {
                if($row_uren['dagnummer'] == $ix5) 
                {
                    ${"frm_valueDag$ix5"} = $row_uren['uren'];
                }
            }
            $tmp_soortuur = $row_uren['soortuur'];
        }
    } 
    else 
    {
        $frm_select_disabled = "";
        $frm_readonly = "";
        $frm_approved = "";
    }

	echo "<tr id='row1'>";
    
    // Loop om de dropdown met soorten uren op te bouwen
    $option = "";
    
    $sql_code = "SELECT * FROM soorturen
                 ORDER BY code";
    $sql_out2 = mysqli_query($dbconn, $sql_code);
    
    while($sql_rows = mysqli_fetch_array($sql_out2)) 
    {
        if ($tmp_soortuur == $sql_rows['code']) 
        {
            $option_selected = 'selected';
        } 
        else 
        {
            $option_selected = '';
        }
        
        $option .= "<option ".$option_selected." value='".$sql_rows['code']."'>".$sql_rows['code']." - ".$sql_rows['omschrijving']."</option>";
    }
    
    echo '<div id="dropdownSoortUren" data-options="'.$option.'"></div>';
    echo "<td><select name='soortuur[]' ".$frm_select_disabled." ".$frm_readonly.">".$option."</select></td>";
    
    $totaal_uren_per_soort = 0;
    
    for($ix7=0; $ix7<7; $ix7++) 
    {
        $frm_value = ${"frm_valueDag$ix7"};
        $ix7b = $ix7 + 1;
        
        echo "<td><input ".$frm_readonly." style='width:3.33vw; text-align:right' type='number' name='dag".$ix7b."[]' min='0' max='24' step='0.25' size='2' value='".$frm_value."'></td>";
        
        $totaal_uren_per_soort = number_format($totaal_uren_per_soort + floatval($frm_value), 2);
        
        if($ix7b == 7) 
        {
            echo "<td class='totaalkolom'><input readonly style='width:3.33vw; text-align:right;' type='number' name='totaalpersoort' min='0' max='24' step='0.25' size='2' value='".$totaal_uren_per_soort."'></td>";
        }
    }

    if($frm_approved == 0) 
    {
        echo "<td><img class='button' src='./img/buttons/icons8-plus-48.png' alt='toevoegen soort uur' title='toevoegen soort uur' onclick='add_row();' /></td>";
    }
    else 
    {
        echo "<td></td>";
    }
    echo "<td></td>";
	echo "</tr>";

} else {
    echo "ERROR: Kan geen connectie met de database maken. ". mysqli_error($dbconn);
}

echo "</table>";
// This button is needed for when user pushes the ENTER button when changing the weeknumber. Button is not displayed
echo "<input type='submit' name='dummy' value='None' style='display: none'>";

if($frm_approved == 0) 
{
    echo "<input class='button' type='submit' name='save' value='save'>";
    echo "<input class='button' type='submit' name='approval' value='submit'>";
}

echo "<input class='button' type='submit' name='cancel' value='cancel'>";
?>
</form>
</div>

<?php
include ("footer.php");
?>
