<?php
/**
 * Peoplefinder class for UNL's online directory.
 * 
 * PHP version 5
 * 
 * @category  Services
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2007 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://peoplefinder.unl.edu/
 */
define('UNL_PF_DISPLAY_LIMIT', 30);
define('UNL_PF_RESULT_LIMIT', 100);

/**
 * Peoplefinder class for UNL's online directory.
 * 
 * PHP version 5
 * 
 * @category  Services
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2007 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://peoplefinder.unl.edu/
 */
class UNL_Peoplefinder
{
    static public $resultLimit        = UNL_PF_RESULT_LIMIT;
    static public $displayResultLimit = UNL_PF_DISPLAY_LIMIT;

    static public $url = '';

    /**
     * Options for this use.
     */
    public $options = array('view'   => 'instructions',
                            'format' => 'html',
                            'mobile' => false);

    /**
     * Driver for data retrieval
     *
     * @var UNL_Peoplefinder_DriverInterface
     */
    public $driver;

    /**
     * The results of the search
     * 
     * @var mixed
     */
    public $output;

    public $view_map = array('instructions' => 'UNL_Peoplefinder_Instructions',
                             'search'       => 'UNL_Peoplefinder_SearchController',
                             'record'       => 'UNL_Peoplefinder_Record');

    /**
     * This list contains the affiliations shown throughout the directory.
     * 
     * Certain affiliations are not appropriate for public display.
     *
     * @var array
     */
    public static $displayedAffiliations = array(
        'student',
//        'graduated',
        'faculty',
        'staff',
        'affiliate',
        'volunteer',
//        'retiree',
        'emeriti',
//        'continue services',
//        'rif',
//        'override',  // (will exist in guest ou)
//        'sponsored', // (will exist in guest ou)
        );

    protected static $replacement_data = array();

    /**
     * Constructor for the object.
     * 
     * @param array $options Options, format, driver, mobile etc.
     */
    function __construct($options = array())
    {
        if (!isset($options['driver'])) {
            $options['driver'] = new UNL_Peoplefinder_Driver_WebService();
        }

        $this->driver = $options['driver'];

        $this->options = $options + $this->options;

        if (isset($_SERVER['HTTP_ACCEPT'])
            && $this->options['format'] == 'html' && (
                ($this->options['mobile'] !== false && $this->options['mobile'] != 'no')
                || (preg_match('/text\/vnd\.wap\.wml|application\/vnd\.wap\.xhtml\+xml/', $_SERVER['HTTP_ACCEPT']))
                    || preg_match('/sony|symbian|nokia|samsung|mobile|windows ce|epoc|opera/', $_SERVER['HTTP_USER_AGENT'])
                    || preg_match('/mini|nitro|j2me|midp-|cldc-|netfront|mot|up\.browser|up\.link|audiovox/', $_SERVER['HTTP_USER_AGENT'])
                    || preg_match('/blackberry|ericsson,|panasonic|philips|sanyo|sharp|sie-/', $_SERVER['HTTP_USER_AGENT'])
                    || preg_match('/portalmmm|blazer|avantgo|danger|palm|series60|palmsource|pocketpc/', $_SERVER['HTTP_USER_AGENT'])
                    || preg_match('/smartphone|rover|ipaq|au-mic,|alcatel|ericy|vodafone\/|wap1\.|wap2\.|iPhone|Android/', $_SERVER['HTTP_USER_AGENT'])
            )) {
            $this->options['mobile'] = true;
        }

        try {
            $this->run();
        } catch(Exception $e) {
            $this->output[] = $e;
        }
    }

    /**
     * Get the main URL for this instance or an individual object
     *
     * @param mixed $mixed             An object to retrieve the URL to
     * @param array $additional_params Querystring params to add
     * 
     * @return string
     */
    public static function getURL($mixed = null, $additional_params = array())
    {
         
        $url = self::$url;
        
        if (is_object($mixed)) {
            switch (get_class($mixed)) {
            default:
                    
            }
        }
        
        return self::addURLParams($url, $additional_params);
    }

