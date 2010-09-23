<?php
$context->uid = (string)$context->uid;
echo json_encode($context->getRawObject());