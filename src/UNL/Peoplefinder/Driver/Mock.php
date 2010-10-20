<?php
class UNL_Peoplefinder_Driver_Mock implements UNL_Peoplefinder_DriverInterface
{
    function getAdvancedSearchMatches($query, $affiliation = null)
    {
        return array();
    }

    function getExactMatches($query, $affiliation = null)
    {
        return array();
    }

    function getLikeMatches($query, $affiliation = null)
    {
        return array();
    }

    function getPhoneMatches($query, $affiliation = null)
    {
        return array();
    }

    function getUID($uid)
    {
        return false;
    }

    function getHRPrimaryDepartmentMatches($query, $affiliation = null)
    {
        return array();
    }

    public function getHROrgUnitNumberMatches($query, $affiliation = null)
    {
        throw new Exception('Not implemented yet!');
    }
}