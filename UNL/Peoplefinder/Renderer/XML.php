<?php
class UNL_Peoplefinder_Renderer_XML
{

    protected $trustedIP = false;
    protected $displayStudentTelephone = false;
    
    protected $sent_headers = false;
    
    function sendHeaders()
    {
        if ($this->sent_headers) {
            return;
        }
        header('Content-type: text/xml');
        echo '<?xml version="1.0" encoding="utf-8"?>
<unl xmlns="http://wdn.unl.edu/xml">'.PHP_EOL;
        $this->sent_headers = true;
    }
    
    public function renderRecord(UNL_Peoplefinder_Record $r)
    {
        $this->sendHeaders();
        echo '<person>';
        foreach (get_object_vars($r) as $key=>$val) {
            echo "<$key>{$val}</$key>\n";
        }
        echo '</person>'.PHP_EOL;
    }
    
    function __destruct()
    {
        if ($this->sent_headers) {
            $this->sendFooter();
        }
    }
    
    function sendFooter()
    {
        echo '</unl>';
    }
}
?>