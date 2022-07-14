<?php
class UNL_Peoplefinder_PersonList_Building extends AppendIterator
{
    public $options = array();
    
    public $user;

    function __construct($options = array())
    {
        parent::__construct();

        // Login is required to view this
        $this->user = UNL_Officefinder::getUser(true);


        if ($options['format'] != 'html') {
            throw new Exception('This is not your personal service');
        }

        $this->options = $options;

        UNL_Peoplefinder::$resultLimit = 5000;

        /* @var $pf UNL_Peoplefinder */
        $pf = $options['peoplefinder'];

        if (!$pf->driver instanceof UNL_Peoplefinder_Driver_LDAP) {
            throw new Exception('Whoah whoah, that ain\'t gonna happen unless you have direct LDAP access');
        }

        // Set the attributes requested to only a subset to save memory
        $pf->driver->detailAttributes = array(
            'sn',
            'cn',
            'telephoneNumber',
            'givenName',
            'title',
            'uid',
            'unlHROrgUnitNumber',
            'unlHRPrimaryDepartment',
        );

        $results = $pf->getBuildingMatches($this->options['building'], 'faculty');
        $this->append(new UNL_Peoplefinder_SearchResults(array('results'=>$results)));
    }
}