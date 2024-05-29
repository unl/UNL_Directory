<?php

/**
 * Handling directory saved user specific info
 *
 * PHP version 7.4
 *
 * @category  Services
 * @package   UNL_PersonInfo_Avatar
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2023 University Communications & Marketing
 * @license   https://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      https://directory.unl.edu/myinfo/avatar
 */

 class UNL_PersonInfo_Avatar {
    
    public $options;

    public $user;
    public $record;

    public static $url = 'https://local-directory.unl.edu';

    public function __construct($options = array())
    {
        $this->options = $options;

        // Force the user to be logged in
        $this->user = UNL_PersonInfo::getUser(true);
        if ($this->user === false) {
            throw new Exception('UNAUTHORIZED', 401);
        }

        // Get their record once they are logged in
        $this->record = new UNL_PersonInfo_Record($this->user);
    }

    /**
     * Get the logged in user
     * @return string UID of the user
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Simplified get avatar url function since we only really need one size
     *
     * @param string|int $size size of the avatar image
     * @return string URL of the user's avatar
     */
    public function get_avatar_URL($size = UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_MEDIUM)
    {
        // Get the person's record
        $avatar = new UNL_Peoplefinder_Record_Avatar(array(
            'uid' => $this->user,
        ));

        // Validate size
        if (!in_array($size, UNL_Peoplefinder_Record_Avatar::getAvatarSizes(false))) {
            $size = UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_MEDIUM;
        }

        return $avatar->getUrl(['s' => $size]);
    }

    /**
     * Get the max file upload size in bytes
     * @return int Max file upload size in bytes
     */
    public function file_upload_max_size() {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $post_max_size = $this->parse_size(ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = $this->parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }

        return $max_size;
    }

    /**
     * Replaces alpha characters with their corresponding byte representation
     *
     * @param string $size Alphanumeric string for bytes
     * @return int size in bytes that the string represented
     */
    public function parse_size($size) {
        // Remove the non-unit characters from the size.
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);

        if ($unit) {
            // Find the position of the unit in the ordered string
            // which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }

    public static function getURL(string $link) {
        return self::$url . $link;
    }
 }
 