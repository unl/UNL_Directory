<?php
/* @var $context RecursiveIteratorIterator */
$root = $context->getInnerIterator()->getInnerIterator();

echo $savvy->render($root);

