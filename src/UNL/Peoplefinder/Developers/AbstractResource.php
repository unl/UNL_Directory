<?php
abstract class UNL_PeopleFinder_Developers_AbstractResource
{
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';
    const FORMAT_PARTIAL = 'partial';
    
    /**
     * @return string - a brief description of the resource
     */
    abstract public function getTitle();
    
    /**
     * @return string - a brief description of the resource
     */
    abstract public function getDescription();

    /**
     * @return mixed - an associative array of property=>description
     */
    abstract public function getAvailableFormats();

    /**
     * @return array - an associative array of property=>description
     */
    abstract public function getJsonProperties();

    /**
     * @return array - an associative array of property=>description
     */
    abstract public function getXmlProperties();

    /**
     * @return array - an associative array of property=>description
     * 
     * The default for partial is to not provide any properties as it is HTML
     * However, it might be helpful to provide some additional information for some resources
     */
    public function getPartialProperties()
    {
        return [];
    }

    /**
     * @return string - the absolute URL for the resource with placeholders
     */
    abstract public function getURI();

    /**
     * @return string - the absolute URL for the resource with placeholders filled in
     */
    abstract public function getExampleURI();
}
