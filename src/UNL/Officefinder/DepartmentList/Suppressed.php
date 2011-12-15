<?php
class UNL_Officefinder_DepartmentList_Suppressed extends UNL_Officefinder_DepartmentList_NameSearch
{
    function getSQL()
    {
        return 'SELECT * FROM departments WHERE suppress = 1';
    }
}