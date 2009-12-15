<?php
require_once 'config.inc.php';

$pf = new UNL_Peoplefinder();

$results = $pf->getExactMatches('Brett Bieber');

foreach ($results as $uid) {
    echo $uid->cn.'<br />';
}