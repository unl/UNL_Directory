<?php
class UNL_Common_JSONDataDriver implements UNL_Common_DataDriverInterface
{
    public static $tour_uri = 'http://maps.unl.edu/';

    function getAllBuildings()
    {
        $uri = self::$tour_uri.'?view=allbuildings&format=json';
        return $this->retrieveJSONData($uri);
    }

    function getCityBuildings()
    {
        $uri = self::$tour_uri.'?view=citybuildings&format=json';
        return $this->retrieveJSONData($uri);
    }

    function getEastBuildings()
    {
        $uri = self::$tour_uri.'?view=eastbuildings&format=json';
        return $this->retrieveJSONData($uri);
    }

    protected function retrieveJSONData($uri)
    {
        if ($contents = file_get_contents($uri)) {
            // try decoding it
            $data = json_decode($contents, true);
            if (is_array($data)) {
                return $data;
            }
        }
        return false;
    }
}