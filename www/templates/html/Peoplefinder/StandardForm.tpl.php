<form method="get" id="peoplefinder" action="<?php echo UNL_Peoplefinder::getURL(); ?>" class="directorySearch first">
    <?php if (isset($context->options['chooser'])) {
        echo '<input type="hidden" name="chooser" value="true" />';
    }
    $default = '';
    if (isset($context->options['q'])
        && !($context->options['q'] instanceof ArrayAccess)) {
        $default = htmlentities((string)$context->options['q'], ENT_QUOTES);
    }
    ?>
    
    <div class="input-group">
        <input type="text" autofocus placeholder="Enter a name" value="<?php echo $default; ?>" id="q" name="q" title="Enter a name to begin your search" plaseholder="Enter a name" class="q" />
        <span class="input-group-btn">
            <button name="submitbutton" type="submit" value="Search" title="Search" class="button wdn-icon-search"></button>
        </span>
    </div>

    <ul id="directoryHelp">
        <li><a href="<?php echo UNL_Peoplefinder::getURL(); ?>?adv" title="Switch searching type"  tabindex="0" id="advancedSearch" class="advanced wdn-button">Advanced Search</a></li>
        <li>
            <a href="http://www.unl.edu/ucomm/splash/fieldguide_directory.shtml" title="Find out what's new in the directory" class="wdn-button">Directory Help</a>
        </li>
    </ul>
</form>

