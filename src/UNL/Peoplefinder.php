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

    public static function getURL()
    {
        if (defined('UNL_PEOPLEFINDER_URI')) {
            return UNL_PEOPLEFINDER_URI;
        }
        return self::$url;
    }

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

    function run()
    {
        $this->determineView();
        if (isset($this->view_map[$this->options['view']])) {
            if ($this->view_map[$this->options['view']] == 'UNL_Peoplefinder_Record') {
                $this->output[] = $this->getUID($this->options['uid']);
                return;
            }
            $this->options['peoplefinder'] =& $this;
            $this->output[] = new $this->view_map[$this->options['view']]($this->options);
        } else {
            throw new Exception('Un-registered view', 404);
        }
    }

    /**
     * Pass through calls to the driver.
     * 
     * @method UNL_Peoplefinder_Record getUID() getUID(string $uid) get a record
     * 
     * @param string $method The method to call
     * @param mixed  $args   Arguments
     * 
     * @return mixed
     */
    function __call($method, $args)
    {
        return call_user_func_array(array($this->driver, $method), $args);
    }

    public static function getDataDir()
    {
        return dirname(__FILE__).'/../../data';
    }
}
