<?php
/* @var $context Exception */
if (false == headers_sent()
    && $code = $context->getCode()) {
    header('HTTP/1.1 '.$code.' '.$context->getMessage());
    header('Status: '.$code.' '.$context->getMessage());
}
if (isset($page)) {
    $page->addScriptDeclaration("WDN.initializePlugin('notice');");
}
?>

<div class="dcf-notice dcf-notice-warning" hidden>
    <h2>Whoops! Sorry, there was an error:</h2>
    <div>
        <p><?php echo $context->getMessage(); ?></p>
    </div>
</div>
