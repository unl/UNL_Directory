<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class UNL_Peoplefinder_Record_Avatar implements UNL_Peoplefinder_DirectOutput, UNL_Peoplefinder_Routable
{
    const GRAVATAR_BASE_URL = 'https://secure.gravatar.com/avatar/';

    const CAMPUS_MAPS_BASE_URL = 'https://maps.unl.edu/';

    const AVATAR_SIZE_TOPBAR = 'topbar';
    const AVATAR_SIZE_TINY = 'tiny';
    const AVATAR_SIZE_SMALL = 'small';
    const AVATAR_SIZE_MEDIUM = 'medium';
    const AVATAR_SIZE_LARGE = 'large';
    const AVATAR_SIZE_ORIGINAL = 'original';

    public static $disable_gravatar = false;

    protected $options;

    protected $record;

    protected $url;

    protected $cache;

    public static function getBuildings()
    {
        $cache = UNL_Peoplefinder_Cache::factory();
        $cacheKey = 'UNL-buildings';
        $bldgs = $cache->get($cacheKey);

        if (!$bldgs) {
            try {
                $bldgs = new UNL_Common_Building();
                $bldgs = $bldgs->getAllCodes();

                if ($bldgs) {
                    $cache->set($cacheKey, $bldgs);
                } else {
                    throw new Exception('Could not load buildings from API');
                }
            } catch (Exception $e) {
                $bldgs = $cache->getSlow($cacheKey);
            }
        }

        return $bldgs;
    }

    public static function getUrlFromBuilding($building, $size = self::AVATAR_SIZE_MEDIUM)
    {
        $supportSizes = self::getAvatarSizes(true);
        if (!isset($supportSizes[$size])) {
            $size = self::AVATAR_SIZE_MEDIUM;
        }

        if ($building) {
            $bldgs = self::getBuildings();
        }

        if (!$building || !isset($bldgs[$building])) {
            // Default building image
            $url = UNL_Peoplefinder::$url . 'images/default-building.jpg';
        } else {
            $url = self::CAMPUS_MAPS_BASE_URL . 'building/' . urlencode($building) . '/image/1/' . $supportSizes[$size];
        }

        return $url;
    }

    /**
     * Get an array of possible sizes for avatars
     *
     * @param bool $forBuilding True if it is for buildings and false for persons
     * @return string[] Associative array of sizes and their values
     */
    public static function getAvatarSizes($forBuilding = false)
    {
        $mapsSizeMap = [
            self::AVATAR_SIZE_SMALL => 'sm',
            self::AVATAR_SIZE_MEDIUM => 'md',
            self::AVATAR_SIZE_LARGE => 'lg',
        ];

        $personAvatarSizeMap = array_combine(UNL_PersonInfo::$avatar_sizes, UNL_PersonInfo::$avatar_sizes);
        $personAvatarSizeMap[self::AVATAR_SIZE_ORIGINAL] = '400';
        $personAvatarSizeMap[self::AVATAR_SIZE_LARGE] = '200';
        $personAvatarSizeMap[self::AVATAR_SIZE_MEDIUM] = '100';
        $personAvatarSizeMap[self::AVATAR_SIZE_SMALL] = '40';
        $personAvatarSizeMap[self::AVATAR_SIZE_TINY] = '24';
        $personAvatarSizeMap[self::AVATAR_SIZE_TOPBAR] = '16';

        if ($forBuilding) {
            return $mapsSizeMap;
        }

        return $personAvatarSizeMap;
    }

    /**
     * Get an array of possible DPIs for avatars
     *
     * @param bool $forBuilding True if it is for buildings and false for persons
     * @return int[] Array of DPIs valid for avatar
     */
    public static function getAvatarDPI($forBuilding = false)
    {
        $mapsDPIMap = array(72);

        $personAvatarDPIMap = UNL_PersonInfo::$avatar_dpi;

        if ($forBuilding) {
            return $mapsDPIMap;
        }

        return $personAvatarDPIMap;
    }

    public function __construct($options = [])
    {
        $this->cache = UNL_Peoplefinder_Cache::factory();

        if ($options instanceof UNL_Peoplefinder_Record || $options instanceof UNL_Officefinder_Department) {
            $this->record = $options;
            $this->options = [];
        } elseif (isset($options['uid'])) {

            // Remove trailing slash
            $options['uid'] = rtrim($options['uid'], '/');

            // Check if they have a record
            try {
                $this->record = new UNL_Peoplefinder_Record(array('uid' => $options['uid']));
            } catch (Exception $e) {
                // If not a 404 it will throw it
                if ($e->getCode() !== 404) {
                    throw $e;
                }

                // If 404 then create a new record
                $this->record = new UNL_Peoplefinder_Record();
                $this->record->uid = $options['uid'];
            }
            $this->options = $options;
        } elseif (isset($options['did'])) {
            $this->record = new UNL_Officefinder_Department(['id' => $options['did']]);
            $this->options = $options;
        }

        if (!$this->record instanceof UNL_Peoplefinder_Record && !$this->record instanceof UNL_Officefinder_Department) {
            throw new Exception('Bad object construction', 500);
        }
    }

    public function setOptions($options = [])
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getUrl($options = [])
    {
        if ($this->url) {
            return $this->url;
        }

        $options = array_merge($this->options, $options);

        if (!isset($options['s'])) {
            $options['s'] = self::AVATAR_SIZE_MEDIUM;
        }

        if ($this->record instanceof UNL_Officefinder_Department || $this->record->ou == 'org') {
            $this->url = $this->generateOrgUrl($options);
        } else {
            $this->url = $this->generatePersonUrl($options);
        }

        return $this->url;
    }

    /**
     * Generate a person's avatar URL
     * @param mixed $options options to set
     * @return string URL to redirect to
     */
    protected function generatePersonUrl($options)
    {
        // Set up variables
        $size = $options['s'] ?? self::AVATAR_SIZE_MEDIUM;
        $dpi = $options['dpi'] ?? "";
        $format = $options['format'] ?? "";
        $cropped = strtolower($options['cropped'] ?? "");

        // Validate format
        $supportFormats = UNL_PersonInfo::$avatar_formats;
        if (!isset($format) || empty($format) || !in_array(strtoupper($format), $supportFormats)) {
            $format = 'jpeg';
        }

        // Validate size
        $supportSizes = self::getAvatarSizes();
        if (!isset($supportSizes[$size])) {
            $size = self::AVATAR_SIZE_MEDIUM;
        }

        $options['uid']

        // Check if they have an avatar image
        $personInfoRecord = new UNL_PersonInfo_Record();
        if ($personInfoRecord->has_images()) {

            // Validate DPI
            $supportDPI = self::getAvatarDPI();
            if (!isset($dpi) || empty($dpi) || !in_array($dpi, $supportDPI)) {
                $dpi = '72';
            }

            // Validate prefix
            $file_name_prefix = 'cropped';
            if (isset($cropped) && in_array($cropped, array('false', '0'))) {
                $file_name_prefix = 'original';
            }

            // Build the URL from the file and return it if its valid
            $avatar_size = $supportSizes[$size];
            $image_file_name = $file_name_prefix . '_' . $avatar_size . '_' . $dpi . '.' . $format;
            $image_url = $personInfoRecord->get_image_url($image_file_name);
            if ($image_url !== false) {
                return $image_url;
            }
        }

        // Get the default avatar image
        $effectiveUrl = UNL_Peoplefinder::$url . 'images/default-avatar.jpg';
        $fallbackUrl = UNL_Peoplefinder::$url . 'images/default-avatar.jpg';

        // Check if gravatar is disabled
        if (self::$disable_gravatar) {

            // This is in here since gravatar does not support avif
            if ($format === 'avif') {
                $effectiveUrl = UNL_Peoplefinder::$url . 'images/default-avatar.avif';
                $fallbackUrl = UNL_Peoplefinder::$url . 'images/default-avatar.avif';
            }

            return $effectiveUrl;
        }

        // Check if they have the right info for gravatar
        if (!$this->record->mail || !$this->record->eduPersonPrincipalName) {
            return $effectiveUrl;
        }

        // Set up the gravatar variables
        $gravatarParams = [
            's' => $supportSizes[$size],
            'd' => $fallbackUrl,
        ];

        // Generate the gravatar URL
        if ($this->record->mail) {
            $gravatarHash = md5($this->record->mail);
        } else {
            $gravatarHash = md5($this->record->eduPersonPrincipalName);
        }
        $profileIconUrl = self::GRAVATAR_BASE_URL . $gravatarHash . '?' . http_build_query($gravatarParams);

        // Return the URL to redirect to
        return $profileIconUrl;
    }

    protected function generateOrgUrl($options)
    {
        $size = $options['s'];
        $supportSizes = self::getAvatarSizes();
        if (!isset($supportSizes[$size])) {
            $size = self::AVATAR_SIZE_MEDIUM;
        }

        if ($this->record instanceof UNL_Officefinder_Department) {
            $fallbackUrl = self::getUrlFromBuilding($this->record->building, $size);

            if (!$this->record->email) {
                return $fallbackUrl;
            }

            $gravatarHash = trim($this->record->email);
            $gravatarParams = [
                's' => $supportSizes[$size],
                'd' => $fallbackUrl,
            ];

            $profileIconUrl = self::GRAVATAR_BASE_URL . $gravatarHash . '?' . http_build_query($gravatarParams);

            return $profileIconUrl;
        } else {
            return self::getUrlFromBuilding(null, $size);
        }
    }

    public function send()
    {
        header('Location: ' . $this->getUrl());
    }
}
