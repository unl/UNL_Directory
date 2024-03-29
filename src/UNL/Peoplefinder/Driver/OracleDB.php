<?php
class UNL_Peoplefinder_Driver_OracleDB implements UNL_Peoplefinder_DriverInterface
{
    /**
     * Connection credentials
     */
    public static $connection_username;
    public static $connection_password;
    public static $connection_host;
    public static $connection_port;
    public static $connection_service;
    public static $email_priority_list;

    private $conn;
    private $resetCache = FALSE;

    /** Sample Data Set in Config File */
    public static $sampleFixLDAPEntries;

    private function connect() 
    {
        $connec = oci_connect(self::$connection_username, self::$connection_password, 
            self::$connection_host . ':' . self::$connection_port . '/' . self::$connection_service);
        if (!$connec) {
            $e = oci_error();
            throw new Exception($e['message'], 500);
        }

        $this->conn = $connec;
    }

    private function closeConnection() 
    {
        oci_close($this->conn);
    }

    public function query($statement, $params = array())
    {
        $cache = $this->getCache();
        
        //Use md5 so we don't exceed the memcached key length
        $cache_key = 'oracle_query_' .  md5($statement) . '--' . md5(serialize($params));

        if ($this->resetCache === TRUE) {
            $cache->remove($cache_key);
        }

        if ($result = $cache->get($cache_key)) {
            $result = unserialize($result);
            return $result;
        }

        $cache->set($cache_key, serialize([]));
        
        if (empty($this->conn)) {
            $this->connect();
        }
        
        // Prepare the statement
        $stid = oci_parse($this->conn, $statement);
        if (!$stid) {
            $e = oci_error($this->conn);
            throw new Exception($e['message'], 500);
        }
        foreach ($params as $key => $value) {
            oci_bind_by_name($stid, ":" . $key, $params[$key]);
        }

        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            throw new Exception($e['message'], 500);
        }

        $arr = array();
        while (($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
            $arr[] = $row;
        }

        $cache->set($cache_key, serialize($arr));
        
        return $arr;
    }

    public function getRoles($uid)
    {
        $results = $this->query("SELECT * FROM unl_appointments_listed_unca_00 appointments, unl_biodemo biodemo WHERE
            biodemo.biodemo_id = appointments.biodemo_id 
            AND biodemo.netid = :user_identification_string 
            AND appointments.end_date >= '" . date('Y-m-d') . "'
            ORDER BY appointments.directory_order ASC",
            array(
                'user_identification_string' => $uid, 
            ));

        // Remove duplicates
        $results = array_map("unserialize", array_unique(array_map("serialize", $results)));

        $final_res = array();

        $title_mod_map = array(
            '1' => 'Acting',
            '2' => 'Interim',
            '3' => 'Adjunct', 
            '4' => 'Courtesy',
            '5' => 'Visiting',
            '6' => 'Emeritus',
            '7' => 'Trainee',
            'K' => 'Continuous',
            'L' => 'Special',
            'M' => 'Academic Administrative',
            'N' => 'Administrative',
            'T' => 'Tenure'
        );

        foreach($results as $result) {
            $res = new \stdClass;
            $res->unlRoleHROrgUnitNumber = $result['ORG_UNIT'];
            $res->description = $result['TITLE'];
            if (!empty($result['TITLE_MODIFIER']) && array_key_exists((string)$result['TITLE_MODIFIER'], $title_mod_map)) {
                $res->description = $title_mod_map[(string)$result['TITLE_MODIFIER']] . ' ' . $result['TITLE'];
            }
            
            $final_res[] = $res;
        }
        return new UNL_Peoplefinder_Person_Roles(['iterator' => new ArrayIterator($final_res)]);
    }

    public function getHROrgUnitNumberMatches($query, $affiliation = null)
    {
        $results = $this->query("SELECT DISTINCT biodemo.netid FROM unl_appointments_listed_unca_00 appointments, unl_biodemo biodemo WHERE
            biodemo.biodemo_id = appointments.biodemo_id 
            AND appointments.org_unit = :org_unit
            AND appointments.end_date >= '" . date('Y-m-d') . "'", 
            array(
                'org_unit' => $query, 
            ));

        $uids = array();
        foreach ($results as $result) {
            $uids[] = $result['NETID'];
        }

        return $uids;
    }

