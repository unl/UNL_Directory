<?php
/**
 * LDAP attribute object
 *
 * PHP version 5
 * 
 * $Id$
 * 
 * @category  Default 
 * @package   UNL_LDAP
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://pear.unl.edu/package/UNL_LDAP
 */

/**
 * Class representing an LDAP entry's attribute
 * 
 * @category  Default 
 * @package   UNL_LDAP
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://pear.unl.edu/package/UNL_LDAP
 */
class UNL_LDAP_Entry_Attribute implements Countable, Iterator
{
    protected $_attribute;
    
    protected $_valid = false;
    
    protected $_currentEntry = false;
    
    /**
     * construct an ldap attribute object
     *
     * @param array $attribute Array returned from ldap_next_attribute
     */
    public function __construct(array $attribute)
    {
        $this->_attribute    = $attribute;
        $this->_valid        = true;
        $this->_currentEntry = 0;
    }
    
    /**
     * returns the current attribute iterated over
     *
     * @return string
     */
    function current()
    {
        return $this->_attribute[$this->_currentEntry];
    }
    
    /**
     * advance to the next attribute
     *
     * @return string | false
     */
    function next()
    {
        if ($this->_currentEntry !== false 
            && $this->_currentEntry < $this->count()-1) {
            $this->_currentEntry ++;
            return $this->current();
        } else {
            $this->_valid = false;
            return false;
        }
    }
    
    /**
     * Reset to the first attribute in the set.
     *
     * @return void
     */
    public function rewind()
    {
        $this->_currentEntry = 0;
    }
    
    /**
     * retrieve a unique key for this attribute, in this case it will
     * be an int
     *
     * @return int
     */
    public function key()
    {
        return $this->_currentEntry; 
    }
    
    /**
     * whether the attributes can be iterated over or not.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->_valid;
    }
    
    /**
     * Return the total number of attributes
     *
     * @return int
     */
    public function count()
    {
        return $this->_attribute['count'];
    }
    
    /**
     * Returns the first attribute entry
     *
     * @return string
     */
    public function __toString()
    {
        return $this->current();
    }
}