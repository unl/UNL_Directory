<?php
class UNL_Peoplefinder_SearchController
{
    public $options = [
        'q' => '',
        'affiliation' => ''
    ];

    /**
     * The search results
     * @var UNL_Peoplefinder_SearchResults
     */
    public $results;

    public $likeResults;

    public $dept_results;

    public function __construct($options = [])
    {
        $this->options = $options + $this->options;

        $this->run();
    }

    public function run()
    {
        /*if ($this->options['format'] === 'html') {
            $hash = '#q/';

            if (isset($this->options['sn']) || isset($this->options['cn'])) {
                if (isset($this->options['cn'])) {
                    $hash .= $this->options['cn'];
                }

                $hash .= '/';

                if (isset($this->options['sn'])) {
                    $hash .= $this->options['cn'];
                }
            } else {
                $hash .= $this->options['q'];
            }

            header('Location: ' . UNL_Peoplefinder::getURL() . $hash);
            exit();
        }*/

        $search_method = 'getExactMatches';

        if (is_numeric(str_replace(['-', '(', ')'], '', $this->options['q']))) {
            // Phone number search
            $search_method = 'getPhoneMatches';
        } elseif (isset($this->options['sn']) || isset($this->options['cn'])) {
            // Detailed search
            $search_method = 'getAdvancedSearchMatches';
            $this->options['q'] = [
                'cn' => '',
                'sn' => ''
            ];

            if (isset($this->options['sn'])) {
                $this->options['q']['sn'] = $this->options['sn'];
            }

            if (isset($this->options['cn'])) {
                $this->options['q']['cn'] = $this->options['cn'];
            }
        } elseif (strpos($this->options['q'], 'd:') === 0) {
            $search_method = 'getHRPrimaryDepartmentMatches';
            $this->options['q'] = substr($this->options['q'], 2);
        }

        $allowedRequestedMethods = [
            'getLikeMatches',
            'getExactMatches',
            'getPhoneMatches',
        ];

        if (isset($this->options['method']) && in_array($this->options['method'], $allowedRequestedMethods)) {
            $search_method = $this->options['method'];
        }

        if (!is_array($this->options['q']) && strlen($this->options['q']) <= 2) {
            throw new UNL_Peoplefinder_InvalidArgumentException('Too few characters were entered.');
        }

        $peopleResults = $this->options['peoplefinder']->$search_method($this->options['q'], $this->options['affiliation']);
        $this->results = new UNL_Peoplefinder_SearchResults($this->options + ['results' => $peopleResults]);

        if ($search_method != 'getAdvancedSearchMatches') {
            if (preg_match('/^[\d]{8}$/', $this->options['q'])) {
                $this->dept_results = new UNL_Officefinder_DepartmentList_OrgUnitSearch($this->options);
            } else {
                $this->dept_results = new UNL_Officefinder_DepartmentList_NameSearch($this->options);
            }
        }

        $resultCount = count($this->results);

        if (is_array($this->options['q']) || $resultCount >= UNL_Peoplefinder::$resultLimit) {
            $this->likeResults = [];
        } else {
            $this->likeResults = $this->options['peoplefinder']->getLikeMatches($this->options['q'], null, $this->results);
        }
    }
}
