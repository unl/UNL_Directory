<?php


/**
* This class implements methods to work on a tree saved using the nested
* tree model.
* explaination: http://research.calacademy.org/taf/proceedings/ballew/index.htm
*
* @access     public
* @package    Tree
*/
class UNL_Officefinder_Record_NestedSet extends UNL_Officefinder_Record
{

    public $lft;
    public $rgt;
    public $level;

    /**
     * Set the current node as the root of the tree.
     *
     * @param bool $moveTreeUnder Whether to move the current tree underneath the new root.
     */
    public function setAsRoot($moveTreeUnder = true)
    {
        $this->_remove();

        if ($moveTreeUnder) {
            $query = sprintf('UPDATE %s 
                                SET lft = lft+1, rgt = rgt+1, level = level+1
                                WHERE lft IS NOT NULL AND rgt IS NOT NULL AND level IS NOT NULL',
                             $this->getTable()
                                );
            self::getDB()->query($query);
        }

        // Set the root node attributes
        $this->lft   = 1;
        $this->rgt   = 2;
        $this->level = 0;
        return $this->update();
    }

    function addChild(UNL_Officefinder_Record_NestedSet $newChild, $prevId = 0)
    {

        // get the "visited"-value where to add the new element behind
        // if $prevId is given, we need to use the right-value
        // if only the $parent_id is given we need to use the left-value
        // look at it graphically, that made me understand it :-)
        // See:
        // http://research.calacademy.org/taf/proceedings/ballew/sld034.htm
        $prevVisited = $prevId ? $this->rgt : $this->lft;

        // Make room for one more
        $this->_add($prevVisited, 1);

        // set the proper right and left values
        $newChild->lft   = $prevVisited + 1;
        $newChild->rgt   = $prevVisited + 2;
        $newChild->level = (int)$this->level + 1;

        if (!$newChild->update()) {
            // rollback
            throw new Exception('Couldn\'t do it');
        }

        // Set the rgt value to what it is in the database
        $this->rgt = $this->rgt + 2;

        return $this;
    }

    // }}}
    // {{{ _add()

    /**
     * this method only updates the left/right values of all the
     * elements that are affected by the insertion
     * be sure to set the parent_id of the element(s) you insert
     *
     * @param  int     this parameter is not the ID!!!
     *                 it is the previous visit number, that means
     *                 if you are inserting a child, you need to use the left-value
     *                 of the parent
     *                 if you are inserting a "next" element, on the same level
     *                 you need to give the right value !!
     * @param  int     the number of elements you plan to insert
     * @return mixed   either true on success or a Tree_Error on failure
     */
    protected function _add($prevVisited, $numberOfElements = 1)
    {

        // update the elements which will be affected by the new insert
        $query = sprintf('UPDATE %s SET %s = %s + %s WHERE%s %s > %s',
                            $this->getTable(),
                            'lft',
                            'lft',
                            $numberOfElements * 2,
                            $this->_getWhereAddOn(),
                            'lft',
                            $prevVisited);
        self::getDB()->query($query);

        $query = sprintf('UPDATE %s SET %s = %s + %s WHERE%s %s > %s',
                            $this->getTable(),
                            'rgt', 'rgt',
                            $numberOfElements * 2,
                            $this->_getWhereAddOn(),
                            'rgt',
                            $prevVisited);
        self::getDB()->query($query);
        return true;
    }

    // }}}
    // {{{ remove()

    /**
     * remove a tree element
     * this automatically remove all children and their children
     * if a node shall be removed that has children
     *
     * @access     public
     * @param      integer $id the id of the element to be removed
     * @return     boolean returns either true or throws an error
     */
    function delete()
    {

        // FIXXME start transaction
        //$this->_storage->autoCommit(false);
        $query = sprintf('DELETE FROM %s WHERE%s %s BETWEEN %s AND %s',
                            $this->getTable(),
                            $this->_getWhereAddOn(),
                            'lft',
                            $this->lft, $this->rgt);
        $res = $this->_storage->query($query);
        if (!$res) {
            throw new Exception('Error removing children');
        }

        if (!($err = $this->_remove($element))) {
            throw new Exception('Error removing the element');
        }
        return true;
    }

