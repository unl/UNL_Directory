<?php
class UNL_PeopleFinder_Developers_Department
{
    public $title       = 'Department Record';

    public $uri         = '{id}|{org_unit}';

    public $exampleURI  = '362';

    public $properties  = array(
        array('parent', '(String) ID for the parent department (if not root)', false, true),
        array('id', '(String) Unique ID for this record', false, true),
        array('name', '(String) Name of this deparmtnet/unit', false, true),
        array('org_unit', '(String) Official org unit ID from SAP', false, true),
        array('building', '(String) Building code', false, true),
        array('room', '(String) Room', false, true),
        array('city', '(String) City', false, true),
        array('state', '(String) State', false, true),
        array('postal_code', '(String) Zip code', false, true),
        array('address', '(String) Postal address', false, true),
        array('phone', '(String) Phone number', false, true),
        array('fax', '(String) Fax number', false, true),
        array('email', '(String) Email address', false, true),
        array('website', '(String) URL to the department website.', false, true),
        array('parent_id', '(String) ID for the parent department.', false, true)
    );
                                
    public $formats = array('json', 'xml', 'partial');
    
    function __construct()
    {
        $this->uri = UNL_Officefinder::getURL() . $this->uri;
        $this->exampleURI  = UNL_Officefinder::getURL() . $this->exampleURI;
    }
}