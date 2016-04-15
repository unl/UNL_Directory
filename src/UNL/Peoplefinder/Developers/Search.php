<?php
class UNL_PeopleFinder_Developers_Search
{
    public $title       = 'Search';
    
    public $uri         = 'service.php?q={query}';
    
    public $exampleURI  = 'service.php?q=fairchild';
    
    public $properties  = array(
        array('{records}', 'An array of all the <a href="?view=developers&resource=Record">Peoplefinder Records</a> for the givien query.', true, true),
    );
                                
    public $formats     = array('json', 'xml', 'partial');
    
    function __construct()
    {
        $this->uri = UNL_Peoplefinder::$url . $this->uri;
        $this->exampleURI  = UNL_Peoplefinder::$url . $this->exampleURI;
    }
}
