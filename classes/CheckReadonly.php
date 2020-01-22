<?php 

class CheckReadonly
{
    var $totaal_uren_per_soort;
    var $frm_value;
    var $js_readonly;
    var $js_aantal_dagen_readonly;
    var $aantal_dagen_readonly;
    var $option;
    
    function __construct()
    {
        echo '<div id="dropdownSoortUren" data-options="'.$this->option.'"></div>';
        $this->totaal_uren_per_soort = 0;
    }
}
?>