<?php

class UNL_Officefinder_Auth
{
    const SESSION_MAP_CACHE_PREFIX = 'unl_cas_map_';

    protected $cache;

    public function __construct()
    {
        if (phpCAS::isInitialized()) {
            return;
        }

        phpCAS::client(CAS_VERSION_2_0, 'login.unl.edu', 443, 'cas', false);
        phpCAS::setPostAuthenticateCallback([$this, 'handleLoginTicket']);
        phpCAS::setSingleSignoutCallback([$this, 'handleLogoutTicket']);
        phpCAS::setCasServerCACert(GuzzleHttp\default_ca_bundle());
    }

    public function handleSingleLogOut()
    {
        phpCAS::handleLogoutRequests(false);
    }

    public function isAuthenticated()
    {
        return phpCAS::isAuthenticated();
    }

    public function getUsername()
    {
        return phpCAS::getUser();
    }

    public function gatewayAuthentication()
    {
        return phpCAS::checkAuthentication();
    }

    public function forceAuthentication()
    {
        return phpCAS::forceAuthentication();
    }

    public function logout($url = '')
    {
        return phpCAS::logout();
    }

    public function getLogoutUrl($url = '')
    {
        return phpCAS::getServerLogoutURL();
    }

    protected function getCache()
    {
        if (!$this->cache) {
            $this->cache = UNL_Peoplefinder_Cache::factory();
        }

        return $this->cache;
    }

    public function handleLoginTicket($ticket)
    {
        $key = $this->getCacheKeyFromTicket($ticket);
        $value = session_id();
        $this->getCache()->set($key, $value);
        return $this;
    }

    public function handleLogoutTicket($ticket)
    {
        $sessionId = $this->getSessionFromCache($ticket);
        if (!$sessionId) {
            return;
        }

        if (session_id() !== "") {
            session_unset();
            session_destroy();
        }

        session_id($sessionId);
        $_COOKIE[session_name()] = $sessionId;

        session_start();
        session_unset();
        session_destroy();
    }

    protected function getCacheKeyFromTicket($ticket)
    {
        return self::SESSION_MAP_CACHE_PREFIX . sha1($ticket);
    }

    protected function getSessionFromCache($ticket)
    {
        $cache = $this->getCache();
        $key = $this->getCacheKeyFromTicket($ticket);

        $sessionId = $cache->get($key);

        if (!$sessionId) {
            $sessionId = $cache->getSlow($key);
        }

        if (!$sessionId) {
            return false;
        }

        $this->removeSessionFromCache($ticket);
        return $sessionId;
    }

    protected function removeSessionFromCache($ticket)
    {
        $key = $this->getCacheKeyFromTicket($ticket);
        $this->getCache()->remove($key);
        return $this;
    }
}
