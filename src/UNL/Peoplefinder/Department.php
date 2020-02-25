<?php
/**
 * Class which represents a department.
 *
 * The departments are pulled from an xml file, generated from SAP data.
 * hr_tree.xml using TreeML schema
 *
 * The object also allows iterating over all the members of the department.
 */
class UNL_Peoplefinder_Department implements Countable, Iterator
{
    /**
     * Name of the organization
     *
     * @var string
     */
    public $name;

    /**
     * The organizational unit number.
     *
     * @var number
     */
    public $org_unit;

    /**
     * Organization abbreviation
     *
     * @var string
     */
    public $org_abbr;

    /**
     * The organizational unit number of department's primary business center.
     *
     * @var number
     */
    public $bc_org_unit;

    /**
     * The name of department's primary business center.
     *
     * @var string
     */
    public $bc_name;

    /**
     * Building the department main office is in.
     *
     * @var string
     */
    public $building;

    /**
     * Room
     *
     * @var string
     */
    public $room;

    /**
     * City
     *
     * @var string
     */
    public $city;

    /**
     * State
     *
     * @var string
     */
    public $state;

    /**
     * zip code
     *
     * @var string
     */
    public $postal_code;

    protected $_results;

    /**
     * SimpleXMLElement of the HR Tree file
     *
     * @var SimpleXMLElement
     */
    protected static $_xml;

    /**
     * @var string Prefix added to all xpath queries
     */
    protected static $_xpath_base = '//attribute[@name="org_unit"][@value="50000003"]/..';

    public $options = [];

    /**
     * construct a department
     *
     * @param string $name Name of the department
     */
    public function __construct($options = [])
    {
        if (!(
                isset($options['d'])
                || isset($options['org_unit'])
                || isset($options['xml'])
              )
           ) {
            throw new Exception('No department name or org_unit! Pass as the d or org_unit value.');
        }
        $this->options = $options + $this->options;

        $xml = self::getXML();

        $result = false;

        if (isset($options['xml'])) {
            $result = $options['xml'];
        } elseif (isset($options['org_unit'])) {
            $result = self::getXMLById($options['org_unit']);
        } elseif (isset($options['d'])) {
            $result = self::getXMLByName($options['d']);
        }

        if (!$result) {
            throw new Exception('Invalid department', 404);
        }

        $this->setFromSimpleXMLElement($result);
    }

    public function setFromSimpleXMLElement(SimpleXMLElement $result)
    {
        foreach ($result as $attribute) {
            if (isset($attribute['name'])) {
                $this->{$attribute['name']} = (string)$attribute['value'];
            }
        }
    }

    /**
     * Get the XML for the HR Tree
     *
     * @return SimpleXMLElement
     */
    protected static function getXML()
    {
        if (!isset(self::$_xml)) {
            self::$_xml = new SimpleXMLElement(file_get_contents(UNL_Peoplefinder::getDataDir().'/hr_tree.xml'));
        }
        return self::$_xml;
    }

    /**
     * Retrieves people records from the LDAP directory
     *
     * @return resource
     */
    public function getLDAPResults()
    {
        if (!isset($this->_results)) {
            $prevResultLimit = UNL_Peoplefinder::$resultLimit;
            UNL_Peoplefinder::$resultLimit = 500;
            $this->_results = UNL_Peoplefinder::getInstance()->getHROrgUnitNumberMatches($this->org_unit);
            UNL_Peoplefinder::$resultLimit = $prevResultLimit;
        }
        return $this->_results;
    }

    /**
     * returns the count of employees
     *
     * @return int
     */
    public function count()
    {
        return count($this->getLDAPResults());
    }

    public function rewind()
    {
        $this->getLDAPResults()->rewind();
    }

    /**
     * Get the current record in the iteration
     *
     * @return UNL_Peoplefinder_Record
     */
    public function current()
    {
        return $this->getLDAPResults()->current();
    }

    public function key()
    {
        return $this->getLDAPResults()->key();
    }

    public function next()
    {
        $this->getLDAPResults()->next();
    }

    public function valid()
    {
        return $this->getLDAPResults()->valid();
    }

    public function hasChildren()
    {
        $results = self::getXML()->xpath(self::$_xpath_base.'//attribute[@name="org_unit"][@value="'.$this->org_unit.'"]/../branch');
        return count($results)?true:false;
    }

    public function getChildren()
    {
        $children = [];
        $results = self::getXML()->xpath(self::$_xpath_base.'//attribute[@name="org_unit"][@value="'.$this->org_unit.'"]/../branch');
        foreach ($results as $result) {
            foreach ($result[0] as $attribute) {
                if (isset($attribute['name'])
                    && $attribute['name']=='org_unit') {
                    $children[] = self::getById((string)$attribute['value']);
                    break;
                }
            }
        }

        return $children;
    }

    /**
     * Retrieve an official SAP Org entry by ID
     *
     * @param int $id ID, such as 5000XXXX
     */
    public static function getById($id, $options = [])
    {
        if ($result = self::getXMLById($id)) {
            $options['xml'] = $result;
            return new self($options);
        }
        return $result;
    }

    public static function getXMLByName($name)
    {
        $quoted = preg_replace('/([\'\"\?])/', '\\$1', $name);
        $xpath  = self::$_xpath_base.'//attribute[@name="name"][@value="'.$quoted.'"]/..';
        return self::getXMLResult($xpath);
    }

    public static function getXMLById($id)
    {
        $xpath = self::$_xpath_base.'//attribute[@name="org_unit"][@value='.$id.']/..';
        return self::getXMLResult($xpath);
    }

    public static function setXPathBase($base)
    {
        self::$_xpath_base = $base;
    }

    protected static function getXMLResult($xpath)
    {
        $xml     = self::getXML();
        $results = $xml->xpath($xpath);
        if (!$results) {
            return false;
        }
        return $results[0];
    }
}
