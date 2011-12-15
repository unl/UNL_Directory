<?php
class UNL_Officefinder_DepartmentList_Filter_HasNoChildren extends FilterIterator
{
    function accept()
    {
        return !$this->current()->hasChildren();
    }
}