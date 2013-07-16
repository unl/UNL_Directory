<?php
class UNL_Peoplefinder_Department_PersonnelSubtree extends ArrayIterator
{
    public $options = array();

    function __construct($mixed)
    {
        if (is_array($mixed)) {
            $this->options = $mixed;
            if (!empty($mixed['org_unit'])) {
                $department = UNL_Officefinder_Department::getByorg_unit($mixed['org_unit']);
            } elseif (isset($mixed['id'])) {
                $department = UNL_Officefinder_Department::getById($mixed['id']);
            }
            $orgUnits = array();
            $orgUnits[] = $department->org_unit;
            foreach ($department->getOfficialChildDepartments() as $sub_dept) {
                $orgUnits[] = $sub_dept->org_unit;
            }
            $peoplefinder  = new UNL_Peoplefinder(array('driver'=>$this->options['driver']));
            $mixed = $peoplefinder->getHROrgUnitNumbersMatches($orgUnits);
        }
        parent::__construct($mixed);
    }
}