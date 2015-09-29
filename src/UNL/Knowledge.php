<?php

class UNL_Knowledge
{
    public $options = array();

    /**
     * Driver for data retrieval
     *
     * @var UNL_Knowledge_DriverInterface
     */
    public $driver;

    public function __construct($options = array())
    {
        if (!isset($options['driver']) || !$options['driver'] instanceof UNL_Knowledge_DriverInterface) {
            $options['driver'] = new UNL_Knowledge_Driver_REST();
        }

        $this->driver = $options['driver'];

        $this->options = $options + $this->options;
    }

    /**
     * Pass through calls to the driver.
     *
     * @param string $method The method to call
     * @param mixed  $args   Arguments
     *
     * @method UNL_Peoplefinder_Record getUID() getUID(string $uid) get a record
     *
     * @return mixed
     */
    function __call($method, $args)
    {
        return call_user_func_array(array($this->driver, $method), $args);
    }
}
