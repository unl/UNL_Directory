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
 * @license   https://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      https://peoplefinder.unl.edu/
 */

/**
 * Peoplefinder class for UNL's online directory.
 *
 * PHP version 5
 *
 * @category  Services
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2007 Regents of the University of Nebraska
 * @license   https://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      https://peoplefinder.unl.edu/
 */
class UNL_Peoplefinder
{
	const ORG_UNIT_NUMBER_RETIREE = 50001351;
	const AFFILIATION_STUDENT = 'student';
	const AFFILIATION_GRADUATED = 'graduated';
	const AFFILIATION_FACULTY = 'faculty';
	const AFFILIATION_STAFF = 'staff';
	const AFFILIATION_AFFILIATE = 'affiliate';
	const AFFILIATION_VOLUNTEER = 'volunteer';
	const AFFILIATION_RETIREE = 'retiree';
	const AFFILIATION_EMERITI = 'emeriti';
	const AFFILIATION_GUEST = 'guest';
	const AFFILIATION_CONTINUE_SERVICES = 'continue services';
	const AFFILIATION_RIF = 'rif';
	const AFFILIATION_OVERRIDE = 'override'; // (will exist in guest ou)
	const AFFILIATION_SPONSORED = 'sponsored'; // (will exist in guest ou)

    public static $allowed_unluncwid_IPs = array();

    public static $resultLimit = 250;

    public static $url = '';

    public static $annotateUrl = 'https://annotate.unl.edu/';

    public static $staticFileVersion = '4.1';

    public static $minifyHtml = true;

    public static $use_oracle = TRUE;

    public static $testDomains = array('directory-test.unl.edu');

    public static $sampleUID;

    static protected $instance;

    /**
     * Options for this use.
     */
    public $options = [
        'view'   => 'instructions',
        'format' => 'html'
    ];

    /**
     * Driver for data retrieval
     *
     * @var UNL_Peoplefinder_DriverInterface
     */
    public $driver;

    public $oracle_driver;

    /**
     * The results of the search
     *
     * @var mixed
     */
    public $output;

    protected $uidViews = [
        'record',
        'avatar',
    ];

    public $view_map = [
        'instructions' => 'UNL_Peoplefinder_Instructions',
        'help' => 'UNL_Peoplefinder_Help',
        'search' => 'UNL_Peoplefinder_SearchController',
        'record' => 'UNL_Peoplefinder_Record',
        'avatar' => 'UNL_Peoplefinder_Record_Avatar',
        'roles' => 'UNL_Peoplefinder_Person_Roles',
        'developers' => 'UNL_Peoplefinder_Developers',
        'alphalisting' => 'UNL_Peoplefinder_PersonList_AlphaListing',
        'facultyedu' => 'UNL_Peoplefinder_FacultyEducationList',
    ];

    /**
     * This list contains the affiliations shown throughout the directory.
     *
     * Certain affiliations are not appropriate for public display.
     *
     * @var array
     */
    public static $displayedAffiliations = array(
	    self::AFFILIATION_STUDENT,
	    self::AFFILIATION_FACULTY,
	    self::AFFILIATION_STAFF,
	    self::AFFILIATION_AFFILIATE,
	    self::AFFILIATION_VOLUNTEER,
	    self::AFFILIATION_EMERITI,
	);

    protected static $replacement_data = array();

    /**
     * Constructor for the object.
     *
     * @param array $options Options, format, driver etc.
     */
    function __construct($options = array())
    {
        self::$instance = $this;

        if (!isset($options['driver'])) {
            $options['driver'] = new UNL_Peoplefinder_Driver_WebService();
        }

        $this->driver = $options['driver'];
        $this->oracle_driver = new UNL_Peoplefinder_Driver_OracleDB();

        if (isset($options['reset-cache'])) {
            $this->oracle_driver->resetCache();
            if ($this->driver instanceof UNL_Peoplefinder_Driver_LDAP) {
                $this->driver->resetCache();
            }
        }

        $this->options = $options + $this->options;

        try {
            $this->run();
        } catch(Exception $e) {
            $this->output = $e;
        }
    }

    public static function getInstance($options = [])
    {
        if (null === self::$instance) {
            return new self($options);
        }

        return self::$instance;
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
        // Prevent double-encoding of URLs
        $url = html_entity_decode($url, ENT_QUOTES, 'utf-8');

        // Get existing params
        $params = self::getURLParams($url);

        // Combine with the new values
        $params = $additional_params + $params;

        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }

        $url .= '?'.http_build_query($params);

        return trim($url, '?');
    }

    /**
     * Get an associative array of the querystring parameters in a URL
     *
     * @param string $url
     *
     * @return array
     */
    public static function getURLParams($url)
    {
        $params = array();

        $query = parse_url($url, PHP_URL_QUERY);
        if (!is_null($query)) {
            parse_str($query, $params);
        }

        return $params;
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
            case isset($this->options['uid']) && !in_array($this->options['view'], $this->uidViews):
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

        $this->options['peoplefinder'] = $this;
        $this->output = new $this->view_map[$this->options['view']]($this->options);

        if ($this->output instanceof UNL_Peoplefinder_DirectOutput) {
            $this->output->send();
            exit();
        }
    }

    /**
     * Pass through calls to the driver.
     *
     * @param string $method The method to call
     * @param mixed  $args   Arguments
     *
     * @method UNL_Peoplefinder_Record getUID() getUID(string $uid) get a record
     * @method UNL_Peoplefinder_Record getEmail() getEmail(string $email) get a record
     *
     * @return mixed
     */
    function __call($method, $args)
    {
        return call_user_func_array(array($this->driver, $method), $args);
    }

    public function getRoles($uid) {
        if (self::$use_oracle) {
            return $this->oracle_driver->getRoles($uid);
        } else {
            return $this->driver->getRoles($uid);
        }
    }

    public function getHROrgUnitNumberMatches($org_unit) {
        if (self::$use_oracle) {
            $uids = $this->oracle_driver->getHROrgUnitNumberMatches($org_unit);
            return $this->driver->getUIDSForDepartment($uids, FALSE);
        } else {
            return $this->driver->getHROrgUnitNumberMatches($org_unit);
        }
    }

    public function getHROrgUnitNumbersMatches($org_units) {
        if (self::$use_oracle) {
            $uids = $this->oracle_driver->getHROrgUnitNumbersMatches($org_units);
            return $this->driver->getUIDSForDepartment($uids, FALSE);
        } else {
            return $this->driver->getHROrgUnitNumbersMatches($org_units);
        }
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

    /**
     * Get the path to the tmp directory for this project
     *
     * @return string
     */
    public static function getTmpDir()
    {
        return dirname(__FILE__).'/../../tmp';
    }

    public static function setReplacementData($field, $data)
    {
        self::$replacement_data[$field] = $data;
    }

    public static function postRun($data)
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