    public function getHROrgUnitNumbersMatches($query, $affiliation = null)
    {
        # construct a binding list and array
        $binding_array = array();
        $binding_list = array();
        for ($i = 0; $i < count($query); $i++) {
            $key = ":org_unit_" . $i;
            $binding_list[] = $key;
            $key = substr($key, 1);
            $binding_array[$key] = $query[$i];
        }

        $results = $this->query("SELECT DISTINCT biodemo.netid FROM unl_appointments_listed_unca_00 appointments, unl_biodemo biodemo WHERE
            biodemo.biodemo_id = appointments.biodemo_id 
            AND appointments.org_unit IN (" . implode(', ', $binding_list) . ")
            AND appointments.end_date >= '" . date('Y-m-d') . "'", 
            $binding_array);

        $uids = array();
        foreach ($results as $result) {
            $uids[] = $result['NETID'];
        }

        return $uids;
    }

    function getAdvancedSearchMatches($query, $affiliation = null)
    {
        return array();
    }

    function getExactMatches($query, $affiliation = null)
    {
        return array();
    }

    function getLikeMatches($query, $affiliation = null)
    {
        return array();
    }

    function getPhoneMatches($query, $affiliation = null)
    {
        return array();
    }
    
    public function getUID($uid)
    {
        // TODO: Implement getUID() method.
    }

