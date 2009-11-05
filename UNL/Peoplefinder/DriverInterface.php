<?php
interface UNL_Peoplefinder_DriverInterface
{
    function getExactMatches($query);
    function getAdvancedSearchMatches($sn, $cn, $eppa);
    function getLikeMatches($query);
    function getPhoneMatches($query);
    function getUID($uid);
}
