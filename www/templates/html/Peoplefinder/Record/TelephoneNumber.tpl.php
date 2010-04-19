<?php
$link = '<a href="';
if (strpos($_SERVER['HTTP_USER_AGENT'], "iPhone") === false) {
    $link .= "wtai://wp/mc;".str_replace(array("(", ")", "-"), "", $context);
} else {
    $link .= "tel:".$context;
}
$link .= '">'.$context.'</a>';
echo $link;