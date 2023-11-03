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
            $url = self::CAMPUS_MAPS_BASE_URL . 'images/building/icon_' . $supportSizes[$size] . '.png';
        } else {
            $url = self::CAMPUS_MAPS_BASE_URL . 'building/' . urlencode($building) . '/image/1/' . $supportSizes[$size];
        }

        return $url;
    }

    public static function getAvatarSizes($forBuilding = false)
    {
        $mapsSizeMap = [
            self::AVATAR_SIZE_SMALL => 'sm',
            self::AVATAR_SIZE_MEDIUM => 'md',
            self::AVATAR_SIZE_LARGE => 'lg',
        ];

        //16, 24, 40, 48, 72, 100, 120, 200, 240, 400, 800

        $planetRedSizeMap = [
            self::AVATAR_SIZE_ORIGINAL => '400', //default
            self::AVATAR_SIZE_LARGE => '200',
            self::AVATAR_SIZE_MEDIUM => '100',
            self::AVATAR_SIZE_SMALL => '40',
            self::AVATAR_SIZE_TINY => '24',
            self::AVATAR_SIZE_TOPBAR => '16',
            '800' => 800,
            '400' => 400,
            '240' => 240,
            '200' => 200,
            '120' => 120,
            '100' => 100,
            '72' => 72,
            '48' => 48,
            '40' => 40,
            '24' => 24,
            '16' => 16,
        ];

        if ($forBuilding) {
            return $mapsSizeMap;
        }

        return $planetRedSizeMap;
    }

    public function __construct($options = [])
    {
        $this->cache = UNL_Peoplefinder_Cache::factory();

        if ($options instanceof UNL_Peoplefinder_Record || $options instanceof UNL_Officefinder_Department) {
            $this->record = $options;
            $this->options = [];
        } elseif (isset($options['uid'])) {
            try {
                $this->record = new UNL_Peoplefinder_Record(array('uid' => $options['uid']));
            } catch (Exception $e) {
                if ($e->getCode() !== 404) {
                    throw $e;
                }

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
            $this->url = $this->sendLocalUserImage($options);
        }

        return $this->url;
    }

    protected function sendLocalUserImage($options)
    {
        $size = $options['s'] ?? self::AVATAR_SIZE_MEDIUM;
        $dpi = $options['dpi'] ?? "";
        $format = $options['format'] ?? "";

        $supportSizes = self::getAvatarSizes();
        if (!isset($supportSizes[$size])) {
            $size = self::AVATAR_SIZE_MEDIUM;
        }

        $personInfoRecord = new UNL_PersonInfo_Record($options['uid']);
        if ($personInfoRecord->has_images()) {
            if (!isset($dpi) || empty($dpi) || !in_array($dpi, array('72', '144'))) {
                $dpi = '72';
            }
            if (!isset($format) || empty($format) || !in_array(strtolower($format), array('jpeg', 'avif'))) {
                $format = 'jpeg';
            }
            $avatar_size = $supportSizes[$size];
            $image_path = $personInfoRecord->get_image_path('cropped_' . $avatar_size . '_' . $dpi . '.' . $format);
            header('Content-Type: image/' . $format);
            readfile($image_path);
            exit();
        }

        $effectiveUrl = UNL_Peoplefinder::$url . 'images/default-avatar.jpg';
        $fallbackUrl = UNL_Peoplefinder::$url . 'images/default-avatar.jpg';

        if (self::$disable_gravatar) {
            return $effectiveUrl;
        }

        if (!$this->record->mail || !$this->record->eduPersonPrincipalName) {
            return $effectiveUrl;
        }

        $gravatarParams = [
            's' => $supportSizes[$size],
            'd' => $fallbackUrl,
        ];

        if ($this->record->mail) {
            $gravatarHash = md5($this->record->mail);
        } else {
            $gravatarHash = md5($this->record->eduPersonPrincipalName);
        }

        $profileIconUrl = self::GRAVATAR_BASE_URL . $gravatarHash . '?' . http_build_query($gravatarParams);

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
