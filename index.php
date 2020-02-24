<?php
session_start();

include ("./config.php");
include ("./db.php");
include ("./function.php");
include ("autoload.php");

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();
if(!isset($_SESSION['username'])) {
    header("location: login.php");
}

include ("header.php");

?>
<div id="main">
<h1>Mirage Urenadministratie</h1>
    
<?php
//displayUserGegevens();
?>
    
<center>
<table>
<tr>
	<th colspan='7' style='text-align: center;'>Overzicht laatste 10 weken</th>
</tr>
<tr>
    <th>Jaar</th>
	<th>Week</th>
	<!-- <th>Uren<br />ingevuld</th>           -->
	<!-- <th>Ter approval<br />aangeboden</th> -->
	<th>Approved</th>
	<th>Datum<br />approved</th>
	<th>Approved door</th>
	<th>Akties</th>
</tr>
	
<?php
jaarWeek();

//$rowcolor = 'row-a';
for ($ix1 = 0; $ix1 < 10; $ix1 ++) {
    $sql_code = "SELECT *, SUM(uren) as toturen FROM uren 
                 WHERE user='" . $_SESSION['username'] . "' 
                 AND jaar='$jaar_nr[$ix1]' 
                 AND week='$week_nr[$ix1]' 
                 GROUP BY user, jaar, week 
                 ORDER BY jaar desc , week desc , datum DESC LIMIT 10";

    if ($sql_out = mysqli_query($dbconn, $sql_code)) {

        if (mysqli_num_rows($sql_out) > 0) {
            $sql_row = mysqli_fetch_array($sql_out);

            $frm_jaar = $jaar_nr[$ix1];
            $frm_week = $week_nr[$ix1];
            $frm_approved = $sql_row['approved'];
            $frm_approveddatum = $sql_row['approveddatum'];
            $frm_approvedbyuser = $sql_row['approvedbyuser'];
            $frm_toturen = $sql_row['toturen'];

            $sql_code_name = "SELECT voornaam, tussenvoegsel, achternaam FROM users
                             WHERE username='$frm_approvedbyuser'";
            if ($sql_out_name = mysqli_query($dbconn, $sql_code_name)) {
                if (mysqli_num_rows($sql_out_name) > 0) {
                    $sql_row_name = mysqli_fetch_array($sql_out_name);
                    $frm_naam_approver = $sql_row_name['voornaam'] . " " . $sql_row_name['tussenvoegsel'] . " " . $sql_row_name['achternaam'];
                } else {
                    $frm_naam_approver = "";
                }
            }
        } else {
            $frm_jaar = $jaar_nr[$ix1];
            $frm_week = $week_nr[$ix1];
            $frm_approved = ' ';
            $frm_approveddatum = ' ';
            $frm_approvedbyuser = ' ';
            $frm_naam_approver = ' ';
            $frm_toturen = 0;
        }

        $week_array = getStartAndEndDate($week_nr[$ix1], $jaar_nr[$ix1]);
        echo '<tr class="colored">';
        echo '<td style="text-align:center;"><b>' . $frm_jaar . '</b></td>';
        echo "<td style='text-align:center;'><b>{$frm_week}</b> ({$week_array['week_start']} t/m {$week_array['week_end']})</td>";

        if ($frm_approved == 1) {
            echo '<td style="text-align:center;"><img class="button" src="./img/icons/checkmark-32.png" alt="1" title="is approved" /></td>';
        } else {
            echo '<td style="text-align:center;"></td>';
        }

        echo "<td>$frm_approveddatum</td>";
        echo "<td>$frm_naam_approver</td>";
        echo '<td><center><a href="uren.php?edtweek=' . $frm_jaar . "-W" . $frm_week . '"><img class="button" src="./img/icons/view-48.png" alt="Toon uren van deze week" title="Toon uren van deze week" /></a></center></td>';
        echo '</tr>';
    } else {
        writelog("index", "ERROR", "De select query is fout gegaan - " . mysqli_error($dbconn));
        exit($MSGDB001E);
    }
}

echo "</table>";
include ("footer.php");
?>	