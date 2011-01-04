<?php
class UNL_Officefinder_DepartmentList_NameSearch extends UNL_Officefinder_DepartmentList
{
    public $options = array('q'=>'', 'parent_orgs' => true);

    function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $records = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT DISTINCT d1.id
                FROM departments d1 ';
        if ((bool)$this->options['parent_orgs'] === true) {
            // Preorder Tree model
            // $sql .= ' AND (departments.rgt != departments.lft+1 OR departments.org_unit IS NOT NULL) ';
            $sql .= 'INNER JOIN departments d2 ON d2.parent_id = d1.id ';
        }
        $sql .= '
                LEFT JOIN department_aliases ds ON ds.department_id = d1.id
                WHERE (
                d1.name LIKE "%'.$mysqli->escape_string($this->options['q']).'%"
                OR
                ds.name LIKE "%'.$mysqli->escape_string($this->options['q']).'%"
                )
                ORDER BY d1.name';
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $records[] = $row[0];
            }
        }
        parent::__construct($records);
    }

}