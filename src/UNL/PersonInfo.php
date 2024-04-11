<?php

/**
 * Handling directory saved user specific info
 *
 * PHP version 7.4
 *
 * @category  Services
 * @package   UNL_PersonInfo
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2023 University Communications & Marketing
 * @license   https://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      https://directory.unl.edu/myinfo/
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
        'signature-generator' => 'UNL_PersonInfo_SignatureGenerator',
    ];

    /**
     * Singleton authentication adapter/client
     *
     * @var UNL_Officefinder_Auth
     */
    protected static $auth;

    /**
     * The currently logged in user.
     *
     * @var UNL_Peoplefinder_Record
     */
    protected static $user = false;

    // Notice for the myinfo form
    public $notice_types = array(
        'INFO' => 'dcf-notice-info',
        'WARNING' => 'dcf-notice-warning',
        'DANGER' => 'dcf-notice-danger',
        'SUCCESS' => 'dcf-notice-success',
    );
    public $notice_message = '';
    public $notice_type = '';
    public $notice_title = '';

    // Avatar variables
    public static $avatar_sizes = array(800, 400, 240, 200, 120, 100, 72, 48, 40, 24, 16);
    public static $avatar_dpi = array(72, 144);
    public static $avatar_formats = array('JPEG', 'AVIF');

    /**
     * Construct a new officefinder object
     *
     * @param array $options Associative array of options
     */
    public function __construct($options = [])
    {
        $this->options = $options + $this->options;
        UNL_Peoplefinder::getInstance($this->options);

        // Check if they are trying to logout
        self::checkLogout();

        // If it is an HTML request check if they are authenticated
        if (in_array($this->options['format'], ['html'])) {
            self::authenticate(true);
        }

        try {
            // Check if it is a post request
            if (!empty($_POST)) {
                $this->handlePost();
            }

            // Run the regular page builder
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
    protected static function getAuth()
    {
        if (!self::$auth) {
            self::$auth = new UNL_Officefinder_Auth();
        }

        return self::$auth;
    }

    /**
     * Check if they are trying to logout
     */
    public static function checkLogout()
    {
        $auth = self::getAuth();
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
        $auth = self::getAuth();

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

        $auth = self::getAuth();
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

        // Validate cross site protection
        if (!$this->validateCSRF()) {
            $this->create_notice(
                "Error With Your Request",
                "Invalid security token provided. If you think this was an error, please retry the request.",
                "WARNING"
            );

            self::redirect(self::getURL(), true);
        }

        // Run the function based on the type
        if ($_POST['_type'] == 'set_avatar') {
            $this->set_avatar();
        }
        if ($_POST['_type'] == 'delete_avatar') {
            $this->delete_avatar();
        }
    }

    /**
     * Returns if we have a notice to display
     * @return bool True if we have a notice to display
     */
    public function has_notice():bool
    {
        return session_status() !== PHP_SESSION_NONE
            && isset($_SESSION["person_info_notice_title"])
            && !empty($_SESSION["person_info_notice_title"]);
    }

    /**
     * Getter for notice type
     * @return string Type of notice
     */
    public function get_notice_type(): string
    {
        return $this->notice_types[strtoupper($_SESSION["person_info_notice_type"])] ?? 'dcf-notice-info';
    }

    /**
     * Getter for notice title
     * @return string Type of title
     */
    public function get_notice_title(): string
    {
        return $_SESSION["person_info_notice_title"];
    }

    /**
     * Getter for notice message
     * @return string Type of message
     */
    public function get_notice_message(): string
    {
        return $_SESSION["person_info_notice_message"];
    }

    /**
     * Function for saving a notice for after the page redirects
     * This is helpful for PRG design
     *
     * @param string $notice_title Title of the notice to be displayed
     * @param string $notice_message Message of the notice
     * @param string $notice_type Type of the notice
     * @return void
     */
    public function create_notice(string $notice_tile, string $notice_message, string $notice_type)
    {
        // Create a session if not started already
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Save the notice info in there
        $_SESSION["person_info_notice_title"]   = $notice_tile;
        $_SESSION["person_info_notice_message"] = $notice_message;
        $_SESSION["person_info_notice_type"]    = $notice_type;
    }

    /**
     * Function for clearing notice info after the redirect
     * This is helpful for PRG design
     *
     * @return void
     */
    public function clear_notice()
    {
        // Unset the values of the session
        unset($_SESSION["person_info_notice_title"]);
        unset($_SESSION["person_info_notice_message"]);
        unset($_SESSION["person_info_notice_type"]);
    }

    /**
     * Function for handing set_avatar form post request
     * @return void
     */
    public function set_avatar()
    {
        // Get the user and their record
        $user = self::$user;
        $user_record = new UNL_PersonInfo_Record($user);

        $file_mime_type = mime_content_type($_FILES['profile_input']['tmp_name']);
        if (!in_array($file_mime_type, array('image/jpeg', 'image/png', 'image/avif', 'application/octet-stream'))) {
            $this->create_notice(
                "Error Updating Your Info",
                "An invalid image was uploaded. This could be a result of large size of the image or the format of the image. Contact an administrator if the issue persists.",
                "WARNING"
            );
            self::redirect(self::getURL(), true);
        }

        // Admin override for the user, this is helpful for updating a user's avatar
        if (
            UNL_Officefinder::isAdmin($user)
            && isset($_POST['admin_user_uid_set'])
            && !empty($_POST['admin_user_uid_set'])
            && $_POST['admin_user_uid_set'] !== $user
        ) {
            $user_record = new UNL_PersonInfo_Record($_POST['admin_user_uid_set']);
        }

        set_time_limit(300);
        // Try to manipulate the image
        try {
            // Create a new image helper
            $image_helper = new UNL_PersonInfo_ImageHelper(
                $_FILES['profile_input']['tmp_name'],
                array(
                    // 'keep_files' => true,
                )
            );

            // Get position and size of the square
            $square_x = intval($_POST['profile_square_pos_x']);
            $square_y = intval($_POST['profile_square_pos_y']);
            $square_size = intval($_POST['profile_square_size']);

            // Crop the image
            $image_helper->crop_image($square_x, $square_y, $square_size, $square_size);

            // Make many sizes and resolutions of the image
            $image_helper->resize_image(self::$avatar_sizes, self::$avatar_dpi);

            // Save all the versions to these formats
            $image_helper->save_to_formats(self::$avatar_formats);

            // Save those files to the user
            $image_helper->write_to_user($user_record);

        } catch (UNL_PersonInfo_Exceptions_InvalidImage $e) {
            $this->create_notice(
                "Error Updating Your Info",
                "An invalid image was uploaded. This could be a result of large size of the image or the format of the image. Contact an administrator if the issue persists.",
                "WARNING"
            );
            self::redirect(self::getURL(), true);
        } catch (UNL_PersonInfo_Exceptions_ImageProcessing $e) {
            $this->create_notice(
                "Error Updating Your Info",
                "An issue has occurred while trying to process your image. Contact an administrator if the issue persists.",
                "WARNING"
            );
            self::redirect(self::getURL(), true);
        } catch (Exception $e) {
            $this->create_notice(
                "Error Updating Your Info",
                "Contact an administrator if the issue persists.",
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

        // Redirect the client, this is so we can't resubmit the form
        self::redirect(self::getURL(), true);
    }

    /**
     * Function for handing delete_avatar form post request
     * @return void
     */
    public function delete_avatar()
    {
        // Get the user and their record
        $user = self::$user;
        $user_record = new UNL_PersonInfo_Record($user);

        // Admin override for the user, this is helpful for removing a user's avatar
        if (
            UNL_Officefinder::isAdmin($user)
            && isset($_POST['admin_user_uid_remove'])
            && !empty($_POST['admin_user_uid_remove'])
            && $_POST['admin_user_uid_remove'] !== $user
        ) {
            $user_record = new UNL_PersonInfo_Record($_POST['admin_user_uid_remove']);
        }

        try {
            // Clears the user images
            $user_record->clear_images();
        } catch (Exception $e) {
            $this->create_notice(
                "Error Deleting Your Avatar",
                "Contact an administrator if the issue persists.",
                "WARNING"
            );
            self::redirect(self::getURL(), true);
        }

        // Let the user know things went well
        $this->create_notice(
            "Deleted Your Avatar",
            "Your avatar has been successfully deleted",
            "SUCCESS"
        );

        // Redirect the client, this is so we can't resubmit the form
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
    public static function getURL($additional_params = [])
    {
        $url = UNL_Peoplefinder::$url.'myinfo/';

        return UNL_Peoplefinder::addURLParams($url, $additional_params);
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
                $this->options['view'] = 'signature-generator';
                return;
            case isset($this->options['d']):
                $this->options['view'] = 'instructions';
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
