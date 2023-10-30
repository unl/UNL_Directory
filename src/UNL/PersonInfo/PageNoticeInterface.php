<?php

interface UNL_PersonInfo_PageNoticeInterface
{
    public function has_notice();
    public function get_notice_type();
    public function get_notice_title();
    public function get_notice_message();
}
