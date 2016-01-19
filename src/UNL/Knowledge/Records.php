<?php

class UNL_Knowledge_Records implements JsonSerializable
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

    protected function getKeyCollection($collection, $tags, $tagFilter = [])
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

    protected function getKeyCollectionOrNull($collection, $tags, $tagFilter = [])
    {
        $results = $this->getKeyCollection($collection, $tags, $tagFilter);

        if (!$results) {
            return null;
        }

        return $results;
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

    protected function getPublicProperties()
    {
        $self = $this;
        $getPublicProperties = function() use ($self) {
            return get_object_vars($self);
        };
        $getPublicProperties = $getPublicProperties->bindTo(null, null);

        return $getPublicProperties();
    }

    public function jsonSerialize()
    {
        $data = $this->getPublicProperties();

        // serialize the formatted values
        foreach ($data as $var => $value) {
            $formatCallable = [$this, 'getFormatted' . ucfirst($var)];

            if (is_callable($formatCallable)) {
                $value = call_user_func($formatCallable);
            }

            $data[$var] = $value;
        }

        return $data;
    }

    public function getFormattedBio()
    {
        if (!$this->bio) {
            return null;
        }

        return $this->bio;
    }

    public function getFormattedEducation()
    {
        return $this->getKeyCollectionOrNull('education', [
            'DEG',
            'SCHOOL',
            'YR_COMP',
        ]);
    }

    public function getFormattedCourses()
    {
        return $this->getKeyCollectionOrNull('courses', [
            ['COURSEPRE', 'COURSENUM'],
            'TITLE',
            ['TYT_TERM', 'TYY_TERM'],
        ]);
    }

    public function getFormattedPapers()
    {
        return $this->getKeyCollectionOrNull('papers', [
            'TITLE',
            'JOURNAL_NAME',
            'BOOK_TITLE',
            ['DTM_PUB', 'DTY_PUB'],
        ]);
    }

    public function getFormattedGrants()
    {
        return $this->getKeyCollectionOrNull('grants', [
            'TITLE',
            'SPONORG',
            ['tag' => 'CONGRANT_INVEST', 'dereference' => [0, 'ROLE']],
            ['DTM_START', 'DTY_START'],
        ], ['STATUS' => 'Declined']);
    }

    public function getFormattedPerformances()
    {
        return $this->getKeyCollectionOrNull('performances', [
            'TITLE',
            'LOCATION',
            ['DTM_START', 'DTY_START'],
        ]);
    }

    public function getFormattedPresentations()
    {
        return $this->getKeyCollectionOrNull('presentations', [
            'TITLE',
            'ORG',
            'LOCATION',
            ['DTM_START', 'DTY_START'],
        ]);
    }

    public function getFormattedHonors()
    {
        return $this->getKeyCollectionOrNull('honors', [
            'NAME',
            'ORG',
            'DTY_DATE',
        ]);
    }
}
