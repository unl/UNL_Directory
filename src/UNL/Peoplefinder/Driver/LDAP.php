<?php
class UNL_Peoplefinder_Driver_LDAP implements UNL_Peoplefinder_DriverInterface
{
    /**
     * Connection credentials
     *
     * @param string
     */
    static public $ldapServer = 'ldaps://ldap.unl.edu';

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
    static public $baseDN             = 'ou=people,dc=unl,dc=edu';
    static public $ldapTimeout        = 10;
    static public $cacheTimeout       = 28800; //8 hours

    /**
     * Attribute arrays
     * Attributes are the fields retrieved in an LDAP QUERY, limit this to
     * ONLY what is USED/DISPLAYED!
     */

    /**
     * List attributes are the attributes displayed in a list of results
     *
     * @var array
     */
    public $listAttributes = array(
        'cn',
        'eduPersonNickname',
        'eduPersonAffiliation',
        'eduPersonPrimaryAffiliation',
        'givenName',
        'postalAddress',
        'sn',
        'telephoneNumber',
        'title',
        'uid',
        'unlHRAddress',
        'unlHRPrimaryDepartment',
        'unlHROrgUnitNumber');

    /**
     * Details are for UID detail display only.
     * @var array
     */
    public $detailAttributes = array(
        'ou',
        'cn',
        'eduPersonAffiliation',
        'eduPersonNickname',
        'eduPersonPrimaryAffiliation',
        'eduPersonPrincipalName',
        'givenName',
        'displayName',
        'mail',
        'postalAddress',
        'sn',
        'telephoneNumber',
        'title',
        'uid',
        'unlHROrgUnitNumber',
        'unlHRPrimaryDepartment',
        'unlHRAddress',
        'unlSISClassLevel',
        'unlSISCollege',
        'unlSISLocalAddr1',
        'unlSISLocalAddr2',
        'unlSISLocalCity',
        'unlSISLocalState',
        'unlSISLocalZip',
        'unlSISPermAddr1',
        'unlSISPermAddr2',
        'unlSISPermCity',
        'unlSISPermState',
        'unlSISPermZip',
        'unlSISMajor',
        'unlEmailAlias');

    /** Connection details */
    public $connected = false;
    protected $linkID;

    /** Result Info */
    public $lastQuery;
    public $lastResult;

    public function __construct()
    {
    }

    /**
     * Binds to the LDAP directory using the bind credentials stored in
     * bindDN and bindPW
     *
     * @return bool
     */
    function bind()
    {
        if ($this->connected) {
            return true;
        }

        $server = self::$ldapServer;
        $this->linkID = ldap_connect($server);

        if (!ldap_set_option($this->linkID, LDAP_OPT_PROTOCOL_VERSION, 3)) {
            throw new Exception('Could not set LDAP_OPT_PROTOCOL_VERSION to 3', 500);
        }

        if (!$this->linkID) {
            throw new Exception('ldap_connect failed! Cound not connect to the LDAP directory.', 500);
        }

        if (!preg_match('/^ldaps:\/\//', $server)) {
            if (!ldap_start_tls($this->linkID)) {
                throw new Exception('Could not connect using StartTLS!', 500);
            }
        }

        $this->connected = ldap_bind($this->linkID, self::$bindDN, self::$bindPW);

        if (!$this->connected) {
            throw new Exception('ldap_bind failed! Could not connect to the LDAP directory.', 500);
        }

        return $this->connected;
    }

