<?php

// For the printer-friendly version, display without the UNL template.
if (in_array($context->options['view'], ['alphalisting'])) {
  echo $savvy->render($context->output);
  return;
}

use UNL\Templates\Templates;

Templates::setCachingService(new UNL\Templates\CachingService\NullService());
$page = Templates::factory('AppLocal', Templates::VERSION_5_3);
$wdnIncludePath = dirname(dirname(__DIR__));

if (file_exists($wdnIncludePath . '/wdn/templates_5.3')) {
    $page->setLocalIncludePath($wdnIncludePath);
}

$savvy->addGlobal('page', $page);

if (isset($siteNotice) && $siteNotice->display) {
    $page->displayDCFNoticeMessage(
        $siteNotice->title,
        $siteNotice->message,
        $siteNotice->type,
        $siteNotice->noticePath,
        $siteNotice->containerID
    );
}

// no menu items, so hide mobile menu
$page->addStyleDeclaration("#dcf-mobile-toggle-menu {display: none!important}");

$page->doctitle = '<title>Directory | University of Nebraska–Lincoln</title>';
$page->titlegraphic = '<a class="dcf-txt-h5" href="' . UNL_Peoplefinder::getURL() . '">Directory</a>';

$page->head .= $savvy->render(null, 'static/head.tpl.php');

if (isset($context->options['q']) || isset($context->options['cn']) || isset($context->options['sn'])) {
    // Don't let search engines index these pages
    $page->head .= $savvy->render(null, 'static/meta-robots.tpl.php');
}

if (in_array($context->options['view'], ['instructions', 'help', 'search'])) {
    //Don't wrap the home page, because we want it to use bands
    $page->maincontentarea = $savvy->render($context->output);
} else {
    //Wrap everything else
    $page->maincontentarea =
        '<div class="dcf-bleed record-container unl-bg-lightest-gray"><div class="dcf-wrapper dcf-pt-8 dcf-pb-8">'
        . $savvy->render($context->output)
        . '</div></div>';
}

$page->maincontentarea .=  $savvy->render(null, 'static/modal.tpl.php');;

// If the controller implements the notice interface and it has a notice we can display it
if ($savvy->getGlobals()['controller'] instanceof UNL_PersonInfo_PageNoticeInterface && $context->has_notice()) {
    $page->addScriptDeclaration("WDN.initializePlugin('notice');");
    $page->displayDCFNoticeMessage(
        $context->get_notice_title(),
        $context->get_notice_message(),
        $context->get_notice_type(),
        'dcf-notice',
        'dcf-main'
    );
    $context->clear_notice();
}

// add entry-point scripts
$page->maincontentarea .= $savvy->render(null, 'static/after-main.tpl.php');
$page->contactinfo = $savvy->render(null, 'static/contact-info.tpl.php');

$loginService = UNL_Officefinder::getURL() . 'editor';
if (strpos($loginService, '//') === 0) {
    $loginService = 'https:' . $loginService;
}
$loginUrl = 'https://shib.unl.edu/idp/profile/cas/login?service=' . urlencode($loginService);
$logoutUrl = 'https://shib.unl.edu/idp/profile/cas/logout?url=' . urlencode($loginService);
$page->addScriptDeclaration("require(['wdn'], function(WDN) {
    WDN.setPluginParam('idm', 'login', '" . $loginUrl ."');
    WDN.setPluginParam('idm', 'logout', '" . $logoutUrl ."');
});");

$html = $page->toHtml();
unset($page);

// Hack to get script tag added to jsbody with an id
// TODO: Update $page->addScriptDeclaration to handle setting of id (only excpects type currently)
$baseUrl = UNL_Peoplefinder::getURL();
$version = UNL_Peoplefinder::$staticFileVersion;
$scriptTag = "
<script id=\"main-entry\">
require(['" . $baseUrl . "js/directory.min.js?v=" . $version ."'], function(directory) {
  directory.initialize('" . $baseUrl . "', '" . UNL_Peoplefinder::$annotateUrl . "');
 });
 </script>";

$jsBodyMarker = '<!-- InstanceBeginEditable name="jsbody" -->';
$html = str_replace($jsBodyMarker, $jsBodyMarker . $scriptTag, $html);

if (in_array($_SERVER['SERVER_NAME'], UNL_Peoplefinder::$testDomains)) {
  $html = str_replace('unlcms.unl.edu', 'unlcms-staging.unl.edu', $html);
}

if (UNL_Peoplefinder::$minifyHtml) {
    echo zz\Html\HTMLMinify::minify($html, [
        'optimizationLevel' => zz\Html\HTMLMinify::OPTIMIZATION_SIMPLE,
        'excludeComment' => [
            '/<!--\s+Membership and regular participation .*?-->/s',
            '/<!-- (?:Instance|Template)Begin template="[^"]+" codeOutsideHTMLIsLocked="false" -->/',
            '/<!-- (?:Instance|Template)BeginEditable name="[^"]+" -->/',
            '/<!-- (?:Instance|Template)EndEditable -->/',
            '/<!-- (?:Instance|Template)Param name="[^"]+" type="[^"]+" value="[^"]*" -->/',
        ]
    ]);
} else {
    echo $html;
}

$savvy->removeGlobal('page');
