<?php
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    if (empty($ip) || !in_array($ip, UNL_Peoplefinder::$allowed_unluncwid_IPs)) {
        unset($context->unluncwid);
    }
?>

<person>
    <?php foreach ($context->jsonSerialize() as $key => $val): ?>
        <?php $savvy->renderXmlNode($key, $val) ?>
    <?php endforeach; ?>
</person>
