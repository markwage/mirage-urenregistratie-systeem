<?php 
session_start();

include ("./config.php");
include ("./db.php");
include ("./function.php");

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

?>   
<div id="main">		
    <h1>Mirage Urenadministratie</h1>
    <?php 
    displayUserGegevens();
    ?>
	<center><table>
	<tr><th colspan='6' style='text-align:center;'>Overzicht laatste 10 weken</th></tr>
	<tr><th>jaar</th><th>week</th><th>Ter approval<br />aangeboden</th><th>Approved</th><th>datum</th><th>approved door</th></tr>
    <?php 
    jaarWeek();
    for($ix1=0; $ix1<10; $ix1++) {
        $sqlCode = "SELECT * FROM uren where user='$username' AND jaar='$mainJaar[$ix1]' AND week='$mainWeek[$ix1]' GROUP BY user, jaar, week ORDER BY jaar desc , week desc , datum desc LIMIT 10";
        writelogrecord("index","Query: ".$sqlCode);
        if($sqlOut = mysqli_query($dbconn, $sqlCode)) {
            writelogrecord("index","Totaal aantal rijen uit de select-query: ".mysqli_num_rows($sqlOut));
            $rowcolor = 'row-a';
            if(mysqli_num_rows($sqlOut) > 0) {
                $sqlRow = mysqli_fetch_array($sqlOut);
                $qry_jaar                  = $sqlRow['jaar'];
                $qry_week                  = $sqlRow['week'];
                $qry_terapprovalaangeboden = $sqlRow['terapprovalaangeboden'];
                $qry_approved              = $sqlRow['approved'];
                $qry_approveddatum         = $sqlRow['approveddatum'];
                $qry_approvedbyuser        = $sqlRow['approvedbyuser'];
            } else {
    	        $qry_jaar                  = $mainJaar[$ix1];
    	        $qry_week                  = $mainWeek[$ix1];
    	        $qry_terapprovalaangeboden = ' ';
    	        $qry_approved              = ' ';
    	        $qry_approveddatum         = ' ';
    	        $qry_approvedbyuser        = ' ';
    	        
            }
            echo '<tr class="'.$rowcolor.'">';
            echo '<td style="text-align:center;">'.$qry_jaar.'</td>';
            echo '<td style="text-align:center;">'.$qry_week.'</td>';
            if ($qry_terapprovalaangeboden == 1) echo '<td style="text-align:center;"><img src="./img/buttons/icons8-ok-48.png" alt="1" title="is ter approval aangeboden" /></td>';
            else echo '<td style="text-align:center;"><img src="./img/buttons/icons8-cancel-48.png" alt="0" title="is nog niet ter approval aangeboden" /></td>';
            if ($qry_approved == 1) echo '<td style="text-align:center;"><img src="./img/buttons/icons8-ok-48.png" alt="1" title="is approved" /></td>';
            else echo '<td style="text-align:center;"><img src="./img/buttons/icons8-cancel-48.png" alt="0" title="is nog niet approved" /></td>';
            echo "<td>$qry_approveddatum</td>";
            echo "<td>$qry_approvedbyuser</td>";
            echo '</tr>';
            if ($rowcolor == 'row-a') $rowcolor = 'row-b';
            else $rowcolor = 'row-a';
        } else {
            echo "ERROR: Could not be able to execute $sqlCode. ". mysqli_error($dbconn);
        }
    }
    echo "</table>";
include ("footer.php");
?>	