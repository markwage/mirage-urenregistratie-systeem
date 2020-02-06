<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();
include ("header.php");

if (isset($_GET['username']))
{
    //decrypt username
    $username_decrypted = convert_string('decrypt', $_GET['username']);
    if($username_decrypted == '')
    {
        writelog("rpt_uren_urensoort","ERROR"," Men heeft geprobeerd om username handmatig aan te passen in de url: ".$_GET['username']);
        exit("Je hebt geprobeerd om username handmatig aan te passen in de url");
    }
}
else
{
    $username_decrypted = "";
}

if(isset($_GET['fullname']) && ($_GET['fullname'] <> ""))
{
    $fullname_decrypted = "Medewerker: ".convert_string('decrypt', $_GET['fullname']);
}
else
{
    $fullname_decrypted = "";
}

?>
<div id="main">
	<h1>Overzicht uren per urensoort</h1>

<?php
displayUserGegevens();

// Bepalen jaartal (= huidig jaar) 
$inputjaar = date('Y');

//------------------------------------------------------------------------------------------------------
// Refresh butto changejaar is op geklikt om een andere week te muteren.
// Door getWeekdays worden de dagen en data van die nieuwe week berekend
//------------------------------------------------------------------------------------------------------
if (isset($_POST['change_jaar']))
{
    $inputjaar = $_POST["jaartal"];
}

//---------------------------------------------------------------------------------
//  Begin van het formulier
//---------------------------------------------------------------------------------

?>
<div id="form_div">
<form name="uren_per_urensoort" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<?php
// Display invoerveld om jaartal in te voeren
echo "<table>";
echo "<tr>";
echo "<td><strong>Jaar</strong></td>";
echo "<td><input type='number' style='width:3.2vw' name='jaartal' min='2019' max='2300' value='".$inputjaar."'>";
echo "<td><input class='button' type='submit' name='change_jaar' value='refresh'></td>";
echo "<td style='width:17vw; text-align:right'><strong>".$fullname_decrypted."</strong></td>";

//echo "<td><strong>Medewerker: ".$_SESSION['voornaam']." ".$_SESSION['tussenvoegsel']." ".$_SESSION['achternaam']."</strong></td>";
echo "</tr>";
echo "</table>";

echo "<center><table id='uren_soortuur'>";
echo "<tr>";
echo "<th colspan='2'>Soortuur</th>
      <th style='width:3.33vw; text-align:right'>Jan</th>
      <th style='width:3.33vw; text-align:right'>Feb</th>
      <th style='width:3.33vw; text-align:right'>Maa</th>
      <th style='width:3.33vw; text-align:right'>Apr</th>
      <th style='width:3.33vw; text-align:right'>Mei</th>
      <th style='width:3.33vw; text-align:right'>Jun</th>
      <th style='width:3.33vw; text-align:right'>Jul</th>
      <th style='width:3.33vw; text-align:right'>Aug</th>
      <th style='width:3.33vw; text-align:right'>Sep</th>
      <th style='width:3.33vw; text-align:right'>Okt</th>
      <th style='width:3.33vw; text-align:right'>Nov</th>
      <th style='width:3.33vw; text-align:right'>Dec</th>";
echo "</tr>";
$rowcolor = 'row-a';

// Hier komt het ophalen en optellen van de uren

if($username_decrypted == "")
{
    $sql_code = "SELECT soortuur, omschrijving, approval_maand, SUM(uren) AS totaal_uren
                 FROM view_uren_soortuur
                 WHERE approval_jaar = ".$inputjaar."
                 GROUP BY approval_maand, soortuur
                 ORDER BY soortuur, approval_maand";
}
else
{
    $sql_code = "SELECT soortuur, omschrijving, approval_maand, SUM(uren) AS totaal_uren
                 FROM view_uren_soortuur
                 WHERE approval_jaar = ".$inputjaar."
                 AND user = '".$username_decrypted."'
                 GROUP BY approval_maand, soortuur
                 ORDER BY soortuur, approval_maand";
}

$sql_out = mysqli_query($dbconn, $sql_code);
if(!$sql_out)
{
    writelog("rpt_uren_urensoort","ERROR","Ophalen van de uren per urensoort gaat fout: ".$sql_code." - ".mysqli_error($dbconn));
}
else
{
    $frm_soortuur = "dummy";
    $frm_omschrijving = 'dummy';
    
    for($ix_init=0; $ix_init<12; $ix_init++)
    {
        $frm_maand[$ix_init]  = ' ';
    }
    
    
    while($sql_rows = mysqli_fetch_array($sql_out))
    {
        $row_soortuur     = $sql_rows['soortuur'];
        $row_omschrijving = $sql_rows['omschrijving'];
        // maandnr - 1 omdat array bij 0 begint
        $row_maandnr      = $sql_rows['approval_maand'] - 1;
        $totaal_uren      = $sql_rows['totaal_uren'];
        
        if($row_soortuur <> $frm_soortuur)
        {
            if($frm_soortuur <> 'dummy')
            { 
                echo '<tr class="'.$rowcolor.'">';
                echo "<td>".$frm_soortuur."</td><td>".$frm_omschrijving."</td>";
                for($ix=0; $ix<12; $ix++)
                {
                    echo "<td style='width:3.33vw; text-align:right'>".$frm_maand[$ix]."</td>";
                    $frm_maand[$ix]  = ' ';
                }
                echo "</tr>";
                check_row_color($rowcolor);
            }
        }
        
        $frm_soortuur = $row_soortuur;
        $frm_omschrijving = $row_omschrijving;
        $frm_maand[$row_maandnr] = $totaal_uren;
    }
    // laatste rij uit de query dus wegschrijven naar formulier
    // Indien $row_soortuur niet is gevuld dan zijn er geen gegevens van het jaar
    if(!isset($row_soortuur))
    {
        echo '</table></center><blockquote class="error">ERROR: Er zijn geen gegevens aanwezig over dit jaar</blockquote>';
    }
    else
    {
        echo '<tr class="'.$rowcolor.'">';
        echo "<td>".$row_soortuur."</td><td>".$row_omschrijving."</td>";
        for($ix=0; $ix<12; $ix++)
        {
            echo "<td style='width:3.33vw; text-align:right'>".$frm_maand[$ix]."</td>";
        }
    }
    echo "</tr>";
    writelog("rpt_uren_urensoort","INFO","Overzicht totaal aantal uren per urensoort in een jaar is uitgevoerd");
}
echo "</table></center>";
// This button is needed for when user pushes the ENTER button when changing the yearnumber. Button is not displayed
echo "<input type='submit' name='dummy' value='None' style='display: none'>";
?>

</form>
</div>
<!-- ------------------------------------------------------------------------------
  Einde van het formulier
------------------------------------------------------------------------------  -->

<?php
include ("footer.php");
?>
