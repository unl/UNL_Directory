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
    public $dn; // distinguished name
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
    public $unlHROrgUnitNumber;
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
    public $unlSISMinor;
    public $unlEmailAlias;

    function __construct($options = array())
    {
        if (isset($options['uid'])
            && $options['peoplefinder']) {
            return $options['peoplefinder']->getUID($options['uid']);
        }
    }

    protected function getCache()
    {
        return new UNL_Cache_Lite(array(
            'cacheDir' => realpath(__DIR__ . '/../../../tmp') . '/',
            'memoryCaching' => true,
            'lifeTime' => 4 * 60 * 60, // 4 hours
        ));
    }

    protected function getBuildings() {
        $c = $this->getCache();
        $bldgs = $c->get('UNL buildings');
        if (!$bldgs) {
            $bldgs = new UNL_Common_Building();
            $bldgs = $bldgs->getAllCodes();
            if ($bldgs) {
                $c->save(serialize($bldgs));
            }
        } else {
            $bldgs = unserialize($bldgs);
        }

        return $bldgs;
    }

    /**
     * Takes in a string from the LDAP directory, usually formatted like:
     *    ___ ###, UNL, 68588-####
     *    Where ### is the room number, ___ = Building Abbreviation, #### zip extension
     *
     * @param string
     * @return array Associative array.
     */
    function formatPostalAddress()
    {
        if (isset($this->postalAddress)) {
            $postalAddress = $this->postalAddress;
        } else {
            $postalAddress = $this->unlHRAddress;
        }

        if (empty($postalAddress)) {
            return array();
        }

        $parts = explode(',', $postalAddress);
        $part = trim(array_shift($parts));

        $bldgs = $this->getBuildings();

        // Set up defaults:
        $address = array();
        $address['street-address'] = $part;
        $address['locality']       = '';
        $address['region']         = '';
        $address['postal-code']    = '';

        $streetParts = explode(' ', $part);
        
        // Extension workers have this prefix
        if (strtolower($streetParts[0]) == 'extension') {
            $address['street-address'] = implode(' ', array_slice($streetParts, 1));
        }
        
        // check for a building code + room number
        if (array_key_exists($streetParts[0], $bldgs) && array_key_exists($streetParts[1], $bldgs)) {
            // oh no, both are building codes! check if one is strictly numeric
            // if so, assume that is the room number
            if (preg_match('/^[\d]*$/', $streetParts[0]) && !preg_match('/^[\d]*$/', $streetParts[1])) {
                // legacy format (room number first)
                $address['unlBuildingCode'] = $streetParts[1];
                $address['roomNumber'] = $streetParts[0];
            } else {
                // assume the first one is building code, as this is the new standard
                $address['unlBuildingCode'] = $streetParts[0];
                if (isset($streetParts[1])) {
                    $address['roomNumber'] = $streetParts[1];
                }
            }
        } else if (isset($bldgs[$streetParts[0]])) {
            $address['unlBuildingCode'] = $streetParts[0];
            if (isset($streetParts[1])) {
                $address['roomNumber'] = $streetParts[1];
            }
        } else if (isset($streetParts[1]) && isset($bldgs[$streetParts[1]])) {
            // legacy format (room number first)
            $address['unlBuildingCode'] = $streetParts[1];
            $address['roomNumber'] = $streetParts[0];
        }
        
        // workers without a set room have the "mobile" room identifier
        if (isset($address['roomNumber']) && strtolower($address['roomNumber']) == 'mobile') {
            unset($address['roomNumber']);
        }

        // postal code should be at the end
        if (count($parts)) {
            $part = trim(array_pop($parts));
             if (preg_match('/^([\d]{5})(\-[\d]{4})?$/', $part)) {
                $address['postal-code'] = $part;
            }
        }

        // next from the end should be locality
        if (count($parts)) {
            $localityTranslate = array(
                'City Campus' => 'Lincoln',
                'UNL' => 'Lincoln',
                'UNO' => 'Omaha',
            );

            $part = trim(array_pop($parts));

            if (isset($localityTranslate[$part])) {
                $part = $localityTranslate[$part];
            }

            $address['locality'] = $part;
        }

        // try to determine region (state) from postal code
        switch (substr($address['postal-code'], 0, 2)) {
            case '65':
                $address['region'] = 'MO';
                break;
            case '69':
            case '68':
                $address['region'] = 'NE';
        }

        return $address;
    }

    /**
     * Formats a major subject code into a text description.
     *
     * @param string $subject Subject code for the major eg: MSYM
     *
     * @return string
     */
    public function formatMajor($subject)
    {
        $c = $this->getCache();
        $majors = $c->get('catalog majors');

        if (!$majors) {
            if ($majors = file_get_contents('http://bulletin.unl.edu/undergraduate/majors/lookup/?format=json')) {
                $c->save($majors);
            } else {
                $majors = '[]';
            }
        }

        $majors = json_decode($majors, true);

        if ($majors && isset($majors[$subject])) {
            return $majors[$subject];
        }

        return $subject;
    }

    /**
     * Get the preferred name for this person, eduPersonNickname or givenName
     * 
     * @return string
     */
    public function getPreferredFirstName()
    {
    
        if (!empty($this->eduPersonNickname)
            && $this->eduPersonNickname != ' ') {
            return $this->eduPersonNickname;
        }

        return $this->givenName;
    }

    /**
     * Format a three letter college abbreviation into the full college name.
     *
     * @param string $college College abbreviation = FPA
     *
     * @return string College of Fine &amp; Performing Arts
     */
    public function formatCollege($college)
    {
        $colleges = new UNL_Common_Colleges();
        if (isset($colleges->colleges[$college])) {
            return htmlentities($colleges->colleges[$college]);
        }

        return $college;
    }

    function getImageURL($size = 'medium')
    {

        if ($this->ou == 'org') {
            return UNL_Peoplefinder::getURL().'images/organization.png';
        }

        switch ($size) {
            case 'large':
            case 'medium':
            case 'small':
            case 'tiny':
            case 'topbar':
                break;
            default:
                $size = 'medium';
        }

        return 'https://planetred.unl.edu/pg/icon/unl_'.str_replace('-', '_', $this->uid).'/'.$size.'/';
    }

    function __toString()
    {
        return (string)$this->uid;
    }
}

