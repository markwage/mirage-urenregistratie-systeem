<?php

class CheckReadonly
{

    var $totaal_uren_per_soort;

    var $frm_value;

    var $js_readonly;

    var $js_aantal_dagen_readonly;

    var $aantal_dagen_readonly;

    var $option;

    var $loopix;

    function __construct()
    {
        $this->totaal_uren_per_soort = 0;
    }

    function write_option()
    {
        echo '<div id="dropdownSoortUren" data-options="' . $this->option . '"></div>';
    }
}
?>