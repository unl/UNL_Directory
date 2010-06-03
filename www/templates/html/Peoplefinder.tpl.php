<?php
$renderer_options = array('uri'=>UNL_PEOPLEFINDER_URI);
$renderer = new UNL_Peoplefinder_Renderer_HTML($renderer_options);
$myself = htmlentities(str_replace('index.php', '', $_SERVER['PHP_SELF']), ENT_QUOTES);
UNL_Templates::$options['version'] = 3;

$template = 'Document';

if (!isset($context->options['mobile'])
    && (preg_match('/text\/vnd\.wap\.wml|application\/vnd\.wap\.xhtml\+xml/', $_SERVER['HTTP_ACCEPT']))
        || preg_match('/sony|symbian|nokia|samsung|mobile|windows ce|epoc|opera/', $_SERVER['HTTP_USER_AGENT'])
        || preg_match('/mini|nitro|j2me|midp-|cldc-|netfront|mot|up\.browser|up\.link|audiovox/', $_SERVER['HTTP_USER_AGENT'])
        || preg_match('/blackberry|ericsson,|panasonic|philips|sanyo|sharp|sie-/', $_SERVER['HTTP_USER_AGENT'])
        || preg_match('/portalmmm|blazer|avantgo|danger|palm|series60|palmsource|pocketpc/', $_SERVER['HTTP_USER_AGENT'])
        || preg_match('/smartphone|rover|ipaq|au-mic,|alcatel|ericy|vodafone\/|wap1\.|wap2\.|iPhone|android/', $_SERVER['HTTP_USER_AGENT'])
        ) {
    $template = 'Popup';
}

$page = UNL_Templates::factory($template);

$page->doctitle = '<title>UNL | Peoplefinder</title>';

$page->head .= '
<meta name="description" content="UNL Peoplefinder is the Faculty, Staff and Student online directory for the University. Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff. Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden." />
<meta name="keywords" content="university of nebraska-lincoln student faculty staff directory vcard" />
<meta name="author" content="Brett Bieber, UNL Office of University Communications" />
<meta name="viewport" content="width = 320" />
<link rel="stylesheet" type="text/css" media="screen" href="css/peoplefinder_default.css" />
<link media="only screen and (max-device-width: 480px)" href="css/small_devices.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="'.UNL_PEOPLEFINDER_URI.'scripts/peoplefinder.js"></script>
';

if (isset($context->options['q']) 
    || isset($context->options['uid'])
    || isset($context->options['cn'])
    || isset($context->options['sn'])) {
    // Don't let search engines index these pages
    $page->head .= '<meta name="robots" content="NOINDEX, NOFOLLOW" />';
}

$page->breadcrumbs = '
<ul>
    <li><a href="http://www.unl.edu/" title="University of Nebraska&ndash;Lincoln">UNL</a></li>
    <li>Peoplefinder</li>
</ul>';

$page->titlegraphic = '<h1>UNL Peoplefinder</h1>';

$page->maincontentarea = $savvy->render($context->output);

$page->footercontent = 'UNL | Office of University Communications | <a href="http://www1.unl.edu/wdn/wiki/About_Peoplefinder" onclick="window.open(this.href); return false;">About Peoplefinder</a><br /><br />';
$page->footercontent .= 'Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff.<br />Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden.<br />';

echo $page;