<?php
/**
 * @Entity
 * @Table(name="departments")
 */
class UNL_Officefinder_Department extends UNL_Officefinder_Record_NestedSetAdjacencyList
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

    public $parent_id;
    public $sort_order;

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

    function getUnofficialChildDepartments($orderBy = 'sort_order')
    {
        return $this->_getChildren('org_unit IS NULL', $orderBy);
    }

    function addAlias($name)
    {
        if (!UNL_Officefinder_Department_Alias::getById($this->id, $name)) {
            $alias                = new UNL_Officefinder_Department_Alias();
            $alias->department_id = $this->id;
            $alias->name          = $name;
            return $alias->insert();
        }
        return true;
    }

    function userCanEdit($user)
    {
        if (in_array($user, UNL_Officefinder::$admins)) {
            return true;
        }

        if (isset($this->id)
            && (bool)UNL_Officefinder_Department_Permission::getById($this->id, $user)) {
            return true;
        }

        if (isset($this->parent_id)
            && true === UNL_Officefinder_Department::getByID($this->parent_id)->userCanEdit($user)) {
            return true;
        }

        return false;
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
            $permission                = new UNL_Officefinder_Department_Permission();
            $permission->department_id = $this->id;
            $permission->uid           = $user;
            return $permission->insert();
        }
        return true;
    }

    function isOfficialDepartment()
    {
        return !empty($this->org_unit);
    }

    function getOfficialParent()
    {
        $query = sprintf('SELECT * FROM %s '.
                            'WHERE %s %s <= %s AND %s >= %s '.
                            ' AND org_unit IS NOT NULL '.
                            'ORDER BY %s DESC LIMIT 1',
                            // set the FROM %s
                            $this->getTable(),
                            // set the additional where add on
                            $this->_getWhereAddOn(),
                            // render 'left<=curLeft'
                            'lft',  $this->lft,
                            // render right>=curRight'
                            'rgt', $this->rgt,
                            // set the order column
                            'lft');

        $res = self::getDB()->query($query);

        if ($res->num_rows == 0) {
            throw new Exception('Should never happen!');
        }

        $obj = new self();
        $obj->synchronizeWithArray($res->fetch_assoc());
        return $obj;
    }

    public function getURL()
    {
        return UNL_Officefinder::getURL().'?view=department&id='.$this->id;
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