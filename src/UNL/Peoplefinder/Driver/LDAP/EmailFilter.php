<?php
class UNL_Peoplefinder_Driver_LDAP_EmailFilter
{
    protected $_filter;

    public function __construct($email, $affiliation = null)
    {
        $email = UNL_Peoplefinder_Driver_LDAP_Util::escape_filter_value($email);
        $this->_filter = "(&(mail=$email))";
    }

    public function __toString()
    {
        $this->_filter = UNL_Peoplefinder_Driver_LDAP_Util::wrapGlobalExclusions($this->_filter);
        return $this->_filter;
    }
}
