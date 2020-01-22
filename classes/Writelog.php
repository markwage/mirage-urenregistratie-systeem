<?php 

class Writelog
{
    var $logfile_name = "C:\\wamp64\\www\\mirage-urenregistratie-systeem\\logs\\systemlogMUS.log";
    var $datumlog;
    var $progname;
    var $user;
    var $loglevel = 'INFO'; // Default loglevel
    var $message_text;
    
    function __construct() 
    {
        
        if (isset($_SESSION['username']))
        {
            $this->user = $_SESSION['username'];
        }
        else
        {
            $this->user = 'user onbekend';
            $this->progname = 'php prog onbekend';
        }
    }
    
    function write_record() {
        date_default_timezone_set('Europe/Amsterdam');
        $this->datumlog = date('Ymd H:i:s');
        file_put_contents($this->logfile_name, PHP_EOL.$this->datumlog.";".$this->progname.";".$this->user.";".$this->loglevel.";".$this->message_text, FILE_APPEND);
    }
}
?>