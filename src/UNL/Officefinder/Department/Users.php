<?php
class UNL_Officefinder_Department_Users extends UNL_Officefinder_UserList
{
    function __construct($options = array())
    {
        $users = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT DISTINCT uid FROM department_permissions ';
        if (isset($options['department_id'])) {
            $sql .= ' WHERE department_id = '.(int)$options['department_id'];
        }
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $users[] = $row[0];
            }
        }
        $mysqli->close();
        parent::__construct($users);
    }
}