    // }}}
    // {{{ _remove()

    /**
     * removes a tree element, but only updates the left/right values
     * to make it seem as if the given element would not exist anymore
     * it doesnt remove the row(s) in the db itself!
     *
     * @see        getElement()
     * @access     private
     * @param      array   the entire element returned by "getElement"
     * @return     boolean returns either true or throws an error
     */
    protected function _remove()
    {
        $delta = $this->rgt - $this->lft + 1;
        $left  = 'lft';
        $right = 'rgt';

        // update the elements which will be affected by the remove
        $query = sprintf("UPDATE
                                %s
                            SET
                                %s = %s - $delta,
                                %s = %s - $delta
                            WHERE%s %s > %s",
                            $this->getTable(),
                            $left, $left,
                            $right, $right,
                            $this->_getWhereAddOn(),
                            $left, $this->lft);
        $res = self::getDB()->query($query);

        $query = sprintf("UPDATE
                                %s
                            SET %s = %s - $delta
                            WHERE
                                %s %s < %s
                              AND
                                %s > %s",
                            $this->getTable(),
                            $right, $right,
                            $this->_getWhereAddOn(),
                            $left, $this->lft,
                            $right, $this->rgt);
        $res = self::getDB()->query($query);

        return true;
    }

    // }}}
    // {{{ move()

    /**
     * move an entry under a given parent or behind a given entry.
     * If a newPrevId is given the newparent_id is dismissed!
     * call it either like this:
     *  $tree->move(x, y)
     *  to move the element (or entire tree) with the id x
     *  under the element with the id y
     * or
     *  $tree->move(x, 0, y);   // ommit the second parameter by setting
     *  it to 0
     *  to move the element (or entire tree) with the id x
     *  behind the element with the id y
     * or
     *  $tree->move(array(x1,x2,x3), ...
     *  the first parameter can also be an array of elements that shall
     *  be moved. the second and third para can be as described above.
     *
     * If you are using the Memory_DBnested then this method would be invain,
     * since Memory.php already does the looping through multiple elements.
     * But if Dynamic_DBnested is used we need to do the looping here
     *
     * @version    2002/06/08
     * @access     public
     * @param      integer  the id(s) of the element(s) that shall be moved
     * @param      integer  the id of the element which will be the new parent
     * @param      integer  if prevId is given the element with the id idToMove
     *                      shall be moved _behind_ the element with id=prevId
     *                      if it is 0 it will be put at the beginning
     * @return     mixed    true for success, Tree_Error on failure
     */
    function move($idsToMove, $newparent_id, $newPrevId = 0)
    {
        settype($idsToMove, 'array');
        $errors = array();
        foreach ($idsToMove as $idToMove) {
            $ret = $this->_move($idToMove, $newparent_id, $newPrevId);
            if (!$ret) {
                $errors[] = $ret;
            }
        }
        // FIXXME the error in a nicer way, or even better
        // let the throwError method do it!!!
        if (count($errors)) {
            throw new Exception('Error moving nodes in the tree');
        }
        return true;
    }

    // }}}
    // {{{ _move()

