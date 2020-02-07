<?php
session_start();

include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");

check_admin();
check_cookies();

$content = file("logs/system.log");
$data = implode("<br>", $content);
echo $data;
echo "<br />";

// foreach($_SERVER as $key_name => $key_value) {
// print $key_name." = ".$key_value."<br>";
// }

// ------------------------------------------------------------------------------------------------------
// BUTTON Cancel
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['close'])) {
    echo "<script>window.close();</script>";
}

// ------------------------------------------------------------------------------------------------------
// BUTTON clean
// ------------------------------------------------------------------------------------------------------
if (isset($_POST['clean'])) {
    $logfile_name = "logs/system.log";
    date_default_timezone_set('Europe/Amsterdam');
    $datumlog = date('Ymd H:i:s');

    file_put_contents($logfile_name, PHP_EOL . $datumlog . "********* De systemlog file is geschoond **********");
    writelog("display_systemlog", "INFO", "De systemlog file is geschoond. Inhoud is verwijderd.");

    echo '<script>alert("De systemlog file is geschoond. Inhoud is verwijderd.")</script>';
    echo "<script>window.close();</script>";
}
?>

<form name="systemlog" action="<?php echo $_SERVER['PHP_SELF']; ?>"
	method="post">
	<p>
		<input class="button" type="submit" name="clean" value="clean log"> <input
			class="button" type="submit" name="close" value="close window">
	</p>
</form>