<?php
class UNL_Officefinder_DepartmentList_AlphaListing_LoginRequired
{
    public $options;
    public $user;
    public $output;

    public function __construct($options = array())
    {
        $this->options = $options;

        // Login is required to view this
        $this->user = UNL_Officefinder::getUser(true);

        // Build the alpha listing
        $this->output = new UNL_Officefinder_DepartmentList_AlphaListing($options);
        session_commit();
    }
}
