<?php
/**
 * Peoplefinder class for UNL's online directory.
 *
 * PHP version 5
 *
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2007 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://peoplefinder.unl.edu/
 */
class UNL_Peoplefinder_Record
{
    public $cn;
    public $ou;
    public $eduPersonAffiliation;
    public $eduPersonNickname;
    public $eduPersonPrimaryAffiliation;
    public $givenName;
    public $displayName;
    public $mail;
    public $postalAddress;
    public $sn;
    public $telephoneNumber;
    public $title;
    public $uid;
    public $unlHRPrimaryDepartment;
    public $unlHRAddress;
    public $unlSISClassLevel;
    public $unlSISCollege;
//    public $unlSISLocalAddr1;
//    public $unlSISLocalAddr2;
//    public $unlSISLocalCity;
//    public $unlSISLocalPhone;
//    public $unlSISLocalState;
//    public $unlSISLocalZip;
//    public $unlSISPermAddr1;
//    public $unlSISPermAddr2;
//    public $unlSISPermCity;
//    public $unlSISPermState;
//    public $unlSISPermZip;
    public $unlSISMajor;
    public $unlEmailAlias;
    
    function __construct($options = array())
    {
        if (isset($options['uid'])
            && $options['peoplefinder']) {
            return $options['peoplefinder']->getUID($options['uid']);
        }
    }
    
    
    
    /**
     * Takes in a string from the LDAP directory, usually formatted like:
     *     ### ___ UNL 68588-####
     *    Where ### is the room number, ___ = Building Abbreviation, #### zip extension
     *
     * @param string
     * @return array Associative array.
     */
    function formatPostalAddress()
    {
        $parts = explode(',', $this->postalAddress);

        // Set up defaults:
        $address = array();
        $address['street-address'] = trim($parts[0]);
        $address['locality']       = '';
        $address['region']         = 'NE';
        $address['postal-code']    = '';
        
        if (count($parts) == 3) {
            // Assume we have a street address, city, zip.
            $address['locality'] = trim($parts[1]);
        }
        
        // Now lets find some important bits.
        foreach ($parts as $part) {
            if (preg_match('/([\d]{5})(\-[\d]{4})?/', $part)) {
                // Found a zip-code
                $address['postal-code'] = trim($part);
            }
        }
        
        switch (substr($address['postal-code'], 0, 3)) {
            case '681':
                $address['locality'] = 'Omaha';
                break;
            case '685':
                $address['locality'] = 'Lincoln';
                break;
        }
        
        return $address;
    }
    
    function getImageURL($size = 'medium')
    {

        if ($this->ou == 'org') {
            return UNL_PEOPLEFINDER_URI.'images/organization.png';
        }

        switch ($size) {
            case 'large':
            case 'medium':
            case 'tiny':
            case 'topbar':
                break;
            default:
                $size = 'medium';
        }

        return 'http://planetred.unl.edu/pg/icon/unl_'.str_replace('-', '_', $this->uid).'/'.$size.'/';
    }
    
    function __toString()
    {
        return $this->uid;
    }
}

?>