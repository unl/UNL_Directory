<?php
// This template is used in the hcard outputs for pulling snippets of HTML
//var_dump($context);
echo $savvy->render($context->output->record, 'Peoplefinder/Record.tpl.php');