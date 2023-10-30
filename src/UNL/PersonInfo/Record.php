<?php
/**
 * Simple active record implementation for UNL's online directory.
 *
 * PHP version 5
 *
 * @package   UNL_PersonInfo_Record
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2010 Regents of the University of Nebraska
 * @license   https://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      https://peoplefinder.unl.edu/
 */
class UNL_PersonInfo_Record
{
    public $uid;

    protected $nonPersistentFields = [
        'nonPersistentFields',
        'options',
    ];

    protected function getTable()
    {
        return 'person_info';
    }

    public function __construct(string $uid) {
        $got_record = $this->getByUID($uid);

        if ($got_record === false) {
            $got_record = $this->createRecord($uid);
            if ($got_record === false) {
                throw new Exception('DATABASE ERROR', 500);
            }
        }
    }

    public function createRecord($uid): bool {
        $this->uid = $uid;
        $got_record = $this->insert();

        return $got_record;
    }

    public function save_image($path_to_file_to_save, $file_name):bool {

        if (!file_exists($path_to_file_to_save)) {
            throw new Exception('File Does Not Exist');
        }

        try {
            $image_details = getimagesize($path_to_file_to_save);
            if (!is_array($image_details)) {
                throw new Exception('Not an Image');
            }
        }catch (Exception $e) {
            throw new Exception('Not an Image');
        }

        $path_to_save_location = dirname(dirname(dirname(__DIR__))) . '/data/person_images/' . $this->uid . '/' . $file_name;

        if (!file_exists(dirname($path_to_save_location))) {
            mkdir(dirname($path_to_save_location));
        }

        return copy($path_to_file_to_save, $path_to_save_location);
    }

    public function get_image_path($file_name):string {
        return dirname(dirname(dirname(__DIR__))) . '/data/person_images/' . $this->uid . '/' . $file_name;
    }

    /**
     * Prepare the insert SQL for this record
     *
     * @param string &$sql The INSERT SQL query to prepare
     *
     * @return array Associative array of field value pairs
     */
    protected function prepareInsertSQL(&$sql)
    {
        $sql    = 'INSERT INTO '.$this->getTable();
        $fields = array_diff_key(get_object_vars($this), array_fill_keys($this->nonPersistentFields, 0));
        $sql .= '(`'.implode('`,`', array_keys($fields)).'`)';
        $sql .= ' VALUES ('.str_repeat('?,', count($fields)-1).'?)';
        return $fields;
    }

    /**
     * Prepare the update SQL for this record
     *
     * @param string &$sql The UPDATE SQL query to prepare
     *
     * @return array Associative array of field value pairs
     */
    protected function prepareUpdateSQL(&$sql)
    {
        $sql    = 'UPDATE '.$this->getTable().' ';
        $fields = array_diff_key(get_object_vars($this), array_fill_keys($this->nonPersistentFields, 0));

        $sql .= 'SET `'.implode('`=?,`', array_keys($fields)).'`=? ';

        $sql .= 'WHERE ';
        foreach ($this->keys() as $key) {
            $sql .= $key.'=? AND ';
        }

        $sql = substr($sql, 0, -4);

        return $fields;
    }

    /**
     * Get the primary keys for this table in the database
     *
     * @return array
     */
    function keys()
    {
        return ['uid'];
    }

    /**
     * Save the record. This automatically determines if insert or update
     * should be used, based on the primary keys.
     *
     * @return bool
     */
    function save()
    {
        $key_set = true;

        foreach ($this->keys() as $key) {
            if (empty($this->$key)) {
                $key_set = false;
            }
        }

        if (!$key_set) {
            return $this->insert();
        }

        return $this->update();
    }

    /**
     * Insert a new record into the database
     *
     * @return bool
     */
    function insert()
    {
        $sql      = '';
        $fields   = $this->prepareInsertSQL($sql);
        $values   = [];
        $values[] = $this->getTypeString(array_keys($fields));
        foreach ($fields as $key=>$value) {
            $values[] =& $this->$key;
        }
        return $this->prepareAndExecute($sql, $values);
    }

    /**
     * Update this record in the database
     *
     * @return bool
     */
    function update()
    {
        $sql      = '';
        $fields   = $this->prepareUpdateSQL($sql);
        $values   = [];
        $values[] = $this->getTypeString(array_keys($fields));
        foreach ($fields as $key=>$value) {
            $values[] =& $this->$key;
        }
        // We're doing an update, so add in the keys!
        $values[0] .= $this->getTypeString($this->keys());
        foreach ($this->keys() as $key) {
            $values[] =& $this->$key;
        }
        return $this->prepareAndExecute($sql, $values);
    }

