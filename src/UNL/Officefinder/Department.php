<?php
/**
 * @Entity
 * @Table(name="departments")
 */
class UNL_Officefinder_Department extends UNL_Officefinder_Record_NestedSet
{
    /** 
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    public $id;
    /** @Column(length=50) */
    public $name;
    /** @Column(length=50) */
    public $org_unit;
    /** @Column(length=50) */
    public $building;
    /** @Column(length=50) */
    public $room;
    /** @Column(length=50) */
    public $city;
    /** @Column(length=50) */
    public $state;
    /** @Column(length=50) */
    public $postal_code;
    /** @Column(length=50) */
    public $address;
    /** @Column(length=250) */
    public $phone;
    /** @Column(length=50) */
    public $fax;
    /** @Column(length=50) */
    public $email;
    /** @Column(length=45) */
    public $website;
    /** @Column(length=45) */
    public $acronym;
    /** @Column(length=50) */
    public $alternate;

    public $lft;
    public $rgt;
    public $level;

    /** @Column(length=45) */
    public $uid;
    /** @Column(length=255) */
    public $uidlastupdated;

    /**
     * Construct a new listing
     * 
     * @param $options = array([id])
     */
    function __construct($options = array())
    {
        $this->options = $options;
        if (!empty($options['id'])) {
            $record = self::getByID($options['id']);

            if ($record === false) {
                throw new Exception('No record with that ID exists', 404);
            }

            UNL_Officefinder::setObjectFromArray($this, $record->toArray());
        }
    }

    function getTable()
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
     * Get office sub-listings
     * 
     * @return UNL_Officefinder_Department_Listings
     */
    public function getListings()
    {
        return new UNL_Officefinder_Department_Listings(array('department_id'=>$this->id));
    }

    function getHRDepartment()
    {
        if (!isset($this->org_unit)) {
            return false;
        }
        return UNL_Peoplefinder_Department::getById($this->org_unit, $this->options);
    }

    function hasOfficialChildDepartments()
    {
        $children = $this->_getChildren('org_unit IS NOT NULL');
        if ($children && count($children) > 0) {
            return true;
        }
        return false;
    }

    function getOfficialChildDepartments($orderBy = 'name ASC')
    {
        return $this->_getChildren('org_unit IS NOT NULL', $orderBy);
    }

    function hasUnofficialChildDepartments()
    {
        $children = $this->_getChildren('org_unit IS NULL');
        if ($children && count($children) > 0) {
            return true;
        }
        return false;
    }

    function getUnofficialChildDepartments($orderBy = 'lft')
    {
        return $this->_getChildren('org_unit IS NULL', $orderBy);
    }

    function addAlias($name)
    {
        if (!UNL_Officefinder_Department_Alias::getById($this->id, $name)) {
            $alias = new UNL_Officefinder_Department_Alias();
            $alias->department_id = $this->id;
            $alias->name = $name;
            return $alias->insert();
        }
        return true;
    }

    function userCanEdit($user)
    {
        return (bool)UNL_Officefinder_Department_Permission::getById($this->id, $user);
    }

    /**
     * 
     * @param string $user
     * 
     * @return bool
     */
    function addUser($user)
    {
        if (false === UNL_Officefinder_Department_Permission::getById($this->id, $user)) {
            $permission = new UNL_Officefinder_Department_Permission();
            $permission->department_id = $this->id;
            $permission->uid           = $user;
            return $permission->insert();
        }
        return true;
    }
}