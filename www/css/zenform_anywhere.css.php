<?php
$zenform = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/wdn/templates_3.0/css/content/zenform.css');
header('Content-type: text/css');
echo str_replace('#maincontent ', '', $zenform);
?>
h3.zenform {
    color:#fff !important;
}
