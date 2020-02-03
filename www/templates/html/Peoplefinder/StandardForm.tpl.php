<form method="get" id="peoplefinder" action="<?php echo UNL_Peoplefinder::getURL(); ?>" class="dcf-form directory-search dcf-d-flex dcf-jc-center">
    <?php if (isset($context->options['chooser'])): ?>
        <input type="hidden" name="chooser" value="true" />
    <?php endif; ?>

    <?php
    $default = '';
    if (isset($context->options['q']) && !($context->options['q'] instanceof ArrayAccess)) {
        $default = htmlentities((string)$context->options['q'], ENT_QUOTES);
    }
    ?>
    <div class="dcf-input-group dcf-w-max-md">
        <input tabindex="0" type="text" autofocus placeholder="Herbie Husker" value="<?php echo $default; ?>" id="q" name="q" aria-label="Enter a name to begin your search" />
        <button name="submitbutton" type="submit" value="Search" class="button dcf-btn dcf-btn-primary">
            <svg class="dcf-h-5 dcf-w-5 dcf-fill-current" aria-hidden="true" focusable="false" height="16" width="16" viewBox="0 0 48 48">
                <path d="M18 36a17.9 17.9 0 0 0 11.27-4l15.31 15.41a2 2 0 0 0 2.84-2.82L32.08 29.18A18 18 0 1 0 18 36zm0-32A14 14 0 1 1 4 18 14 14 0 0 1 18 4z"></path>
            </svg>        
            <span class="dcf-sr-only">Search</span>
        </button>
    </div>
</form>

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
