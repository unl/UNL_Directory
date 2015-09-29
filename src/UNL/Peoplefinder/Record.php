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
class UNL_Peoplefinder_Record implements UNL_Peoplefinder_Routable, Serializable, JsonSerializable
{
    const PLANETRED_BASE_URL = 'https://planetred.unl.edu/pg/';

    const SERIALIZE_VERSION_SAFE = 1;
    const SERIALIZE_VERSION_SAFE_MULTIVALUE = 2;
    const SERIALIZE_VERSION_FULL = 3;

    public $dn; // distinguished name
    public $cn;
    public $ou;
    public $eduPersonAffiliation;
    public $eduPersonNickname;
    public $eduPersonPrimaryAffiliation;
    public $eduPersonPrincipalName;
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

    protected $knowledge = false;

    private $cache;

    protected $options;

    public function __construct($options = [])
    {
        if (isset($options['uid'])) {
            $peoplefinder = isset($options['peoplefinder']) ? $options['peoplefinder'] : UNL_Peoplefinder::getInstance();
            $remoteRecord = self::factory($options['uid'], $peoplefinder);

            foreach (get_object_vars($remoteRecord) as $var => $value) {
                $this->$var = $value;
            }
        }

        $this->options = $options;
    }

    public static function factory($uid, $peoplefinder = null)
    {
        if (!$peoplefinder) {
            $peoplefinder = UNL_Peoplefinder::getInstance();
        }

        $cache = UNL_Peoplefinder_Cache::factory();
        $cacheKey = 'UNL_Peoplefinder_Record-uid-' . $uid;

        $remoteRecord = $cache->get($cacheKey);
        if (!$remoteRecord) {
            $remoteRecord = $peoplefinder->getUID($uid);

            if ($remoteRecord) {
                $cache->set($key, $remoteRecord);
            }
        }

        return $remoteRecord;
    }

    protected function getCache()
    {
        if (!$this->cache) {
            $this->cache = UNL_Peoplefinder_Cache::factory();
        }

        return $this->cache;
    }

    protected function getBuildings() {
        $cache = $this->getCache();
        $cacheKey = 'UNL-buildings';
        $bldgs = $cache->get($cacheKey);

        if (!$bldgs) {
            try {
                $bldgs = new UNL_Common_Building();
                $bldgs = $bldgs->getAllCodes();

                if ($bldgs) {
                    $cache->set($cacheKey, $bldgs);
                } else {
                    throw new Exception('Could not load buildings from API');
                }
            } catch (Exception $e) {
                $bldgs = $cache->getSlow($cacheKey);
            }
        }

        return $bldgs;
    }

    public function shouldShowKnowledge()
    {
        $isShortFormat = (isset($this->options['format']) && $this->options['format'] === 'hcard');
        return !$isShortFormat && !$this->isPrimarilyStudent() && null !== $this->getKnowledge();
    }

    /**
     * Takes in a string from the LDAP directory, usually formatted like:
     *    ___ ###, UNL, 68588-####
     *    Where ### is the room number, ___ = Building Abbreviation, #### zip extension
     *
     * @param string
     * @return array Associative array.
     */
    public function formatPostalAddress()
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
        $cache = $this->getCache();
        $cacheKey = 'UNL-catalog-majors';
        $majors = $cache->get($cacheKey);

        if (!$majors) {
            if ($majors = file_get_contents('https://bulletin.unl.edu/undergraduate/majors/lookup/?format=json')) {
                $cache->set($cacheKey, $majors);
            } else if (!($majors = $cache->getSlow($cacheKey))) {
                $majors = '[]';
            }
        }

        $majors = json_decode($majors, true);

        if ($majors && isset($majors[$subject])) {
            return $majors[$subject];
        }

