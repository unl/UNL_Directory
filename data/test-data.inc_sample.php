<?php

/**
 * To use:
 * Copy this file to /data/test-data.inc.php
 * Make sure the lines related to this file and UNL_Peoplefinder::$sampleUID are uncommented in config.inc.php
 */

/**
 * Sample of the result of the query in UNL_Peoplefinder_Driver_OracleDB->fixLDAPEntries()
 * Simulates the result for one person: hhusker1
 */
UNL_Peoplefinder_Driver_OracleDB::$sampleFixLDAPEntries =
array (
    0 =>
        array (
            'NETID' => 'hhusker1',
            'NU_FERPA' => NULL,
            'MAIL' => 'NOREPLY@UNL.EDU',
        ),
);


/**
 * Sample of the result of ldap_get_entries().
 * Simulates a result for one person: hhusker1
 */
UNL_Peoplefinder_Driver_LDAP::$samplePersonLDAP =
array (
    'count' => 1,
    0 =>
        array (
            'cn' =>
                array (
                    'count' => 1,
                    0 => 'hhusker1',
                ),
            0 => 'cn',
            'sn' =>
                array (
                    'count' => 1,
                    0 => 'Husker',
                ),
            1 => 'sn',
            'title' =>
                array (
                    'count' => 1,
                    0 => 'Mascot',
                ),
            2 => 'title',
            'postaladdress' =>
                array (
                    'count' => 1,
                    0 => 'MSTD, UNL, 685880120',
                ),
            3 => 'postaladdress',
            'telephonenumber' =>
                array (
                    'count' => 1,
                    0 => '4024724224',
                ),
            4 => 'telephonenumber',
            'givenname' =>
                array (
                    'count' => 1,
                    0 => 'Herbie',
                ),
            5 => 'givenname',
            'displayname' =>
                array (
                    'count' => 1,
                    0 => 'Herbie Husker',
                ),
            6 => 'displayname',
            'department' =>
                array (
                    'count' => 1,
                    0 => 'Athletics                            UNL',
                ),
            7 => 'department',
            'samaccountname' =>
                array (
                    'count' => 1,
                    0 => 'hhusker1',
                ),
            8 => 'samaccountname',
            'mail' =>
                array (
                    'count' => 1,
                    0 => 'hhusker1@unl.edu',
                ),
            9 => 'mail',
            'departmentnumber' =>
                array (
                    'count' => 1,
                    0 => '50000850',
                ),
            10 => 'departmentnumber',
            'edupersonprincipalname' =>
                array (
                    'count' => 1,
                    0 => 'hhusker1@unl.edu',
                ),
            11 => 'edupersonprincipalname',
            'edupersonaffiliation' =>
                array (
                    'count' => 3,
                    0 => 'student',
                    1 => 'enrolled',
                    2 => 'staff',
                ),
            12 => 'edupersonaffiliation',
            'edupersonprimaryaffiliation' =>
                array (
                    'count' => 1,
                    0 => 'staff',
                ),
            13 => 'edupersonprimaryaffiliation',
            'unluncwid' =>
                array (
                    'count' => 1,
                    0 => '01234567',
                ),
            14 => 'unluncwid',
            'count' => 15,
            'dn' => 'CN=hhusker1,OU=people,DC=unl,DC=edu',
        ),
);
