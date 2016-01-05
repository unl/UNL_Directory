<?php

use UNL\Templates\Templates;

Templates::setCachingService(new UNL\Templates\CachingService\NullService());
$page = Templates::factory('Fixed', Templates::VERSION_4_1);
$wdnIncludePath = dirname(dirname(__DIR__));

if (file_exists($wdnIncludePath . '/wdn/templates_4.1')) {
    $page->setLocalIncludePath($wdnIncludePath);
}

$savvy->addGlobal('page', $page);

$page->doctitle = '<title>Directory | University of Nebraska–Lincoln</title>';
$page->titlegraphic = 'Directory';

$classes = ['hide-navigation', 'hide-breadcrumbs', 'hide-wdn_footer_related'];

$page->setParam('class', implode(' ', $classes));

$page->head .= $savvy->render(null, 'static/head.tpl.php');

if (isset($context->options['q']) || isset($context->options['cn']) || isset($context->options['sn'])) {
    // Don't let search engines index these pages
    $page->head .= $savvy->render(null, 'static/meta-robots.tpl.php');
}

$page->breadcrumbs = $savvy->render(null, 'static/breadcrumbs.tpl.php');
$page->affiliation = '';
$page->navlinks = '';
$page->pagetitle = '';
$page->leftcollinks = '';

$outputTemplate = null;
$isOutputError = $context->getRaw('output') instanceof Exception;
if ($isOutputError) {
    $outputTemplate = 'Exception.tpl.php';
}

if (in_array($context->options['view'], ['instructions', 'help', 'search'])) {
    //Don't wrap the home page, because we want it to use bands
    $page->maincontentarea = $savvy->render($context->output, $outputTemplate);
} else {
    //Wrap everything else
    $page->maincontentarea = '<div class="wdn-band record-container"><div class="wdn-inner-wrapper wdn-inner-padding-sm">' . $savvy->render($context->output, $outputTemplate) . '</div></div>';
}

$savvy->removeGlobal('page');

// add entry-point scripts
$page->maincontentarea .= $savvy->render(null, 'static/after-main.tpl.php');
$page->contactinfo = $savvy->render(null, 'static/contact-info.tpl.php');

$html = $page->toHtml();
unset($page);

if (UNL_Peoplefinder::$minifyHtml) {
    echo zz\Html\HTMLMinify::minify($html, [
        // 'optimizationLevel' => zz\Html\HTMLMinify::OPTIMIZATION_ADVANCED,
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
