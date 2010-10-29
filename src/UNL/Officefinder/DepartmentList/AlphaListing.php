<?php
class UNL_Officefinder_DepartmentList_AlphaListing extends FilterIterator
{
    public $options = array('q'=>'');

    function __construct($options = array())
    {
        $this->options = $options + $this->options;

        // Require login to view the full directory
        UNL_Officefinder::getUser(true);

        $records = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT DISTINCT departments.id AS id, departments.name AS name
                FROM departments
                WHERE org_unit IS NOT NULL
                    UNION
                SELECT department_aliases.department_id as id, CONCAT(department_aliases.name, " (see ", departments.name, ")") as name
                FROM department_aliases, departments
                WHERE department_aliases.department_id = departments.id

                ORDER BY name;';
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $records[] = $row;
            }
        }
        $mysqli->close();
        parent::__construct(new ArrayIterator($records));
    }

    function accept()
    {
        if ($this->current()->department->hasChildren()
            || isset($this->current()->department->phone)) {
            return true;
        }
        return false;
    }

    function current()
    {
        $row = parent::current();
        return new UNL_Officefinder_DepartmentList_AlphaListing_Department($row[1], UNL_Officefinder_Department::getByID($row[0]));
    }
}