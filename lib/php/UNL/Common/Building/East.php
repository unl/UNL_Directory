<?php
/**
* @author Brett Bieber
* @package UNL_Common
* Created on Sep 27, 2005
*/

require_once dirname(__FILE__).'/../../Common.php'; 

/**
 * Class which can retrieve the buildings and codes for East Campus
 * 
 * @package UNL_Common
 */
class UNL_Common_Building_East
{
	public $codes = array();
	
	/**
     * Constructor connects to database and loads codes and names.
     * @return bool False on error
     */
    function __construct()
    {
        $this->_checkDB();
        if ($result = UNL_Common::getDB()->query('SELECT * FROM building_east')) {
            while ($bldg = $result->fetch()) {
                $this->codes[(string)$bldg['code']]=$bldg['name'];
            }
        }
    }
    
    protected function _checkDB()
    {
       if (!UNL_Common::tableExists('building_east')) {
            UNL_Common::getDB()->queryExec(UNL_Common_Building_East::getTableDefinition());
            UNL_Common::importCSV('building_east', UNL_Common::getDataDir().'building_east.csv');
        }
    }
    
    static function getTableDefinition()
    {
        return "CREATE TABLE building_east (
                  id int(11) NOT NULL,
                  code varchar(10) NOT NULL default '',
                  name varchar(100) NOT NULL default '',
                  PRIMARY KEY  (id)
                ) ;";
    }
}