    /**
     * this method moves one tree element
     *
     * @see     move()
     * @version 2002/04/29
     * @access  public
     * @param   integer the id of the element that shall be moved
     * @param   integer the id of the element which will be the new parent
     * @param   integer if prevId is given the element with the id idToMove
     *                  shall be moved _behind_ the element with id=prevId
     *                  if it is 0 it will be put at the beginning
     * @return  mixed    true for success, Tree_Error on failure
     */
    function _move($idToMove, $newparent_id, $newPrevId = 0)
    {
        // do some integrity checks first
        if ($newPrevId) {
            // dont let people move an element behind itself, tell it
            // succeeded, since it already is there :-)
            if ($newPrevId == $idToMove) {
                return true;
            }
            if (Tree::isError($newPrevious = $this->getElement($newPrevId))) {
                return $newPrevious;
            }
            $newparent_id = $newPrevious['parent_id'];
        } else {
            if ($newparent_id == 0) {
                return Tree::raiseError(TREE_ERROR_UNKOWN_ERROR, null, null, 'no parent id given');
            }
            // if the element shall be moved under one of its children
            // return false
            if ($this->isChildOf($idToMove, $newparent_id)) {
                return Tree::raiseError(TREE_ERROR_UNKOWN_ERROR, null, null,
                            'can not move an element under one of its children');
            }
            // dont do anything to let an element be moved under itself
            // which is bullshit
            if ($newparent_id == $idToMove) {
                return true;
            }
            // try to retreive the data of the parent element
            if (Tree::isError($newParent = $this->getElement($newparent_id))) {
                return $newParent;
            }
        }
        // get the data of the element itself
        if (Tree::isError($element = $this->getElement($idToMove))) {
            return $element;
        }

        $numberOfElements = ($this->rgt - $this->lft + 1) / 2;
        $prevVisited = $newPrevId ? $newPrevious['right'] : $newParent['left'];

        // FIXXME start transaction

        // add the left/right values in the new parent, to have the space
        // to move the new values in
        $err = $this->_add($prevVisited, $numberOfElements);
        if (Tree::isError($err)) {
            // FIXXME rollback
            //$this->_storage->rollback();
            return $err;
        }

        // update the parent_id of the element with $idToMove
        $err = $this->update($idToMove, array('parent_id' => $newparent_id));
        if (Tree::isError($err)) {
            // FIXXME rollback
            //$this->_storage->rollback();
            return $err;
        }

        // update the lefts and rights of those elements that shall be moved

        // first get the offset we need to add to the left/right values
        // if $newPrevId is given we need to get the right value,
        // otherwise the left since the left/right has changed
        // because we already updated it up there. We need to get them again.
        // We have to do that anyway, to have the proper new left/right values
        if ($newPrevId) {
            if (Tree::isError($temp = $this->getElement($newPrevId))) {
                // FIXXME rollback
                //$this->_storage->rollback();
                return $temp;
            }
            $calcWith = $temp['right'];
        } else {
            if (Tree::isError($temp = $this->getElement($newparent_id))) {
                // FIXXME rollback
                //$this->_storage->rollback();
                return $temp;
            }
            $calcWith = $temp['left'];
        }

        // calc the offset that the element to move has
        // to the spot where it should go
        // correct the offset by one, since it needs to go inbetween!
        $offset = $calcWith - $this->lft + 1;

        $left = 'lft';
        $right = 'rgt';
        $query = sprintf("UPDATE
                                %s
                            SET
                                %s = %s + $offset,
                                %s = %s + $offset
                            WHERE
                                %s %s > %s
                                AND
                                %s < %s",
                            $this->getTable(),
                            $right, $right,
                            $left, $left,
                            $this->_getWhereAddOn(),
                            $left, $this->lft - 1,
                            $right, $this->rgt + 1);
        $res = self::getDB()->query($query);

        // remove the part of the tree where the element(s) was/were before
        if (Tree::isError($err = $this->_remove())) {
            // FIXXME rollback
            //$this->_storage->rollback();
            return $err;
        }
        // FIXXME commit all changes
        //$this->_storage->commit();

        return true;
    }

    // }}}
    // {{{ getRoot()

    /**
     * get the root
     *
     * @access     public
     * @version    2002/03/02
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @return     mixed   either the data of the root element or an Tree_Error
     */
    function getRoot()
    {
        $query = sprintf('SELECT * FROM %s WHERE %s = 1',
                            $this->getTable(),
                            'lft');
        $res = self::getDB()->query($query);

        if ($res->num_rows == 0) {
            throw new Exception('Could not find the root of this tree!');
        }
        $obj = new self();
        $obj->synchronizeWithArray($res->fetch_assoc());
        return $obj;
    }

    // }}}
    // {{{ getPath()

