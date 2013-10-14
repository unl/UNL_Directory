<?php
class UNL_Peoplefinder_FacultyEducationList_FacultyMember_Degrees extends FilterIterator
{
    public function __construct($degree_string = '')
    {
        $degree_string = trim($degree_string, ', ');

        $degree_string = explode(',', $degree_string);
        $degrees = new ArrayIterator($degree_string);
        parent::__construct($degrees);
    }

    public function accept()
    {
        $degree = trim($this->current());
        if (substr($degree, 0, 1) == 'B') {
            return false;
        }

        return true;
    }
}