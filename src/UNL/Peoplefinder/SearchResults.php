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
        $by_affiliation                  = array();
        $by_affiliation['faculty']       = array();
        $by_affiliation['staff']         = array();
        $by_affiliation['student']       = array();
        $by_affiliation['organizations'] = array();

        foreach ($results as $record) {
            foreach ($record->ou as $ou) {
                if ($ou == 'org') {
                    $by_affiliation['organizations'][] = $record;
                    break;
                }
            }
    
            if (isset($record->eduPersonAffiliation)) {
                foreach ($record->eduPersonAffiliation as $affiliation) {
                    $by_affiliation[$affiliation][] = $record;
                }
            }
        }
        return $by_affiliation;
    }
}