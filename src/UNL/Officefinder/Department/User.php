<?php
class UNL_Officefinder_Department_User extends UNL_Officefinder_Record
{
    public $department_id;
    public $uid;

    public function getTable()
    {
        return 'department_permissions';
    }

    public function keys()
    {
        return array('department_id', 'uid');
    }

    public static function getById($department_id, $uid)
    {
        $mysqli = UNL_Officefinder::getDB();
        $sql = "SELECT * FROM department_permissions WHERE department_id = ".intval($department_id)." AND uid = '".$mysqli->escape_string($uid)."'";
        if (($result = $mysqli->query($sql))
            && $result->num_rows > 0) {
            $object = new self();
            UNL_Officefinder::setObjectFromArray($object, $result->fetch_assoc());
            return $object;
        }
        return false;
    }

    function __toString()
    {
        return $this->uid;
    }
}