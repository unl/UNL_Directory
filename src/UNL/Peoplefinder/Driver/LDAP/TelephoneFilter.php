<?php
/**
 * Builds a simple telephone filter for searching for records.
 *
 * PHP version 5
 *
 * @category  Default
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2007 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://peoplefinder.unl.edu/
 */
class UNL_Peoplefinder_Driver_LDAP_TelephoneFilter
{
    private $_filter;

    protected $affiliation;

    public function __construct($q, $affiliation = null)
    {
        if (!empty($q)) {
            $q = preg_replace('/\D/', '', $q);
            $this->_filter = '(telephoneNumber=*'.$q.')';
        }

        switch ($affiliation) {
            case 'faculty':
            case 'staff':
            case 'student':
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
