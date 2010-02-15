<?php

class UNL_Common
{
    static public $db_file = 'unl_common.sqlite';
    
    static private $db;
    
    static function getDB()
    {
        if (!isset(self::$db)) {
            return self::__connect();
        }
        return self::$db;
    }

    static public function getDataDir()
    {
        return dirname(dirname(dirname(__FILE__))).'/data/UNL_Common/UNL/data/';
    }
    
    static protected function __connect()
    {
        if (self::$db = new SQLiteDatabase(self::getDataDir().self::$db_file)) {
            return self::$db;
        }
        throw new Exception('Cannot connect to database!');
    }
    
    static function tableExists($table)
    {
        $db = self::getDB();
        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
        return $result->numRows() > 0;
    }
    
    static function importCSV($table, $filename)
    {
        $db = self::getDB();
        if ($h = fopen($filename,'r')) {
            while ($line = fgets($h)) {
                $db->queryExec("INSERT INTO ".$table." VALUES ($line);");
            }
        }
    }
}

?>