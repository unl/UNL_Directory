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
                            'format' => 'html');

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
                             'search'       => 'UNL_Peoplefinder_SearchResults');

    /**
     * Constructor for the object.
     * 
     * @param UNL_Peoplefinder_DriverInterface $driver A compatible driver
     */
    function __construct($options = array())
    {
        if (!isset($options['driver'])) {
            $options['driver'] = new UNL_Peoplefinder_Driver_WebService();
        }

        $this->driver = $options['driver'];

        $this->options = $options + $this->options;

        try {
            $this->run();
        } catch(Exception $e) {
            $this->output[] = $e;
        }
    }

    public static function getURL()
    {
        return self::$url;
    }

    public function determineView()
    {
        switch(true) {
            case isset($this->options['q']):
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
            $this->options['peoplefinder'] =& $this;
            $this->output[] = new $this->view_map[$this->options['view']]($this->options);
        } else {
            throw new Exception('Un-registered view');
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

}
