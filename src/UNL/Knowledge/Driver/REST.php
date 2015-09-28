<?php

class UNL_Knowledge_Driver_REST implements UNL_Knowledge_DriverInterface
{
    /**
     * The address to the webservice
     *
     * @var string
     */
    public $service_url = 'https://www.digitalmeasures.com/login/service/v4/SchemaData/INDIVIDUAL-ACTIVITIES-University/';

    public static $service_user;

    public static $service_pass;

    public static $cache;
    public static $memcache_host;
    public static $memcache_port;
    public static $key_prefix = 'UNL_Directory_FacultyData_';
    public static $cache_length = 86400; //default to one day

    function __construct($options = array())
    {
        if (isset($options['service_url'])) {
            $this->service_url = $options['service_url'];
        }

        self::$cache = new Memcached;
        self::$cache->addServer(self::$memcache_host, self::$memcache_port);
    }

    function getFromCache($key)
    {
        return self::$cache->get($key);
    }

    function cache($key, $object)
    {
        # cache for the given time
        self::$cache->set($key, $object, time() + self::$cache_length);
    }

    function getCategory($category, $uid)
    {
        # check the cache for this
        $key = self::$key_prefix . $category . '_' . $uid;

        try {
            if (($result = $this->getFromCache($key)) !== FALSE) {
                return $result;
            }
        } catch (Exception $e) {
            error_log($e->message);
        }
        
        # if that doesn't work, curl the API
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $this->service_url . 'USERNAME:' . $uid . '/' . $category,
            CURLOPT_USERPWD         => UNL_Knowledge_Driver_REST::$service_user . ':' . UNL_Knowledge_Driver_REST::$service_pass,
            CURLOPT_ENCODING        => 'gzip',
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_RETURNTRANSFER  => true,
        ));

        $responseData = curl_exec($curl);

        if (curl_errno($curl)) {
            $errorMessage = curl_error($curl);
        } else {
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode === 200) {
                $xml = simplexml_load_string($responseData, "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
            }
        }

        curl_close($curl);

        $result = isset($array['Record'][$category]) ? $array['Record'][$category] : null;

        try {
            $this->cache($key, $result);
        } catch (Exception $e) {
            error_log($e->message);
        }
        return $result;
    }

    function getRecords($uid)
    {
        $records = new UNL_Knowledge();

        $records->public_web = $this->getCategory('PUBLIC_WEB', $uid);

        if ($records->public_web) {
            $records->bio           = $this->cleanRecords($records->public_web['BIO']);
            $records->courses       = $this->cleanRecords($records->public_web['SCHTEACH']);
            $records->education     = $this->cleanRecords($records->public_web['EDUCATION']);
            $records->grants        = $this->cleanRecords($records->public_web['CONGRANT']);
            $records->honors        = $this->cleanRecords($records->public_web['AWARDHONOR']);
            $records->papers        = $this->cleanRecords($records->public_web['INTELLCONT']);
            $records->presentations = $this->cleanRecords($records->public_web['PRESENT']);
        }

        return $records;
    }

    function cleanRecords($records)
    {
        if (is_array($records)) {
            foreach ($records as $key => $value) {
                if (isset($value['REF']) && $value['REF'] == false) {
                    // Clear empty record within an array that has a blank REF value
                    unset($records[$key]);
                }
            }

            if (isset($records['REF']) && $records['REF'] == false) {
                // Clear empty record that has a blank REF value
                $records = null;
            } else if (isset($records['REF'])) {
                // Convert single record to indexed array at key 0
                $temp = $records;
                $records = array();
                $records[0] = $temp;
            }
        }



        return $records;
    }
}
