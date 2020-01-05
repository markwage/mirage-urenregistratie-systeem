<?php 

/**
 * Elke nieuwe class moet hier opgenomen worden. Variabele $obj.. wordt 1 opgehoogd en krijgt 
 * als naam de waarde van de class
 */

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

$obj01 = new Writelog();

?>