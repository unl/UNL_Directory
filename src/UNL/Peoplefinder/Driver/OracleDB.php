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

	private $conn;

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
		$results = $this->query("SELECT * FROM unl_appointments appointments, unl_biodemo biodemo WHERE
			biodemo.biodemo_id = appointments.biodemo_id 
			AND biodemo.netid = :user_identification_string 
			AND appointments.end_date >= '" . date('Y-m-d') . "'", 
			array(
				'user_identification_string' => $uid, 
			));

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
		$results = $this->query("SELECT DISTINCT biodemo.netid FROM unl_appointments appointments, unl_biodemo biodemo WHERE
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

		$results = $this->query("SELECT DISTINCT biodemo.netid FROM unl_appointments appointments, unl_biodemo biodemo WHERE
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

	function getUID($uid)
	{
		return false;
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
}