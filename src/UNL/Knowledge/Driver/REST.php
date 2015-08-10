<?php

class UNL_Knowledge_Driver_REST implements UNL_Knowledge_DriverInterface
{
    /**
     * The address to the webservice
     *
     * @var string
     */
    public $service_url = 'https://dmdata.unl.edu/webservice/rest/';

    public static $service_pass;

    function __construct($options = array())
    {
        if (isset($options['service_url'])) {
            $this->service_url = $options['service_url'];
        }
    }

    function getCategory($category, $uid)
    {
        $options = array(
            "http" => array(
                "method" => "GET",
                "header" => "UNL_WS_AUTH: ".UNL_Knowledge_Driver_REST::$service_pass."\r\n" .
                            "Accept: application/json\r\n"
            )
        );
        $context = stream_context_create($options);
        $records = file_get_contents($this->service_url.$category.'?username='.urlencode($uid), false, $context);

        if (false === $records) {
            throw new Exception('Could not find that user!', 404);
        }

        if ($records != 'null' && $records != null && !$records = json_decode($records)) {var_dump($records);

            throw new Exception('Error retrieving the data from the web service for ' . $category);
        }

        // Convert a single record from an object to an array with a single member
        if ($records != 'null' && $records != null && is_object($records->{$category})) {
            // Don't make
            if ($category != 'BIOSKETCH' && $category != 'PCI') {
                $records->{$category} = array($records->{$category});
            }
        }

        return isset($records->{$category}) ? $records->{$category} : null;
    }

    function getRecords($uid)
    {
        $records = new UNL_Knowledge();

        $records->admin = $this->getCategory('ADMIN', $uid);

        if ($records->admin) {
            $records->biosketch = $this->getCategory('BIOSKETCH', $uid);
            $records->courses = $this->getCategory('SCHTEACH', $uid);
            $records->education = $this->getCategory('EDUCATION', $uid);
            //$records->grants = $this->getCategory('CONGRANT', $uid);
            //$records->papers = $this->getCategory('INTELLCONT', $uid);
            $records->personal = $this->getCategory('PCI', $uid);
        }

        return $records;
    }
}
