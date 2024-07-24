<?php
/**
 * Simple active record implementation for UNL's online directory.
 *
 * PHP version 7.4
 *
 * @package   UNL_PersonInfo_Record
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2023 University Communications & Marketing
 * @license   https://www1.unl.edu/wdn/wiki/Software_License BSD License
 */
class UNL_PersonInfo_BaseRecord
{
    public $uid;

    // Non-persistent fields
    protected $nonPersistentFields = [
        'nonPersistentFields',
        'options',
        'field_types'
    ];

    protected $field_types = [];

    /**
     * Gets the database table
     */
    protected function getTable()
    {
        return 'person_info';
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
    public function keys()
    {
        return ['uid'];
    }

    /**
     * Save the record. This automatically determines if insert or update
     * should be used, based on the primary keys.
     *
     * @return bool
     */
    public function save()
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
    public function insert()
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
    public function update()
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
    public function getTypeString($fields)
    {
        $types = '';
        foreach ($fields as $name) {
            if (isset($this->field_types) && !empty($this->field_types)) {
                $types .= $this->field_types[$name] ?? 's';
            } else {
                switch($name) {
                    case 'id':
                        $types .= 'i';
                        break;
                    default:
                        $types .= 's';
                        break;
                }
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
    public function getDate($str)
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
        return true;
    }

    public function getByWhere(string $column, mixed $value)
    {
        $mysqli = self::getDB();
        $sql    = 'SELECT * FROM '
                    . $this->getTable()
                    . ' WHERE '
                    . $column . ' = ?';

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($this->getTypeString([$column]), $value);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false
            || $result->num_rows == 0) {
            return false;
        }

        $this->synchronizeWithArray($result->fetch_assoc());
        return true;
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
     * Return an array representation of the object
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}
