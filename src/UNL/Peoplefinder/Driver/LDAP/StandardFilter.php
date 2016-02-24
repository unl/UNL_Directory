<?php
/**
 * Class builds a pretty good LDAP filter for searching for people.
 *
 * <code>
 * <?php
 * $filter = new UNL_Peoplefinder_Driver_LDAP_StandardFilter('brett bieber','|',false);
 * echo $filter;
 * ?>
 * (|(|(mail=brett)(cn=brett)(givenName=brett)(sn=brett)(eduPersonNickname=brett)(sn=brett-*)(sn=*brett))(|(mail=bieber)(cn=bieber)(givenName=bieber)(sn=bieber)(eduPersonNickname=bieber)(sn=bieber-*)(sn=*bieber))(|(mail=brett bieber)(cn=brett bieber)(givenName=brett bieber)(sn=brett bieber)(eduPersonNickname=brett bieber)(sn=brett bieber-*)(sn=*brett bieber)))
 * </code>
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
class UNL_Peoplefinder_Driver_LDAP_StandardFilter
{
    protected $_filter;

    protected $_excludeRecords = array();

    public static $searchFields = array(
            'mail',
            'cn',
            'givenName',
            'sn',
            'eduPersonNickname'
        );

    /**
     * Construct a standard filter.
     *
     * @param string $inquery  Search string 'bieber, brett' etc
     * @param string $operator LDAP operator to use & or |
     * @param bool   $wild     Append wildcard to search terms?
     */
    function __construct($inquery, $operator = '&', $wild = false)
    {
        if (!empty($inquery)) {
            //ignore grouping and wildcard characters
            $inquery = str_replace(array('"', ',', '*'), '', $inquery);

            //escape query
            $inquery = UNL_Peoplefinder_Driver_LDAP_Util::escape_filter_value($inquery);

            //put the query into an array of words
            $query = preg_split('/\s+/', $inquery, 4);
            if (count($query) > 1) {
                //add the original multi-word query as a search term
                $query[] = $inquery;
            }

            if ($operator != '&') {
                $operator = '|';
            }

            //create our filter
            //search for the string parts
            $filter = "($operator";
            foreach ($query as $arg) {
                //determine if a wildcard should be used
                if ($wild) {
                    $arg = "*$arg*";
                }

                $filter .= '(|';
                foreach (self::$searchFields as $field) {
                    $filter .= "($field=$arg)";
                }

                //find hyphenated and multi-word surnames in the exact matches query
                if (!$wild) {
                    $filter .= "(sn=$arg-*)(sn=*$arg)";
                }

                $filter .= ")";
            }
            $filter .= ")";
        }
        $this->_filter = $filter;
    }

    /**
     * Allows you to exclude specific records from a result set.
     *
     * @param array(string|UNL_Peoplefinder_Record) $records Records to exclude, can be just the uids or record objects
     */
    function excludeRecords($records = array())
    {
        if (count($this->_excludeRecords)) {
            $this->_excludeRecords = array_merge($this->_excludeRecords, $records);
        } else {
            $this->_excludeRecords = $records;
        }
    }

    protected function addExcludedRecords()
    {
        if (count($this->_excludeRecords)) {
            $excludeFilter = '';
            foreach ($this->_excludeRecords as $record) {
                $excludeFilter .= '(uid='.$record->__toString().')';
            }
            $this->_filter = '(&'.$this->_filter.'(!(|'.$excludeFilter.')))';
        }
    }

    function __toString()
    {
        $this->addExcludedRecords();
        $this->_filter = UNL_Peoplefinder_Driver_LDAP_Util::wrapGlobalExclusions($this->_filter);
        return $this->_filter;
    }
}
