<?php
class UNL_PeopleFinder_Developers_Department
{
    public $title       = "Department Record";

    public $uri         = "{id}|{org_unit}";

    public $exampleURI  = "362";

    public $properties  = array(
                                array("parent", "(String) Distinguished name", true, true),
                                array("id", "(String) Unique ID for this record", true, true),
                                array("name", "(String) Name of this deparmtnet/unit", true, true),
                                array("org_unit", "(String) Official org unit ID from SAP", true, true),
                                array("building", "(String) Building code", true, true),
                                array("room", "(String) Room", true, true),
                                array("city", "(String) City", true, true),
                                array("state", "(String) State", true, true),
                                array("postal_code", "(String) Zip code", true, true),
                                array("address", "(String) Postal address", true, true),
                                array("phone", "(String) Phone number", true, true),
                                array("fax", "(String) Fax number", true, true),
                                array("email", "(String) Email address", true, true),
                                array("website", "(String) URL to the department website.", true, true),
                                array("parent_id", "(String) ID for the parent department.", true, true)
                                );
                                
    public $formats = array("xml", "partial");
    
    function __construct()
    {
        $this->uri = UNL_Officefinder::getURL() . $this->uri;
        $this->exampleURI  = UNL_Officefinder::getURL() . $this->exampleURI;
    }
}