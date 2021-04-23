<?php
class UNL_Peoplefinder_Department_PersonnelSubtree extends ArrayIterator
{
    public $options = array();

    function __construct($mixed)
    {
        // Bump the allowed execution time to prevent timeouts due to potential slow load
        set_time_limit (600); // 600 seconds = 10 minutes

        // Increase result limit for this type of search
        UNL_Peoplefinder::$resultLimit = 500;

        if (is_array($mixed)) {
            $this->options = $mixed;
            if (!empty($mixed['org_unit'])) {
                $department = UNL_Officefinder_Department::getByorg_unit($mixed['org_unit']);
            } elseif (isset($mixed['id'])) {
                $department = UNL_Officefinder_Department::getById($mixed['id']);
            }

            // build list of org units
            $orgUnits = array();
            $orgUnits[] = $department->org_unit;
            $this->getChildOrgUnits($department, $orgUnits);

            $mixed = UNL_Peoplefinder::getInstance($this->options)->getHROrgUnitNumbersMatches($orgUnits);
        }
        parent::__construct($mixed);
    }

    /**
     * Find all child departments recursively
     *
     * @param UNL_Officefinder_Department $department The department
     * @param array                       $orgUnits   Array of org units
     */
    protected function getChildOrgUnits($department, &$orgUnits)
    {
        foreach ($department->getOfficialChildDepartments() as $sub_dept) {
            $orgUnits[] = $sub_dept->org_unit;

            // depth-first search
            $this->getChildOrgUnits($sub_dept, $orgUnits);
        }
    }
}