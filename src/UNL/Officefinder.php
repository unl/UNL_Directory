<?php
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

    public $view_map = array('instructions' => 'UNL_Peoplefinder_Instructions',
                             //'search'       => 'UNL_Peoplefinder_Department_Search',
                             'tree'         => 'UNL_Officefinder_TreeView',
                             'search'       => 'UNL_Officefinder_DepartmentList_NameSearch',
                             'department'   => 'UNL_Officefinder_Department',
                             'record'       => 'UNL_Peoplefinder_Department',
                             'alphalisting' => 'UNL_Officefinder_DepartmentList_AlphaListing');

    protected static $auth;

    /**
     * The currently logged in user.
     * 
     * @var UNL_Peoplefinder_Record
     */
    protected static $user = false;

    public static $db_user = 'officefinder';
    
    public static $db_pass = 'officefinder';
    
    function __construct($options = array())
    {
        $this->options = $options + $this->options;

        $this->authenticate(true);
        
        try {
            if (!empty($_POST)) {
                $this->handlePost();
            }
            $this->run();
        } catch(Exception $e) {
            if (isset($this->options['ajaxupload'])) {
                echo $e->getMessage();
                exit();
            }

            if (false == headers_sent()
                && $code = $e->getCode()) {
                header('HTTP/1.1 '.$code.' '.$e->getMessage());
                header('Status: '.$code.' '.$e->getMessage());
            }

            $this->output[] = $e;
        }
    }

    /**
     * Log in the current user
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
//        self::$user->last_login = date('Y-m-d H:i:s');
//        self::$user->update();
    }

    /**
     * get the currently logged in user
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
        switch($_POST['_type']) {
            case 'department':
                $record = $this->handlePostDBRecord('UNL_Officefinder_Department');
                $this->redirect(self::getURL().'?view=department&id='.$record->id);
                break;
            case 'delete_department':
                $record = UNL_Officefinder_Department::getByID($_POST['department_id']);
                if (!$record) {
                    throw new Exception('No deparmtne with that ID was found');
                }
                if (!$record->userCanEdit(self::getUser(true))) {
                    throw new Exception('You have no edit permissions for that record');
                }
                $parent = $record->getParent();
                $record->delete();
                $this->redirect(self::getURL().'?view=department&id='.$parent->id);
                break;
        }
    }
    
    public static function getURL()
    {
        return UNL_Peoplefinder::getURL().'departments/';
    }

    function handlePostDBRecord($type)
    {
        if (!empty($_POST['id'])) {
            if (!($record = call_user_func(array($type, 'getByID'), $_POST['id']))) {
                throw new Exception('The record could not be retrieved', 404);
            }
            if (!$record->userCanEdit(self::getUser(true))) {
                throw new Exception('You cannot edit that record.', 401);
            }
        } else {
            $record = new UNL_Officefinder_Department;
        }

        $this->filterPostValues();

        self::setObjectFromArray($record, $_POST);

        if (!$record->save()) {
            throw new Exception('Could not save the record', 500);
        }

        return $record;
    }

    function filterPostValues()
    {
        unset($_POST['id']);
    }

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

    function run()
    {
        $this->determineView();
        if (isset($this->view_map[$this->options['view']])) {
            $this->output[] = new $this->view_map[$this->options['view']]($this->options);
        } else {
            throw new Exception('Un-registered view', 404);
        }
    }

    /**
     * Connect to the database and return it
     * 
     * @return mysqli
     */
    public static function getDB()
    {
        $db = new mysqli('localhost', self::$db_user, self::$db_pass, 'officefinder');
        if (mysqli_connect_error()) {
            throw new Exception('Database connection error (' . mysqli_connect_errno() . ') '
                    . mysqli_connect_error(), 500);
        }
        $db->set_charset('utf8');
        return $db;
    }

    /**
     * Set the public properties for an object with the values in an associative array
     * 
     * @param mixed &$object The object to set, usually a UNL_Officefinder_Record
     * @param array $values  Associtive array of key=>value
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
            }
            // Failed attempt at unsetting data which is empty.
//             elseif (isset($object->$key)
//                      && !empty($object->$key)) {
//                $object->$key = null;
//            }
        }
    }

    /**
     * 
     * Redirect user to the specified url
     * 
     * @param string $url  Where to redirect
     * @param bool   $exit To exit or not
     */
    static function redirect($url, $exit = true)
    {
        header('Location: '.$url);
        if (false !== $exit) {
            exit($exit);
        }
    }
}