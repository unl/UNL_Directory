<?php

use UNL\PerspnInfo\UNL_PersonInfo_Instructions;

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
 * @link      https://directory.unl.edu/myinfo/signature-generator/
 */

 class UNL_PersonInfo_SignatureGenerator
{
    public $options;
    public $user;
    public $record;
    public static $url = 'https://local-directory.unl.edu';

    public function __construct($options = array())
    {
        $this->options = $options;

        // Force the user to be logged in
        $this->user = UNL_PersonInfo::getUser(true);
        if ($this->user === false) {
            throw new Exception('UNAUTHORIZED', 401);
        }

        // Get their record once they are logged in
        $this->record = new UNL_PersonInfo_Record($this->user);
    
    }

    /**
     * Get the logged in user
     * @return string UID of the user
     */
    public function getUser() {
        return $this->user;
    }

    public static function getURL(string $link) {
        return self::$url . $link;
    }
}
