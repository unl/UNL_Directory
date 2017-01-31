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

    const BAD_SAP_MAIL_PLACEHOLDER = 'none@none.none';

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
                if ($var === 'mail' && $value == UNL_Peoplefinder_Record::BAD_SAP_MAIL_PLACEHOLDER) {
                    continue;
                }

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

        return $peoplefinder->getUID($uid);
    }

    public static function getCleanPhoneNumber($phone)
    {
        return str_replace(array('(', ')', '-', ' '), '', $phone);
    }

    /**
     * Some numbers can be dialed without dialing off-campus.
     *
     * This is especially important for extension offices which would normally
     * be charged long-distance rates.
     *
     * Here is the list of rules for converting 10 digit telephone numbers to their
     * 5 digit equivalents that are dialable from UNL On Campus Phones.
     *
     * 402-472-XXXX 2-XXXX UNL
     * 402-584-38XX 7-38XX HAL (Concord, NE)
     * 402-624-80XX 7-80XX ARDC (Mead, NE)
     * 402-370-40XX 7-40XX NEREC (Norfolk, NE)
     * 308-696-67XX 7-67XX WCREC (North Platte, NE)
     * 308-367-52XX 7-52XX NCTA
     */
    public static function getCampusPhoneNumber($phone)
    {
        $clean_number = self::getCleanPhoneNumber($phone);

        switch (true) {
            case preg_match('/^(402)?472([\d]{4})$/', $clean_number, $matches):
                return '2-' . $matches[2];
            case preg_match('/^(402)?58438([\d]{2})$/', $clean_number, $matches):
                return '7-38' . $matches[2];
            case preg_match('/^(402)?62480([\d]{2})$/', $clean_number, $matches):
                return '7-80' . $matches[2];
            case preg_match('/^(402)?37040([\d]{2})$/', $clean_number, $matches):
                return '7-40' . $matches[2];
            case preg_match('/^(308)?69667([\d]{2})$/', $clean_number, $matches):
                return '7-67' . $matches[2];
            case preg_match('/^(308)?36752([\d]{2})$/', $clean_number, $matches):
                return '7-52' . $matches[2];
        }

        return false;
    }

    public static function getFormattedPhoneNumber($phone)
    {
        $clean_number = self::getCleanPhoneNumber($phone);
        return preg_replace('/([\d]{3})([\d]{3})([\d]{4})/', '$1-$2-$3', $clean_number);
    }

    protected function getCache()
    {
        if (!$this->cache) {
            $this->cache = UNL_Peoplefinder_Cache::factory();
        }

        return $this->cache;
    }

    protected function getBuildings() {
        return UNL_Peoplefinder_Record_Avatar::getBuildings();
    }

    public function shouldShowKnowledge()
    {
        return !$this->isHcardFormat() && !$this->isPrimarilyStudent() && null !== $this->getKnowledge();
    }

    public function isHcardFormat()
    {
        return (isset($this->options['format']) && $this->options['format'] === 'hcard');
    }

    function getUNLBuildingCode()
    {
        $formatted = $this->formatPostalAddress();
        if (isset($formatted['unlBuildingCode'])) {
            return $formatted['unlBuildingCode'];
        } else {
            return false;
        }
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
        $part = str_replace('  ', ' ', trim(array_shift($parts)));

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
        if (isset($streetParts[1], $bldgs[$streetParts[1]], $bldgs[$streetParts[0]])) {
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
        } else if (isset($streetParts[1], $bldgs[$streetParts[1]])) {
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

    public function formatTitle()
    {
        if (!$this->title) {
            return false;
        }

        $haystack = strtolower($this->title);

        $stopWords = [
            'retiree',
            'royalty',
        ];

        foreach ($stopWords as $needle) {
            if (strpos($haystack, $needle) !== false) {
                return false;
            }
        }

        return $this->title;
    }

    public function hasStudentInformation()
    {
        $studentInfomationFields = [
            'unlSISClassLevel' => ['NST'],
            'unlSISCollege' => ['NON-O'],
            'unlSISMajor' => [],
            'unlSISMinor' => [],
        ];

        foreach ($studentInfomationFields as $var => $blacklist) {
            if (!empty($this->$var)) {
                if (!empty($blacklist) && in_array($this->$var, $blacklist)) {
                    return false;
                }

                return true;
            }
        }
    }

    public function formatClassLevel()
    {
        $classLevel = (string) $this->unlSISClassLevel;

        $classLevelMap = [
            'NST' => 'Non-Student',
            '2ND' => 'Second Degree Student',
            'FR' => 'Freshman',
            'SO' => 'Sophomore',
            'JR' => 'Junior',
            'SR' => 'Senior',
            'GR' => 'Graduate Student',
            'P1' => 'Professional Student Year 1',
            'P2' => 'Professional Student Year 2',
            'P3' => 'Professional Student Year 3',
            'P4' => 'Professional Student Year 4',
            '03' => 'Program Student Year 3',
            '04' => 'Program Student Year 4',
        ];

        if (isset($classLevelMap[$classLevel])) {
            return $classLevelMap[$classLevel];
        }

        return $classLevel;
    }

    /**
     * Format a three letter college abbreviation into the full college name.
     *
     * @param string $college College abbreviation = FPA
     *
     * @return string College of Fine & Performing Arts
     */
    public function formatCollege($college)
    {
        // remove student level suffix
        $sisCollegeSuffixes = [
            '-U', // undergrad
            '-G', // grad
            '-P', // professional
            '-O', // other program
            '-T', // ???
        ];
        $college = str_replace($sisCollegeSuffixes, '', $college);

        // SIS values that shouldn't be displayed
        $blacklist = [
            'VST',
            'UGRD',
            'NCLAS',
            'ET',
            'NCT'
        ];

        // similar SIS college names
        $translate = [
            'DENT' => 'DNT',
            'DENTH' => 'DNT',
            'NURBS' => 'NUR',
        ];

        $colleges = [
            'CBA' => [
                'title' => 'College of Business Administration',
                'abbr' => 'CBA',
                'org_unit_number' => '50000897',
            ],
            'FPA' => [
                'title' => 'Hixson-Lied College of Fine & Performing Arts',
                'abbr' => 'HLCFPA',
                'org_unit_number' => '50000898',
            ],
            'ASC' => [
                'title' => 'College of Arts & Sciences',
                'abbr' => 'CAS',
                'org_unit_number' => '50000906',
            ],
            'EHS' => [
                'title' => 'College of Education and Human Sciences',
                'abbr' => 'CEHS',
                'org_unit_number' => '50000910',
            ],
            'JMC' => [
                'title' => 'College of Journalism & Mass Communications',
                'abbr' => 'CoJMC',
                'org_unit_number' => '50000908',
            ],
            'ANR' => [
                'title' => 'College of Agricultural Sciences & Natural Resources',
                'abbr' => 'CASNR',
                'org_unit_number' => '50000787',
            ],
            'ENG' => [
                'title' => 'College of Engineering',
                'abbr' => 'Engineering',
                'org_unit_number' => '50000907',
            ],
            'ARH' => [
                'title' => 'College of Architecture',
                'abbr' => 'CoArch',
                'org_unit_number' => '50000896',
            ],
            'DNT' => [
                'title' => 'UNMC College of Dentistry',
                'abbr' => 'Dentistry',
                'org_unit_number' => '50000719',
            ],
            'LAW' => [
                'title' => 'Nebraska College of Law',
                'abbr' => 'Law',
                'org_unit_number' => '50000899',
            ],
            'GRD' => [
                'title' => 'Office of Graduate Studies',
                'abbr' => 'Grad Studies',
                'org_unit_number' => '50000900',
            ],
            'GEN' => [
                'title' => 'Exploratory & Pre-Professional Advising Center',
                'abbr' => 'Explore Center',
                'org_unit_number' => '50000902',
            ],
            'NUR' => [
                'title' => 'UNMC College of Nursing',
                'abbr' => 'Nursing',
                'org_unit_number' => '50000476',
            ],
            'IEP' => [
                'title' => 'Programs in English as a Second Language',
                'abbr' => 'ESL',
                'org_unit_number' => '50008377',
            ],
            'PAC' => [
                'title' => 'UNO College of Public Affairs and Community Service',
                'abbr' => 'CPACS',
                'org_unit_number' => '50000180',
            ],
            'DVM' => [
                'title' => 'Iowa State University, College of Veterinary Medicine',
                'abbr' => 'Veterinary Medicine Program',
                'org_unit_number' => '50000845',
            ],
            'INT' => [
                'title' => 'Intercampus',
                'abbr' => 'Intercampus',
            ],
            'EEO' => [
                'title' => 'Online & Distance Education',
                'abbr' => 'ODE',
                'org_unit_number' => '50000791',
            ],
            'AUD' => [
                'title' => 'Audiology Program',
                'abbr' => 'Audiology',
                'org_unit_number' => '50001049',
            ],
        ];

        if (isset($translate[$college])) {
            $college = $translate[$college];
        }

        if (isset($colleges[$college])) {
            $college = $colleges[$college];

            if (isset($college['org_unit_number'])) {
                if ($org = UNL_Officefinder_Department::getByorg_unit($college['org_unit_number'])) {
                    $college['link'] = $org->getURL();
                }
            }
        } elseif (in_array($college, $blacklist)) {
            return '';
        }

        return $college;
    }

    public function isPrimarilyStudent()
    {
        return $this->eduPersonPrimaryAffiliation == 'student';
    }

    public function getRoles()
    {
        return UNL_Peoplefinder::getInstance()->getRoles($this->dn);
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

    public function getHRPrimaryDepartment()
    {
        if (!$this->unlHROrgUnitNumber) {
            return null;
        }

        $primaryOrgUnit = '';
        UNL_Peoplefinder_Department::setXPathBase('');
        foreach ($this->unlHROrgUnitNumber as $orgUnit) {
            try {
                $hrDepartment =  UNL_Peoplefinder_Department::getById($orgUnit);
                if (!$hrDepartment) {
                    continue;
                }

                if ($hrDepartment->name === $this->unlHRPrimaryDepartment) {
                    $primaryOrgUnit = $orgUnit;
                    break;
                }
            } catch (Exception $e) {
                //ignore
            }
        }

        if (!$primaryOrgUnit) {
            $primaryOrgUnit = current($this->unlHROrgUnitNumber);
        }

        return UNL_Officefinder_Department::getByorg_unit($primaryOrgUnit) ?: null;
    }

    public function getEditors()
    {
        $editorDepartment = $this->getHRPrimaryDepartment();
        if (!$editorDepartment) {
            return [];
        }

        return $editorDepartment->getEditors();
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

        if ($this->uid) {
            // inject methods as properties
            $data['imageURL'] = $this->getImageURL();

            if ($address = $this->formatPostalAddress()) {
                $data['unlDirectoryAddress'] = $address;
            }

            if ($this->shouldShowKnowledge()) {
                $knowledge = $this->getKnowledge();
                $data['knowledge'] = $knowledge->jsonSerialize();
            }
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
                    if ($var === 'mail' && in_array(self::BAD_SAP_MAIL_PLACEHOLDER, $value)) {
                        continue;
                    }

                    $this->$var = new UNL_Peoplefinder_Driver_LDAP_Multivalue($value);
                } else {
                    if ($var === 'mail' && $value == UNL_Peoplefinder_Record::BAD_SAP_MAIL_PLACEHOLDER) {
                        continue;
                    }

                    $this->$var = $value;
                }
            }
        }
    }

    public function jsonSerialize()
    {
        $data = $this->getPublicProperties();

        if (!$this->uid) {
            return $data;
        }

        //force uid to be a single value
        $data['uid'] = (string) $this->uid;

        // inject method as property
        $data['imageURL'] = $this->getImageURL();

        if ($address = $this->formatPostalAddress()) {
            $data['unlDirectoryAddress'] = $address;
        }

        if ($this->shouldShowKnowledge()) {
            $knowledge = $this->getKnowledge();
            $data['knowledge'] = $knowledge->jsonSerialize();
        }

        return $data;
    }

    public function __toString()
    {
        return (string)$this->uid;
    }
}
