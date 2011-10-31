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

    function insert()
    {
        // Check if we can already match based on this name
        if ($departments = new UNL_Officefinder_DepartmentList_NameSearch(array('q'=>$this->name))) {
            if (in_array($this->department_id, $departments->getArrayCopy())
                && !UNL_Officefinder::isAdmin(UNL_Officefinder::getUser())) {
                // This search alias already matches the department
                throw new Exception(
                    'That alias will already return your department.
                    If you must have this alias added, contact a directory administrator.
                    Here is a list of directory admins — '.implode(', ', UNL_Officefinder::$admins), 500);
            }
        }

        return parent::insert();
    }

    function __toString()
    {
        return $this->name;
    }
}