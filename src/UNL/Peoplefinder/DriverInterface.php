<?php
/**
 * Interface for a peoplefinder data driver.
 * 
 * The driver allows data source abstraction.
 *
 */
interface UNL_Peoplefinder_DriverInterface
{
    /**
     * Return an array of records exactly matching the query.
     *
     * @param string $query       A general query
     * @param string $affiliation eduPersonAffiliation, eg staff/faculty/student
     */
    function getExactMatches($query, $affiliation = null);
    
    /**
     * perform a detailed search
     *
     * @param string $sn   surname, eg bieber
     * @param string $cn   common name, eg brett
     * @param string $eppa eduPersonPrimaryAffiliation, eg staff/faculty/student
     */
    function getAdvancedSearchMatches($sn, $cn, $eppa);
    
    /**
     * Return an array of records somewhat matching the query
     *
     * @param string $query       A general query
     * @param string $affiliation eduPersonAffiliation, eg staff/faculty/student
     */
    function getLikeMatches($query, $affiliation = null);
    
    /**
     * return matches for a phone number search
     *
     * @param string $query       Phone number eg: 472-1598
     * @param string $affiliation eduPersonAffiliation, eg staff/faculty/student
     */
    function getPhoneMatches($query, $affiliation = null);
    
    /**
     * get a UNL_Peoplefinder_Record for the user
     *
     * @param string $uid The unique user id eg: bbieber2
     */
    function getUID($uid);
}
