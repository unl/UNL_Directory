<?php
/**
 * Builds an advanced filter for searching for people records.
 *
 * PHP version 5
 *
 * @category  Default
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2007 Regents of the University of Nebraska
 * @license   https://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      https://peoplefinder.unl.edu/
 */
class UNL_Peoplefinder_Driver_LDAP_AdvancedFilter
{
    private $_filter;

    /**
     * Construct an advanced filter.
     *
     * @param string $sn       Surname 'Bieber'
     * @param string $cn       Common name 'Brett'
     * @param string $eppa     Primary affiliation: student/staff/faculty
     * @param string $operator LDAP operator to use & or |
     * @param bool   $wild     Append wildcard character to search terms?
     */
    public function __construct($sn='',$cn='',$eppa='',$operator='&', $wild=false)
    {
        // Advanced Query, search by LastName (sn) and First Name (cn), and affiliation
        if ($wild == false) {
            $wildcard = '';
        } else {
            $wildcard = '*';
        }
        $filterfields = array();
        $filterfields['sn'] = UNL_Peoplefinder_Driver_LDAP_Util::escape_filter_value($sn) . $wildcard;
        $filterfields['cn'] = UNL_Peoplefinder_Driver_LDAP_Util::escape_filter_value($cn) . $wildcard;
        $primaryAffiliation ='';
        // Determine the eduPersonPrimaryAffiliation to query by
        switch ($eppa) {
            case 'stu':
            case UNL_Peoplefinder::AFFILIATION_STUDENT:
                $primaryAffiliation = '(eduPersonPrimaryAffiliation=student)';
                break;
            case 'fs':
            case UNL_Peoplefinder::AFFILIATION_FACULTY:
            case UNL_Peoplefinder::AFFILIATION_STAFF:
                $primaryAffiliation = '(|(eduPersonPrimaryAffiliation=faculty)(eduPersonPrimaryAffiliation=staff))';
                break;
            default:
                $primaryAffiliation = '(eduPersonPrimaryAffiliation=*)';
                break;
        }
        $this->_filter = '('.$operator.$this->buildFilter($filterfields).$primaryAffiliation.')';
    }

    private function buildFilter(&$field_arr, $op='')
    {
        $filter='';
        foreach ($field_arr as $key=>$value) {
            if (is_array($value)) {
                $tmpvar = array();
                $tmpvar[$key]=$value;
                $filter .= buildFilter($tmpvar);
            } else {
                $filter .= "($key=$value)";
            }
        }
        if ($op!='') $filter = "({$op}{$filter})";
        return $filter;
    }

    public function __toString()
    {
        $this->_filter = UNL_Peoplefinder_Driver_LDAP_Util::wrapGlobalExclusions($this->_filter);
        return $this->_filter;
    }

}
