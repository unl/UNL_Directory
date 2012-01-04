<?php
header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>
<unl xmlns="http://wdn.unl.edu/xml" xmlns:xlink="http://www.w3.org/1999/xlink">'.PHP_EOL;
echo $savvy->render($context->output);
echo '</unl>';
