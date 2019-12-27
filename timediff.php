<?php 
$start_time = new DateTime('07:30');
$stop_time = new DateTime('16:00');
$time_diff = $start_time -> diff($stop_time);

echo $time_diff
?>