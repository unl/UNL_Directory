<?php
require_once 'UNL/LDAP.php';

class UNL_Peoplefinder_Department implements Countable, Iterator
{
    public $name;
    public $org_unit;
    public $building;
    public $room;
    public $city;
    public $state;
    public $postal_code;
    
    protected $_ldap;

    protected $_results;
    
    function __construct($name)
    {
        $this->name = $name;
        $this->xml = new SimpleXMLElement(file_get_contents(dirname(__FILE__).'/../../data/hr_tree.xml'));
        $results = $this->xml->xpath('//attribute[@name="org_unit"][@value="50000003"]/..//attribute[@name="name"][@value="'.$this->name.'"]/..');
        if (isset($results[0])) {
            foreach ($results[0] as $attribute) {
                if (isset($attribute['name'])) {
                    $this->{$attribute['name']} = (string)$attribute['value'];
                }
            }
        }
    }
    
    function getLDAPResults()
    {
        if (!isset($this->_results)) {
            $options = array(
                'bind_dn'       => UNL_Peoplefinder::$bindDN,
                'bind_password' => UNL_Peoplefinder::$bindPW,
                );
            
            $this->_ldap = UNL_LDAP::getConnection($options);
            $name = str_replace(array('(',')','*','\'','"'), '', $this->name);
            $this->_results =  $this->_ldap->search('dc=unl,dc=edu',
                                                    '(unlHRPrimaryDepartment='.$name.')');
            $this->_results->sort('cn');
            $this->_results->sort('sn');
        }
        return $this->_results;
    }
    
    function count()
    {
        return count($this->getLDAPResults());
    }
    
    function rewind()
    {
        $this->getLDAPResults()->rewind();
    }
    
    function current()
    {
        return UNL_Peoplefinder_Record::fromUNLLDAPEntry($this->getLDAPResults()->current());
    }
    
    function key()
    {
        return $this->getLDAPResults()->key();
    }
    
    function next()
    {
        $this->getLDAPResults()->next();
    }
    
    function valid()
    {
        return $this->getLDAPResults()->valid();
    }
}
?>
