<?php

/**
 * TEMP FOR NOW
 *
 * PHP version 7.4
 *
 * @category  Services
 * @package   UNL_PersonInfo
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2023 University Communications & Marketing
 * @license   https://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      https://directory.unl.edu/avatars/
 */
class UNL_PersonInfo implements UNL_PersonInfo_PageNoticeInterface
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
        'instructions' => 'UNL_PersonInfo_Instructions',
        'avatar' => 'UNL_PersonInfo_Avatar',
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

    public $notice_types = array(
        'INFO' => 'dcf-notice-info',
        'WARNING' => 'dcf-notice-warning',
        'DANGER' => 'dcf-notice-danger',
        'SUCCESS' => 'dcf-notice-success',
    );
    public $notice_message = '';
    public $notice_type = '';
    public $notice_title = '';

    /**
     * Construct a new officefinder object
     *
     * @param array $options Associative array of options
     */
    public function __construct($options = [])
    {
        $this->options = $options + $this->options;
        UNL_Peoplefinder::getInstance($this->options);

        self::checkLogout();

        if (in_array($this->options['format'], ['html'])) {
            self::authenticate(true);
        }

        try {
            if (!empty($_POST)) {
                $this->handlePost();
            }

            $this->run();
        } catch(Exception $e) {
            if (!$e->getCode()) {
                $e = new Exception('Something went wrong.', 500, $e);
            }
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
        UNL_PersonInfo::getUser(true);

        if (!$this->validateCSRF()) {
            $this->create_notice(
                "Error With Your Request",
                "Invalid security token provided. If you think this was an error, please retry the request.",
                "WARNING"
            );

            self::redirect(self::getURL(), true);
        }

        if ($_POST['_type'] == 'set_avatar') {
            $this->set_avatar();
        }

        if ($_POST['_type'] == 'delete_avatar') {
            $this->delete_avatar();
        }
    }

    public function has_notice():bool
    {
        return session_status() !== PHP_SESSION_NONE
            && isset($_SESSION["person_info_notice_title"])
            && !empty($_SESSION["person_info_notice_title"]);
    }

    public function get_notice_type(): string
    {
        return $this->notice_types[strtoupper($_SESSION["person_info_notice_type"])] ?? 'dcf-notice-info';
    }

    public function get_notice_title(): string
    {
        return $_SESSION["person_info_notice_title"];
    }

    public function get_notice_message(): string
    {
        return $_SESSION["person_info_notice_message"];
    }

    public function create_notice(string $notice_tile, string $notice_message, string $notice_type)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["person_info_notice_title"]   = $notice_tile;
        $_SESSION["person_info_notice_message"] = $notice_message;
        $_SESSION["person_info_notice_type"]    = $notice_type;
    }

    public function clear_notice()
    {
        unset($_SESSION["person_info_notice_title"]);
        unset($_SESSION["person_info_notice_message"]);
        unset($_SESSION["person_info_notice_type"]);
    }

    public function set_avatar()
    {
        $user = self::$user;
        $user_record = new UNL_PersonInfo_Record($user);

        // Try to manipulate the image
        try {
            // Create a new image helper
            $image_helper = new UNL_PersonInfo_ImageHelper(
                $_FILES['profile_input']['tmp_name'],
                array(
                    // 'keep_files' => true,
                )
            );

            // Crop the image
            $image_helper->crop_image($_POST['profile_square_pos_x'], $_POST['profile_square_pos_y'], $_POST['profile_square_size'], $_POST['profile_square_size']);

            // Make many sizes and resolutions of the image
            $image_helper->resize_image(array(16, 24, 40, 48, 72, 100, 120, 200, 240, 400, 800), array(72, 144));

            // Save all the versions to these formats
            $image_helper->save_to_formats(array('jpeg', 'avif'));

            // Save those files to the user
            $image_helper->write_to_user($user_record);

        } catch (UNL_PersonInfo_Exceptions_InvalidImage $e) {
            $this->create_notice(
                "Error Updating Your Info",
                $e->getMessage(),
                "WARNING"
            );
            self::redirect(self::getURL(), true);
        } catch (UNL_PersonInfo_Exceptions_ImageProcessing $e) {
            $this->create_notice(
                "Error Updating Your Info",
                $e->getMessage(),
                "WARNING"
            );
            self::redirect(self::getURL(), true);
        } catch (Exception $e) {
            $this->create_notice(
                "Error Updating Your Info",
                $e->getMessage(),
                "WARNING"
            );
            self::redirect(self::getURL(), true);
        }

        // Let the user know things went well
        $this->create_notice(
            "Updated Your Info",
            "Your image was uploaded successfully",
            "SUCCESS"
        );

        self::redirect(self::getURL(), true);
    }

    public function delete_avatar()
    {
        $user = self::$user;
        $user_record = new UNL_PersonInfo_Record($user);

        try {
            $user_record->clear_images();
        } catch (Exception $e) {
            $this->create_notice(
                "Error Deleting Your Avatar",
                $e->getMessage(),
                "WARNING"
            );
            self::redirect(self::getURL(), true);
        }

        $this->create_notice(
            "Deleted Your Avatar",
            "Your avatar has been successfully deleted",
            "SUCCESS"
        );

        self::redirect(self::getURL(), true);
    }

    /**
     * Get the URL to the officefinder or a specific object
     *
     * @param mixed  $mixed             Object to retrieve URL for
     * @param string $additional_params Additional query string parameters to pass
     *
     * @return string
     */
    public static function getURL($mixed = null, $additional_params = [])
    {

        $url = UNL_Peoplefinder::$url.'myinfo/';

        if (is_object($mixed)) {
            switch (get_class($mixed)) {
            default:

            }
        }

        return UNL_Peoplefinder::addURLParams($url, $additional_params);
    }

    /**
     * Simple router to determine what view based on options present
     *
     * @return void
     */
    public function determineView()
    {
        $this->options['view'] = 'instructions';
    }

    /**
     * Construct output based on options
     *
     * @return void
     */
    public function run()
    {
        $this->determineView();

        if (!isset($this->view_map[$this->options['view']])) {
            throw new Exception('Un-registered view', 404);
        }

        $view = new $this->view_map[$this->options['view']]($this->options);

        $this->output = $view;
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
     * Wrapper function to help with CSRF tokens
     *
     * @return \Slim\Csrf\Guard
     */
    public function getCSRFHelper()
    {
        static $csrf;

        if (!$csrf) {
            $null = null;
            // Use persistent tokens due to AJAX functionality
            $csrf = new \Slim\Csrf\Guard('csrf', $null, null, 200, 16, true);
            $csrf->validateStorage();
            $csrf->generateToken();
        }

        return $csrf;
    }

    /**
     * Validate a POST request for CSRF
     * 
     * @return bool
     */
    public function validateCSRF()
    {
        $csrf = $this->getCSRFHelper();
        
        if (!isset($_POST[$csrf->getTokenNameKey()])) {
            return false;
        }

        if (!isset($_POST[$csrf->getTokenValueKey()])) {
            return false;
        }
        
        $name = $_POST[$csrf->getTokenNameKey()];
        $value = $_POST[$csrf->getTokenValueKey()];
        
        return $csrf->validateToken($name, $value);
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
}
