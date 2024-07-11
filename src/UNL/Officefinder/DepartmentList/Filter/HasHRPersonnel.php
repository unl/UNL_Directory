<?php
class UNL_Officefinder_DepartmentList_Filter_HasHRPersonnel extends FilterIterator
{
    function accept(): bool
    {
        if ($hr = $this->current()->getHRDepartment()) {
            return count($hr);
        }

        return false;
    }
}