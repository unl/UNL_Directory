<?php
class UNL_MobileDetector
{
    public static function isMobileClient($options = array())
    {
        if (!isset($_SERVER['HTTP_ACCEPT'], $_SERVER['HTTP_USER_AGENT'])) {
            // We have no vars to check
            return false;
        }
    
        if (isset($_COOKIE['wdn_mobile'])
        && $_COOKIE['wdn_mobile'] == 'no') {
            // The user has a cookie set, requesting no mobile views
            return false;
        }
    
        if ( // Check the http_accept and user agent and see
        preg_match('/text\/vnd\.wap\.wml|application\/vnd\.wap\.xhtml\+xml/i', $_SERVER['HTTP_ACCEPT'])
        ||
        (preg_match('/'.
                   'sony|symbian|nokia|samsung|mobile|windows ce|epoc|opera mini|' .
                   'nitro|j2me|midp-|cldc-|netfront|mot|up\.browser|up\.link|audiovox|' .
                   'blackberry|ericsson,|panasonic|philips|sanyo|sharp|sie-|' .
                   'portalmmm|blazer|avantgo|danger|palm|series60|palmsource|pocketpc|' .
                   'smartphone|rover|ipaq|au-mic|alcatel|ericy|vodafone\/|wap1\.|wap2\.|iPhone|Android' .
                   '/i', $_SERVER['HTTP_USER_AGENT'])
        ) && !preg_match('/ipad/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
    
        return false;
    }
}