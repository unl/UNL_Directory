<?php
class UNL_Peoplefinder_Driver_WebService implements UNL_Peoplefinder_DriverInterface
{
    public $service_url = 'http://peoplefinder.unl.edu/service.php';
    
    function getExactMatches($query, $affiliation = null)
    {
        $results = file_get_contents($this->service_url.'?q='.urlencode($query).'&format=php&affiliation='.urlencode($affiliation).'&method=getExactMatches');
        if ($results) {
            $results = unserialize($results);
        }
        return $results;
    }
    function getAdvancedSearchMatches($query, $affliation = null)
    {
        throw new Exception('Not implemented yet');
    }
    function getLikeMatches($query, $affiliation = null)
    {
        $results = file_get_contents($this->service_url.'?q='.urlencode($query).'&format=php&affiliation='.urlencode($affiliation).'&method=getLikeMatches');
        if ($results) {
            $results = unserialize($results);
        }
        return $results;
    }
    function getPhoneMatches($query, $affiliation = null)
    {
        $results = file_get_contents($this->service_url.'?q='.urlencode($query).'&format=php&affiliation='.urlencode($affiliation).'&method=getPhoneMatches');
        if ($results) {
            $results = unserialize($results);
        }
        return $results;
    }
    
    function getUID($uid)
    {
        $record = file_get_contents($this->service_url.'?uid='.urlencode($uid).'&format=php');
        if ($record) {
            $record = unserialize($record);
        }
        return $record;
    }
}
?>