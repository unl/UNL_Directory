<?php
class UNL_Officefinder_DepartmentList_Filter_HasNoHRPersonnel extends FilterIterator
{
    function accept()
    {
        if ($hr = $this->current()->getHRDepartment()) {
            if (count($hr)) {
                return false;
            }
        }

        return true;
    }
}