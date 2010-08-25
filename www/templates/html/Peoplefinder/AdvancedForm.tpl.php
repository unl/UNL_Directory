<form method="get" id="peoplefinder" action="<?php echo htmlentities(str_replace('index.php', '', $_SERVER['PHP_SELF']), ENT_QUOTES); ?>" class="directorySearch">
<fieldset>
    <legend>Search for faculty, staff and students.</legend>
<ol>
    <li>
        <label for="sn" id="queryString">Last Name</label> 
        <?php if (isset($context->options['chooser'])) {
            echo '<input type="hidden" name="chooser" value="true" />';
        }
        if (isset($context->options['sn'])) {
            $default = htmlentities($context->options['sn'], ENT_QUOTES);
        } else {
            $default = '';
        }
        ?>
        <input type="text" value="<?php echo $default; ?>" id="sn" name="sn" class="sn" />
        <label for="cn">First Name</label> 
        <?php if (isset($context->options['chooser'])) {
            echo '<input type="hidden" name="chooser" value="true" />';
        }
        if (isset($context->options['cn'])) {
            $default = htmlentities($context->options['cn'], ENT_QUOTES);
        } else {
            $default = '';
        }
        ?>
        <input type="text" value="<?php echo $default; ?>" id="cn" name="cn" class="cn" />
        <input name="submitbutton" type="image" src="images/formSearch.png" value="Submit" />
    </li>
</ol>
</fieldset>
</form>