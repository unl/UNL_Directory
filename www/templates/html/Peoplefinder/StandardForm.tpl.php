<form method="get" id="peoplefinder" action="<?php echo UNL_Peoplefinder::getURL(); ?>" class="directory-search">
    <?php if (isset($context->options['chooser'])): ?>
        <input type="hidden" name="chooser" value="true" />
    <?php endif; ?>

    <?php
    $default = '';
    if (isset($context->options['q']) && !($context->options['q'] instanceof ArrayAccess)) {
        $default = htmlentities((string)$context->options['q'], ENT_QUOTES);
    }
    ?>
    <div class="wdn-input-group">
        <input tabindex="0" type="text" autofocus placeholder="Herbie Husker" value="<?php echo $default; ?>" id="q" name="q" title="Enter a name to begin your search" />
        <span class="wdn-input-group-btn">
            <button name="submitbutton" type="submit" value="Search" title="Search" class="button wdn-icon-search"></button>
        </span>
    </div>
</form>
<p class="wdn-sans-serif"><a href="<?php echo UNL_Peoplefinder::getURL() ?>help/">Help</a></p>

<?php echo $savvy->render((object) [
    'id' => 'noticeTemplate',
    'template' => 'Search/NoticeTemplate.tpl.php',
], 'jsrender.tpl.php') ?>

<?php echo $savvy->render((object) [
    'id' => 'genericErrorTemplate',
    'template' => 'Search/GenericErrorTemplate.tpl.php',
], 'jsrender.tpl.php') ?>

<?php echo $savvy->render((object) [
    'id' => 'queryLengthTemplate',
    'template' => 'Search/QueryLengthTemplate.tpl.php',
], 'jsrender.tpl.php') ?>
