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
        $this->results = $this->xml->xpath('//attribute[@name="org_unit"][@value="50000001"]/..//attribute[@name="name"][contains(translate(@value,"ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"),"'.$q.'")]');
    }
    
    function current(): mixed
    {
        return new UNL_Peoplefinder_Department(array('d'=>$this->results[$this->current]['value']));
    }
    
    function next(): void
    {
        $this->current++;
    }
    
    function valid(): bool
    {
        if ($this->current < count($this->results)) {
            return true;
        }
        return false;
    }
    
    function count(): int
    {
        return count($this->results);
    }
    
    function rewind(): void
    {
        $this->current = 0;
    }
    
    function key(): mixed
    {
        return $this->current()->name;
    }
}
?>
