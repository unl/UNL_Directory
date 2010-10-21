<?php
class UNL_Peoplefinder_Department_Personnel extends FilterIterator implements Countable
{
    /**
     * 
     * @var UNL_Peoplefinder
     */
    protected $peoplefinder;

    protected $_lastRecord;

    function __construct($iterator, $peoplefinder)
    {
        parent::__construct($iterator);
        $this->peoplefinder = $peoplefinder;
    }

    function count()
    {
        return count($this->getInnerIterator());
    }

    function accept()
    {
        // Get the DN for this record in the LDAP
        $dn = self::key();

        // DN: cn=landerson3-00009723,uid=landerson3,ou=people,dc=unl,dc=edu
        $path = explode(',', $dn);

        // shift the cn off the top of the path
        array_shift($path);

        // we just need the uid
        list($var, $uid) = explode('=', $path[0]);
        
        if ($var != 'uid') {
            // Whoah, this is an unexpected entry
            return false;
        }

        try {
            $this->_lastRecord = $this->peoplefinder->getUID($uid);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    function current()
    {
        return $this->_lastRecord;
    }
}