    /**
     * This function will attempt to fix LDAP entries with Oracle sourced attributes, such as `mail`
     * The `mail` attribute is not accurate in AD
     * 
     * @param array $entries
     * @return array
     * @throws Exception
     */
    public function fixLDAPEntries(array $entries)
    {
        if (empty($entries)) {
            return $entries;
        }
        
        $uids = [];
        $binding_array = array();
        $binding_list = array();
        $i=0;
        
        // Loop through each entry and build a binding array for the SQL query
        // Also create a mapping array so that we can stitch the Oracle and LDAP results together
        foreach ($entries as $key=>$entry) {
            if (!isset($entry['uid']) || empty($entry['uid'])) {
                // We have no UID to reference (odd... perhaps not a person object?)
                continue;
            }
            // Create a mapping to stitch the results back together
            $uids[$entry['uid'][0]] = $key;
            
            // Create binding arrays for the SQL query
            $key = ":uid_" . $i;
            $binding_list[] = $key;
            $key = substr($key, 1);
            $binding_array[$key] = $entry['uid'][0];
            $i++;
        }

        if (count($uids) == 1 && isset($uids[UNL_Peoplefinder::$sampleUID])) {
            $results = self::$sampleFixLDAPEntries;
        } else {
            // UNL_EMAILS_00.TYPE = 'USERINFO' is the work email address that we want
            $query = "
            SELECT UNL_BIODEMO.NETID, UNL_BIODEMO.BIODEMO_ID as BIODEMO_ID, UNL_BIODEMO.NU_FERPA, UNL_EMAILS_00.EMAIL as MAIL
            FROM UNL_BIODEMO
            LEFT JOIN UNL_EMAILS_00 ON UNL_BIODEMO.BIODEMO_ID = UNL_EMAILS_00.BIODEMO_ID AND UNL_EMAILS_00.TYPE = 'USERINFO'
                        WHERE UNL_BIODEMO.NETID IN (" . implode(', ', $binding_list) . ")
            ";
            $results = $this->query($query, $binding_array);
        }

        $biodemo_id_values = array();
        $biodemo_id_binding = array();
        
        // Now stitch everything back together
        foreach ($results as $row) {
            $key = $uids[$row['NETID']];
            if (!empty($row['MAIL'])) {

                // if we have not stored an email yet the just store it
                if (!isset($entries[$key]['mail']) || empty($entries[$key]['mail'])) {
                    $value = new UNL_Peoplefinder_Driver_LDAP_Multivalue(array(
                        strtolower($row['MAIL'])
                    ));
                    
                    $entries[$key]['mail'] = $value;
                } else {
                    // We only want to swap the email for a different one if the email domain is more preferred
                    $newEmailDomain = substr(
                        strtolower($row['MAIL']),
                        strpos($row['MAIL'], '@') + 1
                    );
                    $currentEmailDomain = substr(
                        strtolower($entries[$key]['mail']),
                        strpos($entries[$key]['mail'], '@') + 1
                    );

                    // Initialize it to be bigger than the array
                    $newEmailRanking = count(self::$email_priority_list) + 1;
                    $currentEmailRanking = count(self::$email_priority_list) + 1;

                    // Finds the domain in the priority list
                    $newEmailIndex = array_search($newEmailDomain, self::$email_priority_list);
                    $currentEmailIndex = array_search($currentEmailDomain, self::$email_priority_list);

                    if ($newEmailIndex !== false) {
                        $newEmailRanking = $newEmailIndex;
                    }
                    if ($currentEmailIndex !== false) {
                        $currentEmailRanking = $currentEmailIndex;
                    }

                    // Ranking lower is better
                    if ($newEmailRanking < $currentEmailRanking) {
                        $value = new UNL_Peoplefinder_Driver_LDAP_Multivalue(array(
                            strtolower($row['MAIL'])
                        ));

                        $entries[$key]['mail'] = $value;
                    }
                }

            }

            //prepares the BIODEMO_IDs to be used in the affiation query
            if (!empty($row['BIODEMO_ID'])) {
                $biodemo_key = ":uid_" . count($biodemo_id_binding);
                $biodemo_id_binding[] = $biodemo_key;
                $biodemo_key = substr($biodemo_key, 1);
                $biodemo_id_values[$biodemo_key] = $row['BIODEMO_ID'];
            }

            // Remove the student affiliation if the privacy flag is set
            if (!empty($row['NU_FERPA']) && isset($entries[$key]['edupersonaffiliation'])) {
                $value = new UNL_Peoplefinder_Driver_LDAP_Multivalue(
                    array_diff(iterator_to_array($entries[$key]['edupersonaffiliation']), array(UNL_Peoplefinder::AFFILIATION_STUDENT))
                );

                $entries[$key]['edupersonaffiliation'] = $value;
            }
        }

        //if we have BIODEMO_IDs we can then search for their affiliations
        if (!empty($biodemo_id_values)) {

            $affiliation_query = "
                SELECT UNL_AFFILIATIONS_02.EPA_AFFILIATION, UNL_AFFILIATIONS_02.NETID
                FROM UNL_AFFILIATIONS_02 
                WHERE UNL_AFFILIATIONS_02.BIODEMO_ID IN (" . implode(', ', $biodemo_id_binding) . ")
            ";
            $affiliation_results = $this->query($affiliation_query, $biodemo_id_values);     

            //aggragating the results based on NETID
            $agg_results = array();
            foreach ($affiliation_results as $row) {
                if (is_null($row['EPA_AFFILIATION'])) {
                    continue;
                }
                if (isset($agg_results[$row['NETID']]) && !empty($agg_results[$row['NETID']])) {
                    $agg_results[$row['NETID']] .= ";";
                }
                if (!isset($agg_results[$row['NETID']])) {
                    $agg_results[$row['NETID']] = "";
                }
                $agg_results[$row['NETID']] .= $row['EPA_AFFILIATION'];
            }

            // Use the affiliations from Oracle. There is additional processing being done on the UNL_AFFILIATIONS_00
            // view before it reaches us to remove affiliations associated with "Directory Order=NL" appointments.
            foreach ($agg_results as $aff_NETID => $aff_AFFILIATION) {
                $key = $uids[$aff_NETID];
                $affiliations = explode(';', $aff_AFFILIATION);
                $affiliations = array_unique(array_map('strtolower', $affiliations));
                $value = new UNL_Peoplefinder_Driver_LDAP_Multivalue($affiliations);
                $entries[$key]['edupersonaffiliation'] = $value;
            }
        }

        return $entries;
    }

    function getHRPrimaryDepartmentMatches($query, $affiliation = null)
    {
        return array();
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
            //make it the same timeout as the LDAP driver
            'fast_lifetime' => UNL_Peoplefinder_Driver_LDAP::$cacheTimeout,
        ]);

        return $cache;
    }

    public function resetCache() {
        $this->resetCache = TRUE;
    }
}
