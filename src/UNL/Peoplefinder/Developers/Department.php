<?php
class UNL_Peoplefinder_Developers_Department extends UNL_Peoplefinder_Developers_AbstractResource
{
    /**
     * @return string - a brief description of the resource
     */
    public function getTitle()
    {
        return 'Department Record';
    }

    public function getDescription()
    {
        return 'Get details about a department';
    }

    public function getAvailableFormats()
    {
        return [self::FORMAT_JSON, self::FORMAT_XML, self::FORMAT_PARTIAL];
    }

    public function getJsonProperties()
    {
        return [
            'name' => 'Name of this deparmtnet/unit',
            'org_unit' => 'Official org unit ID from SAP',
        ];
    }

    public function getXmlProperties()
    {
        return [
            'department' => 'The department element, which contains \'parent\' and \'child\' children elements.',
            'parent' => 'A link to the parent department the link is available in the xlink:href attribute',
            'child' => 'A link to the child department, the link is available in the xlink:href attribute (there can be many of these)',
        ];
    }

    public function getPartialProperties()
    {
        return [];
    }

    /**
     * @return string - the absolute URL for the resource with placeholders
     */
    public function getURI()
    {
        return UNL_Officefinder::getURL() . '{id}|{org_unit}?format={format}';
    }

    /**
     * @return string - the absolute URL for the resource with placeholders filled in
     */
    public function getExampleURI()
    {
        return UNL_Officefinder::getURL()  . '362?format={format}';
    }
}
