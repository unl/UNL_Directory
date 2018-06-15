<?php
/**
 * Builds a simple HR primary department filter for records.
 *
 * PHP version 5
 *
 * @category  Default
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2010 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://peoplefinder.unl.edu/
 */
class UNL_Peoplefinder_Driver_LDAP_HROrgUnitNumbersFilter
{
    private $_filter;

    /**
     * Create a filter for HR primary department filtering.
     *
     * @param string $hrPrimaryDepartment HR primary department eg:College of Engineering
     */
    function __construct($orgUnits)
    {
        if (!is_array($orgUnits)) {
            throw new Exception('Must set primary department.');
        }

        $this->_filter = '(&(objectClass=person)(|(departmentNumber=';
        $this->_filter .= implode(')(departmentNumber=', $orgUnits);
        $this->_filter .= ')))';
    }

    function __toString()
    {
        $this->_filter = UNL_Peoplefinder_Driver_LDAP_Util::wrapGlobalExclusions($this->_filter);
        return $this->_filter;
    }
}
