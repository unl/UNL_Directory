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
		    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}

		$this->conn = $connec;
	}

	private function closeConnection() 
	{
		oci_close($this->conn);
	}

	public function query($statement, $params = array())
	{
		$this->connect();
		// Prepare the statement
		$stid = oci_parse($this->conn, $statement);
		if (!$stid) {
		    $e = oci_error($this->conn);
		    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		foreach ($params as $key => $value) {
			oci_bind_by_name($stid, ":" . $key, $value);
		}

		// Perform the logic of the query
		$r = oci_execute($stid);
		if (!$r) {
		    $e = oci_error($stid);
		    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}

		$arr = array();
		while (($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
		    $arr[] = $row;
		}
		$this->closeConnection();
		return $arr;
	}

	public function getRoles($uid)
    {
        $results = $this->query("SELECT * FROM campus_sync.appointments, campus_sync.campus_relationship campus_relationship1 WHERE
    campus_relationship1.biodemo_id = campus_sync.appointments.campus_relationship_biodemo_id 
    AND campus_relationship1.netid = :user_identification_string", 
        	array('user_identification_string' => $uid));

        $final_res = array();
        foreach($results as $result) {
        	$res = new \stdClass;
        	$res->unlRoleHROrgUnitNumber = $result['ORG_UNIT'];
        	$res->description = $result['TITLE'];
        	$final_res[] = $res;
        }	

        return new UNL_Peoplefinder_Person_Roles(['iterator' => new ArrayIterator($final_res)]);
    }

    public function getHROrgUnitNumberMatches($query, $affiliation = null)
    {
        // Michael: TODO: implement a query here to get all peoples via orgunit #
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
}