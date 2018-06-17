<?php
class UNL_Peoplefinder_Driver_LDAP_UIDSFilter
{
    protected $_filter;

    public function __construct($uids, $affiliation = null)
    {
        $uids = array_map(function ($uid) {
            return UNL_Peoplefinder_Driver_LDAP_Util::escape_filter_value($uid);
        }, $uids);
        $this->_filter = "(&(|(sAMAccountName=" . implode(')(sAMAccountName=', $uids) . ")))";
    }

    public function __toString()
    {
        $this->_filter = UNL_Peoplefinder_Driver_LDAP_Util::wrapGlobalExclusions($this->_filter);
        return $this->_filter;
    }
}
