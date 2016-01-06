<?php
/**
 * Interface for a UNL_Knowledge data driver.
 *
 * The driver allows data source abstraction.
 *
 */
interface UNL_Knowledge_DriverInterface
{
    /**
     * Get a set of UNL_Knowledge_Record for the user.
     *
     * @param string $uid The unique user id eg: erasmussen2
     */
    public function getRecords($uid);
}
