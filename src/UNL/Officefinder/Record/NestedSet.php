<?php
abstract class UNL_Officefinder_Record_NestedSet extends UNL_Officefinder_Record
{
    abstract public function setAsRoot($moveTreeUnder = true);
    abstract public function addChild(UNL_Officefinder_Record_NestedSet $newChild, $insertAsLastChild = true);
    abstract function move(UNL_Officefinder_Record_NestedSet $newParent, UNL_Officefinder_Record_NestedSet $newPrevious = null);

    /**
     * get the parent of the current element
     * 
     * @return UNL_Officefinder_Record_NestedSet
     */
    abstract function getParent();

    /**
     * get the children of the given element or if the parameter is an array.
     * 
     * @return     mixed   the array with the data of all children
     *                     or false, if there are none
     */
    abstract function getChildren($orderBy);

    abstract function isChildOf(UNL_Officefinder_Record_NestedSet $parent);

    abstract function hasChildren();
}