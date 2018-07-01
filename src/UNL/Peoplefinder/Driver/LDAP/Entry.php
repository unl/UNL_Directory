<?php

class UNL_Peoplefinder_Driver_LDAP_Entry extends ArrayObject
{
    /**
     * Affiliation Mapping
     * Some new affiliations were incorrectly introduced in the 2017 update to LDAP.
     *
     * @var array
     */
    public static $affiliationMapping = array(
        't' => 'faculty',
        'faculty/executive' => 'faculty',
        'administrative' => 'staff',
    );

    public function __construct(array $entry)
    {
        $entry = self::normalizeEntry($entry);
        parent::__construct($entry, ArrayObject::ARRAY_AS_PROPS);
    }

    protected static function normalizeEntry(array $entry)
    {
        $entry = self::fix2018LdapToAdChanges($entry);
        $entry = self::fix2017LdapChanges($entry);
        $entry = UNL_Peoplefinder_Driver_LDAP_Util::filterArrayByKeys($entry, 'is_string');
        unset($entry['count']);
        foreach ($entry as $attribute => $value) {
            if (is_array($value)) {
                $value = new UNL_Peoplefinder_Driver_LDAP_Multivalue($value);
                $entry[$attribute] = $value;
            }
        }

        return $entry;
    }

    /**
     * @param array $entry
     * @return array
     */
    protected static function fix2017LdapChanges(array $entry)
    {
        if (!isset($entry['uid'])) {
            //This is likely an objecttype=role entry, not a person.
            return $entry;
        }

        if (isset($entry['edupersonnickname']) && $entry['edupersonnickname'] == $entry['cn']) {
            $entry['edupersonnickname'] = null;
        }

        if (isset($entry['mail'])) {
            foreach ($entry['mail'] as $key => $value) {

                if (is_string($key)) {
                  //Skip keys like 'count'
                  continue;
                }

                $entry['mail'][$key] = strtolower($value);
            }
        }

        if (isset($entry['edupersonprimaryaffiliation'])) {
            //Some records appear to not have this attribute.
            foreach ($entry['edupersonprimaryaffiliation'] as $key => $value) {
                
                //Prevent student phone numbers from showing
                if ($entry['edupersonprimaryaffiliation'][$key] == 'student') {
                    unset($entry['telephonenumber']);
                }

                //Prevent student phone numbers and other protected from showing (in case the upstream data source send them to us on accident)
                if ($entry['edupersonprimaryaffiliation'][$key] == 'student') {
                    unset(
                        $entry['telephonenumber'],
                        $entry['unlhraddress'],
                        $entry['postaladdress'],
                        $entry['mail']
                    );
                }
                
                if (is_string($key)) {
                    //Skip keys like 'count'
                    continue;
                }

                if (!isset(self::$affiliationMapping[$value])) {
                    //Nothing to map, so skip
                    continue;
                }

                $newValue = self::$affiliationMapping[$value];

                if (in_array($newValue, $entry['edupersonprimaryaffiliation'])) {
                    //This affiliation is already in the data, so don't add it again.
                    unset($entry['edupersonprimaryaffiliation'][$key]);
                } else {
                    //Change the affiliation
                    $entry['edupersonprimaryaffiliation'][$key] = $newValue;
                }
            }
        }

        if (isset($entry['edupersonaffiliation'])) {
            //Some records appear to not have this attribute.
            foreach ($entry['edupersonaffiliation'] as $key => $value) {
                if (is_string($key)) {
                    //Skip keys like 'count'
                    continue;
                }

                if (!isset(self::$affiliationMapping[$value])) {
                    //Nothing to map, so skip
                    continue;
                }

                $newValue = self::$affiliationMapping[$value];

                if (in_array($newValue, $entry['edupersonaffiliation'])) {
                    //This affiliation is already in the data, so don't add it again.
                    unset($entry['edupersonaffiliation'][$key]);
                } else {
                    //Change the affiliation
                    $entry['edupersonaffiliation'][$key] = $newValue;
                }
            }
        }

        return $entry;
    }

    /**
     * Attribute names (and possibly values) have changed.
     * To continue to support downstream data from directory.unl.edu, map to our old values.
     * 
     * @param array $entry
     * @return array
     */
    protected static function fix2018LdapToAdChanges(array $entry)
    {
        if (!isset($entry['samaccountname'])) {
            //This is likely an objecttype=role entry, not a person.
            //So fail early
            return $entry;
        }

        //fix uid
        if (isset($entry['samaccountname'])) {
            $entry['uid'] = $entry['samaccountname'];
        }
        
        if (isset($entry['department'])) {
            // Clean up the department value. It now appears to always and in a series of whitespace followed by UNL or IANR
            // for example: 'College of Ag Sci & Nat Res         IANR'
            foreach ($entry['department']  as $key=>$department) {
                if (is_string($key)) {
                    continue;
                }
                
                $department = preg_replace('/UNL$/', '', $department);
                $department = preg_replace('/IANR$/', '', $department);
                $department = trim($department);
                $entry['department'][$key] = $department;
            }

            $entry['unlhrprimarydepartment'] = $entry['department'];
        }

        if (isset($entry['departmentnumber'])) {
            $entry['unlhrorgunitnumber'] = $entry['departmentnumber'];
        }
        
        return $entry;
    }

    public function append($value)
    {
        throw new Exception('Unimplemented');
    }

    public function exchangeArray($input)
    {
        $input = self::normalizeEntry($input);
        return parent::exchangeArray($input);
    }

    public function offsetExists($index)
    {
        $index = strtolower($index);
        return parent::offsetExists($index);
    }

    public function offsetGet($index)
    {
        $index = strtolower($index);
        return parent::offsetGet($index);
    }

    public function offsetSet($index, $newval)
    {
        $index = strtolower($index);
        return parent::offsetSet($index, $newval);
    }

    public function offsetUnset($index)
    {
        $index = strtolower($index);
        return parent::offsetUnset($index);
    }
}
