<?php
class UNL_Peoplefinder_Driver_LDAP_UIDFilter
{
    protected $_filter;

    public function __construct($uid, $affiliation = null)
    {
        $uid = UNL_Peoplefinder_Driver_LDAP_Util::escape_filter_value($uid);
        $this->_filter = "(&(uid=$uid))";
    }

    public function __toString()
    {
        $this->_filter = UNL_Peoplefinder_Driver_LDAP_Util::wrapGlobalExclusions($this->_filter);
        return $this->_filter;
    }
}
