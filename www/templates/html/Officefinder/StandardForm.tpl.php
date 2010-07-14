<form method="get" action="?" id="officefinder" class="directorySearch">
	<fieldset>
	    <legend>Search for departments</legend>
	<ol>
	    <li>
		    <label for="q2" id="queryString2">Enter a name to begin your search</label> 
		    <input type="text" value="<?php echo $context->options['q']; ?>" id="q2" name="q" />
		    <input name="submitbutton" type="image" src="images/formSearch.png" value="Submit" />
	    </li>
	</ol>
	</fieldset>
</form>