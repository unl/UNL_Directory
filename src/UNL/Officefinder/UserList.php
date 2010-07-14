<?php
class UNL_Officefinder_UserList extends ArrayIterator
{
    /**
     * @return UNL_Peoplefinder_Record
     */
    function current()
    {
        return new UNL_Peoplefinder_Record(array('uid'=>parent::current()));
    }
}