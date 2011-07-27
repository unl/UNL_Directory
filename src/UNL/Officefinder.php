<?php
/**
 * Main controller for Officefinder/Yellow pages of the online directory
 * 
 * PHP version 5
 * 
 * @category  Services
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2010 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://peoplefinder.unl.edu/
 */

/**
 * Peoplefinder class for UNL's online directory.
 * 
 * PHP version 5
 * 
 * @category  Services
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2010 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://peoplefinder.unl.edu/
 */
class UNL_Officefinder
{
    /**
     * Options for this use.
     */
    public $options = array('view'   => 'instructions',
                            'format' => 'html',
                            'q'      => '',
                            'mobile' => false);

    /**
     * The results of the search
     * 
     * @var mixed
     */
    public $output;

    public $view_map = array('instructions'   => 'UNL_Peoplefinder_Instructions',
                             //'search'       => 'UNL_Peoplefinder_Department_Search',
                             'tree'           => 'UNL_Officefinder_TreeView',
                             'search'         => 'UNL_Officefinder_DepartmentList_NameSearch',
                             'buildingsearch' => 'UNL_Officefinder_DepartmentList_BuildingSearch',
                             'department'     => 'UNL_Officefinder_Department',
                             'deptlistings'   => 'UNL_Peoplefinder_Department_Personnel',
                             'record'         => 'UNL_Peoplefinder_Department',
                             'alphalisting'   => 'UNL_Officefinder_DepartmentList_AlphaListing_LoginRequired',
                             'mydepts'        => 'UNL_Officefinder_User_Departments',
                             'academicdepts'  => 'UNL_Officefinder_DepartmentList_AcademicDepartments',
    );

    protected static $auth;

    public static $admins = array();

    /**
     * The currently logged in user.
     * 
     * @var UNL_Peoplefinder_Record
     */
    protected static $user = false;

    public static $db_user = 'officefinder';
    
    public static $db_pass = 'officefinder';

    /**
     * Construct a new officefinder object
     * 
     * @param array $options Associative array of options
     */
    function __construct($options = array())
    {
        $this->options = $options + $this->options;

        $this->authenticate(true);

        if (!empty($_POST)) {
            try {
                $this->handlePost();
            } catch(Exception $e) {
                $this->output[] = $e;
            }
        }

        try {
            $this->run();
        } catch(Exception $e) {
            $this->output[] = $e;
        }

    }

    /**
     * Log in the current user
     * 
     * @param bool $logoutonly Only allow logging out, and not logging in.
     * 
     * @return void
     */
    static function authenticate($logoutonly = false)
    {
        if (isset($_GET['logout'])) {
            self::$auth = UNL_Auth::factory('SimpleCAS');
            self::$auth->logout();
        }

        if ($logoutonly) {
            return true;
        }

        self::$auth = UNL_Auth::factory('SimpleCAS');
        self::$auth->login();

        if (!self::$auth->isLoggedIn()) {
            throw new Exception('You must log in to view this resource!');
            exit();
        }
        self::$user = self::$auth->getUser();
        //self::$user->last_login = date('Y-m-d H:i:s');
        //self::$user->update();
    }

    /**
     * get the currently logged in user
     * 
     * @param bool $forceAuth Whether to force authentication or not
     * 
     * @return UNL_Peoplefinder_Record
     */
    public static function getUser($forceAuth = false)
    {
        if (self::$user) {
            return self::$user;
        }
        
        if ($forceAuth) {
            self::authenticate();
        }
        
        return self::$user;
    }

    /**
     * Set the currently logged in user, useful for testing and command line scripts
     *
     * @param string $user Currently logged in user
     * 
     * @return void
     */
    public static function setUser($user)
    {
        self::$user = $user;
    }

    /**
     * Handle data that is POST'ed to the controller.
     * 
     * @return void
     */
    function handlePost()
    {
        if (!isset($_POST['_type'])) {
            // Nothing to do here
            return;
        }
        $redirect = false;
        switch($_POST['_type']) {
        case 'department':
            $record = $this->handlePostDBRecord('UNL_Officefinder_Department');
            if (isset($_POST['parent_id'])) {
                $redirect = self::getURL().$record->parent_id;
            } else {
                $redirect = $record->getURL();
            }
            break;
        case 'delete_department':
            $record = $this->getPostedDepartment();
            $parent = $record->getParent();
            $record->delete();
            $redirect = $parent->getURL();
            break;
        case 'add_dept_user':
            $record = $this->getPostedDepartment();
            if (empty($_POST['uid'])) {
                throw new Exception('You must enter a username before adding a user.');
            }
            $peoplefinder = new UNL_Peoplefinder(array('driver'=>$this->options['driver']));
            $user         = $peoplefinder->getUID($_POST['uid']);
            $record->addUser($user->uid);
            $redirect = $record->getURL();
            break;
        case 'delete_dept_user':
            $record = $this->getPostedDepartment();
            $user   = UNL_Officefinder_Department_User::getById($record->id, $_POST['uid']);
            $user->delete();
            $redirect = $record->getURL();
            break;
        case 'add_dept_alias':
            $record = $this->getPostedDepartment();
            if (empty($_POST['name'])) {
                throw new Exception('You must enter the alias before submitting the form.');
            }
            $record->addAlias($_POST['name']);
            $redirect = $record->getURL();
            break;
        case 'delete_dept_alias':
            $record = $this->getPostedDepartment();
            $alias  = UNL_Officefinder_Department_Alias::getById($record->id, $_POST['name']);
            $alias->delete();
            $redirect = $record->getURL();
            break;
        }
        if ($redirect
            && !(isset($this->options['redirect'])
                 && $this->options['redirect'] == '0')) {
            $this->redirect($redirect);
        }
    }

