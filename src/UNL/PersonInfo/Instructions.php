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
}