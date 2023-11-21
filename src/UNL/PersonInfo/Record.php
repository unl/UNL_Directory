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
class UNL_PersonInfo_Record
{
    // PersonInfo database columns
    public $uid;
    public $avatar_updated_on;

    // Non-persistent fields
    private $person_images_dir;
    protected $nonPersistentFields = [
        'nonPersistentFields',
        'person_images_dir',
        'options',
    ];

    /**
     * Gets the database table
     */
    protected function getTable()
    {
        return 'person_info';
    }

    /**
     * @throws Exception Database Error
     */
    public function __construct(string $uid) {
        // The directory that the user's images would be saved to
        $this->person_images_dir = dirname(dirname(dirname(__DIR__))) . '/www/person_images/';

        // Gets the database record by their UID
        $got_record = $this->getByUID($uid);
        if ($got_record === false) {
            $got_record = $this->createRecord($uid);
            if ($got_record === false) {
                throw new Exception('DATABASE ERROR', 500);
            }
        }
    }

    /**
     * Creates a new database record for that UID
     *
     * @param string $uid UID to create the record for
     * @return bool False if it did not insert
     */
    public function createRecord($uid): bool {
        $this->uid = $uid;
        return $this->insert();
    }

    /**
     * Removes all the users saved images
     * @return void
     */
    public function clear_images(): void
    {
        // Checks if that directory exists
        $path_to_save_location = $this->person_images_dir . $this->uid;
        if (file_exists($path_to_save_location)) {
            // Loops through all the files and deletes them
            $tmp_files = array_diff(scandir($path_to_save_location), array('.','..'));
            foreach ($tmp_files as $file) {
                unlink($path_to_save_location . '/' . $file);
            }
        }
    }

    /**
     * Saves a new image to the user's record
     *
     * @param string $path_to_file_to_save Path to the file to be saved
     * @param string $file_name Filename for the newly saved file
     * @return bool False if the file was not saved
     */
    public function save_image($path_to_file_to_save, $file_name):bool {
        // Validates that the file exists
        if (!file_exists($path_to_file_to_save)) {
            throw new UNL_PersonInfo_Exceptions_InvalidImage('File Does Not Exist');
        }

        // Creates path to the new file save location and checks that the parent directory exists
        $path_to_save_location = $this->person_images_dir . $this->uid . '/' . $file_name;
        if (!file_exists(dirname($path_to_save_location))) {
            mkdir(dirname($path_to_save_location));
        }

        // Updates database record
        $this->avatar_updated_on = date('Y-m-d H:m:s');
        $this->save();

        // Copies the file
        return copy($path_to_file_to_save, $path_to_save_location);
    }

    /**
     * Returns true if the user has any images saved to their record
     *
     * @return bool
     */
    public function has_images(): bool
    {
        // Checks if their directory exists
        $path_to_save_location = $this->person_images_dir . $this->uid;
        if (!file_exists($path_to_save_location)) {
            return false;
        }

        // Returns true if the directory is not empty
        $tmp_files = array_diff(scandir($path_to_save_location), array('.','..'));
        return !empty($tmp_files);
    }

    /**
     * Gets the file path for the file that is attached to the user
     *
     * @param string $file_name The file that should be attached to the user
     * @return string|bool The file path to that file or false if it doesn't
     */
    public function get_image_path($file_name)
    {
        // Check if the file exists and if so it will return the path
        $file_path = $this->person_images_dir . $this->uid . '/' . $file_name;
        if (!file_exists($file_path)) {
            return false;
        }
        return $file_path;
    }

    /**
     * Gets the image URL for a file that is attached to the user
     *
     * @param string $file_name The file that should be attached to the user
     * @return string|bool The file path to that file or false if it doesn't
     */
    public function get_image_url($file_name)
    {
        // Check if the file exists and if so it will return the URL
        $file_path = $this->person_images_dir . $this->uid . '/' . $file_name;
        if (!file_exists($file_path)) {
            return false;
        }
        return UNL_Peoplefinder::getURL() . 'images/avatars/' . $this->uid . '/' . $file_name;
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
            $types .= 's';
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
