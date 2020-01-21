<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

if (isset($_GET['aktie'])) 
{
    $aktie = $_GET['aktie'];
}
else 
{
    $aktie = "";
}

check_admin();     // Controleren of gebruiker admin-rechten heeft
check_cookies();   // Controleren of cookie aanwezig is. Zo niet, login-scherm displayen

include ("header.php");

?>
<div id="main">		
	<h1>Openstaande approvals afgelopen maand</h1>
			
<?php 

//------------------------------------------------------------------------------------------------------
//
//       *******************   SUBMITTED   *******************
//
// From here this code runs if the form has been submitted
//------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------
// BUTTON Cancel
//------------------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])) 
{
	header("location: approve.php?aktie=disp");
}

//------------------------------------------------------------------------------------------------------
// BUTTON Approve
//------------------------------------------------------------------------------------------------------
if (isset($_POST['approve'])) 
{
    $approvedbyuser = $_SESSION['username'];
    $user           = $_POST['username'];
    $maand          = $_POST['maand'];
    $jaar           = $_POST['jaar'];
    $sql_code = "UPDATE uren
                 SET approveddatum = '".date('Y-m-d')."',
                 approvedbyuser = '".$approvedbyuser."',
                 approved = '1'
                 WHERE user = '".$user."'
                 AND month(datum) = '".$maand."'
                 AND year(datum) = '".$jaar."'";
    $sql_out = mysqli_query($dbconn, $sql_code);
    
    $log_record = new Writelog();
    $log_record->progname = $_SERVER['PHP_SELF'];
    $log_record->message_text  = "Voor user {$user} is maand {$jaar} {$maand} approved";
    $log_record->write_record();
    
    $sql_insert = "INSERT INTO approvals (maand, jaar, user)
                   VALUES('".$maand."', 
                          '".$jaar."',
                          '".$user."')";
    $sql_out_insert = mysqli_query($dbconn, $sql_insert);
    $log_record = new Writelog();
    $log_record->progname = $_SERVER['PHP_SELF'];
    $log_record->message_text  = "Record succesvol toegevoegd in tabel approvals voor user {$user} periode {$jaar} {$maand}";
    $log_record->write_record();

    header("location: approve.php?aktie=disp");
}

//------------------------------------------------------------------------------------------------------
//
//       *******************   START   *******************
//
// Dit wordt uitgevoerd wanneer de gebruiker in linkermenu op "Openstaande approvals" klikt
// Er wordt een lijst met de te approven maanden getoond, max half jaar terug
// Alleen die maanden worden getoond die niet approved zijn
//------------------------------------------------------------------------------------------------------
if ($aktie == 'disp') 
{
    $vorig_jaar = date('Y', strtotime('-1 month', time()));
    $vorige_maand = date('m', strtotime('-1 month', time()));
    $vorig_jaar_tot = date('Y', strtotime('-6 month', time()));
    $vorige_maand_tot = date('m', strtotime('-6 month', time()));
    
    $sql_code = "SELECT * FROM view_uren_get_full_username
                WHERE ((approval_jaar BETWEEN ".$vorig_jaar." AND ".$vorig_jaar. "
                AND approval_maand BETWEEN ".$vorige_maand." AND ".$vorige_maand. ")
                OR (approval_jaar IS NULL))
                AND uren_invullen = 1
                GROUP BY user, approval_jaar, approval_maand
                ORDER BY achternaam, user;";
    
	$sql_out = mysqli_query($dbconn, $sql_code);
	
	if(!$sql_out)
	{
	    $log_record = new Writelog();
	    $log_record->progname = $_SERVER['PHP_SELF'];
	    $log_record->loglevel = 'ERROR';
	    $log_record->message_text  = "Select gaat fout: ".$sql_code." - ".mysqli_error($dbconn);
	    $log_record->write_record();
	}
	
	echo "<center><table>";
	echo "<tr><th>Medewerker</th><th>Maand</th><th></th></tr>";
	$rowcolor = 'row-a';
	
	while($sql_rows = mysqli_fetch_array($sql_out)) 
	{
	    $approved = $sql_rows['approved'];
	    $username = $sql_rows['user'];
		$maand    = $sql_rows['approval_maand'];
		$jaar     = $sql_rows['approval_jaar'];
		$voornaam      = $sql_rows['voornaam'];
		$tussenvoegsel = $sql_rows['tussenvoegsel'];
		$achternaam    = $sql_rows['achternaam'];
		
		if(($jaar <> '') && ($approved == 0))
		{
		    echo '<tr class="'.$rowcolor.'">
            <td><b>'.$achternaam.', '.$voornaam.' '.$tussenvoegsel.'</b></td><td style=\'text-align:center\'>'.$jaar.' '.$maand.'</td>
			<td><a href="approve.php?aktie=dspuren&user='.$username.'&jaar='.$jaar.'&maand='.$maand.'"><img class="button" src="./img/buttons/icons8-glasses-48.png" alt="Toon week" title="Toon de uren van deze week" /></a></td>
			</tr>';
		}
		elseif($jaar == '')
		{
		    echo '<tr class="'.$rowcolor.'">
            <td><b>'.$achternaam.', '.$voornaam.' '.$tussenvoegsel.'</b></td><td style=\'text-align:center\'>'.$jaar.' '.$maand.'</td>
			<td>Geen gegevens aanwezig over afgelopen maand</td>
			</tr>';
		}
		elseif($approved == 1)
		{
		    echo '<tr class="'.$rowcolor.'">
            <td><b>'.$achternaam.', '.$voornaam.' '.$tussenvoegsel.'</b></td><td style=\'text-align:center\'>'.$jaar.' '.$maand.'</td>
			<td>Approved</td>
			</tr>';
		}
		check_row_color($rowcolor);
	}
	echo "</table></center>";
}

