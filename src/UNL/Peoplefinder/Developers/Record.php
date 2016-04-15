<?php
class UNL_PeopleFinder_Developers_Record
{
    public $title       = 'Person Record';
    
    public $uri         = 'service.php?uid={uid}';
    
    public $exampleURI  = 'service.php?uid=s-mfairch4';
    
    public $properties  = array(
        array('dn', '(String) Distinguished name', true, true),
        array('cn', '(Array(String)) Common Name', true, true),
        array('ou', '(Array(String)) Organizational unit', true, true),
        array('eduPersonAffiliation', '(Array(String)) Affiliation type', true, true),
        array('eduPersonNickname', '(Array(String)) nickname', true, true),
        array('eduPersonPrimaryAffiliation', '(Array(String)) Primary affiliation', true, true),
        array('givenName', '(Array(String)) Given Name', true, true),
        array('displayName', '(Array(String)) Display name', true, true),
        array('mail', '(Array(String)) Email address', true, true),
        array('postalAddress', '(Array(String)) Postal address', true, true),
        array('sn', '(Array(String)) Short name', true, true),
        array('telephoneNumber', '(Array(String)) Phone number', true, true),
        array('title', '(Array(String)) Title', true, true),
        array('uid', '(String) Uinque ID.', true, true),
        array('unlHROrgUnitNumber', '(Array(String)) HR organizational unit', true, true),
        array('unlHRPrimaryDepartment', '(Array(String)) Primary Department', true, true),
        array('unlHRAddress', '(Array(String)) HR Address', true, true),
        array('unlSISClassLevel', '(Array(String)) Class Level', true, true),
        array('unlSISCollege', '(Array(String)) College', true, true),
        array('unlSISMajor', '(Array(String)) Majors', true, true),
        array('unlSISMinor', '(Array(String)) Minors', true, true),
        array('unlEmailAlias', '(Array(String)) Email Alias', true, true),
        array('imageURL', '(URL) The url to the person\'s picture.', true, true)
    );
                                
    public $formats     = array('json', 'xml', 'partial');
    
    function __construct()
    {
        $this->uri = UNL_Peoplefinder::$url . $this->uri;
        $this->exampleURI  = UNL_Peoplefinder::$url . $this->exampleURI;
    }
}
