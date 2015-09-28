<?php

$clean_number = str_replace(array('(', ')', '-', ' '), '', $context);
?>
<a href="tel:<?php echo $clean_number ?>" class="tel" itemprop="telephone">
    <?php echo preg_replace('/([\d]{3})([\d]{3})([\d]{4})/', '$1-$2-$3', $clean_number) ?>
</a>
<?php
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
 * 402-584-38XX 7-38XX HAL (Concord, NE)
 * 402-624-80XX 7-80XX ARDC (Mead, NE)
 * 402-370-40XX 7-40XX NEREC (Norfolk, NE)
 * 308-696-67XX 7-67XX WCREC (North Platte, NE)
 * 308-367-52XX 7-52XX NCTA
 */


$on_campus_prefix = ' <abbr class="on-campus-dialing" title="For on-campus dialing only. Off-campus, dial '.$context.'">On-campus, ';
$on_campus_suffix = '</abbr>';
switch(true) {
    case preg_match('/^(402)?472([\d]{4})$/', $clean_number, $matches):
        echo $on_campus_prefix . '2-' . $matches[2] . $on_campus_suffix;
        break;
    case preg_match('/^(402)?58438([\d]{2})$/', $clean_number, $matches):
        echo $on_campus_prefix . '7-38' . $matches[2] . $on_campus_suffix;
        break;
    case preg_match('/^(402)?62480([\d]{2})$/', $clean_number, $matches):
        echo $on_campus_prefix . '7-80' . $matches[2] . $on_campus_suffix;
        break;
    case preg_match('/^(402)?37040([\d]{2})$/', $clean_number, $matches):
        echo $on_campus_prefix . '7-40' . $matches[2] . $on_campus_suffix;
        break;
    case preg_match('/^(308)?69667([\d]{2})$/', $clean_number, $matches):
        echo $on_campus_prefix . '7-67' . $matches[2] . $on_campus_suffix;
        break;
    case preg_match('/^(308)?36752([\d]{2})$/', $clean_number, $matches):
        echo $on_campus_prefix . '7-52' . $matches[2] . $on_campus_suffix;
        break;
}
