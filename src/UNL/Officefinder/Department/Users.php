<?php
class UNL_Officefinder_Department_Users extends UNL_Officefinder_UserList
{
    public $department_id;

    public function __construct($options = array())
    {

        if (!isset($options['department_id'])) {
            throw new Exception('Must pass a department to get a list of users for');
        }

        $this->department_id = (int)$options['department_id'];

        $users = array();
        $mysqli = UNL_Officefinder::getDB();
        $sql = 'SELECT DISTINCT uid FROM department_permissions ';
        if (isset($options['department_id'])) {
            $sql .= ' WHERE department_id = '.$this->department_id;
        }
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $users[] = $row[0];
            }
        }
        parent::__construct($users);
    }

    public function current(): mixed
    {
        $user = new UNL_Officefinder_Department_User();
        $user->department_id = $this->department_id;
        $user->uid = parent::current();
        return $user;
    }

    public function getUniqueOrganizations()
    {
        $orgs = [];
        foreach ($this as $user) {
            $person = $user->getPerson();
            if (!$person) {
                continue;
            }

            $primaryHrOrg = $person->getHRPrimaryDepartment();
            if (!$primaryHrOrg) {
                continue;
            }

            if (!isset($orgs[$primaryHrOrg->id])) {
                $orgs[$primaryHrOrg->id] = $primaryHrOrg;
            }
        }

        return $orgs;
    }
}