    /**
     * Add unique querystring parameters to a URL
     * 
     * @param string $url               The URL
     * @param array  $additional_params Additional querystring parameters to add
     * 
     * @return string
     */
    public static function addURLParams($url, $additional_params = array())
    {
        $params = array();
        $params = array_merge($params, $additional_params);

        $url .= '?';
        
        foreach ($params as $option=>$value) {
            if ($option == 'driver') {
                continue;
            }
            if ($option == 'format'
                && $value = 'html') {
                continue;
            }
            if (!empty($value)) {
                $url .= "&$option=$value";
            }
        }
        $url = str_replace('?&', '?', $url);
        return trim($url, '?;=');
    }

    /**
     * Simple router which determines what view to use, based on $_GET parameters
     * 
     * @return void
     */
    public function determineView()
    {
        switch(true) {
        case isset($this->options['q']):
        case isset($this->options['sn']):
        case isset($this->options['cn']):
            $this->options['view'] = 'search';
            return;
        case isset($this->options['uid']):
            $this->options['view'] = 'record';
            return;
        }

    }

    /**
     * Render output based on the view determined
     * 
     * @return void
     */
    function run()
    {
        $this->determineView();
        if (!isset($this->view_map[$this->options['view']])) {
            throw new Exception('Un-registered view', 404);
        }
        if ($this->view_map[$this->options['view']] == 'UNL_Peoplefinder_Record') {
            $this->output[] = $this->getUID($this->options['uid']);
            return;
        }
        $this->options['peoplefinder'] =& $this;

        $this->output[] = new $this->view_map[$this->options['view']]($this->options);
    }

    /**
     * Pass through calls to the driver.
     * 
     * @param string $method The method to call
     * @param mixed  $args   Arguments
     * 
     * @method UNL_Peoplefinder_Record getUID() getUID(string $uid) get a record
     * 
     * @return mixed
     */
    function __call($method, $args)
    {
        return call_user_func_array(array($this->driver, $method), $args);
    }

    /**
     * Get the path to the data directory for this project
     *
     * @return string
     */
    public static function getDataDir()
    {
        return dirname(__FILE__).'/../../data';
    }

    public static function setReplacementData($field, $data)
    {
        self::$replacement_data[$field] = $data;
    }
    
    function postRun($data)
    {

        if (isset(self::$replacement_data['doctitle'])
            && strstr($data, '<title>')) {
            $data = preg_replace('/<title>.*<\/title>/',
                                '<title>'.self::$replacement_data['doctitle'].'</title>',
                                $data);
            unset(self::$replacement_data['doctitle']);
        }

        if (isset(self::$replacement_data['head'])
            && strstr($data, '</head>')) {
            $data = str_replace('</head>', self::$replacement_data['head'].'</head>', $data);
            unset(self::$replacement_data['head']);
        }

        if (isset(self::$replacement_data['breadcrumbs'])
            && strstr($data, '<!-- InstanceBeginEditable name="breadcrumbs" -->')) {

            $start = strpos($data, '<!-- InstanceBeginEditable name="breadcrumbs" -->')+strlen('<!-- InstanceBeginEditable name="breadcrumbs" -->');
            $end = strpos($data, '<!-- InstanceEndEditable -->', $start);

            $data = substr($data, 0, $start).self::$replacement_data['breadcrumbs'].substr($data, $end);
            unset(self::$replacement_data['breadcrumbs']);
        }

        if (isset(self::$replacement_data['pagetitle'])
            && strstr($data, '<!-- InstanceBeginEditable name="pagetitle" -->')) {

            $start = strpos($data, '<!-- InstanceBeginEditable name="pagetitle" -->')+strlen('<!-- InstanceBeginEditable name="pagetitle" -->');
            $end = strpos($data, '<!-- InstanceEndEditable -->', $start);

            $data = substr($data, 0, $start).self::$replacement_data['pagetitle'].substr($data, $end);
            unset(self::$replacement_data['pagetitle']);
        }
        return $data;
    }
}
