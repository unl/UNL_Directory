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

require_once dirname(__FILE__).'/Peoplefinder/Record.php';

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

    /**
     * Driver for data retrieval
     *
     * @var UNL_Peoplefinder_DriverInterface
     */
    public $driver;

    /**
     * Constructor for the object.
     */
    function __construct(UNL_Peoplefinder_DriverInteface $driver = null)
    {
        if (!$driver) {
            $driver = new UNL_Peoplefinder_Driver_LDAP();
        }
        $this->driver = $driver;
    }

    function __call($method, $args)
    {
        return call_user_func_array(array($this->driver, $method), $args);
    }

}
