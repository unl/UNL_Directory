<?php
$output = $savvy->render($context->output);
if (UNL_Peoplefinder::$minifyHtml) {
	echo zz\Html\HTMLMinify::minify($output, [
	    'optimizationLevel' => zz\Html\HTMLMinify::OPTIMIZATION_ADVANCED,
	]);
} else {
	echo $output;
}
