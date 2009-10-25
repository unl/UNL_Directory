<?php
/**
 * Peoplefinder class for UNL's online directory.
 * 
 * PHP version 5
 * 
 * @category  Services
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2007 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://peoplefinder.unl.edu/
 */
define('UNL_PF_DISPLAY_LIMIT', 30);
define('UNL_PF_RESULT_LIMIT', 100);

require_once dirname(__FILE__).'/Peoplefinder/Record.php';

/**
 * Peoplefinder class for UNL's online directory.
 * 
 * PHP version 5
 * 
 * @category  Services
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2007 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://peoplefinder.unl.edu/
 */
class UNL_Peoplefinder
{
    /** Connection credentials */
    static public $ldapServer = 'ldap.unl.edu ldap-backup.unl.edu';
    /**
     * LDAP Connection bind distinguised name
     *
     * @var string
     * @ignore
     */
    static public $bindDN = 'uid=insertyouruidhere,ou=service,dc=unl,dc=edu';
    /**
     * LDAP connection password.
     *
     * @var string
     * @ignore
     */
    static public $bindPW             = 'putyourpasswordhere';
    static public $baseDN             = 'dc=unl,dc=edu';
    static public $ldapTimeout        = 10;
    static public $resultLimit        = UNL_PF_RESULT_LIMIT;
    static public $displayResultLimit = UNL_PF_DISPLAY_LIMIT;
    public $startTime;

    /** Connection details */
    public $connected = false;
    public $linkID;

    /** Result Info */
    public $lastQuery;
    public $lastResult;
    public $lastResultCount = 0;

    /**
     * Attribute arrays
     * Attributes are the fields retrieved in an LDAP QUERY, limit this to
     * ONLY what is USED/DISPLAYED!
     */
    //List attributes are the attributes displayed in a list of results
    public $listAttributes = array();

    // Details are for UID detail display only.
    public $detailAttributes = array();

    /**
     * Constructor for the object.
     */
    function __construct()
    {
        $listAttributes[] = 'cn';
        $listAttributes[] = 'eduPersonNickname';
        $listAttributes[] = 'eduPersonPrimaryAffiliation';
        $listAttributes[] = 'givenName';
        $listAttributes[] = 'sn';
        $listAttributes[] = 'telephoneNumber';
        $listAttributes[] = 'uid';
        $listAttributes[] = 'unlHRPrimaryDepartment';
        
        $detailAttributes[] = 'cn';
        $detailAttributes[] = 'eduPersonNickname';
        $detailAttributes[] = 'eduPersonPrimaryAffiliation';
        $detailAttributes[] = 'givenName';
        $detailAttributes[] = 'displayName';
        $detailAttributes[] = 'mail';
        $detailAttributes[] = 'postalAddress';
        $detailAttributes[] = 'sn';
        $detailAttributes[] = 'telephoneNumber';
        $detailAttributes[] = 'title';
        $detailAttributes[] = 'uid';
        $detailAttributes[] = 'unlHRPrimaryDepartment';
        $detailAttributes[] = 'unlHRAddress';
        $detailAttributes[] = 'unlSISClassLevel';
        $detailAttributes[] = 'unlSISCollege';
        $detailAttributes[] = 'unlSISLocalAddr1';
        $detailAttributes[] = 'unlSISLocalAddr2';
        $detailAttributes[] = 'unlSISLocalCity';
        $detailAttributes[] = 'unlSISLocalState';
        $detailAttributes[] = 'unlSISLocalZip';
        $detailAttributes[] = 'unlSISPermAddr1';
        $detailAttributes[] = 'unlSISPermAddr2';
        $detailAttributes[] = 'unlSISPermCity';
        $detailAttributes[] = 'unlSISPermState';
        $detailAttributes[] = 'unlSISPermZip';
        $detailAttributes[] = 'unlSISMajor';
        $detailAttributes[] = 'unlEmailAlias';
        
        $this->startTime = time();
    }
    
    /**
     * Binds to the LDAP directory using the bind credentials stored in
     * bindDN and bindPW
     *
     * @return bool
     */
    function bind()
    {
        $this->linkID = ldap_connect(UNL_Peoplefinder::$ldapServer);
        if ($this->linkID) {
            $this->connected = ldap_bind($this->linkID,
                                         UNL_Peoplefinder::$bindDN,
                                         UNL_Peoplefinder::$bindPW);
            if ($this->connected) {
                return $this->connected;
            }
        }
        throw new Exception('Cound not connect to LDAP directory.');
    }

    /**
     * Send a query to the ldap directory
     *
     * @param string $filter     LDAP filter (uid=blah)
     * @param array  $attributes attributes to return for the entries
     * @param bool   $setResult  whether or not to set the last result
     * 
     * @return mixed
     */
    function query($filter,$attributes,$setResult=true)
    {
        $this->bind();
        $this->lastQuery = $filter;
        $sr              = @ldap_search($this->linkID, 
                                        UNL_Peoplefinder::$baseDN,
                                        $filter,
                                        $attributes,
                                        0,
                                        UNL_Peoplefinder::$resultLimit,
                                        UNL_Peoplefinder::$ldapTimeout);
        if ($setResult) {
            $this->lastResultCount = @ldap_count_entries($this->linkID, $sr);
            $this->lastResult      = @ldap_get_entries($this->linkID, $sr);
            $this->unbind();
            //sort the results
            for ($i=0;$i<$this->lastResult['count'];$i++) {
                if (isset($this->lastResult[$i]['givenname'])) {
                    $name = $this->lastResult[$i]['sn'][0]
                          . ', '
                          . $this->lastResult[$i]['givenname'][0];
                } else {
                    $name = $this->lastResult[$i]['sn'][0];
                }
                $this->lastResult[$i]['insensitiveName'] = strtoupper($name);
            }
            @reset($this->lastResult);
            $this->lastResult = @$this->array_csort($this->lastResult,
                                                    'insensitiveName',
                                                    SORT_ASC);
            return $this->lastResult;
        } else {
            $result = ldap_get_entries($this->linkID, $sr);
            $this->unbind();
            return $result;
        }
    }
    
