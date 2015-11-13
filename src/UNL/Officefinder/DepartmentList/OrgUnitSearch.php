<?php
class UNL_Officefinder_DepartmentList_OrgUnitSearch extends UNL_Officefinder_DepartmentList
{
    public $options = array('q'=>'');

    function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $records = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = $this->getSQL();
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $records[] = $row[0];
            }
        }
        parent::__construct($records);
    }

    function getSQL()
    {
        $query = (int)$this->options['q'];

        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT id, name FROM departments
                WHERE org_unit = '.$query.'
                ORDER BY name';
        return $sql;
    }

}