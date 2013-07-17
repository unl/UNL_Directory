<?php
class UNL_Peoplefinder_SearchController
{
    public $options = array('q'           => '',
                            'affiliation' => '');
    
    /**
     * The search results
     * @var UNL_Peoplefinder_SearchResults
     */
    public $results;

    public $dept_results;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;

        $this->run();
    }

    function run()
    {
        $search_method = 'getExactMatches';

        if (is_numeric(str_replace(array('-', '(', ')'),
                                   '',
                                   $this->options['q']))) {
            // Phone number search
            $search_method = 'getPhoneMatches';
        } elseif (isset($this->options['sn']) || isset($this->options['cn'])) {
            // Detailed search
            $search_method = 'getAdvancedSearchMatches';
            $this->options['q'] = array('cn'=>'', 'sn'=>'');
            if (isset($this->options['sn'])) {
                $this->options['q']['sn'] = $this->options['sn'];
            }
            if (isset($this->options['cn'])) {
                $this->options['q']['cn'] = $this->options['cn'];
            }
        } elseif (strpos($this->options['q'], 'd:') === 0) {
            $search_method = 'getHRPrimaryDepartmentMatches';
            $this->options['q'] = substr($this->options['q'], 2);
        } else {
            // Standard text search, run exact matches first.
        }
        
        if (isset($this->options['method'])) {
            switch($this->options['method']) {
                case 'getLikeMatches':
                case 'getExactMatches':
                case 'getPhoneMatches':
                    $search_method = $this->options['method'];
                    break;
            }
        }

        if (!is_array($this->options['q'])
            && strlen($this->options['q']) <= 2) {
            throw new UNL_Peoplefinder_InvalidArgumentException('Too few characters were entered.');
        }

        $this->results = new UNL_Peoplefinder_SearchResults(
            $this->options + array('results'=>
                $this->options['peoplefinder']->$search_method($this->options['q'], $this->options['affiliation'])));

        if ($search_method != 'getAdvancedSearchMatches') {
            if (preg_match('/^[\d]{8}$/', $this->options['q'])) {
                $this->dept_results = new UNL_Officefinder_DepartmentList_OrgUnitSearch($this->options);
            } else {
                $this->dept_results = new UNL_Officefinder_DepartmentList_NameSearch($this->options);
            }
        }
    }
}