<?php
class UNL_Peoplefinder_Department_Search implements Countable, Iterator
{
    public $options = array('q' => '');
    
    public $q;
    
    /**
     * SimpleXMLElement for the HR XML Tree
     * 
     * @var SimpleXMLElement
     */
    protected $xml;
    
    protected $results;
    
    protected $current = 0;
    
    function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $q = strtolower(str_replace('"', '', $this->options['q']));
        $this->xml = new SimpleXMLElement(file_get_contents(UNL_Peoplefinder::getDataDir().'/hr_tree.xml'));
        $this->results = $this->xml->xpath('//attribute[@name="org_unit"][@value="50000002"]/..//attribute[@name="name"][contains(translate(@value,"ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"),"'.$q.'")]');
    }
    
    function current()
    {
        return new UNL_Peoplefinder_Department(array('d'=>$this->results[$this->current]['value']));
    }
    
    function next()
    {
        $this->current++;
    }
    
    function valid()
    {
        if ($this->current < count($this->results)) {
            return true;
        }
        return false;
    }
    
    function count()
    {
        return count($this->results);
    }
    
    function rewind()
    {
        $this->current = 0;
    }
    
    function key()
    {
        return $this->current()->name;
    }
}
?>
