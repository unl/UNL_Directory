<?php

class UNL_Officefinder_Department extends UNL_Officefinder_Record_NestedSetAdjacencyList
{
    public $id;

    public $name;

    public $org_unit;

    public $building;

    public $room;

    public $city;

    public $state;

    public $postal_code;

    public $address;

    public $phone;

    public $fax;

    public $email;

    public $website;

    public $academic;

    public $suppress;

    public $uid;

    public $uidlastupdated;

    protected $internal = [];

    /**
     * Construct a new listing
     *
     * @param $options = array([id])
     */
    public function __construct($options = array())
    {
        $this->nonPersistentFields[] = 'internal';
        $this->options = $options;
        if (!empty($options['id'])) {
            $record = self::getByID($options['id']);

            if ($record === false) {
                throw new Exception('No record with that ID exists', 404);
            }

            UNL_Officefinder::setObjectFromArray($this, $record->toArray());
        }
        if (!empty($options['sap'])) {
            $record = self::getByorg_unit($options['sap']);

            if ($record === false) {
                throw new Exception('No record with that SAP ID exists', 404);
            }

            UNL_Officefinder::setObjectFromArray($this, $record->toArray());
        }
    }

    public function getTable()
    {
        return 'departments';
    }

    /**
     * Retrieve a department
     *
     * @param int $id
     *
     * @return UNL_Officefinder_Department
     */
    public static function getByID($id)
    {
        if ($record = UNL_Officefinder_Record::getRecordByID('departments', $id)) {
            $object = new self();
            UNL_Officefinder::setObjectFromArray($object, $record);
            return $object;
        }
        return false;
    }

    /**
     * Retrieve a department
     *
     * @param int $id
     *
     * @return UNL_Officefinder_Department
     */
    public static function getByorg_unit($id)
    {
        return self::getByAnyField('UNL_Officefinder_Department', 'org_unit', $id);
    }

    public static function getNameByOrgUnit($id)
    {
        static $names = array();
        if (!isset($names[$id])) {
            if ($org = self::getByorg_unit($id)) {
                $names[$id] = $org->name;
            } else {
                $names[$id] = false;
            }
        }

        return $names[$id];
    }

    public function getHRDepartment()
    {
        if (!isset($this->org_unit)) {
            return false;
        }

        // Remove any base so we can retrieve departments from anywhere
        UNL_Peoplefinder_Department::setXPathBase('');
        return UNL_Peoplefinder_Department::getById($this->org_unit, $this->options);
    }

    public function hasOfficialChildDepartments()
    {
        $children = $this->_getChildren('org_unit IS NOT NULL');
        if ($children && count($children) > 0) {
            return true;
        }
        return false;
    }

    public function getOfficialChildDepartments($orderBy = 'name ASC')
    {
        return $this->_getChildren('org_unit IS NOT NULL', $orderBy);
    }

    public function hasUnofficialChildDepartments()
    {
        $children = $this->_getChildren('org_unit IS NULL');
        if ($children && count($children) > 0) {
            return true;
        }
        return false;
    }

    public function getUnofficialChildDepartments($orderBy = 'sort_order')
    {
        return $this->_getChildren('org_unit IS NULL', $orderBy);
    }

    public function addAlias($name)
    {
        if (!UNL_Officefinder_Department_Alias::getById($this->id, $name)) {
            $alias                = new UNL_Officefinder_Department_Alias();
            $alias->department_id = $this->id;
            $alias->name          = $name;
            return $alias->insert();
        }
        return true;
    }

    public function userCanEdit($user)
    {
        if (!$user) {
            return false;
        } elseif (in_array($user, UNL_Officefinder::$admins)) {
            return true;
        }

        if (!isset($this->internal['canEdit'])) {
            //default to false
            $this->internal['canEdit'] = false;

            if (isset($this->id) && (bool) UNL_Officefinder_Department_Permission::getById($this->id, $user)) {
                $this->internal['canEdit'] = true;
            }

            if (isset($this->parent_id) && true === UNL_Officefinder_Department::getByID($this->parent_id)->userCanEdit($user)) {
                $this->internal['canEdit'] = true;
            }
        }

        return $this->internal['canEdit'];
    }

