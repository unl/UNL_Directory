<form method="get" id="form1" action="<?php echo str_replace('index.php', '', $_SERVER['PHP_SELF']); ?>">
	<div>
	<label for="q">Search People:&nbsp;</label> 
	<?php if (isset($_GET['chooser'])) {
		echo '<input type="hidden" name="chooser" value="true" />';
	} ?>
	<input style="width:18ex;" type="text" value="<?php echo @$_GET['q']; ?>" id="q" name="q" /> 
	<input style="margin-bottom:-7px;" name="submitbutton" type="image" src="/ucomm/templatedependents/templatecss/images/go.gif" value="Submit" id="submitbutton" />
	</div> 
</form>