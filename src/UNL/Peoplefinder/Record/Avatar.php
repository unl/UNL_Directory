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

        $planetRedSizeMap = [
            self::AVATAR_SIZE_ORIGINAL => '400', //default
            self::AVATAR_SIZE_LARGE => '200',
            self::AVATAR_SIZE_MEDIUM => '100',
            self::AVATAR_SIZE_SMALL => '40',
            self::AVATAR_SIZE_TINY => '25',
            self::AVATAR_SIZE_TOPBAR => '16',
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
        $size = $options['s'];
        $supportSizes = self::getAvatarSizes();
        if (!isset($supportSizes[$size])) {
            $size = self::AVATAR_SIZE_MEDIUM;
        }

        //TODO: Get the format
        //TODO: Get the size
        //TODO: Grab the correct image from that data
        //TODO: Display it

        $this->record = new UNL_PersonInfo_Record($options['uid']);
        $image_path = $this->record->get_image_path('original.png');

        header('Content-Type: image/png');
        readfile($image_path);

        exit();
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
