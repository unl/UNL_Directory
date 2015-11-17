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
    public static $cache_length = 900; //default to 15 minutes

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
            error_log($errorMessage);
            $result = 'Error retrieving Faculty CV Data.';
        } else {
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode === 200) {
                $xml = simplexml_load_string($responseData, "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);

                $result = isset($array['Record'][$category]) ? $array['Record'][$category] : null;

                try {
                    $this->cache($key, $result);
                } catch (Exception $e) {
                    error_log($e->message);
                }
            } else {
                $result = 'Error retrieving Faculty CV Data.';
            }
        }

        curl_close($curl);

        return $result;
    }

    function getRecords($uid)
    {
        $records = new UNL_Knowledge();

        $results = $this->getCategory('PUBLIC_WEB', $uid);

        if (is_array($results)) {
            $records->public_web = $results;
            $keys_map = array(
                'BIO' => 'bio',
                'SCHTEACH' => 'courses',
                'EDUCATION' => 'education',
                'CONGRANT' => 'grants',
                'AWARDHONOR' => 'honors',
                'INTELLCONT' => 'papers',
                'PRESENT' => 'presentations',
                'PERFORM_EXHIBIT' => 'performances'
            );
            foreach ($keys_map as $key => $value) {
                if (array_key_exists($key, $records->public_web)) { 
                    $records->$value = $this->cleanRecords($records->public_web[$key]);
                } else {
                    $records->$value = NULL;
                }
            }   
        } else {
            $records->error = $results;
        }

        return $records;
    }

    function cleanRecords($records)
    {
        if (is_array($records)) {
            foreach ($records as $key => $value) {
                if (isset($value['REF']) && $value['REF'] == FALSE) {
                    // Clear empty record within an array that has a blank REF value
                    unset($records[$key]);
                }
            }

            if (isset($records['REF']) && $records['REF'] == FALSE) {
                // Clear empty record that has a blank REF value
                $records = NULL;
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
