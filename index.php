<?php 
session_start();

include ("./config.php");
include ("./db.php");
include ("./function.php");

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="Description" content="Information architecture, Web Design, Web Standards." />
<meta name="Keywords" content="your, keywords" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="Distribution" content="Global" />
<meta name="Author" content="Mark Wage" />
<meta name="Robots" content="index,follow" />

<link rel="stylesheet" href="./css/style.css" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<title>Mirage urenregistratie</title>
</head>
<body>
<!-- wrap starts here -->
<div id="wrap">
	<div id="header"><div id="header-content">	
		<h1 id="logo"><a href="index.html" title=""><span class="gray">M</span>irage<span class="gray">us</span></a></h1>	
		<h2 id="slogan">Mirage Urenregistratie Systeem...</h2>		
		
		<!-- TopMenu Tabs -->
		<?php include ("./menu_top.php") ?>

	</div></div>
	
	<!-- content-wrap starts here -->
	<div id="content-wrap"><div id="content">		
		<div id="sidebar" ><?php include ("./menu_links.php") ?></div>	
		<div id="main">		
			<h1>Mirage Urenadministratie</h1>
			<?php 
			displayUserGegevens();
			$sql_select = "SELECT * FROM uren where user='".$username."' GROUP BY user, jaar, week ORDER BY jaar desc , week desc , datum desc LIMIT 10";
			writelogrecord("index","Query: ".$sql_select);
			if($sql_result = mysqli_query($dbconn, $sql_select)) {
			    writelogrecord("index","Totaal aantal rijen uit de select-query: ".mysqli_num_rows($sql_result));
			    if(mysqli_num_rows($sql_result) > 0) {
			        echo "<center><table>";
			        echo "<tr>";
			            echo "<th colspan='6' style='text-align:center;'>Overzicht laatste 10 ingevulde weken</th>";
			        echo "</tr>";
			        echo "<tr>";
                        echo "<th>jaar</th>";
                        echo "<th>week</th>";
                        echo "<th>Ter approval<br />aangeboden</th>";
                        echo "<th>Approved</th>";
                        echo "<th>datum</th>";
                        echo "<th>approved door</th>";
                    echo "</tr>";
			        $rowcolor = 'row-a';
			        while($row_selecturen = mysqli_fetch_array($sql_result)) {
			            $qry_jaar                  = $row_selecturen['jaar'];
			            $qry_week                  = $row_selecturen['week'];
			            $qry_terapprovalaangeboden = $row_selecturen['terapprovalaangeboden'];
			            $qry_approved              = $row_selecturen['approved'];
			            $qry_approveddatum         = $row_selecturen['approveddatum'];
			            $qry_approvedbyuser        = $row_selecturen['approvedbyuser'];
			            echo '<tr class="'.$rowcolor.'">';
			                echo '<td style="text-align:center;">'.$qry_jaar.'</td>';
			                echo '<td style="text-align:center;">'.$qry_week.'</td>';
			                echo '<td style="text-align:center;">'.$qry_terapprovalaangeboden.'</td>';
			                echo '<td style="text-align:center;">'.$qry_approved.'</td>';
			                echo '<td>'.$qry_approveddatum.'</td>';
                            echo '<td>'.$qry_approvedbyuser.'</td>';
			            echo '</tr>';
			            if ($rowcolor == 'row-a') $rowcolor = 'row-b';
			            else $rowcolor = 'row-a';
			        }
			        echo "</table>";
			    } else {
			        echo "Er zijn geen records gevonden";
			    }
			} else {
			    echo "ERROR: Could not be able to execute $sql_select. ". mysqli_error($dbconn);
			}
			
include ("footer.php");
?>	