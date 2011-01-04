<?php
class UNL_Officefinder_Department_Users extends UNL_Officefinder_UserList
{
    public $department_id;

    function __construct($options = array())
    {

        if (!isset($options['department_id'])) {
            throw new Exception('Must pass a department to get a list of users for');
        }

        $this->department_id = (int)$options['department_id'];

        $users = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT DISTINCT uid FROM department_permissions ';
        if (isset($options['department_id'])) {
            $sql .= ' WHERE department_id = '.$this->department_id;
        }
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $users[] = $row[0];
            }
        }
        parent::__construct($users);
    }

    function current()
    {
        $user = new UNL_Officefinder_Department_User();
        $user->department_id = $this->department_id;
        $user->uid = parent::current();
        return $user;
    }
}