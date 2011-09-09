<?php
class UNL_Officefinder_DepartmentList_AcademicDepartments extends UNL_Officefinder_DepartmentList_AlphaListing
{

    protected function getSQL()
    {
        $sql = 'SELECT DISTINCT departments.id AS id, departments.name AS name
                FROM departments
                WHERE org_unit IS NOT NULL AND academic = 1
                    UNION
                SELECT department_aliases.department_id as id, CONCAT(department_aliases.name, " (see ", departments.name, ")") as name
                FROM department_aliases, departments
                WHERE department_aliases.department_id = departments.id AND departments.academic = 1

                ORDER BY name;';
        return $sql;
    }

    function getCacheKey()
    {
        return false;
    }
}