//------------------------------------------------------------------------------------------------------
// Wordt uitgevoerd wanneer men op de button klikt om uren van die user / maand te displayen
//------------------------------------------------------------------------------------------------------
if ($aktie == 'dspuren') 
{
    $username = $_GET['user'];
    $maand    = $_GET['maand'];
    $jaar     = $_GET['jaar'];
    
    $sql_code = "SELECT * FROM users
                WHERE username = '$username'";
    $sql_out = mysqli_query($dbconn, $sql_code);
    $sql_rows = mysqli_fetch_array($sql_out);
    $voornaam      = $sql_rows['voornaam'];
    $tussenvoegsel = $sql_rows['tussenvoegsel'];
    $achternaam    = $sql_rows['achternaam'];
    $emailadres    = $sql_rows['emailadres'];
    
    echo "<center><b>Maand: </b>".$jaar." ".$maand."<br /><b>Medewerker: </b>".$voornaam." ".$tussenvoegsel." ".$achternaam." ";
    echo "<h3>Overzicht per dag</h3>";
    echo "<center><table>";
    echo "<tr><th>Datum</th><th>Soortuur</th><th>Uren</th></tr>";
    $rowcolor = 'row-a';
    
    $sql_code = "SELECT * FROM view_uren_soortuur
                 WHERE user = '$username'
                 AND approval_maand = '$maand'
                 AND approval_jaar = '$jaar'
                 ORDER BY datum, soortuur";
    $sql_out = mysqli_query($dbconn, $sql_code);
    
    while ($sql_rows = mysqli_fetch_array($sql_out)) 
    {
        $datum                 = $sql_rows['datum'];
        $soortuur              = $sql_rows['soortuur'];
        $uren                  = $sql_rows['uren'];
        $omschrijving_soortuur = $sql_rows['omschrijving'];
        
        echo '<tr class="'.$rowcolor.'">
			<td><b>'.$datum.'</b></td><td>'.$soortuur.' - '.$omschrijving_soortuur.'</td><td style=\'text-align:right\'>'.$uren.'</td>
            </tr>';
        
        check_row_color($rowcolor);
    }
    echo "</table></center>";
    
    // Display totaal per soortuur
    echo "<h3>Totaal per urensoort</h3>";
    echo "<center><table>";
    echo "<tr><th>Soort uur</th><th>Totaal</th></tr>";
    
    $rowcolor = 'row-a';
    
    $sql_code = "SELECT SUM(uren) as toturen, soortuur, omschrijving, approved FROM view_uren_soortuur
                WHERE user = '$username'
                AND approval_maand = '$maand'
                AND approval_jaar = '$jaar'
                GROUP BY soortuur
                ORDER BY soortuur";
    $sql_out = mysqli_query($dbconn, $sql_code);
    while ($sql_rows = mysqli_fetch_array($sql_out)) 
    {
        $approved     = $sql_rows['approved'];
        $toturen      = $sql_rows['toturen'];
        $soortuur     = $sql_rows['soortuur'];
        $omschrijving = $sql_rows['omschrijving'];
        
        echo '<tr class="'.$rowcolor.'">
			<td>'.$soortuur.' - '.$omschrijving.'</td><td style=\'text-align:right\'>'.$toturen.'</td>
            </tr>';
        
        check_row_color($rowcolor);
    }
    
    echo "</table></center>";
    
    // Display buttons. Met behulp van een formulier
    ?>
    <form style='background-color:#FFF;' name="approve" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"> 
    <!-- Volgende velden zijn hidden om toch de waarden door te geven voor de update-query -->
    <input type="hidden" name="maand" value="<?php if (isset($maand)) { echo $maand; } ?>">
    <input type="hidden" name="jaar" value="<?php if (isset($jaar)) { echo $jaar; } ?>">
    <input type="hidden" name="username" value="<?php if (isset($username)) { echo $username; } ?>">
    <input type="hidden" name="jaar" value="<?php if (isset($datum)) { echo substr($datum,0,4); } ?>">
    <!--  Tot hier -->
    <input class="button" type="submit" name="cancel" value="cancel" formnovalidate>
    <?php 
    if (!isset($_SESSION['approvenallowed']) || (!$_SESSION['approvenallowed'])) 
    {
        echo '<blockquote>Je hebt geen rechten om te approven</blockquote>';
    }
    else 
    {
        if($approved == 0)
        {
            echo '<input class="button" type="submit" name="approve" value="approve">';
        }
    }
    ?>
    </form>
    <?php 
}
	
include ("footer.php");
?>		


