<?php
class UNL_Officefinder_User_Departments extends UNL_Officefinder_DepartmentList
{
    public $options = array('uid'=>'');

    function __construct($options = array())
    {
        $this->options = $options + $this->options;

        // Require authentication
        $authUser = UNL_Officefinder::getUser(true);

        if (empty($this->options['uid'])) {
            $this->options['uid'] = $authUser;
        }

        if ($this->options['uid'] !== $authUser && !UNL_Officefinder::isAdmin($authUser)) {
            throw new Exception('Only administrators can view departments others have permission to.');
        }

        $records = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT DISTINCT departments.id FROM departments, department_permissions
                WHERE
                    departments.id = department_permissions.department_id
                    AND department_permissions.uid = "'.$mysqli->escape_string($this->options['uid']).'"';
        $sql .= ' ORDER BY departments.name';
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $records[] = $row[0];
            }
        }
        parent::__construct($records);
    }
}