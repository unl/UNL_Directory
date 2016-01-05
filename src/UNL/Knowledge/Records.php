<?php

class UNL_Knowledge_Records
{
	public $bio;

    public $courses;

    public $education;

    public $grants;

    public $honors;

    public $papers;

    public $presentations;

    public $performances;

    protected $recordsMap = [
        'bio' => 'BIO',
        'courses' => 'SCHTEACH',
        'education' => 'EDUCATION',
        'grants' => 'CONGRANT',
        'honors' => 'AWARDHONOR',
        'papers' => 'INTELLCONT',
        'presentations' => 'PRESENT',
        'performances' => 'PERFORM_EXHIBIT',
    ];

    public function getRecordsMap()
    {
    	return $this->recordsMap;
    }

    public function getKeyCollection($collection, $tags, $tagFilter = [])
    {
    	$stringResults = [];

    	if (!isset($this->$collection) || !isset($this->recordsMap[$collection])) {
    		return $stringResults;
    	}

    	$section = $this->recordsMap[$collection];

    	foreach ($this->$collection as $item) {
    		$skipItem = false;

    		foreach ($tagFilter as $tag => $filter) {
    			if (isset($item[$section][$tag]) && $item[$section][$tag] == $filter) {
    				$skipItem = true;
    				break;
    			}
    		}

    		if ($skipItem) {
    			continue;
    		}

    		$resultCandidates = [];

    		foreach ($tags as $tag) {
    			$dereference = false;

    			if (is_array($tag)) {
    				if (!isset($tag['tag'])) {
    					$resultCandidates[] = $this->getMergedKey($item, $section, $tag);
    					continue;
    				}

    				if (isset($tag['dereference'])) {
    					$dereference = $tag['dereference'];
    				}

    				$tag = $tag['tag'];
    			}

    			if ($tag) {
    				$candidateResult = $this->getKey($item, $section, $tag);

    				if ($dereference) {
    					foreach ($dereference as $key) {
    						if (!isset($candidateResult[$key])) {
    							$candidateResult = false;
    							break;
    						}

    						$candidateResult = $candidateResult[$key];
    					}
    				}

    				$resultCandidates[] = $candidateResult;
    			}
    		}

    		$stringResults[] = implode(', ', array_filter($resultCandidates));
    	}

    	return $stringResults;
    }

    protected function getKey($item, $section, $tag) {
        if (isset($item[$section][$tag]) && is_scalar($item[$section][$tag])) {
            return $item[$section][$tag];
        } else {
            return false;
        }
    }

    protected function getMergedKey($item, $section, $tags)
    {
    	$results = [];

    	foreach ($tags as $tag) {
    		$candidateResult = $this->getKey($item, $section, $tag);

    		if (!$candidateResult) {
    			return false;
    		}

    		$results[] = $candidateResult;
    	}

    	return implode(' ', $results);
    }
}
