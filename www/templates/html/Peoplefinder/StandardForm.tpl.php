<form method="get" id="peoplefinder" action="<?php echo UNL_Peoplefinder::getURL(); ?>" class="directorySearch">
<fieldset>
    <legend>Search for faculty, staff and students.</legend>
<ol>
    <li>
	    <label for="q" id="queryString">Enter a name to begin your search</label> 
	    <?php if (isset($context->options['chooser'])) {
	        echo '<input type="hidden" name="chooser" value="true" />';
	    }
	    if (isset($context->options['q'])) {
	        $default = htmlentities((string)$context->options['q'], ENT_QUOTES);
	    } else {
	        $default = '';
	    }
	    ?>
	    <input type="text" value="<?php echo $default; ?>" id="q" name="q" class="q" />
	    <input name="submitbutton" type="image" src="<?php echo UNL_Peoplefinder::getURL(); ?>images/formSearch.png" value="Submit" />
    </li>
</ol>
</fieldset>
</form>
<ul id="directoryHelp">
	<li><a href="<?php echo UNL_Peoplefinder::getURL(); ?>?adv" title="Switch searching type"  tabindex="0" id="advancedSearch" class="advanced">Advanced Search</a></li>
	<li>
		<?php if ($context->options['mobile'] != true) { ?>
			<a href="http://www.unl.edu/ucomm/splash/fieldguide_directory.shtml" title="Find out what's new in the directory" class="tooltip">Directory Help</a>
		<?php } else {?>
			<a href="http://m.unl.edu/?view=proxy&u=http://www.unl.edu/ucomm/splash/fieldguide_directory.shtml" title="Find out what's new in the directory" class="tooltip">Directory Help</a>
		<?php }?>
	</li>
</ul>
