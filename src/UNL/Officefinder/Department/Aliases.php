<?php
class UNL_Officefinder_Department_Aliases extends ArrayIterator
{
    public $department_id;

    function __construct($options = array())
    {
        if (!isset($options['department_id'])) {
            throw new Exception('You must pass a department ID!');
        }

        $this->department_id = (int)$options['department_id'];

        $aliases = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT
                    DISTINCT name
                FROM department_aliases
                WHERE department_id = '.$this->department_id;
        if ($result = $mysqli->query($sql)) {
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                $aliases[] = $row[0];
            }
        }
        parent::__construct($aliases);
    }
    
    function current()
    {
        $alias                = new UNL_Officefinder_Department_Alias();
        $alias->department_id = $this->department_id;
        $alias->name          = parent::current();
        return $alias;
    }
}