    public function getEditors()
    {
        UNL_Peoplefinder::getInstance($this->options);
        if (!isset($this->internal['editors'])) {

            $this->internal['editors'] = [];

            if (isset($this->id)) {
                $users = $this->getUsers();
                if (count($users)) {
                    $this->internal['editors'] = $users;
                    return $users;
                }
            }

            if (isset($this->parent_id)) {
                $this->internal['editors'] = self::getByID($this->parent_id)->getEditors();
            }
        }

        return $this->internal['editors'];
    }

    /**
     *
     * @param string $user
     *
     * @return bool
     */
    public function addUser($user)
    {
        $user = strtolower(trim($user));
        if (false === UNL_Officefinder_Department_Permission::getById($this->id, $user)) {
            $permission                = new UNL_Officefinder_Department_Permission();
            $permission->department_id = $this->id;
            $permission->uid           = $user;
            return $permission->insert();
        }
        return true;
    }

    public function isOfficialDepartment()
    {
        return !empty($this->org_unit) && $this->org_unit[0] != '_';
    }

    public function save()
    {
        if (!empty($this->website)
            && !preg_match('/^https?\:\/\/.*/', $this->website)) {
            $this->website = 'http://'.$this->website;
        }

        if (!empty($this->phone)
            && preg_match('/^2\-?([\d]{4})$/', $this->phone, $matches)) {
            $this->phone = '402-472-'.$matches[1];
        }

        if (!empty($this->fax)
            && preg_match('/^2\-?([\d]{4})$/', $this->fax, $matches)) {
            $this->fax = '402-472-'.$matches[1];
        }

        if (empty($this->suppress)) {
            // Default suppression to false
            $this->suppress = 0;
        }

        if (empty($this->academic)) {
            $this->academic = 0;
        }

        if (empty($this->sort_order) && !$this->isRoot()) {
            $this->sort_order = count($this->getParent()->getChildren()) + 1;
        }

        return parent::save();
    }

    public function update()
    {
        if ($user = UNL_Officefinder::getUser()) {
            $this->uidlastupdated = $user;
        }
        return parent::update();
    }

    /**
     * get the parent of the current element
     *
     * @return UNL_Officefinder_Department
     */
    public function getParent()
    {
        if (empty($this->parent_id)) {
            return false;
        }

        if (!isset($this->internal['parent'])) {
            $this->internal['parent'] = self::getById($this->parent_id);
        }

        return $this->internal['parent'];
    }

    public function getOfficialParent()
    {
        if (isset($this->internal['hr_parent'])) {
            return $this->internal['hr_parent'];
        }

        $parent = $this->getParent();

        if (!$parent) {
            return false;
        } else if ($parent->isOfficialDepartment()) {
            return $parent;
        }

        $ancestor = $parent;

        while ($ancestor && $ancestor->parent_id) {
            $ancestor = self::getByID($ancestor->parent_id);

            if ($ancestor && $ancestor->isOfficialDepartment()) {
                $this->internal['hr_parent'] = $ancestor;
                return $ancestor;
            }
        }

        return false;
    }

    public function getImageURL($size = UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_MEDIUM)
    {
        $avatar = new UNL_Peoplefinder_Record_Avatar($this);
        return $avatar->getUrl(['s' => $size]);
    }

    public function hasAddress()
    {
        $addressFields = [
            'building',
            'address',
            'city',
            'state',
            'postal_code',
        ];

        foreach ($addressFields as $var) {
            if (!empty($this->$var)) {
                return true;
            }
        }

        return false;
    }

    public function isSummaryView()
    {
        return (isset($this->options['view']) && $this->options['view'] === 'deptsummary');
    }

    public function getURL()
    {
        return UNL_Officefinder::getURL().$this->id;
    }

    public function getNewChildURL()
    {
        return $this->getURL() . '/new';
    }

    /**
     * Get the aliases for this department
     *
     * @return UNL_Officefinder_Department_Aliases
     */
    public function getAliases()
    {
        return new UNL_Officefinder_Department_Aliases(array('department_id'=>$this->id));
    }

    /**
     * Get the users with permission over this department
     *
     * @return UNL_Officefinder_Department_Users
     */
    public function getUsers()
    {
        return new UNL_Officefinder_Department_Users(array('department_id'=>$this->id));
    }
}
