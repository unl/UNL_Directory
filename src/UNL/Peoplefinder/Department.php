<?php
require_once 'UNL/LDAP.php';
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
    
    protected $_ldap;

    protected $_results;
    
    protected $_xml;
    
    public $options = array();
    
    /**
     * construct a department
     *
     * @param string $name Name of the department
     */
    function __construct($options = array())
    {
        if (!isset($options['d'])) {
            throw new Exception('No department name! Pass as the d array value');
        }
        $this->options = $options + $this->options;
        $this->name = $options['d'];
        $this->_xml = new SimpleXMLElement(file_get_contents(UNL_Peoplefinder::getDataDir().'/hr_tree.xml'));
        $results = $this->_xml->xpath('//attribute[@name="org_unit"][@value="50000003"]/..//attribute[@name="name"][@value="'.$this->name.'"]/..');

        if (!isset($results[0])) {
            throw new Exception('Invalid department name.', 404);
        }

        foreach ($results[0] as $attribute) {
            if (isset($attribute['name'])) {
                $this->{$attribute['name']} = (string)$attribute['value'];
            }
        }
    }
    
    /**
     * Retrieves people records from the LDAP directory
     *
     * @return resource
     */
    function getLDAPResults()
    {
        if (!isset($this->_results)) {
            $pf = new UNL_Peoplefinder($this->options);
            $this->_results = new ArrayIterator($pf->getHRPrimaryDepartmentMatches($this->name));
        }
        return $this->_results;
    }
    
    /**
     * returns the count of employees
     *
     * @return int
     */
    function count()
    {
        return count($this->getLDAPResults());
    }
    
    function rewind()
    {
        $this->getLDAPResults()->rewind();
    }
    
    /**
     * Get the current record in the iteration
     *
     * @return UNL_Peoplefinder_Record
     */
    function current()
    {
        return $this->getLDAPResults()->current();
    }
    
    function key()
    {
        return $this->getLDAPResults()->key();
    }
    
    function next()
    {
        $this->getLDAPResults()->next();
    }
    
    function valid()
    {
        return $this->getLDAPResults()->valid();
    }
    
    function hasChildren()
    {
        $results = $this->_xml->xpath('//attribute[@name="org_unit"][@value="50000003"]/..//attribute[@name="name"][@value="'.$this->name.'"]/../branch');
        return count($results)?true:false;
    }
    
    function getChildren()
    {
        $children = array();
        $results = $this->_xml->xpath('//attribute[@name="org_unit"][@value="50000003"]/..//attribute[@name="name"][@value="'.$this->name.'"]/../branch');
        foreach ($results as $result) {
            foreach ($result[0] as $attribute) {
                if (isset($attribute['name'])
                    && $attribute['name']=='name') {
                    $children[] = (string)$attribute['value'];
                    break;
                }
            }
        }
        asort($children);
        return $children;
    }

    /**
     * Retrieve an official SAP Org entry by ID
     * 
     * @param int $id ID, such as 5000XXXX
     */
    public static function getById($id, $options = array())
    {
        $xml = new SimpleXMLElement(file_get_contents(UNL_Peoplefinder::getDataDir().'/hr_tree.xml'));
        $results = $xml->xpath('//attribute[@name="org_unit"][@value="50000003"]/..//attribute[@name="org_unit"][@value='.$id.']/..');
        if (!$results) {
            return false;
        }
        $options['d'] = (string)$results[0][0]->attribute['value'];
        return new self($options);
    }
}
