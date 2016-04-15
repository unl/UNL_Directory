<?php
class UNL_PeopleFinder_Developers_Department_Personnel
{
    public $title       = 'Department Personnel';

    public $uri         = '{id}|{org_unit}/personnel';

    public $exampleURI  = '362/personnel';

    public $properties  = array(
        array('person', '(Array) Array of person objects', true, true),
    );
                                
    public $formats = array('json', 'xml', 'partial');
    
    function __construct()
    {
        $this->uri = UNL_Officefinder::getURL() . $this->uri;
        $this->exampleURI  = UNL_Officefinder::getURL() . $this->exampleURI;
    }
}