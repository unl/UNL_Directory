<?php
if (count($context->results)) {
    echo $savvy->render($context->results);
} else {
    echo 'No results found!'.PHP_EOL;
}