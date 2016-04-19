<?php
class UNL_PeopleFinder_Developers_Search extends UNL_PeopleFinder_Developers_AbstractResource
{
    /**
     * @return string - a brief description of the resource
     */
    public function getTitle()
    {
        return 'Search';
    }

    /**
     * @return string - a brief description of the resource
     */
    public function getDescription()
    {
        return 'The the results of a search in different formats';
    }

    /**
     * @return mixed - an associative array of property=>description
     */
    public function getAvailableFormats()
    {
        return [self::FORMAT_JSON, self::FORMAT_XML, self::FORMAT_PARTIAL];
    }

    /**
     * @return array - an associative array of property=>description
     */
    public function getJsonProperties()
    {
        ['{records}' => 'An array of all the <a href="?view=developers&resource=Record">person records</a> for the given query'];
    }

    /**
     * @return array - an associative array of property=>description
     */
    public function getXmlProperties()
    {
        ['{records}' => 'A list of all the <a href="?view=developers&resource=Record">person records</a> for the given query'];
    }

    /**
     * @return string - the absolute URL for the resource with placeholders
     */
    public function getURI()
    {
        return $this->uri = UNL_Peoplefinder::$url . 'service.php?q={query}';
    }

    /**
     * @return string - the absolute URL for the resource with placeholders filled in
     */
    public function getExampleURI()
    {
        return UNL_Peoplefinder::$url . 'service.php?q=fairchild';
    }
}
