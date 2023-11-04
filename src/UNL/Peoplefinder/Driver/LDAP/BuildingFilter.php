<?php
/**
 * Builds a simple telephone filter for searching for records.
 *
 * PHP version 5
 *
 * @category  Default
 * @package   UNL_Peoplefinder
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2022 Regents of the University of Nebraska
 * @license   https://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      https://peoplefinder.unl.edu/
 */
class UNL_Peoplefinder_Driver_LDAP_BuildingFilter
{
    private $_filter;

    protected $affiliation;

    public function __construct($q, $affiliation = null)
    {
        if (!empty($q)) {
            $q = preg_replace('/\D/', '', $q);
            $this->_filter = '(postalAddress='.$q.'*)';
        }

        switch ($affiliation) {
            case UNL_Peoplefinder::AFFILIATION_FACULTY:
            case UNL_Peoplefinder::AFFILIATION_STAFF:
            case UNL_Peoplefinder::AFFILIATION_STUDENT:
                $this->affiliation = $affiliation;
                break;
        }
    }

    public function __toString()
    {
        if ($this->affiliation) {
            $this->_filter = '(&'.$this->_filter.'(eduPersonAffiliation='.$this->affiliation.'))';
        }
        $this->_filter = UNL_Peoplefinder_Driver_LDAP_Util::wrapGlobalExclusions($this->_filter);
        return $this->_filter;
    }
}
