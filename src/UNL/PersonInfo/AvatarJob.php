<?php
/**
 * Simple active record implementation for UNL's online directory.
 *
 * PHP version 5
 *
 * @category  Services
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2010 Regents of the University of Nebraska
 * @license   https://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      https://peoplefinder.unl.edu/
 */
class UNL_PersonInfo_AvatarJob extends UNL_PersonInfo_BaseRecord
{
    // PersonInfo database columns
    public $id;
    public $uid;
    public $status;
    public $file;
    public $square_x;
    public $square_y;
    public $square_size;
    public $error;

    const STATUS_QUEUED = 'queued';
    const STATUS_WORKING = 'working';
    const STATUS_FINISHED = 'finished';
    const STATUS_ERROR = 'error';

    protected $nonPersistentFields = [
        'nonPersistentFields',
        'field_types',
        'options',
    ];

    protected $field_types = [
        'id' => 'i',
        'uid' => 's',
        'status' => 's',
        'file' => 's',
        'square_x' => 'i',
        'square_y' => 'i',
        'square_size' => 'i',
        'error' => 's',
    ];

    /**
     * Gets the database table
     */
    protected function getTable()
    {
        return 'person_info_avatar_job';
    }

    public function __construct()
    {
    }

    public function save_image_for_later(string $filename): string
    {
        // Creates a tmp directory
        $random_id = uniqid();
        $tmp_path = dirname(dirname(dirname(__DIR__))) . '/www/person_images/tmp/' . $random_id;

        // Validates the image exists
        if (!file_exists($filename)) {
            throw new UNL_PersonInfo_Exceptions_InvalidImage('Image does not exist or has exceeded max upload size');
        }

        $copy_success = copy($filename, $tmp_path);
        if (!$copy_success) {
            throw new UNL_PersonInfo_Exceptions_InvalidImage('File not copied');
        }

        return $tmp_path;
    }

        /**
     * Creates a new database record for that UID
     *
     * @param string $uid UID to create the record for
     * @return bool False if it did not insert
     */
    public function createRecord(string $uid, string $filename, int $square_x, int $square_y, int $square_size): bool
    {
        // Delete any current jobs for that UID
        $this->deleteAllCompletedRecords($uid);

        $saved_file_path = $this->save_image_for_later($filename);

        $this->uid = $uid;
        $this->status = self::STATUS_QUEUED;
        $this->file = $saved_file_path;
        $this->square_x = $square_x;
        $this->square_y = $square_y;
        $this->square_size = $square_size;
        return $this->insert();
    }

    public function deleteAllCompletedRecords(string $uid): bool
    {
        $mysqli = self::getDB();
        $sql = 'DELETE FROM ' . $this->getTable()
        . ' WHERE uid = "' . $mysqli->escape_string($uid) . '"'
        . ' AND (status = "' . self::STATUS_FINISHED . '"'
        . ' OR status = "' . self::STATUS_ERROR . '")';
        $result = $mysqli->query($sql);

        return $result !== false;
    }

    public function getFirstQueued(): bool
    {
        return $this->getByWhere('status', self::STATUS_QUEUED);
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_FINISHED || $this->status === self::STATUS_ERROR;
    }
}