    /**
     * gets the path from the current element down
     * to the root. The returned array is sorted to start at root
     * for simply walking through and retreiving the path
     *
     * @access public
     * @return mixed  either the data of the requested elements
     *                      or an Tree_Error
     */
    function getPath()
    {
        $query = sprintf('SELECT id FROM %s '.
                            'WHERE %s %s <= %s AND %s >= %s '.
                            'ORDER BY %s',
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

        return $this->_prepareResult($res);
    }

    // }}}
    // {{{ getLevel()

    function getLevel()
    {
        $query = sprintf('SELECT (COUNT(parent.id) - 1) AS depth
                            FROM %s AS node, %s AS parent
                            WHERE 
                                node.lft BETWEEN parent.lft AND parent.rgt
                                AND node.id = %s
                            GROUP BY node.id
                            ORDER BY node.lft;',
                            $this->getTable(), $this->getTable(),
                            $this->id);
        $res = self::getDB()->query($query);

        if ($res->num_rows < 1) {
            throw new Exception('Error depetermining the level');
        }

        $row = $res->fetch_row();
        return (int)$row[0];
    }

    // }}}
    // {{{ getLeft()

    /**
     * gets the element to the left, the left visit
     *
     * @access     public
     * @version    2002/03/07
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer  the ID of the element
     * @return     mixed    either the data of the requested element
     *                      or an Tree_Error
     */
    function getLeft()
    {

        $query = sprintf('SELECT * FROM %s WHERE%s (%s = %s OR %s = %s)',
                            $this->getTable(),
                            $this->_getWhereAddOn(),
                            'rgt', $this->lft - 1,
                            'lft',  $this->lft - 1);
        $res = $this->_storage->queryRow($query, array());
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        return $this->_prepareResult($res);
    }

    // }}}
    // {{{ getRight()

    /**
     * gets the element to the right, the right visit
     *
     * @access     public
     * @version    2002/03/07
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer  the ID of the element
     * @return     mixed    either the data of the requested element
     *                      or an Tree_Error
     */
    function getRight()
    {
        $query = sprintf('SELECT * FROM %s WHERE (%s = %s OR %s = %s)',
                            $this->getTable(),
                            'lft',  $this->rgt + 1,
                            'rgt', $this->rgt + 1);
        $res = self::getDB()->query($query);

        return $this->_prepareResult($res);
    }

    // }}}
    // {{{ getParent()

    /**
     * get the parent of the current element
     * 
     * @return UNL_Officefinder_Record_NestedSet
     */
    function getParent()
    {
        if ($this->isRoot()) {
            throw new Exception('This is the root.');
        }

        $query = sprintf('SELECT
                                p.*
                            FROM
                                %s p
                            WHERE
                                %s p.level = %s-1 AND p.lft < %s AND p.rgt > %s',
                            $this->getTable(),
                            $this->_getWhereAddOn(' AND ', 'p'),
                            $this->level,
                            $this->lft,
                            $this->rgt);
        $res = self::getDB()->query($query);

        if ($res->num_rows == 0) {
            throw new Exception('No parent was found!');
        }

        $obj = new self();
        $obj->synchronizeWithArray($res->fetch_assoc());
        return $obj;
    }

    function isRoot()
    {
        return ($this->lft == 1);
    }

    // }}}
    // {{{ getChildren()

    /**
     * get the children of the given element or if the parameter is an array.
     * 
     * @return     mixed   the array with the data of all children
     *                     or false, if there are none
     */
    function getChildren($orderBy = 'lft')
    {
        return $this->_getChildren(NULL, $orderBy);
    }

    function getChildLeafNodes($orderBy = 'lft')
    {
        return $this->_getChildren('rgt = lft + 1', $orderBy);
    }

    function getChildrenWithChildren($orderBy = 'lft')
    {
        return $this->_getChildren('rgt != lft + 1', $orderBy);
    }

    protected function _getChildren($whereAdd = '', $orderBy = 'lft')
    {
        if (!$this->hasChildren()) {
            return false;
        }

        $id      = 'id';
        $left    = 'lft';
        if (!empty($whereAdd)) {
            $whereAdd .= ' AND ';
        }

        $query = sprintf('SELECT DISTINCT
                                id
                            FROM
                                %s
                            WHERE
                                %s level = %s+1 AND lft BETWEEN %s AND %s '.
                            'ORDER BY
                                %s',
                            $this->getTable(),
                            $whereAdd,
                            $this->level,
                            $this->lft,
                            $this->rgt,
                            // order by left, so we have it in the order
                            // as it is in the tree if no 'order'-option
                            // is given
                            $orderBy
                   );

        $res = self::getDB()->query($query);
        if ($res->num_rows == 0) {
            return array();
        }

        return $this->_prepareResult($res);
    }

    // }}}
    // {{{ nextSibling()

    /**
     * get the next element on the same level
     * if there is none return false
     *
     * @access     public
     * @version    2002/04/15
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer the ID of the element
     * @return     mixed   the array with the data of the next element
     *                     or false, if there is no next
     *                     or Tree_Error
     */
    function nextSibling()
    {
        $query = sprintf('SELECT
                                n.*
                            FROM
                                %s n, %s e
                            WHERE
                                %s e.%s = n.%s - 1
                              AND
                                e.%s = n.%s
                              AND
                                e.%s = %s',
                            $this->getTable(), $this->getTable(),
                            $this->_getWhereAddOn(' AND ', 'n'),
                            'rgt',
                            'lft',
                            'id',
                            'id',
                            'id',
                            $this->id);
        $res = self::getDB()->query($query);

        return !$res ? false : $this->_prepareResult($res);
    }

    // }}}
    // {{{ previousSibling()

    /**
     * get the previous element on the same level
     * if there is none return false
     *
     * @access     public
     * @version    2002/04/15
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer the ID of the element
     * @return     mixed   the array with the data of the previous element
     *                     or false, if there is no previous
     *                     or a Tree_Error
     */
    function previousSibling()
    {
        $query = sprintf('SELECT
                                p.*
                            FROM
                                %s p, %s e
                            WHERE
                                %s e.%s = p.%s + 1
                              AND
                                e.%s = p.%s
                              AND
                                e.%s = %s',
                            $this->getTable(), $this->getTable(),
                            $this->_getWhereAddOn(' AND ', 'p'),
                            'lft',
                            'rgt',
                            'id',
                            'id',
                            'id',
                            $this->id);
        $res = self::getDB()->query($query);

        return !$res ? false : $this->_prepareResult($res);
    }

    // }}}
    // {{{ isChildOf()

    /**
     * returns if $childId is a child of $id
     *
     * @abstract
     * @version    2002/04/29
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      int     id of the element
     * @return     boolean true if it is a child
     */
    function isChildOf(UNL_Officefinder_Record_NestedSet $parent)
    {
        // check simply if the left and right of the child are within the
        // left and right of the parent, if so it definitly is a child :-)

        if ($parent->lft < $this->lft
            && $parent->rgt > $this->rgt)
        {
            return true;
        }

        return false;
    }

    // }}}
    // {{{ getDepth()

    /**
     * return the maximum depth of the tree
     *
     * @version    2003/02/25
     * @access     public
     * @author "Denis Joloudov" <dan@aitart.ru>, Wolfram Kriesing <wolfram@kriesing.de>
     * @return integer the depth of the tree
     */
    function getDepth()
    {
        $left  = 'lft';
        $right = 'rgt';
        // FIXXXME TODO!!!
        $query = sprintf('SELECT COUNT(*) FROM %s p, %s e '.
                            'WHERE %s (e.%s BETWEEN p.%s AND p.%s) AND '.
                            '(e.%s BETWEEN p.%s AND p.%s)',
                            $this->getTable(), $this->getTable(),
                            // first line in where
                            $this->_getWhereAddOn(' AND ','p'),
                            $left, $left, $right,
                            // second where line
                            $right, $left, $right
                            );
        $res = self::getDB()->query($query);

        return $this->_prepareResult($res);
    }

    // }}}
    // {{{ hasChildren()

    /**
     * Tells if the node with the given ID has children.
     *
     * @version    2003/03/04
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer the ID of a node
     * @return     boolean if the node with the given id has children
     */
    function hasChildren()
    {
        // if the diff between left and right > 1 then there are children
        return ($this->rgt - $this->lft) > 1;
    }

    function hasChildrenWithChildren()
    {
        $children = $this->_getChildren('rgt != lft + 1');
        if ($children && count($children) > 0) {
            return true;
        }
        return false;
    }

    // }}}
    // {{{ getIdByPath()

    /**
     * return the id of the element which is referenced by $path
     * this is useful for xml-structures, like: getIdByPath('/root/sub1/sub2')
     * this requires the structure to use each name uniquely
     * if this is not given it will return the first proper path found
     * i.e. there should only be one path /x/y/z
     * experimental: the name can be non unique if same names are in different levels
     *
     * @version    2003/05/11
     * @access     public
     * @author     Pierre-Alain Joye <paj@pearfr.org>
     * @param      string   $path       the path to search for
     * @param      integer  $startId    the id where to start the search
     * @param      string   $nodeName   the name of the key that contains
     *                                  the node name
     * @param      string   $seperator  the path seperator
     * @return     integer  the id of the searched element
     */
    function getIdByPath($path, $startId = 0, $nodeName = 'name', $separator = '/')
    // should this method be called getElementIdByPath ????
    // Yes, with an optional private paramater to get the whole node
    // in preference to only the id?
    {
        if ($separator == '') {
            return Tree::raiseError(TREE_ERROR_UNKOWN_ERROR, null, null,
                'getIdByPath: Empty separator not allowed');
        }

        if ($path == $separator) {
            if (Tree::isError($root = $this->getRoot())) {
                return $root;
            }
            return $root['id'];
        }

        if (!($colname = $this->_getColName($nodeName))) {
            return Tree::raiseError(TREE_ERROR_UNKOWN_ERROR, null, null,
                'getIdByPath: Invalid node name');
        }

        if ($startId != 0) {
            // If the start node has no child, returns false
            // hasChildren calls getElement. Not very good right
            // now. See the TODO
            $startElem = $this->getElement($startId);
            if (Tree::isError($startElem)) {
                return $startElem;
            }

            // No child? return
            if (!is_array($startElem)) {
                return null;
            }

            $rangeStart = $startElem['left'];
            $rangeEnd   = $startElem['right'];
            // Not clean, we should call hasChildren, but I do not
            // want to call getELement again :). See TODO
            $startHasChild = ($rangeEnd - $rangeStart) > 1 ? true : false;
            $cwd = '/' . $this->getPathAsString($startId);
        } else {
            $cwd = '/';
            $startHasChild = false;
        }

        $t = $this->_preparePath($path, $cwd, $separator);
        if (Tree::isError($t)) {
            return $t;
        }

        list($elems, $sublevels) = $t;
        $cntElems = count($elems);

        $query = '
            SELECT '
                . 'id' .
            ' FROM '
                . $this->getTable() .
            ' WHERE '
                . $colname;

        $element = $cntElems == 1 ? $elems[0] : $elems[$cntElems - 1];
        $query .= ' = ' . $this->_storage->quote($element, 'text');

        if ($startHasChild) {
            $query  .= ' AND ('.
                        'lft'.' > '.$rangeStart.
                        ' AND '.
                        'rgt'.' < '.$rangeEnd.')';
        }

        $res = $this->_storage->queryOne($query, 'integer');
        if (!$res) {
            throw new Exception('Error');
        }
        return $res ? (int)$res : false;
    }

    // }}}

    // {{{ _getWhereAddOn()
    /**
     *
     *
     * @access     private
     * @version    2002/04/20
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      string  the current where clause
     * @return     string  the where clause we want to add to a query
     */
    function _getWhereAddOn($addAfter = ' AND ', $tableName = '')
    {
        if (!empty($this->conf['whereAddOn'])) {
            return ' ' . ($tableName ? $tableName . '.' : '') . $this->conf['whereAddOn'] . $addAfter;
        }
        return '';
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

