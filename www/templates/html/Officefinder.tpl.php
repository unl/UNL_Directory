<?php
UNL_Templates::$options['version'] = 3;
$page = UNL_Templates::factory('Document');
$page->doctitle = '<title>UNL | Officefinder</title>';
$page->titlegraphic = '<h1>Officefinder</h1>';
$page->addStylesheet(UNL_Peoplefinder::getURL().'css/all_peoplefinder.css');
$page->head .= '
<meta name="description" content="UNL Officefinder is the searchable department directory for the University. Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff. Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden." />
<meta name="keywords" content="university of nebraska-lincoln student faculty staff directory vcard" />
<meta name="author" content="Brett Bieber, UNL Office of University Communications" />
<meta name="viewport" content="width = 320" />
<link media="only screen and (max-device-width: 480px)" href="'.UNL_Peoplefinder::getURL().'css/small_devices.css" type="text/css" rel="stylesheet" />';

if (isset($context->options['q'])) {
    $page->head .= '<meta name="robots" content="NOINDEX, NOFOLLOW" />
    <script type="text/javascript" src="'.UNL_Peoplefinder::getURL().'scripts/officefinder.js"></script>';
    
}

if (UNL_Officefinder::getUser()) {
    $page->head .= '
    <script type="text/javascript">
    WDN.loadJS("wdn/templates_3.0/scripts/plugins/ui/jQuery.ui.js");
    WDN.loadJS("'.UNL_Peoplefinder::getURL().'scripts/edit_functions.js");
    WDN.loadCSS("'.UNL_Peoplefinder::getURL().'css/editing.css");
    </script>';
}

if ($context->options['mobile'] === true) {
    $page->maincontentarea = $savvy->render($context->output);
} else {
    $page->maincontentarea = '<div class="four_col">' . $savvy->render($context->output) . '</div>';
}
$page->footercontent = 'UNL | Office of University Communications | <a href="http://www1.unl.edu/wdn/wiki/About_Peoplefinder" onclick="window.open(this.href); return false;">About Peoplefinder</a><br /><br />';
$page->footercontent .= 'Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff.<br />Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden.<br />';

echo $page;