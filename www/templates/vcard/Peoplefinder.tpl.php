<?php
$output = $context->output;
if ($output instanceof UNL_Peoplefinder_Record) {
	header('Content-Disposition: attachment; filename="'.$output->sn.', '.$output->givenName.'.vcf"');
}

// Just pass through the output
echo $savvy->render($output);