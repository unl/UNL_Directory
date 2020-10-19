<?php
class UNL_Peoplefinder_SearchResults extends ArrayIterator
{
    public $options = array('affiliation' => '',
                            'results'     => array());

    function __construct($options = array())
    {
        $this->options = $options + $this->options;

        parent::__construct($this->options['results']);
    }

    /**
     * Convert a group of results into results by affiliation
     * 
     * @param Traversable $results Array of peoplefinder records
     * 
     * @return associative array
     */
    public static function groupByAffiliation($results)
    {
        $by_affiliation = array();

        foreach ($results as $record) {
            if (isset($record->eduPersonAffiliation)) {
                $affiliations = static::affiliationsToArray($record->eduPersonAffiliation);
                foreach (array_unique($affiliations) as $affiliation) {
                    if (!in_array($affiliation, UNL_Peoplefinder::$displayedAffiliations)) {
                        // This is an affiliation we do not want displayed
                        continue;
                    }
                    if (!isset($by_affiliation[$affiliation])) {
                        $by_affiliation[$affiliation] = array();
                    }
                    $by_affiliation[$affiliation][] = $record;
                }
            }
        }
        return $by_affiliation;
    }

    public static function affiliationsToArray($affiliations) {
        if ($affiliations instanceof ArrayIterator) {
            $affiliations = $affiliations->getArrayCopy();
        }
        return $affiliations;
    }

    public static function affiliationSort($affiliation1, $affiliation2)
    {
        if ($affiliation1 == 'emeriti') {
            return true;
        }
        return $affiliation1 > $affiliation2;
    }
}