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
	<h1>Openstaande approvals</h1>
			
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
    $week           = $_POST['week'];
    $jaar           = $_POST['jaar'];
    $sql_code = "UPDATE uren SET 
			approveddatum = '".date('Y-m-d')."', approvedbyuser = '".$approvedbyuser."', approved = '1'  
            WHERE user = '".$user."' AND week = '".$week."' AND jaar = '".$jaar."'";
    $sql_out = mysqli_query($dbconn, $sql_code);
    writelogrecord("approve","INFO Van user {$user} is week {$jaar}{$week} approved");

    header("location: approve.php?aktie=disp");
}

//------------------------------------------------------------------------------------------------------
//
//       *******************   START   *******************
//
// Dit wordt uitgevoerd wanneer de gebruiker in linkermenu op "Openstaande approvals" klikt
// Er wordt een lijst met de te approven weken getoond
//------------------------------------------------------------------------------------------------------
if ($aktie == 'disp') 
{
    
    $sql_code = "SELECT * FROM view_uren_get_full_username
                WHERE terapprovalaangeboden = 1
                AND approved = 0
                GROUP BY user, jaar, week
                ORDER BY jaar, week, user;";
	$sql_out = mysqli_query($dbconn, $sql_code);
	
	echo "<center><table>";
	echo "<tr><th>Medewerker</th><th>Week</th><th></th></tr>";
	$rowcolor = 'row-a';
	
	while($sql_rows = mysqli_fetch_array($sql_out)) 
	{
		$username = $sql_rows['user'];
		$week     = $sql_rows['week'];
		$jaar     = $sql_rows['jaar'];
		$voornaam      = $sql_rows['voornaam'];
		$tussenvoegsel = $sql_rows['tussenvoegsel'];
		$achternaam    = $sql_rows['achternaam'];
		
		echo '<tr class="'.$rowcolor.'">
			<td><b>'.$voornaam.' '.$tussenvoegsel.' '.$achternaam.'</b></td><td style=\'text-align:center\'>'.$jaar.' '.$week.'</td>
			<td><a href="approve.php?aktie=dspuren&user='.$username.'&jaar='.$jaar.'&week='.$week.'"><img class="button" src="./img/buttons/icons8-glasses-48.png" alt="Toon week" title="Toon de uren van deze week" /></a></td>
			</tr>';
		
		check_row_color($rowcolor);
	}
	echo "</table></center>";
}

//------------------------------------------------------------------------------------------------------
// Wordt uitgevoerd wanneer men op de button klikt om uren van die user / week te displayen
//------------------------------------------------------------------------------------------------------
if ($aktie == 'dspuren') 
{
    $username = $_GET['user'];
    $week     = $_GET['week'];
    $jaar     = $_GET['jaar'];
    
    $sql_code = "SELECT * FROM users
                WHERE username = '$username'";
    $sql_out = mysqli_query($dbconn, $sql_code);
    $sql_rows = mysqli_fetch_array($sql_out);
    $voornaam      = $sql_rows['voornaam'];
    $tussenvoegsel = $sql_rows['tussenvoegsel'];
    $achternaam    = $sql_rows['achternaam'];
    $emailadres    = $sql_rows['emailadres'];
    
    echo "<center><b>Weeknummer: </b>".$jaar." ".$week."<br /><b>Medewerker: </b>".$voornaam." ".$tussenvoegsel." ".$achternaam." ";
    echo "<h3>Overzicht per dag</h3>";
    echo "<center><table>";
    echo "<tr><th>Datum</th><th>Soortuur</th><th>Uren</th></tr>";
    $rowcolor = 'row-a';
    
    $sql_code = "SELECT * FROM view_uren_soortuur
                 WHERE user = '$username'
                 AND week = '$week'
                 AND jaar = '$jaar'
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
    
    $sql_code = "SELECT SUM(uren) as toturen, soortuur, omschrijving FROM view_uren_soortuur
                WHERE user = '$username'
                AND week = '$week'
                AND jaar = '$jaar'
                GROUP BY soortuur
                ORDER BY soortuur";
    $sql_out = mysqli_query($dbconn, $sql_code);
    while ($sql_rows = mysqli_fetch_array($sql_out)) 
    {
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
    <input type="hidden" name="week" value="<?php if (isset($week)) { echo $week; } ?>">
    <input type="hidden" name="jaar" value="<?php if (isset($jaar)) { echo $jaar; } ?>">
    <input type="hidden" name="username" value="<?php if (isset($username)) { echo $username; } ?>">
    <input type="hidden" name="jaar" value="<?php if (isset($datum)) { echo substr($datum,0,4); } ?>">
    <!--  Tot hier -->
    <input class="button" type="submit" name="cancel" value="cancel" formnovalidate>
    <?php 
    if (!isset($_SESSION['approvenallowed']) || (!$_SESSION['approvenallowed'])) echo '<blockquote>Je hebt geen rechten om te approven</blockquote>';
    else echo '<input class="button" type="submit" name="approve" value="approve">';
    ?>
    </form>
    <?php 
}
	
include ("footer.php");
?>		


