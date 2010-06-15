<?php
/**
 * Structure for the UNL Common Building portions of the UNL_Common package.
 * 
 * @author Brett Bieber
 * @package UNL_Common
 * @created Created on Sep 27, 2005
 */
require_once 'UNL/Common/Building/City.php';
require_once 'UNL/Common/Building/East.php';

/**
 * Simple object which can retreive the buildings on both city and east campus.
 * 
 * @package UNL_Common
 */
class UNL_Common_Building {
    
    var $codes = array();
    
    function __construct()
    {
        $east = new UNL_Common_Building_East();
        $city = new UNL_Common_Building_City();
        $this->codes = $east->codes;
        foreach ($city->codes as $code=>$bldg) {
            $this->codes[(string)$code] = $bldg;
        }
        asort($this->codes,SORT_STRING);
    }
    
    /**
    * Return all the codes
    *
    * @access  public
    * @return  array   all codes as associative array
    */
    function getAllCodes()
    {
        return $this->codes;
    }
    
    /**
     * Checks if a building with the given code exists.
     * @param string Building code.
     * @return bool true|false
     */
    function buildingExists($code)
    {
        if (isset($this->codes[$code])) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * returns city or east
     * 
     * @param string $code Building Code
     */
    function getCampus($code)
    {
        $east = new UNL_Common_Building_East();
        $city = new UNL_Common_Building_City();
        if (isset($east->codes[$code])) {
            return 'east';
        } elseif (isset($city->codes[$code])) {
            return 'city';
        } else {
            return false;
        }
    }
}

