<?php
class UNL_Officefinder_DepartmentList_Filter_Suppressed extends FilterIterator implements Countable
{
    public function accept()
    {
        return !$this->current()->suppress;
    }

    public function count()
    {
        return iterator_count($this);
    }
}
