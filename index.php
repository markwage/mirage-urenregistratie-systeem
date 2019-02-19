<?php 

session_start();

include ("./config.php");
include ("./db.php");
include ("./function.php");
//db_connect();


// Connectie met de database maken en database selecteren.
//mysql_connect($dbhost, $dbuser, $dbpassw) or die ("Kan de connectie met de database niet maken");
//mysql_select_db($dbname) or die ("Kan de database niet openen");

// Connectie met de database maken en database selecteren.

$dbconn = mysqli_connect($dbhost, $dbuser, $dbpassw);
if (!$dbconn) {
    die("Kan de connectie met de database niet maken");
}

$dbselect = mysqli_select_db($dbconn, $dbname);
if (!$dbselect) {
    die("Kan de database niet openen : " . mysqli_error());
}

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta name="Description" content="Information architecture, Web Design, Web Standards." />
<meta name="Keywords" content="your, keywords" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="Distribution" content="Global" />
<meta name="Author" content="Mark Wage" />
<meta name="Robots" content="index,follow" />

<link rel="stylesheet" href="./css/style.css" type="text/css" />
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
			echo "<p><table>";
			echo '<tr><th colspan="3">Status laatste 10 weken</th></tr>';
			echo "<tr><th><strong>Weeknr</th><th>Start<br>datum</th><th>Approved</th>";
			echo "</table></p>";
			
include ("footer.php");
?>	