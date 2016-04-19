<?php
class UNL_PeopleFinder_Developers_Department_Personnel extends UNL_PeopleFinder_Developers_AbstractResource
{
    /**
     * @return string - a brief description of the resource
     */
    public function getTitle()
    {
        return 'Department Personnel';
    }

    /**
     * @return string - a brief description of the resource
     */
    public function getDescription()
    {
        return 'This resource returns personnel information for a given department';
    }

    /**
     * @return mixed - an associative array of property=>description
     */
    public function getAvailableFormats()
    {
        return array(self::FORMAT_JSON, self::FORMAT_XML, self::FORMAT_PARTIAL);
    }

    /**
     * @return array - an associative array of property=>description
     */
    public function getJsonProperties()
    {
        return array('person' => '(Array) Array of <a href="?view=developers&resource=Record">person records</a>');
    }

    /**
     * @return array - an associative array of property=>description
     */
    public function getXmlProperties()
    {
        return array('person' => 'A list of <a href="?view=developers&resource=Record">person records</a>');
    }

    /**
     * @return string - the absolute URL for the resource with placeholders
     */
    public function getURI()
    {
        return UNL_Officefinder::getURL() . '{id}|{org_unit}/personnel';
    }

    /**
     * @return string - the absolute URL for the resource with placeholders filled in
     */
    public function getExampleURI()
    {
        return UNL_Officefinder::getURL() . '362/personnel';
    }
}
