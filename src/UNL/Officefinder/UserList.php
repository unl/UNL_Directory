<?php
class UNL_Officefinder_UserList extends ArrayIterator
{
    /**
     * Object for interaction with peoplefinder
     *
     * @var UNL_Peoplefinder
     */
    protected static $peoplefinder;

    /**
     * @return UNL_Peoplefinder_Record
     */
    function current()
    {
        try {
            return self::getPeoplefinder()->getUID(parent::current());
        } catch (Exception $e) {
            return parent::current();
        }
    }

    /**
     * Get the peoplefinder interaction object
     *
     * @return new UNL_Peoplefinder
     */
    public static function getPeoplefinder()
    {
        if (!isset(self::$peoplefinder)) {
            self::setPeoplefinder(UNL_Peoplefinder::getInstance());
        }

        return self::$peoplefinder;
    }

    public static function setPeoplefinder(UNL_Peoplefinder $peoplefinder)
    {
        self::$peoplefinder = $peoplefinder;
    }
}
