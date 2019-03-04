<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<link rel="stylesheet" href="./css/style.css" type="text/css" />
<link rel="stylesheet" href="./css/calendar.css" type="text/css" />
<title>Mirage urenregistratie</title>

<script language="javascript" src="./js/functions.js"></script>
<script type="text/javascript" src="./js/calendar.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
var autoExpand = function (field) {

	// Reset field height
	field.style.height = 'inherit';

	// Get the computed styles for the element
	var computed = window.getComputedStyle(field);

	// Calculate the height
	var height = parseInt(computed.getPropertyValue('border-top-width'), 10)
	             + parseInt(computed.getPropertyValue('padding-top'), 10)
	             + field.scrollHeight
	             + parseInt(computed.getPropertyValue('padding-bottom'), 10)
	             + parseInt(computed.getPropertyValue('border-bottom-width'), 10);

	field.style.height = height + 'px';

};

document.addEventListener('input', function (event) {
	if (event.target.tagName.toLowerCase() !== 'textarea') return;
	autoExpand(event.target);
}, false);
</script>

</head>
<body>
<!-- wrap starts here -->
<div id="wrap">
	<div id="header"><div id="header-content">	
		<h1 id="logo"><a href="index.php" title=""><span class="gray">M</span>irage<span class="gray">us</span></a></h1>	
		<h2 id="slogan">Mirage Urenregistratie Systeem...</h2>		
		
		<!-- TopMenu Tabs -->
		<?php include ("./menu_top.php") ?>

	</div></div>
	
	<!-- content-wrap starts here -->
	<div id="content-wrap"><div id="content">		
		<div id="sidebar" >
			<?php include ("./menu_links.php") ?>
		</div>	
