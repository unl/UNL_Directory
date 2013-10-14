<?php
class UNL_Peoplefinder_FacultyEducationList extends FilterIterator
{
    /*
     * Columns of data within the CSV file
     */
    protected $cols = array();

    protected $seen_nuids = array();

    function __construct($options = array())
    {
        ini_set('auto_detect_line_endings', true);
        $file = new SplFileObject(__DIR__ . '/../../../data/faculty_education.csv');
        $file->setFlags(SplFileObject::READ_CSV);

        // Read the columns
        $this->cols = $file->current();

        parent::__construct(new LimitIterator($file, 1));
    }

    function current()
    {
        $data = parent::current();
        return new UNL_Peoplefinder_FacultyEducationList_FacultyMember(array_combine($this->cols, $data));
    }

    function accept()
    {
        $faculty = $this->current();
        if (in_array($faculty->nu_id, $this->seen_nuids)) {
            return false;
        }

        // Mark this faculty member as seen
        $this->seen_nuids[] = $faculty->nu_id;
        return true;
    }
}