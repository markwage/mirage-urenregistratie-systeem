<?php  
session_start();
 
include ("config.php");
include ("db.php");
include ("function.php");
 
//$connect = mysqli_connect("localhost", "root", "", "test_db");  
if(isset($_POST['submit_row'])) {
    $number = count($_POST["name"]);  
    if($number > 0) {
        for($i=0; $i<$number; $i++) {
            if(trim($_POST["name"][$i] != '')) {
                //$sql = "INSERT INTO tbl_name(name) VALUES('".mysqli_real_escape_string($dbconn, $_POST["name"][$i])."') or die(mysqli_error($db))";  
                //mysqli_query($connect, $sql);  
                echo "Naam: ".$_POST["name"][$i]."--<br />";
            }  
        }  
        echo "Data Inserted";  
    } else {  
        echo "Please Enter Name";  
    }  
}
?> 