<?php

class UNL_Peoplefinder_Record_Avatar implements UNL_Peoplefinder_DirectOutput, UNL_Peoplefinder_Routable
{
    const PLANETRED_BASE_URL = UNL_Peoplefinder_Record::PLANETRED_BASE_URL . 'icon/';

    const GRAVATAR_BASE_URL = 'https://secure.gravatar.com/avatar/';

    const AVATAR_SIZE_TOPBAR = 'topbar';
    const AVATAR_SIZE_TINY = 'tiny';
    const AVATAR_SIZE_SMALL = 'small';
    const AVATAR_SIZE_MEDIUM = 'medium';
    const AVATAR_SIZE_LARGE = 'large';

    protected $options;

    protected $record;

    protected $url;

    public function __construct($options = [])
    {
        if ($options instanceof UNL_Peoplefinder_Record) {
            $this->record = $options;
            $this->options = [];
        } elseif (isset($options['uid']) && $options['peoplefinder']) {
            $this->record = $options['peoplefinder']->getUID($options['uid']);
            $this->options = $options;
        }

        if (!$this->record instanceof UNL_Peoplefinder_Record) {
            throw new Exception('Bad object constructions', 500);
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

    public function getAvatarSizes()
    {
        return [
            self::AVATAR_SIZE_LARGE => '200',
            self::AVATAR_SIZE_MEDIUM => '100', //default
            self::AVATAR_SIZE_SMALL => '40',
            self::AVATAR_SIZE_TINY => '25',
            self::AVATAR_SIZE_TOPBAR => '16',
        ];
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
        $size = $options['s'];
        $supportSizes = $this->getAvatarSizes();
        if (!isset($supportSizes[$size])) {
            $size = self::AVATAR_SIZE_MEDIUM;
        }

        if ($this->record->ou == 'org') {
            $profileIconUrl = UNL_Peoplefinder::getURL() . 'images/organization';
            if ($size !== self::AVATAR_SIZE_MEDIUM) {
                $profileIconUrl .= $supportSizes[$size];
            }
            $profileIconUrl .= '.png';
            return $profileIconUrl;
        }

        $planetRedUid = $this->record->getProfileUid();
        $profileIconUrl = self::PLANETRED_BASE_URL . 'unl_' . $planetRedUid . '/' . $size . '/';

        $request = new HTTP_Request2($profileIconUrl, HTTP_Request2::METHOD_HEAD);
        $response = $request->send();

        if ($response->getStatus() == 200) {
            $this->url = $profileIconUrl;
            return $this->url;
        } elseif ($response->getStatus() == 302) {
            $fallbackUrl = $response->getHeader('Location');
        } else {
            $fallbackUrl = 'mm';
        }

        $gravatarParams = [
            's' => $supportSizes[$size],
            'd' => $fallbackUrl,
        ];
        $gravatarHash = md5($this->record->eduPersonPrincipalName);
        $profileIconUrl = self::GRAVATAR_BASE_URL . $gravatarHash . '?' . http_build_query($gravatarParams);

        $this->url = $profileIconUrl;
        return $this->url;
    }

    public function send()
    {
        header('Location: ' . $this->getUrl());
    }
}
