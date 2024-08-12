<?php
class UNL_Peoplefinder_FacultyEducationList extends FilterIterator implements Countable
{
    /*
     * Columns of data within the CSV file
     */
    protected $cols = array();

    protected $seen_nuids = array();

    /**
     * Array of options passed from the constructor
     * @var unknown
     */
    public $options = array(
            'offset' => 0,
            'limit'  => 30,
            );

    function __construct($options = array())
    {

        $this->options = $options + $this->options;
        
        ini_set('auto_detect_line_endings', true);
        $file = new SplFileObject($this->getFacultyEducationFileName());
        $file->setFlags(SplFileObject::READ_CSV);

        // Read the columns
        $this->cols = $file->current();

        parent::__construct(new LimitIterator($file, 1));
    }

    protected function getFacultyEducationFileName()
    {
        return __DIR__ . '/../../../data/faculty_education.csv';
    }

    function current()
    {
        $data = array_combine($this->cols, parent::current());
        return new UNL_Peoplefinder_FacultyEducationList_FacultyMember($data, $this->options);
    }

    function accept(): bool
    {
        $faculty = $this->current();
        if (in_array($faculty->nu_id, $this->seen_nuids)) {
            return false;
        }

        // Mark this faculty member as seen
        $this->seen_nuids[] = $faculty->nu_id;
        return true;
    }

    public function count(): int
    {
        $count = 0;
        // Reset the number of seen NUIDs
        $this->seen_nuids = array();
        foreach ($this as $null) {
            $count++;
        }
        // Reset the number of seen NUIDs
        $this->seen_nuids = array();
        return $count;
    }

    public function getDateLastUpdated($format = 'm/d/y')
    {
        return date($format, filemtime($this->getFacultyEducationFileName()));
    }
}