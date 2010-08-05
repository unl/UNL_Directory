<?php
class UNL_Officefinder_DepartmentList_NameSearch extends UNL_Officefinder_DepartmentList
{
    public $options = array('q'=>'');

    function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $records = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT id FROM departments ';
        $sql .= 'WHERE name LIKE "%'.$mysqli->escape_string($this->options['q']).'%"'
             . ' AND rgt != lft+1 ORDER BY name';
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $records[] = $row[0];
            }
        }
        $mysqli->close();
        parent::__construct($records);
    }

}