    /**
     * Disconnect from the ldap directory.
     *
     * @return unknown
     */
    function unbind()
    {
        $this->connected = false;
        return ldap_unbind($this->linkID);
    }

    /**
     * Display the standard search form.
     *
     * @return void
     */
    function displayStandardForm()
    {
        include 'standardForm.php';
    }

    /**
     * Display the advanced form.
     *
     * @return void
     */
    function displayAdvancedForm()
    {
        include 'advancedForm.php';
    }
    
    /**
     * Get records which match the query exactly.
     *
     * @param string $q Search string.
     * 
     * @return array(UNL_Peoplefinder_Record)
     */
    public function getExactMatches($q)
    {
        include_once dirname(__FILE__).'/Peoplefinder/StandardFilter.php';
        $filter = new UNL_Peoplefinder_StandardFilter($q, '&', false);
        $this->query($filter->__toString(), $this->listAttributes);
        return $this->getRecordsFromResults();
    }
    
    /**
     * Returns an array of UNL_Peoplefinder_Record objects from the ldap
     * query result.
     *
     * @return array(UNL_Peoplefinder_Record)
     */
    protected function getRecordsFromResults()
    {
        $r = array();
        if ($this->lastResultCount > 0) {
            for ($i = 0; $i < $this->lastResultCount; $i++) {
                $r[] = UNL_Peoplefinder_Record::fromLDAPEntry($this->lastResult[$i]);
            }
        }
        return $r;
    }
    
    /**
     * Get results for an advanced/detailed search.
     *
     * @param string $sn   Surname/last name
     * @param string $cn   Common name/first name
     * @param string $eppa Primary Affiliation
     * 
     * @return array(UNL_Peoplefinder_Record)
     */
    public function getAdvancedSearchMatches($sn, $cn, $eppa)
    {
        include_once dirname(__FILE__).'/Peoplefinder/AdvancedFilter.php';
        $filter = new UNL_Peoplefinder_AdvancedFilter($sn, $cn, $eppa, '&', true);
        $this->query($filter->__toString(), $this->listAttributes);
        return $this->getRecordsFromResults();
    }
    
    /**
     * Find matches similar to the query given
     *
     * @param string $q                Search query
     * @param array  $excluded_records Array of records to exclude.
     * 
     * @return array(UNL_Peoplefinder_Record)
     */
    public function getLikeMatches($q, $excluded_records = array())
    {
        include_once dirname(__FILE__).'/Peoplefinder/StandardFilter.php';
        // Build filter excluding those displayed above
        $filter = new UNL_Peoplefinder_StandardFilter($q, '&', true);
        $filter->excludeRecords($excluded_records);
        $this->query($filter->__toString(), $this->listAttributes);
        return $this->getRecordsFromResults();
    }
    
    /**
     * Get an array of records which matche by the phone number.
     *
     * @param string $q EG: 472-1598
     * 
     * @return array(UNL_Peoplefinder_Record)
     */
    public function getPhoneMatches($q)
    {
        include_once dirname(__FILE__).'/Peoplefinder/TelephoneFilter.php';
        $filter = new UNL_Peoplefinder_TelephoneFilter($q);
        $this->query($filter->__toString(), $this->listAttributes);
        return $this->getRecordsFromResults();
    }

    /**
     * Get the ldap record for a specific uid eg:bbieber2
     *
     * @param string $uid The unique ID for the user you want to get.
     * 
     * @return UNL_Peoplefinder_Record
     */
    function getUID($uid)
    {
        $r = $this->query("(&(uid=$uid))", $this->detailAttributes, false);
        if (isset($r[0])) {
            return UNL_Peoplefinder_Record::fromLDAPEntry($r[0]);
        } else {
            header('HTTP/1.0 404 Not Found');
            throw new Exception('Cannot find that UID.');
        }
    }
    
    /**
     * sort a multidimensional array
     *
     * @return array
     */
    function array_csort()
    {
        $args   = func_get_args();
        $marray = array_shift($args);
        
        $msortline = "return(array_multisort(";
        foreach ($args as $arg) {
            @$i++;
            if (is_string($arg)) {
                foreach ($marray as $row) {
                    $sortarr[$i][] = $row[$arg];
                }
            } else {
                $sortarr[$i] = $arg;
            }
            $msortline .= "\$sortarr[".$i."],";
        }
        $msortline .= "\$marray));";
        
        eval($msortline);
        return $marray;
    }
    
    /**
     * Displays the instructions for using peoplefinder.
     *
     * @param bool $adv Show advanced instructions or default instructions.
     * 
     * @return void
     */
    function displayInstructions($adv=false)
    {
        echo '<div style="padding-top:10px;width:270px;" id="instructions">';
        if ($adv) {
            echo 'Enter in as much of the first and/or last name you know, ' .
                 'you can also select a primary affiliation to refine your search.';
        } else {
            echo 'Enter in as much of the name as you know, first and/or last '.
                 'name in any order.<br /><br />Reverse telephone number lookup: '.
                 'enter last three or more digits.';
        }
        echo '</div>';
    }

}
