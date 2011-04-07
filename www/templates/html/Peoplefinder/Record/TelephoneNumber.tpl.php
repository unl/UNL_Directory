<?php

$clean_number = str_replace(array('(', ')', '-', ' '), '', $context);

$link = '<a href="';
if (isset($_SERVER['HTTP_USER_AGENT']) &&
    strpos($_SERVER['HTTP_USER_AGENT'], "iPhone") === false) {
    $link .= "wtai://wp/mc;".$clean_number;
} else {
    $link .= "tel:".$clean_number;
}
$link .= '">'.preg_replace('/([\d]{3})([\d]{3})([\d]{4})/', '$1-$2-$3', $clean_number).'</a>';
echo $link;


/**
 * Some numbers can be dialed without dialing off-campus.
 *
 * This is especially important for extension offices which would normally
 * be charged long-distance rates.
 *
 * Here is the list of rules for converting 10 digit telephone numbers to their
 * 5 digit equivalents that are dialable from UNL On Campus Phones.
 *
 * 402-472-XXXX 2-XXXX UNL
 * 402-584-38XX 5-38XX HAL (Concord, NE)
 * 402-624-80XX 5-80XX ARDC (Mead, NE)
 * 402-370-40XX 5-40XX NEREC (Norfolk, NE)
 * 308-696-67XX 5-67XX WCREC (North Platte, NE)
 */


$on_campus_prefix = ' <abbr class="on-campus-dialing" title="For on-campus dialing only. Off-campus, dial '.$context.'">On-campus, ';
$on_campus_suffix = '</abbr>';
switch(true) {
    case preg_match('/^(402)?472([\d]{4})$/', $clean_number, $matches):
        echo $on_campus_prefix . '2-' . $matches[2] . $on_campus_suffix;
        break;
    case preg_match('/^(402)?58438([\d]{2})$/', $clean_number, $matches):
        echo $on_campus_prefix . '5-38' . $matches[2] . $on_campus_suffix;
        break;
    case preg_match('/^(402)?62480([\d]{2})$/', $clean_number, $matches):
        echo $on_campus_prefix . '5-80' . $matches[2] . $on_campus_suffix;
        break;
    case preg_match('/^(402)?37040([\d]{2})$/', $clean_number, $matches):
        echo $on_campus_prefix . '5-40' . $matches[2] . $on_campus_suffix;
        break;
    case preg_match('/^(308)?69667([\d]{2})$/', $clean_number, $matches):
        echo $on_campus_prefix . '5-67' . $matches[2] . $on_campus_suffix;
        break;
}