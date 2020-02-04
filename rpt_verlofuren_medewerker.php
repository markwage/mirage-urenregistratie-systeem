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
	<h1>Opgenomen verlofuren per medewerker</h1>

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
<form name="verlofuren_per_medewerker" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<?php

echo "<table>";
echo "<tr>";
echo "<td><strong>Jaar</strong></td>";
echo "<td><input type='number' style='width:3.2vw' name='jaartal' min='2019' max='2300' value='".$inputjaar."'>";
echo "<td><input class='button' type='submit' name='change_jaar' value='refresh'></td>";
echo "</tr>";
echo "</table>";

echo "<center><table id='verlofuren_mdw'>";
echo "<tr>";
echo "<th>Medewerker</th>
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
      <th style='width:3.33vw; text-align:right'>Dec</th>
      <th style='width:4.2vw; text-align:right'>Totaal</th>";
echo "</tr>";
$rowcolor = 'row-a';

// Hier komt het ophalen en optellen van de uren

$sql_code = "SELECT user, voornaam, tussenvoegsel, achternaam, approval_maand, SUM(uren) AS totaal_uren
             FROM view_uren_get_full_username
             WHERE approval_jaar = ".$inputjaar."
             AND soortuur = 'MIR001VL'
             GROUP BY approval_maand, user
             ORDER BY achternaam, approval_maand";

$sql_out = mysqli_query($dbconn, $sql_code);
if(!$sql_out)
{
    writelog("rpt_verlofuren_medewerker","ERROR","Ophalen van de verlofuren per medewerker gaat fout: ".$sql_code." - ".mysqli_error($dbconn));
}
else
{
    $frm_username = "dummy";
    $frm_voornaam = 'dummy';
    $frm_tussenvoegsel = 'dummy';
    $frm_achternaam = 'dummy';
    $frm_jaartotaal_uren = 0;
    
    for($ix_init=0; $ix_init<12; $ix_init++)
    {
        $frm_maand[$ix_init]  = ' ';
    }
    
    while($sql_rows = mysqli_fetch_array($sql_out))
    {
        $row_username      = $sql_rows['user'];
        $row_voornaam      = $sql_rows['voornaam'];
        $row_tussenvoegsel = $sql_rows['tussenvoegsel'];
        $row_achternaam    = $sql_rows['achternaam'];
        // maandnr - 1 omdat array bij 0 begint
        $row_maandnr       = $sql_rows['approval_maand'] - 1;
        $totaal_uren       = $sql_rows['totaal_uren'];
        
        if($row_username <> $frm_username)
        {
            if($frm_username <> 'dummy')
            { 
                echo '<tr class="'.$rowcolor.'">';
                echo "<td>".$frm_achternaam.", ".$frm_voornaam." ".$frm_tussenvoegsel."</td>";
                for($ix=0; $ix<12; $ix++)
                {
                    echo "<td style='width:3.33vw; text-align:right'>".$frm_maand[$ix]."</td>";
                    $frm_maand[$ix]  = ' ';
                }
                echo "<td style='width:4.2vw; text-align:right'><strong>".number_format($frm_jaartotaal_uren, 2)."</strong></td>";
                echo "</tr>";
                $frm_jaartotaal_uren = 0;
                check_row_color($rowcolor);
            }
        }
        
        $frm_username = $row_username;
        $frm_voornaam = $row_voornaam;
        $frm_tussenvoegsel = $row_tussenvoegsel;
        $frm_achternaam = $row_achternaam;
        $frm_maand[$row_maandnr] = $totaal_uren;
        $frm_jaartotaal_uren = $frm_jaartotaal_uren + $totaal_uren;
    }
    // laatste rij uit de query dus wegschrijven naar formulier
    // Indien $frm_achternaam leeg is dan zijn er geen gegevens van het betreffende jaar
    if(!isset($row_username))
    {
        echo '</table></center><blockquote class="error">ERROR: Er zijn geen gegevens van dit jaar</blockquote>';
    }
    else
    {
        echo '<tr class="'.$rowcolor.'">';
        echo "<td>".$frm_achternaam.", ".$frm_voornaam." ".$frm_tussenvoegsel."</td>";
        for($ix=0; $ix<12; $ix++)
        {
            echo "<td style='width:3.33vw; text-align:right'>".$frm_maand[$ix]."</td>";
        }
        echo "<td style='width:4.2vw; text-align:right'><strong>".number_format($frm_jaartotaal_uren, 2)."</strong></td>";
    }
    echo "</tr>";
    writelog("rpt_verlofuren_medewerker","INFO","Overzicht opgenomen verlofuren per medewerker in een jaar is uitgevoerd");
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
