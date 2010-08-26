<form method="get" id="peoplefinder" action="<?php echo UNL_Peoplefinder::getURL();?>" class="directorySearch advanced">
<fieldset>
    <legend>Search for faculty, staff and students.</legend>
<ol>
    <li>
        <label for="cn" class="cn">First Name</label>
        <?php if (isset($context->options['chooser'])) {
            echo '<input type="hidden" name="chooser" value="true" />';
        }
        if (isset($context->options['cn'])) {
            $default = htmlentities($context->options['cn'], ENT_QUOTES);
        } else {
            $default = '';
        }
        ?>
        <input type="text" value="<?php echo $default; ?>" id="cn" name="cn" class="n q" />
        <label for="sn" class="sn">Last Name</label>
        <?php if (isset($context->options['chooser'])) {
            echo '<input type="hidden" name="chooser" value="true" />';
        }
        if (isset($context->options['sn'])) {
            $default = htmlentities($context->options['sn'], ENT_QUOTES);
        } else {
            $default = '';
        }
        ?>
        <input type="text" value="<?php echo $default; ?>" id="sn" name="sn" class="s n q" />
        <input type="hidden" name="adv" value="1" />
        <input name="submitbutton" type="image" src="images/formSearch.png" value="Submit" />
    </li>
</ol>
</fieldset>
</form>