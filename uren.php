<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();
include ("header.php");

?>
<div id="main">
	<h1>Urenadministratie</h1>

<?php
displayUserGegevens();

// Indien weeknr en jaar is doorgegeven via url dan dit de inputweeknr maken
// Anders is vandaag de inputdatum
// edtweek wordt doorgegeven via het home-scherm als user op edit-button klikt
// 
if (isset($_GET['edtweek']))
{
    $inputweeknr = $_GET['edtweek'];
}
else
{
    $inputweeknr = date('Y')."-W".date('W');
}
getWeekdays($inputweeknr);

//------------------------------------------------------------------------------------------------------
// Haal alle soorturen op uit de database zodat deze in de dropdown getoond worden
//------------------------------------------------------------------------------------------------------
$option = "";

$sql_code1 = "SELECT * FROM soorturen
             ORDER BY code";
$sql_out1 = mysqli_query($dbconn, $sql_code1);

while($sql_rows1 = mysqli_fetch_array($sql_out1)) 
{
    $option .= "<option value='".$sql_rows1['code']."'>".$sql_rows1['code']." - ".$sql_rows1['omschrijving']."</option>";
}

/**
 * Dit is het begin van de code wat uitgevoerd wordt indien het formulier is gesubmit
 * Welk gedeelte van de code is afhankelijk van de button waarop geclickt is.
 */

