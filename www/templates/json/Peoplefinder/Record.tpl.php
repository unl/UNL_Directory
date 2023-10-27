<?php
unset($context->ou);
unset($context->unluncwid);
unset($context->unlSISMajor);
unset($context->unlSISMinor);
unset($context->unlSISClassLevel);
echo json_encode($context);
