<?php
class UNL_Officefinder_Department_Alias extends UNL_Officefinder_Record
{
    public $department_id;
    public $name;

    function getTable()
    {
        return 'department_aliases';
    }

    function keys()
    {
        return array('department_id', 'name');
    }

    public static function getById($department_id, $name)
    {
        $mysqli = UNL_Officefinder::getDB();
        $sql = "SELECT * FROM department_aliases WHERE department_id = ".intval($department_id)." AND name = '".$mysqli->escape_string($name)."'";
        if (($result = $mysqli->query($sql))
            && $result->num_rows > 0) {
            $object = new self();
            UNL_Officefinder::setObjectFromArray($object, $result->fetch_assoc());
            return $object;
        }
        return false;
    }
}