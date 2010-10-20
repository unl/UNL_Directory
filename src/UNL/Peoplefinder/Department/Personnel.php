<?php
class UNL_Peoplefinder_Department_Personnel extends IteratorIterator implements Countable
{
    /**
     * 
     * @var UNL_Peoplefinder
     */
    protected $peoplefinder;

    function __construct($iterator, $peoplefinder)
    {
        parent::__construct($iterator);
        $this->peoplefinder = $peoplefinder;
    }

    function count()
    {
        return count($this->getInnerIterator());
    }

    function current()
    {
        // Get the DN for this record in the LDAP
        $dn = self::key();

        // DN: cn=landerson3-00009723,uid=landerson3,ou=people,dc=unl,dc=edu
        $path = explode(',', $dn);

        // shift the cn off the top of the path
        array_shift($path);

        // we just need the uid
        list(,$uid) = explode('=', $path[0]);

        return $this->peoplefinder->getUID($uid);
    }
}