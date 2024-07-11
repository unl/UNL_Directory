<?php
class UNL_Officefinder_DepartmentList_Filter_HasNoChildren extends FilterIterator
{
    function accept(): bool
    {
        return !$this->current()->hasChildren();
    }
}