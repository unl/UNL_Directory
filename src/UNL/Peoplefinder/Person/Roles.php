<?php
class UNL_Peoplefinder_Person_Roles extends IteratorIterator
{
    function __construct($options = array())
    {
        if (!isset($options['dn'])) {
            throw new Exception('You must supply a base DN from which to search.');
        }
        $ldap   = UNL_LDAP::getConnection(
                      array('uri'           => UNL_Peoplefinder_Driver_LDAP::$ldapServer,
                            'base'          => UNL_Peoplefinder_Driver_LDAP::$baseDN,
                            'suffix'        => 'ou=People,dc=unl,dc=edu',
                            'bind_dn'       => UNL_Peoplefinder_Driver_LDAP::$bindDN,
                            'bind_password' => UNL_Peoplefinder_Driver_LDAP::$bindPW));
        $results = $ldap->search($options['dn'], '(objectClass=unlRole)');
        parent::__construct($results);
    }
}