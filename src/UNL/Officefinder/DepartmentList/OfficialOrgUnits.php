<?php
class UNL_Officefinder_DepartmentList_OfficialOrgUnits extends UNL_Officefinder_DepartmentList_NameSearch
{
    function getSQL()
    {
        return 'SELECT * FROM departments WHERE org_unit IS NOT NULL';
    }
}