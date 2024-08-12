<?php
class UNL_Officefinder_DepartmentList_Filter_HasChildren extends FilterIterator
{
    function accept(): bool
    {
        return $this->current()->hasChildren();
    }
}