<?php
class UNL_Officefinder_User_Departments extends UNL_Officefinder_DepartmentList
{
    public $options = array('uid'=>'');

    function __construct($options = array())
    {
        $this->options = $options + $this->options;
        if (empty($this->options['uid'])) {
            throw new Exception('User id is required');
        }
        $records = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT DISTINCT departments.id FROM departments, department_users
                WHERE
                    departments.id = department_users.department_id
                    AND department_users.uid = "'.$mysqli->escape_string($this->options['uid']).'"';
        $sql .= ' ORDER BY departments.name';
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $records[] = $row[0];
            }
        }
        $mysqli->close();
        parent::__construct($records);
    }
}