<?php
class UNL_Peoplefinder_FacultyEducationList_FacultyMember
{
    protected $data;
    protected $options = array();

    public function __construct($data = array(), $options = array())
    {
        $this->data    = $data;
        $this->options = $options;
    }

    public function __get($field)
    {
        switch($field) {
            case 'degree_string':
                return $this->getEducation();
            default:
                return $this->data[$field]; 
        }
    }

    public function getEducation()
    {
        return new UNL_Peoplefinder_FacultyEducationList_FacultyMember_Degrees($this->data['degree_string']);
    }

    /**
     * Get this faculty member's UNL_Peoplefinder_Record
     * 
     * @return UNL_Peoplefinder_Record
     */
    public function getRecord()
    {
        return $this->options['peoplefinder']->getByNUID($this->nu_id);
    }
}