    /**
     * Determine what department the user is referring to in POST data
     * 
     * @param bool $checkUserPermissions Check if the user has permission
     * 
     * @throws Exception
     * 
     * @return UNL_Officefinder_Department
     */
    protected function getPostedDepartment($checkUserPermissions = true)
    {
        $record = UNL_Officefinder_Department::getByID($_POST['department_id']);
        if (!$record) {
            throw new Exception('No department with that ID was found');
        }
        if (true === $checkUserPermissions
            && false === $record->userCanEdit(self::getUser(true))) {
            throw new Exception('You have no edit permissions for that record');
        }
        return $record;
    }

    /**
     * Get the URL to the officefinder/yellow pages or a specific object
     *
     * @param mixed  $mixed             Object to retrieve URL for
     * @param string $additional_params Additional querystring parameters to pass
     * 
     * @return string
     */
    public static function getURL($mixed = null, $additional_params = array())
    {
         
        $url = UNL_Peoplefinder::$url.'departments/';
        
        if (is_object($mixed)) {
            switch (get_class($mixed)) {
            default:
                    
            }
        }

        return UNL_Peoplefinder::addURLParams($url, $additional_params);
    }

    /**
     * Handle saving of a posted record
     * 
     * @param string $type Name of a class that extends UNL_Officefinder_Record
     * 
     * @return UNL_Officefinder_Record
     */
    function handlePostDBRecord($type)
    {
        if (!empty($_POST['id'])) {
            if (!($record = call_user_func(array($type, 'getByID'), $_POST['id']))) {
                throw new Exception('The record could not be retrieved', 404);
            }
        } else {
            $record = new $type;
        }

        $this->filterPostValues();

        self::setObjectFromArray($record, $_POST);

        if (!$record->userCanEdit(self::getUser(true))) {
            throw new Exception('You cannot edit that record.', 401);
        }

        if (!$record->save()) {
            throw new Exception('Could not save the record', 500);
        }

        return $record;
    }

    /**
     * Filter out any unwanted POST variables. Useful when records are
     * synchronized from the POST array and certain fields should not be
     * added or modified.
     * 
     * @return void
     */
    function filterPostValues()
    {
        unset($_POST['id']);
    }

    /**
     * Simple router to determine what view based on options present
     * 
     * @return void
     */
    public function determineView()
    {
        switch(true) {
        case !empty($this->options['q']):
            $this->options['view'] = 'search';
            return;
        case isset($this->options['d']):
            $this->options['view'] = 'record';
            return;
        }

    }

    /**
     * Construct output based on options
     * 
     * @return void
     */
    function run()
    {
        $this->determineView();
        if (!isset($this->view_map[$this->options['view']])) {
            throw new Exception('Un-registered view', 404);
        }
        $this->output[] = new $this->view_map[$this->options['view']]($this->options);
    }

    /**
     * Connect to the database and return it
     * 
     * @return mysqli
     */
    public static function &getDB()
    {
        static $db = false;
        if (!$db) {
            $db = new mysqli('localhost', self::$db_user, self::$db_pass, 'officefinder');
            if (mysqli_connect_error()) {
                throw new Exception('Database connection error (' . mysqli_connect_errno() . ') '
                        . mysqli_connect_error(), 500);
            }
            $db->set_charset('utf8');
        }
        return $db;
    }

    /**
     * Set the public properties for an object with the values in an associative array
     * 
     * @param mixed &$object The object to set, usually a UNL_Officefinder_Record
     * @param array $values  Associtive array of key=>value
     * 
     * @throws Exception
     * 
     * @return void
     */
    public static function setObjectFromArray(&$object, $values)
    {
        if (!isset($object)) {
            throw new Exception('No object passed!');
        }
        foreach (get_object_vars($object) as $key=>$default_value) {
            if (isset($values[$key]) && !empty($values[$key])) {
                $object->$key = $values[$key]; 
            } elseif (isset($object->$key)                  // The object has the var set
                      && !empty($object->$key)              // The object has a value
                      && isset($values[$key])               // A value to sync has been set
                      && (null === $values[$key]            // If the var is === null
                          || '' === $values[$key])          // OR the var is set to '' assume null
                      && !(in_array($key, $object->keys())) // Disallow unsetting keys
                      ) {
                // unset data which is should be set to null
                $object->$key = null;
            }
        }
    }

    /**
     * Redirect user to the specified url
     * 
     * @param string $url  Where to redirect
     * @param bool   $exit To exit or not
     * 
     * @return void
     */
    static function redirect($url, $exit = true)
    {
        header('Location: '.$url);
        if (false !== $exit) {
            exit($exit);
        }
    }

    /**
     * Check if the user is an admin or not
     *
     * @param string $user The UID of the user, eg:bbieber2
     *
     * @return bool
     */
    public static function isAdmin($user)
    {
        return in_array($user, self::$admins);
    }

    public static function setReplacementData($field, $data)
    {
        UNL_Peoplefinder::setReplacementData($field, $data);
    }
    
    public static function postRun($data)
    {
        return UNL_Peoplefinder::postRun($data);
    }
}