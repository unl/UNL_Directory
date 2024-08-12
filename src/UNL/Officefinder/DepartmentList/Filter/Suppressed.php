<?php
class UNL_Officefinder_DepartmentList_Filter_Suppressed extends FilterIterator implements Countable
{
    public function accept(): bool
    {
        return !$this->current()->suppress;
    }

    public function count(): int
    {
        return iterator_count($this);
    }
}
