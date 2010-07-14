<?php
/**
 * This package houses and returns spatial data for the UNL Campus.
 * 
 * Initially we only have latitude and longitude for a few buildings on campus.
 * 
 * 
 * @author Brett Bieber
 * @package UNL_Geography_SpatialData_Campus
 */
require_once 'UNL/Common/Building.php';

class UNL_Geography_SpatialData_Campus
{
    static public $db_file = 'spatialdata.sqlite';
    
    static private $db;
    
	public $bldgs;
	
	function __construct()
	{
		$this->bldgs = new UNL_Common_Building();
	}
	
    static function getDB()
    {
        if (!isset(self::$db)) {
            return self::__connect();
        }
        return self::$db;
    }
	
    static function tableExists($table)
    {
        $db = self::getDB();
        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
        return $result->numRows() > 0;
    }
    
    static protected function __connect()
    {
        if (self::$db = new SQLiteDatabase(self::getDataDir().self::$db_file)) {
            return self::$db;
        }
        throw new Exception('Cannot connect to database!');
    }
    
    static function importCSV($table, $filename)
    {
        $db = self::getDB();
        if ($h = fopen($filename,'r')) {
            while ($line = fgets($h)) {
                $data = array();
                $line = str_replace('NULL', '""', $line);
                foreach (explode('","',$line) as $field) {
                    $data[] = "'".sqlite_escape_string(stripslashes(trim($field, "\"\n")))."'";
                }
                $data = implode(',',$data);
                $db->queryExec("INSERT INTO ".$table." VALUES ($data);");
            }
        }
    }
	
	/**
	 * Returns the geographical coordinates for a building.
	 * 
	 * @param string $code Building Code for the building you want coordinates of.
	 * @return Associative array of coordinates lat and lon. false on error. 
	 */
	function getGeoCoordinates($code)
	{
		if ($this->buildingExists($code)) {
    		// Code is valid, find the geo coordinates.
		    $this->_checkDB();
            if ($result = self::getDB()->query('SELECT lat,lon FROM campus_spatialdata WHERE code = \''.$code.'\';')) {
                while ($coords = $result->fetch()) {
                    return array('lat'=>$coords['lat'],
                                 'lon'=>$coords['lon']);
                }
            }
		}
		return false;
	}
    
    protected function _checkDB()
    {
       if (!self::tableExists('campus_spatialdata')) {
            self::getDB()->queryExec(self::getTableDefinition());
            self::importCSV('campus_spatialdata', self::getDataDir().'campus_spatialdata.csv');
        }
    }
	
	/**
	 * Checks if a building with the given code exists.
	 * @param string Building code.
	 * @return bool true|false
	 */
	function buildingExists($code)
	{
	    if (isset($this->bldgs->codes[$code])) {
	        return true;
	    } else {
	        return false;
	    }
	}
	
	/**
	 * returns the map url for a given building.
	 * 
	 * @param string $code Building code.
	 * @return string URL to the map.
	 */
	function getMapURL($code)
	{
	    $mapurl = 'http://www1.unl.edu/tour/';
	    return $mapurl.$code;
	}
	
    static public function getDataDir()
    {
        if ('/Users/bbieber/Documents/workspace/peoplefinder/lib/data' == '@@DATA'.'_DIR@@') {
            return dirname(__FILE__) . '/data/';
        }
        return '/Users/bbieber/Documents/workspace/peoplefinder/lib/data/UNL_Geography_SpatialData_Campus/data/';
    }
	
	static public function getTableDefinition()
	{
	    return "CREATE TABLE campus_spatialdata (
                  id int(11) NOT NULL,
                  code varchar(10) NOT NULL default '',
                  lat float(16,14) NOT NULL default '0.00000000000000',
                  lon float(16,14) NOT NULL default '0.00000000000000',
                  PRIMARY KEY  (id),
                  UNIQUE (code)
                ) ; ";
	}
}

?>