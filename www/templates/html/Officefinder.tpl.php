<?php
UNL_Templates::$options['version'] = 3;
$page = UNL_Templates::factory('Document');
$page->doctitle = '<title>UNL | Officefinder</title>';
$page->titlegraphic = '<h1>Officefinder</h1>';
$page->addStylesheet(UNL_Peoplefinder::getURL().'css/peoplefinder_default.css');
$page->head .= '
<meta name="description" content="UNL Officefinder is the searchable department directory for the University. Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff. Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden." />
<meta name="keywords" content="university of nebraska-lincoln student faculty staff directory vcard" />
<meta name="author" content="Brett Bieber, UNL Office of University Communications" />
<meta name="viewport" content="width = 320" />
<link media="only screen and (max-device-width: 480px)" href="'.UNL_Peoplefinder::getURL().'css/small_devices.css" type="text/css" rel="stylesheet" />';

if (isset($context->options['q'])) {
    $page->head .= '<meta name="robots" content="NOINDEX, NOFOLLOW" />';
}

$q = '';
if (!empty($context->options['q'])) {
    $q = htmlentities($context->options['q'], ENT_QUOTES);
} elseif (!empty($context->options['d'])) {
    $q = htmlentities($context->options['d'], ENT_QUOTES);
}
$page->maincontentarea = $savvy->render($context, 'Officefinder/StandardForm.tpl.php');

$page->maincontentarea .= $savvy->render($context->output);

echo $page;