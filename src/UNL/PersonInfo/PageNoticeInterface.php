<?php

interface UNL_PersonInfo_PageNoticeInterface
{
    public function has_notice();
    public function get_notice_type();
    public function get_notice_title();
    public function get_notice_message();
    public function create_notice(string $notice_tile, string $notice_message, string $notice_type);
    public function clear_notice();
}
