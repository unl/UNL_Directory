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

    public static $cache_length = 900; //default to 15 minutes

    protected $recordsMap;

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

        $this->recordsMap = [
            'bio' => 'BIO',
            'courses' => 'SCHTEACH',
            'education' => 'EDUCATION',
            'grants' => 'CONGRANT',
            'honors' => 'AWARDHONOR',
            'papers' => 'INTELLCONT',
            'presentations' => 'PRESENT',
            'performances' => 'PERFORM_EXHIBIT',
        ];
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
        $isAPIError = false;

        if (!curl_errno($curl)) {
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode === 200) {
                // type juggle XML to JSON
                $array = json_decode(json_encode(simplexml_load_string($responseData, "SimpleXMLElement", LIBXML_NOCDATA)), true);
                $result = isset($array['Record'][$category]) ? $array['Record'][$category] : null;
            } else {
                // Server returns 500 errors for not found
                $result = null;
            }
        } else {
            curl_error($curl);
            error_log($errorMessage);
            $isAPIError = true;
        }

        curl_close($curl);

        if ($isAPIError) {
            $result = self::$cache->getSlow($key);

            if ($result) {
                return $result;
            }
        }

        try {
            $this->cache($key, $result);
        } catch (Exception $e) {
            error_log($e->message);
        }
        return $result;
    }

    public function getRecords($uid)
    {
        $data = $this->getCategory('PUBLIC_WEB', $uid);

        if ($data) {
            $records = new UNL_Knowledge_Records();
            foreach ($this->recordsMap as $var => $dataKey) {
                if (isset($data[$dataKey])) {
                    $records->$var = $this->cleanRecords($data[$dataKey]);
                }
            }

            return $records;
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
