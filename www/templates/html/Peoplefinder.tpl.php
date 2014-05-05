<?php
UNL_Templates::setCachingService(new UNL_Templates_CachingService_Null());

UNL_Templates::$options['version'] = 4.0;

$template = 'Fixed';

$page = UNL_Templates::factory($template);

$savvy->addGlobal('page', $page);

$page->doctitle     = '<title>Directory | UNL</title>';
$page->titlegraphic = '<a href="' . UNL_Peoplefinder::getURL() . '">Directory</a>';

$classes = array('terminal');

if (in_array($controller->options['view'], array('instructions', 'search'))) {
    $classes[] = 'hide-pagetitle';
}
$page->__params['class']['value'] = implode(' ', $classes);

$page->head .= '
<meta name="description" content="UNL Directory is the Faculty, Staff and Student online directory for the University. Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff. Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden." />
<meta name="keywords" content="university of nebraska-lincoln student faculty staff directory vcard" />
<meta name="author" content="Brett Bieber, UNL Office of University Communications" />
';

if ($context->options['mobile'] === true) {
    $page->head .='<link href="'.UNL_Peoplefinder::getURL().'css/small_devices.css" type="text/css" rel="stylesheet" />';
} else {
    $page->head .='<link href="'.UNL_Peoplefinder::getURL().'css/all_peoplefinder.css" type="text/css" rel="stylesheet" />
                <!--[if IE]>
                <link rel="stylesheet" type="text/css" media="screen" href="'.UNL_Peoplefinder::getURL().'css/ie.css" />
                <![endif]-->
                <link rel="stylesheet" type="text/css" media="print" href="'.UNL_Peoplefinder::getURL().'css/print.css" />
                <link rel="stylesheet" type="text/css" href="'.UNL_Peoplefinder::getURL().'css/vcard.css" />
                <script type="text/javascript">var PF_URL = "'.UNL_Peoplefinder::getURL().'", ANNOTATE_URL = "'.UNL_Peoplefinder::$annotateUrl.'";</script>
                <script type="text/javascript" src="'.UNL_Peoplefinder::getURL().'scripts/toolbar_peoplefinder.js"></script>
                <script type="text/javascript" src="'.UNL_Peoplefinder::getURL().'scripts/peoplefinder.js"></script>';
}

if ($context->getRawObject() instanceof UNL_Officefinder) {
    $login_url = UNL_Officefinder::getURL(null, $context->getRaw('options'));

    if (strpos($login_url, '//') === 0) {
        $login_url = 'https:'.$login_url;
    }

    $page->head .= '<link rel="login" href="https://login.unl.edu/cas/login?service='.urlencode($login_url).'" />';
}

if (isset($context->options['print'])) {
    $page->head .= '<script type="text/javascript">WDN.jQuery(document).ready(function(){window.print()});</script>';
}


if ($context->options['view'] != 'alphalisting'
    && UNL_Officefinder::getUser()
    && 
        (
        UNL_Officefinder::isAdmin(UNL_Officefinder::getUser())
        || count(new UNL_Officefinder_User_Departments(array('uid'=>UNL_Officefinder::getUser())))
        )
    ) {
    $page->head .= '
    <script type="text/javascript">
        WDN.loadJS("/wdn/templates_3.0/scripts/plugins/ui/jQuery.ui.js", function() {WDN.loadJS("'.UNL_Peoplefinder::getURL().'scripts/edit_functions.js");});
        WDN.loadCSS("'.UNL_Peoplefinder::getURL().'css/editing.css");
    </script>';
    $page->titlegraphic .= '<div id="userDepts"><a class="mydepts" href="'.UNL_Officefinder::getURL().'?view=mydepts">My Departments</a></div>';
}

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
    <li><a href="'.UNL_Peoplefinder::getURL().'">Directory</a></li>
    <li>Search</li>
</ul>';


$page->pagetitle = '<h1>Search the Directory</h1>';

if ($context->options['view'] == 'instructions') {
    //Don't wrap the home page, because we want it to use bands
    $page->maincontentarea = $savvy->render($context->output);
} else {
    //Wrap everything else
    $page->maincontentarea = '<div class="wdn-band"><div class="wdn-inner-wrapper">' . $savvy->render($context->output) . '</div></div>';
}



$page->footercontent = 'UNL | Office of University Communications | <a href="http://www1.unl.edu/wdn/wiki/About_Peoplefinder" onclick="window.open(this.href); return false;">About Directory</a> | <a href="http://www1.unl.edu/comments/" title="Click here to direct your comments and questions" class="dir_correctionRequest">comments?</a>';
$page->footercontent .= $savvy->render($context, 'CorrectionForm.tpl.php');
$page->footercontent .= '<br /><br />Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff.<br />Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden.<br />';

echo $page;