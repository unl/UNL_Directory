<?php
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
    public $options = [
        'view' => 'instructions',
        'format' => 'html',
        'q' => '',
        'render' => '',
        'redirect' => '',
    ];

    /**
     * The results of the search
     *
     * @var mixed
     */
    public $output;

    public $view_map = [
        'tree' => 'UNL_Officefinder_TreeView',
        'search' => 'UNL_Officefinder_DepartmentList_NameSearch',
        'buildingsearch' => 'UNL_Officefinder_DepartmentList_BuildingSearch',
        'department' => 'UNL_Officefinder_Department',
        'deptsummary' => 'UNL_Officefinder_Department_Summary',
        'deptlistings' => 'UNL_Peoplefinder_Department_Personnel',
        'personnelsubtree' => 'UNL_Peoplefinder_Department_PersonnelSubtree',
        'record' => 'UNL_Peoplefinder_Department',
        'alphalisting' => 'UNL_Officefinder_DepartmentList_AlphaListing_LoginRequired',
        'mydepts' => 'UNL_Officefinder_User_Departments',
        'academicdepts' => 'UNL_Officefinder_DepartmentList_AcademicDepartments',
    ];

    /**
     * Singleton authentication adapter/client
     *
     * @var UNL_Officefinder_Auth
     */
    protected static $auth;

    public static $admins = [];

    /**
     * The currently logged in user.
     *
     * @var UNL_Peoplefinder_Record
     */
    protected static $user = false;

    public static $db_user = 'officefinder';

    public static $db_pass = 'officefinder';

    public static $db_name = 'officefinder';

    public static $db_host = 'localhost';

    /**
     * Construct a new officefinder object
     *
     * @param array $options Associative array of options
     */
    public function __construct($options = [])
    {
        $this->options = $options + $this->options;

        self::checkLogout();

        if (in_array($this->options['format'], ['html'])) {
            self::authenticate(true);
        }

        // prevent unauthenticated edit rendering
        if ($this->options['render'] === 'editing') {
            self::authenticate();
        }

        try {
            if (!empty($_POST)) {
                $this->handlePost();
            }

            $this->run();
        } catch(Exception $e) {
            $this->output = $e;
        }
    }

    /**
     * Lazy load the authentication client
     *
     * @return SimpleCAS
     */
    protected static function _getAuth()
    {
        if (!self::$auth) {
            self::$auth = new UNL_Officefinder_Auth();
        }

        return self::$auth;
    }

    public static function checkLogout()
    {
        $auth = self::_getAuth();
        if (isset($_GET['logout'])) {
            $auth->logout();
        }

        $auth->handleSingleLogOut();
    }

    /**
     * Log in the current user
     *
     * @param boolean $gateway [optional] Should a gateway authentication be attempted
     * @throws Exception
     */
    public static function authenticate($gateway = false)
    {
        $auth = self::_getAuth();

        if ($auth->isAuthenticated()) {
            self::$user = $auth->getUsername();
            return;
        }

        if ($gateway) {
            $auth->gatewayAuthentication();
        } else {
            $auth->forceAuthentication();
            if (!$auth->isLoggedIn()) {
                throw new Exception('You must log in to view this resource!');
                exit();
            }
        }
    }

    /**
     * get the currently logged in user
     *
     * @param bool $forceAuth Whether to force authentication or not
     *
     * @return string
     */
    public static function getUser($forceAuth = false)
    {
        if (self::$user) {
            return self::$user;
        }

        $auth = self::_getAuth();
        if ($auth->isAuthenticated()) {
            return self::$user = $auth->getUsername();
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
    public function handlePost()
    {
        if (!isset($_POST['_type'])) {
            // bad post request
            $this->redirect(UNL_Peoplefinder::getURL());
        }

        $redirect = false;
        $noRedirect = $this->options['redirect'] === '0';
        $noRender = empty($this->options['render']);
        $redirectAndRender = $this->options['redirect'] === '2';

        switch($_POST['_type']) {
            case 'department':
                $record = $this->handlePostDBRecord('UNL_Officefinder_Department');
                if ($redirectAndRender || $record->isOfficialDepartment()) {
                    $redirect = $record->getURL();
                } else {
                    $redirect = $record->getOfficialParent()->getURL();
                }
                break;
            case 'delete_department':
                $record = $this->getPostedDepartment();
                $parent = $record->getOfficialParent();
                $record->delete();
                $redirect = $parent->getURL();
                $noRender = true;
                break;
            case 'sort_departments':
                $record = $this->getPostedDepartment();
                if (empty($_POST['sort_json'])) {
                    throw new Exception('You must provide a valid JSON sort array', 400);
                }
                $sortJson = json_decode($_POST['sort_json'], true);
                if (!$sortJson) {
                    throw new Exception('You must provide a valid JSON sort array', 400);
                }
                $this->reorderDepartments($record, $sortJson);
                $redirect = $record->getURL();
                break;
            case 'add_dept_user':
                $record = $this->getPostedDepartment();
                if (empty($_POST['uid'])) {
                    throw new Exception('You must enter a username before adding a user.', 400);
                }
                $peoplefinder = new UNL_Peoplefinder(['driver' => $this->options['driver']]);
                $user = $peoplefinder->getUID($_POST['uid']);
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
                    throw new Exception('You must enter the alias before submitting the form.', 400);
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

        if ($redirect && !$noRedirect) {
            if ($redirectAndRender) {
                $params = [
                    'render' => $this->options['render'],
                    'format' => $this->options['format'],
                ];

                $redirect .= '?' . http_build_query($params);
            }

            $this->redirect($redirect);
        } elseif ($noRender) {
            exit();
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
            throw new Exception('No department with that ID was found', 404);
        }
        if ($checkUserPermissions && !$record->userCanEdit(self::getUser(true))) {
            throw new Exception('You have no edit permissions for that record', 403);
        }
        return $record;
    }

    protected function reorderDepartments($rootDepartment, $sortChildren)
    {
        foreach ($sortChildren as $i => $sortChild) {
            $sortOrder = $i + 1;
            $record = UNL_Officefinder_Department::getByID($sortChild['id']);
            if (!$record) {
                throw new Exception('No department with that ID was found', 404);
            }
            if (!$record->userCanEdit(self::getUser(true))) {
                throw new Exception('You have no edit permissions for that record', 403);
            }

            $record->parent_id = $rootDepartment->id;
            $record->sort_order = $sortOrder;
            $record->save();

            if (isset($sortChild['children'])) {
                $this->reorderDepartments($record, $sortChild['children']);
            }
        }
    }

    /**
     * Get the URL to the officefinder/yellow pages or a specific object
     *
     * @param mixed  $mixed             Object to retrieve URL for
     * @param string $additional_params Additional querystring parameters to pass
     *
     * @return string
     */
    public static function getURL($mixed = null, $additional_params = [])
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
    public function handlePostDBRecord($type)
    {
        if (!empty($_POST['id'])) {
            if (!($record = call_user_func([$type, 'getByID'], $_POST['id']))) {
                throw new Exception('The record could not be retrieved', 404);
            }
        } else {
            $record = new $type;
        }

        $this->filterPostValues();

        self::setObjectFromArray($record, $_POST);

        if (!$record->userCanEdit(self::getUser(true))) {
            throw new Exception('You cannot edit that record.', 403);
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
    public function filterPostValues()
    {
        unset($_POST['id']);
        unset($_POST['org_unit']);
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
    public function run()
    {
        $this->determineView();

        if ($this->options['view'] === 'instructions') {
            header('Location: ' . UNL_Peoplefinder::getURL());
            exit();
        }

        if (!isset($this->view_map[$this->options['view']])) {
            throw new Exception('Un-registered view', 404);
        }

        $view = new $this->view_map[$this->options['view']]($this->options);

        if ($this->options['view'] === 'department' && $this->options['render'] === 'editing') {
            if (empty($view->id)) {
                if (isset($this->options['parent_id'])) {
                    $view->parent_id = $this->options['parent_id'];
                }
            }

            if (!$view->userCanEdit(self::getUser(true))) {
                throw new Exception('You cannot edit that record.', 401);
            }
        }

        $this->output = $view;

        if ($view instanceof UNL_Officefinder_DepartmentList_AlphaListing_LoginRequired) {
            // output will be way too big to try to minify
            UNL_Peoplefinder::$minifyHtml = false;
        }
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
            $db = new mysqli(self::$db_host, self::$db_user, self::$db_pass, self::$db_name);
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
                if (is_string($values[$key])) {
                    $values[$key] = trim($values[$key]);
                }
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
    public static function redirect($url, $exit = true)
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
