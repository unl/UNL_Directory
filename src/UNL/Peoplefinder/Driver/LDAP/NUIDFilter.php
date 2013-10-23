<?php
class UNL_Peoplefinder_Driver_LDAP_NUIDFilter
{
    protected $_filter;

    function __construct($nuid, $affiliation = null)
    {
        $this->_filter = "(&(unlUNCWID=$nuid))";
    }

    function __toString()
    {
        $this->_filter = UNL_Peoplefinder_Driver_LDAP_Util::wrapGlobalExclusions($this->_filter);
        return $this->_filter;
    }
}