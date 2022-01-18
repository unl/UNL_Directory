<?php
class UNL_Peoplefinder_Driver_LDAP_AffiliationFilter extends UNL_Peoplefinder_Driver_LDAP_StandardFilter
{
    protected $affiliation = UNL_Peoplefinder::AFFILIATION_STAFF;

    public function __construct($query, $affiliation, $operator = '&', $wild = false)
    {
        switch($affiliation) {
            case UNL_Peoplefinder::AFFILIATION_STUDENT:
            case UNL_Peoplefinder::AFFILIATION_FACULTY:
            case UNL_Peoplefinder::AFFILIATION_STAFF:
            case UNL_Peoplefinder::AFFILIATION_GUEST:
                $this->affiliation = $affiliation;
                break;
        }
        parent::__construct($query, $operator, $wild);
    }

    public function __toString()
    {
        $this->addExcludedRecords();
        $this->_filter = '(&'.$this->_filter.'(eduPersonAffiliation='.$this->affiliation.'))';
        $this->_filter = UNL_Peoplefinder_Driver_LDAP_Util::wrapGlobalExclusions($this->_filter);
        return $this->_filter;
    }
}
