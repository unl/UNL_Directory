<?php
class UNL_Officefinder_Department_Listing extends UNL_Officefinder_Record
{
    
    
    /**
     * Construct a new listing
     * 
     * @param $options = array([id])
     */
    function __construct($options = array())
    {
        if (isset($options['id'])) {
            $record = self::getByID($options['id']);
            UNL_Officefinder::setObjectFromArray($this, $record->toArray());
        }
    }

    function getTable()
    {
        return 'listings';
    }

    /**
     * Retrieve a listing
     * 
     * @param int $id
     * 
     * @return UNL_Officefinder_Department_Listing::
     */
    public static function getByID($id)
    {
        if ($record = UNL_Officefinder_Record::getRecordByID('listings', $id)) {
            $object = new self();
            UNL_Officefinder::setObjectFromArray($object, $record);
            return $object;
        }
        return false;
    }
}
