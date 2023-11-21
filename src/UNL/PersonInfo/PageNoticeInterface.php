<?php

/**
 * Interface for pages that would have notices after form submit
 *
 * PHP version 7.4
 *
 * @category  Interface
 * @package   UNL_PersonInfo_PageNoticeInterface
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2023 University Communications & Marketing
 * @license   https://www1.unl.edu/wdn/wiki/Software_License BSD License
 */
interface UNL_PersonInfo_PageNoticeInterface
{
    public function has_notice();
    public function get_notice_type();
    public function get_notice_title();
    public function get_notice_message();
    public function create_notice(string $notice_tile, string $notice_message, string $notice_type);
    public function clear_notice();
}
