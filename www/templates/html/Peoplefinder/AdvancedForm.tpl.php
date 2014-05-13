<form method="get" id="peoplefinder" action="<?php echo UNL_Peoplefinder::getURL();?>" class="directorySearch advanced">

    <div class="input-group">
        <?php 
        if (isset($context->options['chooser'])) {
            echo '<input type="hidden" name="chooser" value="true" />';
        }
        $cn_default = '';
        if (isset($context->options['cn'])) {
            $cn_default = htmlentities($context->options['cn'], ENT_QUOTES);
        }
        $sn_default = '';
        if (isset($context->options['sn'])) {
            $sn_default = htmlentities($context->options['sn'], ENT_QUOTES);
        }
        ?>
<input type="text" value="<?php echo $cn_default; ?>" id="cn" name="cn" title="First Name" placeholder="First Name" class="n q" /><input type="text" value="<?php echo $sn_default; ?>" id="sn" name="sn" title="Last Name" placeholder="Last Name" class="s n q" />
        <?php 
        ?>
        
        <input type="hidden" name="adv" value="1" />
        <span class="input-group-btn">
            <button name="submitbutton" type="submit" value="Search" title="Search" class="button wdn-icon-search"></button>
        </span>
    </div>

    <ul id="directoryHelp">
        <li><a href="<?php echo UNL_Peoplefinder::getURL(); ?>?std" title="Switch searching type"  tabindex="0" id="simpleSearch" class="simple wdn-button">Simple Search</a></li>
        <li>
            <a href="http://www.unl.edu/ucomm/splash/fieldguide_directory.shtml" title="Find out what's new in the directory" class="wdn-button">Directory Help</a>
        </li>
    </ul>
</form>