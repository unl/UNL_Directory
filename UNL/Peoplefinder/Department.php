<?php
require_once 'UNL/LDAP.php';

class UNL_Peoplefinder_Department implements Countable, Iterator
{
    public $name;
    
    protected $_ldap;

    protected $_results;
    
    function __construct($name)
    {
        $this->name = $name;
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
