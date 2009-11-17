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
    
    protected $_xml;
    
    function __construct($name)
    {
        $this->name = $name;
        $this->_xml = new SimpleXMLElement(file_get_contents(dirname(__FILE__).'/../../data/hr_tree.xml'));
        $results = $this->_xml->xpath('//attribute[@name="org_unit"][@value="50000003"]/..//attribute[@name="name"][@value="'.$this->name.'"]/..');
        if (isset($results[0])) {
            foreach ($results[0] as $attribute) {
                if (isset($attribute['name'])) {
                    $this->{$attribute['name']} = (string)$attribute['value'];
                }
            }
        } else {
            throw new Exception('Invalid department name.');
        }
    }
    
    function getLDAPResults()
    {
        if (!isset($this->_results)) {
            $options = array(
                'bind_dn'       => UNL_Peoplefinder_Driver_LDAP::$bindDN,
                'bind_password' => UNL_Peoplefinder_Driver_LDAP::$bindPW,
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
    
    function hasChildren()
    {
        $results = $this->_xml->xpath('//attribute[@name="org_unit"][@value="50000003"]/..//attribute[@name="name"][@value="'.$this->name.'"]/../branch');
        return count($results)?true:false;
    }
    
    function getChildren()
    {
        $children = array();
        $results = $this->_xml->xpath('//attribute[@name="org_unit"][@value="50000003"]/..//attribute[@name="name"][@value="'.$this->name.'"]/../branch');
        foreach ($results as $result) {
            foreach ($result[0] as $attribute) {
                if (isset($attribute['name'])
                    && $attribute['name']=='name') {
                    $children[] = (string)$attribute['value'];
                    break;
                }
            }
        }
        asort($children);
        return $children;
    }
}
?>
