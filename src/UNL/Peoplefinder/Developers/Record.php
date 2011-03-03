<?php
class UNL_PeopleFinder_Developers_Record
{
    public $title       = "Record";
    
    public $uri         = "service.php?uid={uid}";
    
    public $exampleURI  = "service.php?uid=s-mfairch4";
    
    public $properties  = array(
                                array("dn", "Distinguished name", true, true),
                                array("cn", "", true, true),
                                array("ou", "", true, true),
                                array("eduPersonAffiliation", "", true, true),
                                array("eduPersonNickname", "", true, true),
                                array("eduPersonPrimaryAffiliation", "", true, true),
                                array("givenName", "", true, true),
                                array("displayName", "", true, true),
                                array("mail", "", true, true),
                                array("postalAddress", "", true, true),
                                array("sn", "", true, true),
                                array("telephoneNumber", "", true, true),
                                array("title", "", true, true),
                                array("uid", "", true, true),
                                array("unlHROrgUnitNumber", "", true, true),
                                array("unlHRPrimaryDepartment", "", true, true),
                                array("unlHRAddress", "", true, true),
                                array("unlSISClassLevel", "", true, true),
                                array("unlSISCollege", "", true, true),
                                array("unlSISMajor", "", true, true),
                                array("unlSISMinor", "", true, true),
                                array("unlEmailAlias", "", true, true)
                                );
                                
    public $formats     = array("json", "xml", "partial");
    
    function __construct()
    {
        $this->uri = UNL_Peoplefinder::$url . $this->uri;
        $this->exampleURI  = UNL_Peoplefinder::$url . $this->exampleURI;
    }
}