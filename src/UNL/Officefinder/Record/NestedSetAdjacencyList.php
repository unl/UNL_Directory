<?php
class UNL_Officefinder_Record_NestedSetAdjacencyList extends UNL_Officefinder_Record_NestedSet
{
    public $parent_id;
    public $sort_order;

    public function setAsRoot($moveTreeUnder = true)
    {
        $this->parent_id = 0;
        return $this->update();
    }

    public function isRoot()
    {
        return $this->parent_id == 0;
    }

    public function addChild(UNL_Officefinder_Record_NestedSet $newChild, $insertAsLastChild = true)
    {
        $newChild->parent_id = $this->id;
        if ($insertAsLastChild) {
            $newChild->sort_order = count($this->getChildren()) + 1;
        }
        $newChild->update();
    }

    public function move(UNL_Officefinder_Record_NestedSet $newParent, UNL_Officefinder_Record_NestedSet $newPrevious = null)
    {
        $this->parent_id = $newParent->id;
        return $this->update();
    }

    /**
     * get the parent of the current element
     *
     * @return UNL_Officefinder_Record_NestedSet
     */
    public function getParent()
    {
        if (empty($this->parent_id)) {
            return false;
        }
        return self::getById($this->parent_id);
    }

    /**
     * get the children of the given element or if the parameter is an array.
     *
     * @return     mixed   the array with the data of all children
     *                     or false, if there are none
     */
    public function getChildren($orderBy = 'sort_order, id')
    {
        return $this->_getChildren(null, $orderBy);
    }

    protected function _getChildren($whereAdd = null, $orderBy = 'sort_order, id')
    {
        if (!empty($whereAdd)) {
            $whereAdd .= ' AND ';
        }

        $query = sprintf('SELECT DISTINCT
                                id, name, sort_order
                            FROM
                                %s
                            WHERE
                                %s parent_id = %s '.
                            'ORDER BY
                                %s',
                            $this->getTable(),
                            $whereAdd,
                            $this->id,
                            $orderBy
                   );

        $res = self::getDB()->query($query);

        return $this->_prepareResult($res);
    }

    public function isChildOf(UNL_Officefinder_Record_NestedSet $parent)
    {
        if ($this->parent_id == $parent->id) {
            return true;
        }
        return false;
    }

    public function hasChildren()
    {
        return count($this->getChildren());
    }

    /**
     * remove a tree element
     * this automatically remove all children and their children
     * if a node shall be removed that has children
     *
     * @access     public
     * @return     boolean returns either true or throws an error
     */
    public function delete()
    {

        foreach ($this->getChildren() as $child) {
            $child->delete();
        }

        return parent::delete();
    }

    /**
     * Prepare a mysqli_result object and return an iterator of the NestedSet objects
     *
     * @param mysqli_result $res The result containing ids of records
     */
    protected function _prepareResult($res)
    {
        $ids = array();
        if (is_object($res)) {
            while($row = $res->fetch_row()) {
                $ids[] = $row[0];
            }
        }
        return new UNL_Officefinder_DepartmentList($ids);
    }
}
