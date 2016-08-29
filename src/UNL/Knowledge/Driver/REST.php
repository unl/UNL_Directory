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

    protected static $cache;

    public static $memcache_host;

    public static $memcache_port;

    protected static $key_prefix = 'UNL_Directory_FacultyData_';

    public static $cache_length = 1200; //default to 20 minutes

    public function __construct($options = array())
    {
        if (isset($options['service_url'])) {
            $this->service_url = $options['service_url'];
        }

        self::$cache = UNL_Peoplefinder_Cache::factory([
            'memcache_host' => self::$memcache_host,
            'memcache_port' => self::$memcache_port,
            'fast_lifetime' => self::$cache_length,
        ]);
    }

    protected function getFromCache($key)
    {
        return self::$cache->get($key);
    }

    protected function cache($key, $object)
    {
        # cache for the given time
        self::$cache->set($key, $object, time() + self::$cache_length);
    }

    protected function getCategory($category, $uid)
    {
        # check the cache for this
        $key = self::$key_prefix . $category . '_' . $uid;

        try {
            if (($result = $this->getFromCache($key)) !== false) {
                return $result;
            } 

            # if it's not there, for whatever reason, hit the slow cache
            $result = self::$cache->getSlow($key);

            if ($result) {
                return $result;
            }
        } catch (Exception $e) {
            error_log($e);
        }
        return null;
    }

    protected function getCategoryForAll($category)
    {
        $full_result = array();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $this->service_url . $category,
            CURLOPT_USERPWD         => UNL_Knowledge_Driver_REST::$service_user . ':' . UNL_Knowledge_Driver_REST::$service_pass,
            CURLOPT_ENCODING        => 'gzip',
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_CONNECTTIMEOUT  => 15,
            CURLOPT_TIMEOUT         => 15,
        ));

        $responseData = curl_exec($curl);
        $isAPIError = false;
        $error_message = '';

        if (!curl_errno($curl)) {
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode === 200) {
                // type juggle XML to JSON
                $array = json_decode(json_encode(simplexml_load_string($responseData, "SimpleXMLElement", LIBXML_NOCDATA)), true);
                
                # this query gives an array of records under teh "Record" key
                foreach ($array['Record'] as $record) {
                    $result = isset($record[$category]) ? $record[$category] : null;
                    $key = self::$key_prefix . $category . '_' . $record['@attributes']['username'];
                    try {
                        $this->cache($key, $result);
                    } catch (Exception $e) {
                        error_log($e);
                    }
                    $full_result[] = $result;
                }
            } else {
                // Server returns 500 errors for not found
                $full_result = null;
            }
        } else {
            $error_message = curl_error($curl);
            error_log($error_message);
            $isAPIError = true;
        }

        curl_close($curl);

        if ($isAPIError) {
            $full_result = 'ERROR: ' . $error_message ;
            return $full_result;
        }

        return $full_result;
    }

    public function getRecords($uid)
    {
        $data = $this->getCategory('PUBLIC_WEB', $uid);

        if ($data) {
            $records = new UNL_Knowledge_Records();
            foreach ($records->getRecordsMap() as $var => $dataKey) {
                if (isset($data[$dataKey])) {
                    $records->$var = $this->cleanRecords($data[$dataKey]);
                }
            }

            return $records;
        }

        return null;
    }

    public function getAllRecords()
    {
        $full_result = $this->getCategoryForAll('PUBLIC_WEB');

        if (is_array($full_result)) {
            $full_records = array();

            foreach ($full_result as $data) {
                $records = new UNL_Knowledge_Records();
                foreach ($records->getRecordsMap() as $var => $dataKey) {
                    if (isset($data[$dataKey])) {
                        $records->$var = $this->cleanRecords($data[$dataKey]);
                    }
                }

                $full_records[] = $records;
            }

            return $full_records;
        } else if (is_string($full_result)) {
            return $full_result;
        }

        return null;       
    }

    protected function cleanRecords($records)
    {
        if (is_array($records)) {
            // Clear empty record within an array that has a blank REF value
            $records = array_filter($records, function($value) {
                return !(isset($value['REF']) && $value['REF'] == false);
            });

            if (isset($records['REF']) && $records['REF'] == false) {
                // Clear empty record that has a blank REF value
                $records = null;
            } else if (isset($records['REF'])) {
                // Convert single record to indexed array at key 0
                $records = [$records];
            }
        }

        return $records;
    }
}
