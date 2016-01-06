<?php
class UNL_Peoplefinder_Person_Roles extends IteratorIterator implements Countable, Serializable
{
    protected $renderLinks = true;

    public function __construct($options = array())
    {
        if (isset($options['iterator']) && $options['iterator'] instanceof Iterator) {
            $iterator = $options['iterator'];
        } else {
            if (empty($options['dn'])) {
                throw new Exception('You must supply a base DN from which to search.');
            }
            $iterator = $options['peoplefinder']->getRoles($options['dn']);
        }
        parent::__construct($iterator);
    }

    public function serialize()
    {
        return serialize($this->getInnerIterator());
    }

    public function unserialize($serialized)
    {
        $iterator = unserialize($serialized);
        parent::__construct($iterator);
    }

    public function isRenderLinks()
    {
        return $this->renderLinks;
    }

    public function enableRenderLinks($enable = true)
    {
        $this->renderLinks = (bool) $enable;
        return $this;
    }

    /**
     * Get the number of roles this person has
     *
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->getInnerIterator());
    }
}