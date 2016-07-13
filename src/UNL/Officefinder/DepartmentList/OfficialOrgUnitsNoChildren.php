<?php
class UNL_Officefinder_DepartmentList_OfficialOrgUnitsNoChildren extends UNL_Officefinder_DepartmentList_NameSearch
{
    public function getSQL()
    {
        return 'SELECT d.*
FROM departments d
LEFT JOIN (
  SELECT parent_id
  FROM departments
  WHERE parent_id IS NOT NULL
  GROUP BY parent_id
) p ON d.id = p.parent_id
WHERE d.org_unit IS NOT NULL AND p.parent_id IS NULL';
    }
}