        return $subject;
    }

    public function hasNickname()
    {
        return !empty($this->eduPersonNickname) && $this->eduPersonNickname != ' ';
    }

    /**
     * Get the preferred name for this person, eduPersonNickname or givenName
     *
     * @return string
     */
    public function getPreferredFirstName()
    {
        if ($this->hasNickname()) {
            return $this->eduPersonNickname;
        }

        return $this->givenName;
    }

    public function formatAffiliations()
    {
        if (!$this->eduPersonAffiliation) {
            return false;
        }

        $affiliations = $this->eduPersonAffiliation;
        if ($affiliations instanceof ArrayIterator) {
            $affiliations = $affiliations->getArrayCopy();
        }

        $affiliations = array_intersect(UNL_Peoplefinder::$displayedAffiliations, $affiliations);

        return implode(', ', $affiliations);
    }

    public function hasStudentInformation()
    {
        $studentInfomationFields = [
            'unlSISClassLevel',
            'unlSISCollege',
            'unlSISMajor',
            'unlSISMinor',
        ];

        foreach ($studentInfomationFields as $var) {
            if (isset($this->$var)) {
                return true;
            }
        }
    }

    public function formatClassLevel()
    {
        switch ($this->unlSISClassLevel) {
            case 'FR':
                $class = 'Freshman';
                break;
            case 'SR':
                $class = 'Senior';
                break;
            case 'SO':
                $class = 'Sophomore';
                break;
            case 'JR':
                $class = 'Junior';
                break;
            case 'GR':
                $class = 'Graduate Student';
                break;
            default:
                $class = $this->unlSISClassLevel;
        }

        return $class;
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
        // clean up data from PeopleSoft
        $college = str_replace('-U', '', $college);

        $colleges = new UNL_Common_Colleges();
        return isset($colleges->colleges[$college]) ? $colleges->colleges[$college] : $college;
    }

    public function isPrimarilyStudent()
    {
        return $this->eduPersonPrimaryAffiliation == 'student';
    }

    public function getRoles()
    {
        $cache = $this->getCache();
        $cacheKey = 'UNL_Peoplefinder_Record_Roles-uid-' . $this->uid;

        $roles = $cache->get($cacheKey);

        if (!$roles) {
            $roles = UNL_Peoplefinder::getInstance()->getRoles($this->dn);

            if ($roles) {
                $cache->set($cacheKey, $roles);
            }
        }

        return $roles;
    }

    public function getKnowledge()
    {
        if ($this->knowledge !== false) {
            return $this->knowledge;
        }

        $knowledgeDriver = new UNL_Knowledge();
        $this->knowledge = $knowledgeDriver->getRecords((string) $this->uid);

        return $this->knowledge;
    }

    public function getProfileUid()
    {
        return str_replace('-', '_', $this->uid);
    }

    public function getProfileURL()
    {
        if ($this->ou === 'org') {
            return false;
        }

        return self::PLANETRED_BASE_URL . 'profile/unl_' . $this->getProfileUid();
    }

    public function getImageURL($size = UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_MEDIUM)
    {
        $url = $this->getRecordUrl('avatar');
        if ($size !== UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_MEDIUM) {
            $url .= '?' . http_build_query(['s' => $size]);
        }
        return $url;
    }

    protected function getRecordUrl($type)
    {
        return UNL_Peoplefinder::getURL() . $type . '/' . $this->uid;
    }

    public function getUrl($options = [])
    {
        $baseUrl = $this->getRecordUrl('people');

        if ($options) {
            return $baseUrl . '?' . http_build_query($options);
        }

        return $baseUrl;
    }

    public function getVcardUrl()
    {
        return $this->getRecordUrl('vcards');
    }

    public function getHcardUrl()
    {
        return $this->getRecordUrl('hcards');
    }

    public function getPrintUrl()
    {
        return $this->getUrl(['print' => true]);
    }

    public function getQRCodeUrl($content)
    {
        // WARNING: Google has officially deprecated this API on April 20, 2012
        $options = [
            'cht' => 'qr',
            'chs' => '400x400',
            'chl' => $content,
            'chld' => 'L|1',
        ];
        return 'https://chart.googleapis.com/chart?' . http_build_query($options);
    }

    protected function getPublicProperties()
    {
        $self = $this;
        $getPublicProperties = function() use ($self) {
            return get_object_vars($self);
        };
        $getPublicProperties = $getPublicProperties->bindTo(null, null);

        return $getPublicProperties();
    }

    public function serialize($version = self::SERIALIZE_VERSION_FULL)
    {
        $data = $this->getPublicProperties();

        foreach ($data as $key => $value) {
            if ($value instanceof Traversable) {
                if ($version === self::SERIALIZE_VERSION_SAFE) {
                    $data[$key] = (string) $value;
                } else {
                    $data[$key] = iterator_to_array($value);
                }
            }
        }

        // inject methods as properties
        $data['imageURL'] = $this->getImageURL();

        if ($address = $this->formatPostalAddress()) {
            $data['unlDirectoryAddress'] = $address;
        }

        // for backwards compatibliity (safe), cast to object
        if ($version === self::SERIALIZE_VERSION_SAFE || $version === self::SERIALIZE_VERSION_SAFE_MULTIVALUE) {
            $data = (object) $data;
        }

        return serialize($data);
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        foreach (array_keys($this->getPublicProperties()) as $var) {
            if (isset($data[$var])) {
                $value = $data[$var];
                if (is_array($value)) {
                    $this->$var = new UNL_Peoplefinder_Driver_LDAP_Multivalue($value);
                } else {
                    $this->$var = $value;
                }
            }
        }
    }

    public function jsonSerialize()
    {
        $data = $this->getPublicProperties();

        //force uid to be a single value
        $data['uid'] = (string) $this->uid;

        // inject method as property
        $data['imageURL'] = $this->getImageURL();

        if ($address = $this->formatPostalAddress()) {
            $data['unlDirectoryAddress'] = $address;
        }

        return $data;
    }

    public function __toString()
    {
        return (string)$this->uid;
    }
}
