<?php
class UNL_Officefinder_Department_Listing_Users extends UNL_Officefinder_UserList
{
    function __construct($options = array())
    {
        $users = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT DISTINCT uid FROM listing_permissions ';
        if (isset($options['listing_id'])) {
            $sql .= ' WHERE listing_id = '.(int)$options['listing_id'];
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