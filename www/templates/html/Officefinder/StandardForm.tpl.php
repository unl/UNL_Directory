<form method="get" action="?" id="officefinder" class="directorySearch">
    <fieldset>
        <legend>Search for departments</legend>
    <ol>
        <li>
            <label for="q2" id="queryString2">Enter a name to begin your search</label>
            <?php
            if (isset($context->options['q'])) {
                $default = htmlentities($context->options['q'], ENT_QUOTES);
            } else {
                $default = '';
            }
            ?>
            <input type="text" value="<?php echo $default; ?>" id="q2" class="q" name="q" />
            <input name="submitbutton" type="image" src="images/formSearch.png" value="Submit" />
        </li>
    </ol>
    </fieldset>
</form>