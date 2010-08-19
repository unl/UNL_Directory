<?php
class UNL_Officefinder_TreeView
{
    function __construct($options = array())
    {
        // retrieve the left and right value of the $root node  
        $root = UNL_Officefinder_Department::getByname('University of Nebraska - Lincoln');

        // start with an empty $right stack  
        $right = array();

        // now, retrieve all descendants of the $root node  
        $result = UNL_Officefinder::getDB()->query('SELECT name, lft, rgt, level FROM departments '.
                              'WHERE lft BETWEEN '.$root->lft.' AND '.
                              $root->rgt.' ORDER BY lft ASC;');

        echo '<pre>';
        // display each row
        while ($row = $result->fetch_assoc()) {  

            // display indented node title
            echo str_repeat('  ', $row['level']).$row['name']."\n";

        }
        echo '</pre>';
        exit();
    }
}