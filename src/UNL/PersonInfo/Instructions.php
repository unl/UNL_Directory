<?php
class UNL_PersonInfo_Instructions
{
    public $options;

    public $user;
    public $record;

    public function __construct($options = array())
    {
        $this->options = $options;

        $this->user = UNL_PersonInfo::getUser(true);

        if ($this->user === false) {
            throw new Exception('UNAUTHORIZED', 401);
        }
        $this->record = new UNL_PersonInfo_Record($this->user);
    }

    public function getUser() {
        return $this->user;
    }

    public function get_avatar_URL($size = UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_MEDIUM)
    {
        $avatar = new UNL_Peoplefinder_Record_Avatar(array(
            'uid' => $this->user,
        ));
        return $avatar->getUrl(['s' => $size]);
    }

    // Returns a file size limit in bytes based on the PHP upload_max_filesize
    // and post_max_size
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

    public function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }
}