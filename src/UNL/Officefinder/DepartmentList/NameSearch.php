<?php
class UNL_Officefinder_DepartmentList_NameSearch extends UNL_Officefinder_DepartmentList
{
    public $options = array('q'=>'', 'parent_orgs' => true);

    function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $records = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT DISTINCT departments.id FROM departments, department_aliases
                WHERE (departments.name LIKE "%'.$mysqli->escape_string($this->options['q']).'%" OR
                    (department_aliases.name LIKE "%'.$mysqli->escape_string($this->options['q']).'%" AND department_aliases.department_id = departments.id))';
        if ((bool)$this->options['parent_orgs'] === true) {
            // Preorder Tree model
            // $sql .= ' AND (departments.rgt != departments.lft+1 OR departments.org_unit IS NOT NULL) ';
            $sql .= ' AND departments.id IN (SELECT DISTINCT departments.parent_id FROM departments WHERE parent_id IS NOT NULL) ';
        }
        $sql .= ' ORDER BY departments.name';
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $records[] = $row[0];
            }
        }
        parent::__construct($records);
    }

}