//------------------------------------------------------------------------------------------------------
// BUTTON changeWeeknr is op geklikt om een andere week te muteren.
// Door getWeekdays worden de dagen en data van die nieuwe week berekend
//------------------------------------------------------------------------------------------------------
if (isset($_POST['updateweeknr']) || isset($_POST['week']) || isset($_POST['week_nummer']))
{
    $inputweeknr = $_POST["week_nummer"];
    getWeekdays($_POST['week_nummer']);
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
//   ix1 = loop aantal rijen dat er uren ingevuld zijn
//     ix2 = dagen binnen ix1 (ma t/m zo)
//   
//------------------------------------------------------------------------------------------------------
if (isset($_POST['save']) || isset($_POST['approval'])) 
{
    getWeekdays($_POST['week_nummer']);
    
    $sql_select_uren = "SELECT * FROM uren 
                        WHERE user='".$username."' 
                        AND week='".$week."' 
                        AND jaar='".$year."'";
    $check_select_uren = mysqli_query($dbconn, $sql_select_uren);
    
    //--------------------------------------------------------------------------
    // niet de uren verwijderen die al approved zijn in de vorige maand
    //--------------------------------------------------------------------------
    
    if (mysqli_num_rows($check_select_uren) > 0) {
        $sql_delete_uren = "DELETE FROM uren 
                            WHERE user='".$username."' 
                            AND week='".$week."' 
                            AND jaar='".$year."'
                            AND approved = 0";
        $check_delete_uren = mysqli_query($dbconn, $sql_delete_uren);
        $log_record = new Writelog();
        $log_record->progname = $_SERVER['PHP_SELF'];
        $log_record->message_text  = "Records worden verwijderd van jaar ".$year." en week ".$week." voor het updaten van de betreffende week";
        $log_record->write_record();
        //writelogrecord("uren","INFO Records worden verwijderd van jaar ".$year." en week ".$week." voor het updaten van de betreffende week");
    }
    
    $inputweeknr = $_POST['week_nummer'];
    $aantalRijen = count($_POST["dag1"]);
    
    for($ix1=0; $ix1<$aantalRijen; $ix1++) 
    {
        if(trim($_POST["soortuur"][$ix1] != ''))    
        {
            
            // Check de ingevulde velden op correctheid
            checkIngevuldeUrenPerSoort($ix1);
            
            
            // Check of de week al voorkomt in de database Indien ja EN al approved dan kunnen de gegevens niet gewijzigd worden
            for($ix2=0; $ix2<7; $ix2++) 
            {
                if ($urenarray[$ix2] > 0) 
                {
                    $datum = date("Y-m-d", strtotime($year.'W'.str_pad($week, 2, 0, STR_PAD_LEFT).' +'.$ix2.' days'));
                    $str_datum = strtotime($datum);
                    $maand = date("m", $str_datum);
                    $dagnummer = $ix2;
                    
                    // niet de uren inserten die al approved waren en dus in vorige step niet verwijderd zijn
                    // Controleren of de datum van die user/week al in de database aanwezig is. Zou niet 
                    //   moeten zijn omdat de week verwijderd is. Indien wel dan was deze dus al approved
                    
                    $sql_check_datum_approved = "SELECT * FROM uren
                                                 WHERE user='".$username."' 
                                                 AND datum='".$datum."'
                                                 AND soortuur='".$_POST['soortuur'][$ix1]."'";
                    $check_check_datum_approved = mysqli_query($dbconn, $sql_check_datum_approved);
                    if (!$check_check_datum_approved)
                    {
                        $log_record = new Writelog();
                        $log_record->progname = $_SERVER['PHP_SELF'];
                        $log_record->loglevel = 'ERROR';
                        $log_record->message_text  = "Er is een fout opgetreden bij het selecteren van uren -> ".mysqli_error($dbconn);
                        $log_record->write_record();
                        //writelogrecord("uren","ERR03INS001Er is een fout opgetreden bij het inserten van uren -> ".mysqli_error($dbconn));
                    }
                    
                    $rows_check_datum_approved = mysqli_num_rows($check_check_datum_approved);
                    
                    if ($rows_check_datum_approved == 0)
                    {
                        $sql_insert_uren = "INSERT INTO uren (jaar, maand, week, dagnummer, soortuur, datum, uren, user)
                                            VALUES('".$year."', 
                                                   '".$maand."', 
                                                   '".$week."', 
                                                   '".$dagnummer."', 
                                                   '".$_POST['soortuur'][$ix1]."', 
                                                   '".$datum."', 
                                                   '".$urenarray[$ix2]."', 
                                                   '".$username."')";
                        $check_insert_uren = mysqli_query($dbconn, $sql_insert_uren);
                        
                        if (!$check_insert_uren)
                        {
                            $log_record = new Writelog();
                            $log_record->progname = $_SERVER['PHP_SELF'];
                            $log_record->loglevel = 'ERROR';
                            $log_record->message_text  = "Er is een fout opgetreden bij het inserten van uren -> ".mysqli_error($dbconn);
                            $log_record->write_record();
                            //writelogrecord("uren","ERROR Er is een fout opgetreden bij het inserten van uren -> ".mysqli_error($dbconn));
                        }
                    }
                    $log_record = new Writelog();
                    $log_record->progname = $_SERVER['PHP_SELF'];
                    $log_record->message_text  = "Records worden toegevoegd van jaar ".$year." en week ".$week." voor het updaten van de betreffende week";
                    $log_record->write_record();
                    //writelogrecord("uren","INFO Records worden toegevoegd van jaar ".$year." en week ".$week." voor het updaten van de betreffende week");
                }
            }
        }
    }
}
?>

<!-- ------------------------------------------------------------------------------
  Begin van het formulier
------------------------------------------------------------------------------  -->
<div id="form_div">
<form name="add_uren" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<?php

echo "<table>";
echo "<tr>";
echo "<td><strong>Weeknummer</strong></td>";
echo "<td><input type='week' style='width:9vw' name='week_nummer' value='".$inputweeknr."' required></td>";
echo "<td><input class='button' type='submit' name='change_week' value='submit'></td>";
echo "</tr>";
echo "</table>";

echo "<center><table id='uren_table'>";
echo "<tr>";
echo "<th>Soortuur</th>";

// Tabelheaders aanmaken met datum/afkorting dagnaam
// En controleren of de maand waarin de dag valt al approved is
for($ix6=0; $ix6<7; $ix6++) 
{
    echo "<th><center>".$weekDatum[$ix6]."<br>".$weekDagNaam[$ix6]."</center></th>";
        
    $sql_check_approved = "SELECT * FROM approvals
                           WHERE user='".$username."'
                           AND maand='".$weekMaand[$ix6]."'
                           AND jaar='".$weekJaar[$ix6]."'";
    $check_check_approved = mysqli_query($dbconn, $sql_check_approved);
    
    if(!$check_check_approved)
    {
        $log_record = new Writelog();
        $log_record->progname = $_SERVER['PHP_SELF'];
        $log_record->loglevel = 'ERROR';
        $log_record->message_text  = "Select gaat fout: ".$sql_code." - ".mysqli_error($dbconn);
        $log_record->write_record();
    }
    
    $rows_check_approved = mysqli_num_rows($check_check_approved);
    
    if($rows_check_approved > 0)
    {
        $dag_readonly[$ix6] = 'readonly';
    }
    else
    {
        $dag_readonly[$ix6] = '';
    }
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

// Om regels per soortuur te krijgen
// 
//   Er dient ook gechecked te worden of alle dagen van deze week approved zijn
//   Zo niet dan moet de +-button achteraan gewoon getoond worden
//   Options veld van soortuur moet wel readonly zijn indien er minimaal één dag approved is

$tmp_soortuur = 'eersteloop';

$sql_code2 = "SELECT * FROM uren 
             WHERE user='".$username."' 
             AND week='".$week."' 
             AND jaar='".$year."' 
             ORDER BY soortuur, dagnummer";
if($sql_out2 = mysqli_query($dbconn, $sql_code2)) 
{
    $sql_rows2 = mysqli_num_rows($sql_out2);
    if(mysqli_num_rows($sql_out2) > 0) 
    {
        
        while($row_uren2 = mysqli_fetch_array($sql_out2)) 
        {            
            // Onderstaande if uitvoeren als het soortuur anders is dan laatste gelezen record. Dit betekent dat er
            // een nieuwe regel gedisplayed moet worden omdat per soortuur een regel op scherm komt
            // 'eersteloop' wordt gebruikt om het eerst gelezen record niet meteen op het scherm te displayen
            
            if(($tmp_soortuur <> $row_uren2['soortuur']) && ($tmp_soortuur <> 'eersteloop')) 
            {
                // Loop om de dropdown met soorten uren op te bouwen
                // En om te bepalen of de betreffende soortuur voor de regel geldt waarvoor uren zijn ingevuld
                $sql_soorturen2 = mysqli_query($dbconn, "SELECT * FROM soorturen ORDER BY code");
                $option = "";
                
                while($row_soorturen2 = mysqli_fetch_array($sql_soorturen2)) 
                {
                    if ($tmp_soortuur == $row_soorturen2['code'])
                    {
                        $option_selected = 'selected';
                        $option_disabled = 'enabled';
                    }
                    else
                    {
                        $option_selected = '';
                        if(($dag_readonly[0] == 'readonly') || ($dag_readonly[6] == 'readonly'))
                        {
                            $option_disabled = 'disabled';
                        }
                        else
                        {
                            $option_disabled = 'enabled';
                        }
                    }
                    $option .= "<option ".$option_selected." ".$option_disabled." value='".$row_soorturen2['code']."'>".$row_soorturen2['code']." - ".$row_soorturen2['omschrijving']."</option>";
                }
                echo "<tr id='row1'>";
                echo '<div id="dropdownSoortUren" data-options="'.$option.'"></div>';
               
                $totaal_uren_per_soort = 0;
                
                for($ix5=0; $ix5<7; $ix5++) 
                {
                    $frm_value = ${"frm_valueDag$ix5"};
                    if($dag_readonly[$ix5] == 'readonly')
                    {
                        $js_readonly = 'readonly';
                        $js_aantal_dagen_readonly = $ix5;
                    }
                    if($ix5 == 0)
                    {
                        echo "<td><select name='soortuur[]' selected>".$option."</select></td>";
                    }
                    
                    $ix5b = $ix5 + 1;
                    echo "<td title='Geef waarde in decimalen. Hierbij is een kwartier 0.25, half uur 0.5 en 45 minuten is 0.75'><input ".$dag_readonly[$ix5]." style='width:3.33vw; text-align:right' type='number' name='dag".$ix5b."[]' min='0' max='24' step='0.25' size='2' value='".$frm_value."'></td>";
                    
                    $totaal_uren_per_soort = number_format($totaal_uren_per_soort + floatval($frm_value), 2);
                    
                    if($ix5b == 7) 
                    {
                        echo "<td class='totaalkolom'><input readonly style='width:3.33vw; text-align:right' type='number' name='totaalpersoort' min='0' max='24' step='0.25' size='2' value='".$totaal_uren_per_soort."'></td>";
                    }
                }
                
                if($dag_readonly[6] == '') // of de laatste zondag valt niet in de maand die approved is.
                {
                    if(isset($js_aantal_dagen_readonly)){
                        $aantal_dagen_readonly = $js_aantal_dagen_readonly;
                    }
                    else
                    {
                        $aantal_dagen_readonly = '';
                    }
                    echo "<td><img class='button' src='./img/buttons/icons8-plus-48.png' alt='toevoegen nieuwe regel' title='toevoegen nieuwe regel' onclick='add_row(".$aantal_dagen_readonly.");' /></td>";
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
            
            for($ix8=0; $ix8<7; $ix8++) 
            {
                if($row_uren2['dagnummer'] == $ix8) 
                {
                    ${"frm_valueDag$ix8"} = $row_uren2['uren'];
                }
            }
            $tmp_soortuur = $row_uren2['soortuur'];
        }
    } 
    else 
    {
        $frm_select_disabled = "";
    }

	echo "<tr id='row1'>";
    
    // Loop om de dropdown met soorten uren op te bouwen
    // En om te bepalen of de betreffende soortuur voor de regel geldt waarvoor uren zijn ingevuld
    // $option_add wordt gebruikt omdat bij het toevoegen van een nieuwe regel de dropdown niet disabled moet zijn
    $option = "";
    $option_add = "";
    
    $sql_code3 = "SELECT * FROM soorturen
                 ORDER BY code";
    $sql_out3 = mysqli_query($dbconn, $sql_code3);
    
    while($sql_rows3 = mysqli_fetch_array($sql_out3)) 
    {
        
        if ($tmp_soortuur == $sql_rows3['code']) 
        {
            $option_selected = 'selected';
            $option_disabled = 'enabled';
        } 
        else 
        {
            $option_selected = '';
            if(($dag_readonly[0] == 'readonly') || ($dag_readonly[6] == 'readonly'))
            {
                $option_disabled = 'disabled';
            }
            else
            {
                $option_disabled = 'enabled';
            }
        }
        $option .= "<option ".$option_selected." ".$option_disabled." value='".$sql_rows3['code']."'>".$sql_rows3['code']." - ".$sql_rows3['omschrijving']."</option>";
    }
    
    echo '<div id="dropdownSoortUren" data-options="'.$option.'"></div>';
    
    $totaal_uren_per_soort = 0;
    
    for($ix7=0; $ix7<7; $ix7++) 
    {
        $frm_value = ${"frm_valueDag$ix7"};
            // Onderstaande variabele wordt in javascript gebruikt om te bepalen hoevel lege velden readonly moeten zijn
            // De variabele krijgt de hoogste waarda van ix7 als inhoud dag_readonly gelijk is aan readonly
            if($dag_readonly[$ix7] == 'readonly') 
            {   
                $js_readonly = 'readonly';
                $js_aantal_dagen_readonly = $ix7;
            }
        
        // Indien al de eerste dag readonly is mag soortuur niet meer gewijzigd worden
           
        if($ix7 == 0)
        {
            echo "<td><select name='soortuur[]' selected>".$option."</select></td>";
        }
        
        $ix7b = $ix7 + 1;
        echo "<td title='Geef waarde in decimalen. Hierbij is een kwartier 0.25, half uur 0.5 en 45 minuten is 0.75'><input ".$dag_readonly[$ix7]." style='width:3.33vw; text-align:right' type='number' name='dag".$ix7b."[]' min='0' max='24' step='0.25' size='2' value='".$frm_value."'></td>";
        
        $totaal_uren_per_soort = number_format($totaal_uren_per_soort + floatval($frm_value), 2);
        
        // Vullen van de totaalkolom
        if($ix7b == 7) 
        {
            echo "<td class='totaalkolom'><input readonly style='width:3.33vw; text-align:right;' type='number' name='totaalpersoort' min='0' max='24' step='0.25' size='2' value='".$totaal_uren_per_soort."'></td>";
        }
    }

    // De save button
    
    if($dag_readonly[6] == '') // of de laatste zondag valt niet in de maand die approved is.
    {
        if(isset($js_aantal_dagen_readonly)){
            $aantal_dagen_readonly = $js_aantal_dagen_readonly;
        }
        else 
        {
            $aantal_dagen_readonly = '';    
        }
        echo "<td><img class='button' src='./img/buttons/icons8-plus-48.png' alt='toevoegen nieuwe regel' title='toevoegen nieuwe regel' onclick='add_row(".$aantal_dagen_readonly.");' /></td>";
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

echo "</table></center>";
// This button is needed for when user pushes the ENTER button when changing the weeknumber. Button is not displayed
echo "<input type='submit' name='dummy' value='None' style='display: none'>";

if($dag_readonly[6] == '') 
{
    echo "<input class='button' type='submit' name='save' value='save'>";
}

echo "<input class='button' type='submit' name='cancel' value='cancel'>";
?>
</form>
</div>
<!-- ------------------------------------------------------------------------------
  Einde van het formulier
------------------------------------------------------------------------------  -->

<?php
include ("footer.php");
?>
