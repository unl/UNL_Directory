<?php

class UNL_Officefinder_Auth
{
    const SESSION_MAP_CACHE_PREFIX = 'unl_cas_map_';

    /**
     * @var UNL_Peoplefinder_Cache
     */
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

    /**
     * Handle a request that may match a backchannel SLO request from the CAS server
     */
    public function handleSingleLogOut()
    {
        phpCAS::handleLogoutRequests(false);
    }

    /**
     * Checks if CAS authentication was previously done or has an active ticket
     * @return boolean
     */
    public function isAuthenticated()
    {
        return phpCAS::isAuthenticated();
    }

    /**
     * Get the Username from a successful CAS session
     */
    public function getUsername()
    {
        return phpCAS::getUser();
    }

    /**
     * Transparently redirect to the CAS Server to check for an SSO session
     */
    public function gatewayAuthentication()
    {
        return phpCAS::checkAuthentication();
    }

    /**
     * Redirect to the CAS Server to start the authentication flow
     */
    public function forceAuthentication()
    {
        return phpCAS::forceAuthentication();
    }

    /**
     * Destroy the CAS session and redirect to SLO on CAS server
     */
    public function logout()
    {
        return phpCAS::logout();
    }

    /**
     * Get the CAS Server SLO URL
     * @return string
     */
    public function getLogoutUrl()
    {
        return phpCAS::getServerLogoutURL();
    }

    /**
     * Lazy-loads a caching controller
     * @return UNL_Peoplefinder_Cache
     */
    protected function getCache()
    {
        if (!$this->cache) {
            $this->cache = UNL_Peoplefinder_Cache::factory();
        }

        return $this->cache;
    }

    /**
     * A callback for when the CAS library receives a valid service ticket
     * @param  string $ticket
     * @return self
     */
    public function handleLoginTicket($ticket)
    {
        $key = $this->getCacheKeyFromTicket($ticket);
        $value = session_id();
        $this->getCache()->set($key, $value);
        return $this;
    }

    /**
     * A callback for when the CAS library receives a valid logout request
     * @param  string $ticket
     */
    public function handleLogoutTicket($ticket)
    {
        $sessionId = $this->getSessionFromCache($ticket);
        if (!$sessionId) {
            return;
        }

        $this->removeSessionFromCache($ticket);

        // destroy the mapped session

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

    /**
     * @param  string $ticket
     * @return string
     */
    protected function getCacheKeyFromTicket($ticket)
    {
        return self::SESSION_MAP_CACHE_PREFIX . sha1($ticket);
    }

    /**
     * @param  string $ticket
     * @return string|false
     */
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

        return $sessionId;
    }

    /**
     * @param  string $ticket
     * @return self
     */
    protected function removeSessionFromCache($ticket)
    {
        $key = $this->getCacheKeyFromTicket($ticket);
        $this->getCache()->remove($key);
        return $this;
    }
}
