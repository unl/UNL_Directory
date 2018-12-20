<?php
$template = $savvy->render(null, 'static/jsrender/' . $context->template);

if (UNL_Peoplefinder::$minifyHtml) {
	$template = zz\Html\HTMLMinify::minify($template, [
		'optimizationLevel' => zz\Html\HTMLMinify::OPTIMIZATION_SIMPLE
	]);
}
?>
<script type="text/x-jsrender" id="<?php echo $context->id ?>">
<?php echo $template ?>
</script>
