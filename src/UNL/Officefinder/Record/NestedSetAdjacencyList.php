<?php
class UNL_Officefinder_Record_NestedSetAdjacencyList extends UNL_Officefinder_Record_NestedSet
{
    public $parent_id;
    public $sort_order;

    function setAsRoot($moveTreeUnder = true)
    {
        $this->parent_id = 0;
        return $this->update();
    }

    function isRoot()
    {
        return $this->parent_id == 0;
    }

    function addChild(UNL_Officefinder_Record_NestedSet $newChild, $insertAsLastChild = true)
    {
        $newChild->parent_id = $this->id;
        $newChild->update();
    }

    function move(UNL_Officefinder_Record_NestedSet $newParent, UNL_Officefinder_Record_NestedSet $newPrevious = null)
    {
        $this->parent_id = $newParent->id;
        return $this->update();
    }

    /**
     * get the parent of the current element
     * 
     * @return UNL_Officefinder_Record_NestedSet
     */
    function getParent()
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
    function getChildren($orderBy = 'sort_order')
    {
        return $this->_getChildren(null, $orderBy);
    }

    protected function _getChildren($whereAdd = null, $orderBy = 'sort_order, id')
    {
        if (!empty($whereAdd)) {
            $whereAdd .= ' AND ';
        }

        $query = sprintf('SELECT DISTINCT
                                id
                            FROM
                                %s
                            WHERE
                                %s parent_id = %s '.
                            'ORDER BY
                                %s',
                            $this->getTable(),
                            $whereAdd,
                            $this->id,
                            // order by left, so we have it in the order
                            // as it is in the tree if no 'order'-option
                            // is given
                            $orderBy
                   );

        $res = self::getDB()->query($query);

        return $this->_prepareResult($res);
    }

    function isChildOf(UNL_Officefinder_Record_NestedSet $parent)
    {
        if ($this->parent_id == $parent->id) {
            return true;
        }
        return false;
    }

    function hasChildren()
    {
        return count($this->getChildren());
    }

    /**
     * Prepare a mysqli_result object and return an iterator of the NestedSet objects
     *
     * @param mysqli_result $res The result containing ids of records
     */
    protected function _prepareResult($res)
    {
        $ids = array();
        while($row = $res->fetch_row()) {
            $ids[] = $row[0];
        }
        return new UNL_Officefinder_DepartmentList($ids);
    }
}