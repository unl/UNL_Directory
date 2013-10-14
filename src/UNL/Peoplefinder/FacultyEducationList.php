<?php
class UNL_Peoplefinder_FacultyEducationList extends LimitIterator
{
    /*
     * Columns of data within the CSV file
     */
    protected $cols = array();

    function __construct($options = array())
    {
        $file = new SplFileObject(__DIR__ . '/../../../data/faculty_education.csv');
        $file->setFlags(SplFileObject::READ_CSV);

        // Read the columns
        $this->cols = $file->current();

        parent::__construct($file, 1);
    }

    function current()
    {
        $data = parent::current();
        return new UNL_Peoplefinder_FacultyEducationList_FacultyMember(array_combine($this->cols, $data));
    }
}