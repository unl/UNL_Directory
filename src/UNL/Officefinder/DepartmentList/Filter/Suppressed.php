<?php
class UNL_Officefinder_DepartmentList_Filter_Suppressed extends FilterIterator
{
    function accept()
    {
        return !$this->current()->suppress;
    }
}