    /**
     * Disconnect from the ldap directory.
     *
     * @return bool
     */
    public function unbind()
    {
        $this->connected = false;

        if (is_resource($this->linkID)) {
            return ldap_unbind($this->linkID);
        }

        return true;
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
    public function query($filter, $attributes, $setResult = true, $dn = false)
    {
        if (!$dn) {
            $dn = self::$baseDN;
        }

        $cache = $this->getCache();

        $cache_key = $filter . '-' . implode(',', $attributes) . '-' . $setResult . '-' . $dn;
        //Our key will most likely exceed the memcached key length limit, so reduce it
        $cache_key = 'ldap-'.md5($cache_key);

        if ($result = $cache->get($cache_key)) {
            $result = unserialize($result);

            if ($setResult) {
                $this->lastResult = $this->caseInsensitiveSortLDAPResults($result);
            }

            return $result;
        }

        //Prevent cache stampede (return empty results until the first one finishes)
        $cache->set($cache_key, serialize([]));

        $limit = UNL_Peoplefinder::$resultLimit;
        $timeout = self::$ldapTimeout;

        $this->lastQuery = $filter;

        $tries = 1;
        $maxTries = 5;

        //Try several times in case of a connection error
        do {
            $retry = false;
            $this->bind();
            $sr = @ldap_search($this->linkID, $dn, $filter, $attributes, 0, $limit, $timeout);
            if (!$sr) {
                //log error
                $errno = ldap_errno($this->linkID);
                $error = ldap_error($this->linkID);

                $ldap_error_file = UNL_Peoplefinder::getTmpDir() . '/ldap_error.log';

                if (!file_exists($ldap_error_file)) {
                    touch($ldap_error_file);
                }

                $error_str = date('c') . ' - ' . $errno . ' - ' . $error . ' - ' . $filter . PHP_EOL;
                file_put_contents($ldap_error_file, $error_str, FILE_APPEND);

                if (3 == $errno) {
                    //Time limit exceeded, don't retry again and cache the empty result
                    break;
                }

                //Otherwise, retry.
                $this->unbind();
                $retry = $tries++ < $maxTries;

            }
        } while ($retry);

        if (!$sr) {
            return [];
        }

        $result = self::normalizeLdapEntries(@ldap_get_entries($this->linkID, $sr));

        if ($setResult) {
            $this->lastResult = $this->caseInsensitiveSortLDAPResults($result);
        }

        ldap_free_result($sr);

        $cache->set($cache_key, serialize($result));

        return $result;
    }

    /**
     * @return UNL_Peoplefinder_Cache
     */
    protected function getCache()
    {
        static $cache;

        if ($cache) {
            return $cache;
        }

        $cache = UNL_Peoplefinder_Cache::factory([
            'fast_lifetime' => self::$cacheTimeout,
        ]);

        return $cache;
    }

    protected function caseInsensitiveSortLDAPResults($result)
    {
        if (empty($result) || !is_array($result)) {
            return $result;
        }

        uasort($result, function($a, $b) {
            $nameA = '';
            if (isset($a['sn'])) {
                $nameA .= $a['sn'];
            }
            if (isset($a['givenname'])) {
                $nameA .= ', ' . $a['givenname'];
            }

            $nameB = '';
            if (isset($b['sn'])) {
                $nameB .= $b['sn'];
            }
            if (isset($b['givenname'])) {
                $nameB .= ', ' . $b['givenname'];
            }

            return strcasecmp($nameA, $nameB);
        });

        return $result;
    }


    /**
     * Get records which match the query exactly.
     *
     * @param string $query       Search string.
     * @param string $affiliation eduPersonAffiliation, eg staff/faculty/student
     *
     * @return array(UNL_Peoplefinder_Record)
     */
    public function getExactMatches($query, $affiliation = null)
    {
        if ($affiliation) {
            $filter = new UNL_Peoplefinder_Driver_LDAP_AffiliationFilter($query, $affiliation, '&', false);
        } else {
            $filter = new UNL_Peoplefinder_Driver_LDAP_StandardFilter($query, '&', false);
        }
        $this->query($filter->__toString(), $this->detailAttributes);
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
        return self::convertResultsToRecords($this->lastResult);
    }

    protected static function convertResultsToRecords($results)
    {
        $records = [];
        foreach ((array) $results as $entry) {
            $records[] = self::recordFromLDAPEntry($entry);
        }

        return $records;
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
    public function getAdvancedSearchMatches($query, $affiliation = null)
    {
        $filter = new UNL_Peoplefinder_Driver_LDAP_AdvancedFilter($query['sn'], $query['cn'], $affiliation, '&', true);
        $this->query($filter->__toString(), $this->detailAttributes);
        return $this->getRecordsFromResults();
    }

    /**
     * Find matches similar to the query given
     *
     * @param string $query            Search query
     * @param string $affiliation      eduPersonAffiliation, eg staff/faculty/student
     * @param array  $excluded_records Array of records to exclude.
     *
     * @return array(UNL_Peoplefinder_Record)
     */
    public function getLikeMatches($query, $affiliation = null, $excluded_records = array())
    {
        if ($affiliation) {
            $filter = new UNL_Peoplefinder_Driver_LDAP_AffiliationFilter($query, $affiliation, '&', true);
        } else {
            $filter = new UNL_Peoplefinder_Driver_LDAP_StandardFilter($query, '&', true);
        }
        // Exclude those displayed above
        $filter->excludeRecords($excluded_records);
        $this->query($filter->__toString(), $this->detailAttributes);
        return $this->getRecordsFromResults();
    }

    /**
     * Get an array of records which matche by the phone number.
     *
     * @param string $q           EG: 472-1598
     * @param string $affiliation eduPersonAffiliation, eg staff/faculty/student
     *
     * @return array(UNL_Peoplefinder_Record)
     */
    public function getPhoneMatches($query, $affiliation = null)
    {
        $filter = new UNL_Peoplefinder_Driver_LDAP_TelephoneFilter($query, $affiliation);
        $this->query($filter->__toString(), $this->detailAttributes);
        return $this->getRecordsFromResults();
    }

    /**
     * Get the ldap record for a specific uid eg:bbieber2
     *
     * @param string $uid The unique ID for the user you want to get.
     *
     * @return UNL_Peoplefinder_Record
     */
    public function getUID($uid)
    {
        $filter = new UNL_Peoplefinder_Driver_LDAP_UIDFilter($uid);
        $r = $this->query($filter->__toString(), $this->detailAttributes, false);
        if (empty($r)) {
            throw new Exception('Cannot find that UID.', 404);
        }
        return self::recordFromLDAPEntry(current($r));
    }

    public function getByNUID($nuid)
    {
        $filter = new UNL_Peoplefinder_Driver_LDAP_NUIDFilter($nuid);
        $r = $this->query($filter->__toString(), $this->detailAttributes, false);
        if (empty($r)) {
            throw new Exception('Cannot find that NUID.', 404);
        }
        return self::recordFromLDAPEntry(current($r));
    }

    public function getRoles($dn)
    {
        $results = $this->query('(&(objectClass=unlRole)(!(unlListingOrder=NL)))', [], false, $dn);
        // $results->sort('unlListingOrder');
        return new UNL_Peoplefinder_Person_Roles(['iterator' => new ArrayIterator($results)]);
    }

    protected static function normalizeLdapEntries(array $entries)
    {
        $entries = UNL_Peoplefinder_Driver_LDAP_Util::filterArrayByKeys($entries, 'is_int');
        $entry = current($entries);
        if ($entry instanceof UNL_Peoplefinder_Driver_LDAP_Entry) {
            return $entries;
        }

        $results = [];
        foreach ($entries as $entry) {
            $key = $entry['dn'];
            $results[$key] = new UNL_Peoplefinder_Driver_LDAP_Entry($entry);
        }

        return $results;
    }

    public static function recordFromLDAPEntry($entry)
    {
        $r = new UNL_Peoplefinder_Record();

        if (is_array($entry)) {
            $entry = new UNL_Peoplefinder_Driver_LDAP_Entry($entry);
        }

        if (!$entry instanceof UNL_Peoplefinder_Driver_LDAP_Entry) {
            throw new Exception('Cannot make a record from the given LDAP entry', 500);
        }

        foreach (array_keys(get_object_vars($r)) as $var) {
            if (isset($entry[$var])) {
                if ($var === 'mail' && $entry[$var] == UNL_Peoplefinder_Record::BAD_SAP_MAIL_PLACEHOLDER) {
                    continue;
                }

                $r->$var = $entry[$var];
            }
        }

        return $r;
    }

    public function getHRPrimaryDepartmentMatches($query, $affiliation = null)
    {
        $filter = new UNL_Peoplefinder_Driver_LDAP_HRPrimaryDepartmentFilter($query);
        $this->query($filter->__toString(), $this->detailAttributes);
        return $this->getRecordsFromResults();
    }

    public function getHROrgUnitNumberMatches($query, $affiliation = null)
    {
        $filter = new UNL_Peoplefinder_Driver_LDAP_HROrgUnitNumberFilter($query);
        $this->query($filter->__toString(), $this->listAttributes);

        return new UNL_Peoplefinder_Department_Personnel(new ArrayIterator($this->getRecordsFromResults()));
    }

    public function getHROrgUnitNumbersMatches($query, $affiliation = null)
    {
        $filter = new UNL_Peoplefinder_Driver_LDAP_HROrgUnitNumbersFilter($query);
        $this->query($filter->__toString(), $this->listAttributes);

        return new UNL_Peoplefinder_Department_Personnel(new ArrayIterator($this->getRecordsFromResults()));
    }

    public function __destruct()
    {
        $this->unbind();
    }
}
