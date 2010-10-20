<?php
/**
 * This file conducts a simple ldap search
 *
 * PHP version 5
 * 
 * $Id$
 * 
 * @category  Default 
 * @package   UNL_LDAP
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://pear.unl.edu/package/UNL_LDAP
 */
set_include_path(realpath(dirname(__FILE__).'/../..'));
require_once 'UNL/LDAP.php';
require_once 'config.inc.php';

$ldap   = UNL_LDAP::getConnection($options);
//$results = $ldap->search('dc=unl,dc=edu', '(|(sn=ryan lim)(cn=ryan lim)(&(| (givenname=ryan) (sn=ryan) (mail=ryan) (unlemailnickname=ryan) (unlemailalias=ryan))(| (givenname=lim) (sn=lim) (mail=lim) (unlemailnickname=lim) (unlemailalias=lim))))');
//$results->sort('uid');

//$results = $ldap->search('dc=unl,dc=edu', '(uid=jwiltse2)');
//$results = $ldap->search('dc=unl,dc=edu', '(cn=jwiltse2-00000040)');
//$results = $ldap->search('dc=unl,dc=edu', '(cn=bbieber2-*)');
//$results = $ldap->search('dc=unl,dc=edu', '(cn=jbrand1-*)');
//$results = $ldap->search('dc=unl,dc=edu', '(uid=rcrisler1)');
//$results = $ldap->search('dc=unl,dc=edu', '(uid=bbieber2)');
//$results = $ldap->search('dc=unl,dc=edu', '(eduPersonNickname=meg)');
//$results = $ldap->search('dc=unl,dc=edu', '(unlHROrgUnitNumber=50000852)');
$results = $ldap->search('uid=bbieber2,ou=people,dc=unl,dc=edu','(objectClass=person)');


echo count($results).' results found.'.PHP_EOL;

foreach ($results as $dn=>$entry) {
    echo '<pre>';
    echo $dn.PHP_EOL;
    print_r($entry);
    
    echo $entry->givenName.' '.$entry->sn.' is '.$entry->uid.PHP_EOL;
    echo $entry->cn;
    if (count($entry->objectClass)) {
        echo $entry->givenName.' is a member of:';
        foreach ($entry->objectClass as $class) {
            echo $class.',';
        }
        echo PHP_EOL.'<br>';
    }
}
highlight_file(__FILE__);