    /**
     * Prepare the SQL statement and execute the query
     *
     * @param string $sql    The SQL query to execute
     * @param array  $values Values used in the query
     *
     * @throws Exception
     *
     * @return true
     */
    protected function prepareAndExecute($sql, $values)
    {
        $mysqli = self::getDB();

        if (!$stmt = $mysqli->prepare($sql)) {
            throw new Exception('Error preparing database statement! '.$mysqli->error, 500);
        }

        call_user_func_array([$stmt, 'bind_param'], $values);
        if ($stmt->execute() === false) {
            throw new Exception($stmt->error, 500);
        }

        if ($mysqli->insert_id !== 0) {
            $this->uid = $mysqli->insert_id;
        }

        return true;
    }

    /**
     * Get the type string used with prepared statements for the fields given
     *
     * @param array $fields Array of field names
     *
     * @return string
     */
    function getTypeString($fields)
    {
        $types = '';
        foreach ($fields as $name) {
            switch($name) {
            default:
                $types .= 's';
                break;
            }
        }
        return $types;
    }

    /**
     * Convert the string given into a usable date for the RDBMS
     *
     * @param string $str A textual description of the date
     *
     * @return string|false
     */
    function getDate($str)
    {
        if ($time = strtotime($str)) {
            return date('Y-m-d', $time);
        }

        if (strpos($str, '/') !== false) {
            list($month, $day, $year) = explode('/', $str);
            return $this->getDate($year.'-'.$month.'-'.$day);
        }
        // strtotime couldn't handle it
        return false;
    }

    // /**
    //  * Delete this record in the database
    //  *
    //  * @return bool
    //  */
    // function delete()
    // {
    //     $mysqli = self::getDB();
    //     $sql    = "DELETE FROM ".$this->getTable()." WHERE ";
    //     foreach ($this->keys() as $key) {
    //         if (empty($this->$key)) {
    //             throw new Exception('Cannot delete this record.' .
    //                                 'The primary key, '.$key.' is not set!',
    //                                 400);
    //         }
    //         $value = $this->$key;
    //         if ($this->getTypeString([$key]) == 's') {
    //             $value = '"'.$mysqli->escape_string($value).'"';
    //         }
    //         $sql .= $key.'='.$value.' AND ';
    //     }
    //     $sql  = substr($sql, 0, -4);
    //     $sql .= ' LIMIT 1;';
    //     if ($result = $mysqli->query($sql)) {
    //         return true;
    //     }
    //     return false;
    // }

    /**
     * Magic method for static calls
     *
     * @param string $method Method called
     * @param array  $args   Array of arguments passed to the method
     *
     * @method getBy[FIELD NAME]
     *
     * @throws Exception
     *
     * @return mixed
     */
    // public static function __callStatic($method, $args)
    // {
    //     switch (true) {
    //     case preg_match('/getBy([\w]+)/', $method, $matches):
    //         $class    = get_called_class();
    //         $field    = strtolower($matches[1]);
    //         $whereAdd = null;
    //         if (isset($args[1])) {
    //             $whereAdd = $args[1];
    //         }
    //         return self::getByAnyField($class, $field, $args[0], $whereAdd);

    //     }
    //     throw new Exception('Invalid static method called.');
    // }

    public function getByUID($uid, $whereAdd = '')
    {
        if (!empty($whereAdd)) {
            $whereAdd = $whereAdd . ' AND ';
        }

        $mysqli = self::getDB();
        $sql    = 'SELECT * FROM '
                    . $this->getTable()
                    . ' WHERE '
                    . $whereAdd
                    . 'uid = "' . $mysqli->escape_string($uid) . '"';
        $result = $mysqli->query($sql);

        if ($result === false
            || $result->num_rows == 0) {
            return false;
        }

        $this->synchronizeWithArray($result->fetch_assoc());
    }

    /**
     * Get the DB
     *
     * @return mysqli
     */
    public static function getDB()
    {
        return UNL_Officefinder::getDB();
    }

    /**
     * Synchronize member variables with the values in the array
     *
     * @param array $data Associative array of field=>value pairs
     *
     * @return void
     */
    public function synchronizeWithArray($data)
    {
        foreach ($data as $key=>$value) {
            $this->$key = $value;
        }
    }

    /**
     * Reload data from the database and refresh member variables
     *
     * @return void
     */
    public function reload()
    {
        $record = self::getByUid($this->uid);
        $this->synchronizeWithArray($record->toArray());
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}