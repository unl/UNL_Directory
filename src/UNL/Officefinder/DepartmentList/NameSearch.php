<?php
class UNL_Officefinder_DepartmentList_NameSearch extends UNL_Officefinder_DepartmentList
{
    public $options = [
        'q' => '',
        'parent_orgs' => true,
    ];

    public function __construct($options = [])
    {
        $this->options = $options + $this->options;
        $records = [];
        $mysqli = UNL_Officefinder::getDB();
        $sql = $this->getSQL();
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $records[] = $row[0];
            }
        }
        parent::__construct($records);
    }

    public function getSQL()
    {
        $query = $this->options['q'];

        $query = preg_replace('/\s(and|\&)\s/', ' % ', $query);

        // Expand multiple words, so Ag Education matches Agricultural Education
        $query = preg_replace('/(\w+)\s+(\w+)/', '$1% $2', $query);

        $mysqli = UNL_Officefinder::getDB();
        $esapedQuery = "'%" . $mysqli->escape_string($query) . "%'";
        $where = ['d1.name LIKE ' . $esapedQuery . ' OR ds.name LIKE ' . $esapedQuery];
        $sql = 'SELECT DISTINCT d1.id, d1.name FROM departments d1 ';

        if ((bool)$this->options['parent_orgs'] === true) {
            $sql .= 'LEFT JOIN (SELECT parent_id FROM departments WHERE parent_id IS NOT NULL GROUP BY parent_id) d2 ON d2.parent_id = d1.id ';
            $where[] = 'd2.parent_id IS NOT NULL OR d1.org_unit IS NOT NULL';
        }

        $sql .= 'LEFT JOIN department_aliases ds ON ds.department_id = d1.id ';
        $sql .= 'WHERE (' . implode(') AND (', $where) . ') ';
        $sql .= 'ORDER BY d1.name';
        return $sql;
    }
}
