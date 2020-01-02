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
    
    $rowcolor = 'row-a';
    for($ix1=0; $ix1<10; $ix1++) 
    {
        $sql_code = "SELECT * FROM uren 
                    WHERE user='$username' 
                    AND jaar='$mainJaar[$ix1]' 
                    AND week='$mainWeek[$ix1]' 
                    GROUP BY user, jaar, week 
                    ORDER BY jaar desc , week desc , datum DESC LIMIT 10";
        
        if($sql_out = mysqli_query($dbconn, $sql_code)) 
        {
            
            if(mysqli_num_rows($sql_out) > 0) 
            {
                $sql_row = mysqli_fetch_array($sql_out);
                $qry_jaar                  = $sql_row['jaar'];
                $qry_week                  = $sql_row['week'];
                $qry_terapprovalaangeboden = $sql_row['terapprovalaangeboden'];
                $qry_approved              = $sql_row['approved'];
                $qry_approveddatum         = $sql_row['approveddatum'];
                $qry_approvedbyuser        = $sql_row['approvedbyuser'];
            } 
            else 
            {
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
            
            if ($qry_terapprovalaangeboden == 1) 
            {
                echo '<td style="text-align:center;"><img class="button" src="./img/buttons/icons8-ok-48.png" alt="1" title="is ter approval aangeboden" /></td>';
            }
            else 
            {
                echo '<td style="text-align:center;"><img class="button" src="./img/buttons/icons8-cancel-48.png" alt="0" title="is nog niet ter approval aangeboden" /></td>';
            }
            
            if ($qry_approved == 1) 
            {
                echo '<td style="text-align:center;"><img class="button" src="./img/buttons/icons8-ok-48.png" alt="1" title="is approved" /></td>';
            }
            else 
            {
                echo '<td style="text-align:center;"><img class="button" src="./img/buttons/icons8-cancel-48.png" alt="0" title="is nog niet approved" /></td>';
            }
            
            echo "<td>$qry_approveddatum</td>";
            echo "<td>$qry_approvedbyuser</td>";
            echo '</tr>';
            
            check_row_color($rowcolor);
        } 
        else 
        {
            echo "ERROR: Could not be able to execute $sql_code. ". mysqli_error($dbconn);
        }
    }
    
    echo "</table>";
include ("footer.php");
?>	