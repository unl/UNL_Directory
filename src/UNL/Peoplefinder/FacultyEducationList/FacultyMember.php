<?php
class UNL_Peoplefinder_FacultyEducationList_FacultyMember
{
    protected $data;

    public function __construct($data = array())
    {
        $this->data = $data;
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
}