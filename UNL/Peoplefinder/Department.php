<?php
require_once 'UNL/LDAP.php';

class UNL_Peoplefinder_Department implements Countable, Iterator
{
    public $name;
    
    protected $_results;
    
    function __construct($name)
    {
        $this->name = $name;
        $options = array(
            'bind_dn'       => UNL_Peoplefinder::$bindDN,
            'bind_password' => UNL_Peoplefinder::$bindPW,
            );
        
        $ldap = UNL_LDAP::getConnection($options);
        $name = str_replace(array('(',')','*','\'','"'), '', $name);
        $this->_results = $ldap->search(UNL_Peoplefinder::$baseDN,
                                        '(unlHRPrimaryDepartment='.$name.')');
        $this->_results->sort('cn');
    }
    
    function count()
    {
        return count($this->_results);
    }
    
    function rewind()
    {
        $this->_results->rewind();
    }
    
    function current()
    {
        return UNL_Peoplefinder_Record::fromUNLLDAPEntry($this->_results->current());
    }
    
    function key()
    {
        return $this->_results->key();
    }
    
    function next()
    {
        $this->_results->next();
    }
    
    function valid()
    {
        return $this->_results->valid();
    }
}
?>