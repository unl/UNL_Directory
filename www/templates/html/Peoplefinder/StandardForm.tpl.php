<form method="get" id="peoplefinder" action="<?php echo htmlentities(str_replace('index.php', '', $_SERVER['PHP_SELF']), ENT_QUOTES); ?>" class="directorySearch">
<fieldset>
    <legend>Search for faculty, staff and students.</legend>
<ol>
    <li>
	    <label for="q" id="queryString">Enter a name to begin your search</label> 
	    <?php if (isset($context->options['chooser'])) {
	        echo '<input type="hidden" name="chooser" value="true" />';
	    }
	    if (isset($context->options['q'])) {
	        $default = htmlentities($context->options['q'], ENT_QUOTES);
	    } else {
	        $default = '';
	    }
	    ?>
	    <input type="text" value="<?php echo $default; ?>" id="q" name="q" class="q" />
	    <input name="submitbutton" type="image" src="images/formSearch.png" value="Submit" />
    </li>
</ol>
</fieldset>
</form>