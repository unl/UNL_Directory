<?php
class UNL_Officefinder_Department_Listings extends ArrayIterator
{
    function __construct($options = array())
    {
        $records = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT id FROM listings ';
        $sql .= 'WHERE department_id = '.(int)$options['department_id'];
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $records[] = $row[0];
            }
        }
        $mysqli->close();
        parent::__construct($records);
    }

    function current()
    {
        return UNL_Officefinder_Department_Listing::getByID(parent::current());
    }
}
