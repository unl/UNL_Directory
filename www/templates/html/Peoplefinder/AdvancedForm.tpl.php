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
        <input name="submitbutton" type="submit" value="Search" />
    </li>
</ol>
</fieldset>
<ul id="directoryHelp">
    <li><a href="<?php echo UNL_Peoplefinder::getURL(); ?>?std" title="Switch searching type"  tabindex="0" id="simpleSearch" class="simple">Simple Search</a></li>
    <li>
        <?php if ($context->options['mobile'] != true) { ?>
            <a href="http://www.unl.edu/ucomm/splash/fieldguide_directory.shtml" title="Find out what's new in the directory">Directory Help</a>
        <?php } else {?>
            <a href="http://m.unl.edu/?view=proxy&u=http://www.unl.edu/ucomm/splash/fieldguide_directory.shtml" title="Find out what's new in the directory">Directory Help</a>
        <?php }?>
    </li>
</ul>
</form>