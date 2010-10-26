<?php

$template = 'Document';

if ($context->options['mobile'] === true) {
    $template = 'Mobile';
} 

$page = UNL_Templates::factory($template);

$page->doctitle     = '<title>UNL | Directory</title>';
$page->titlegraphic = '<h1>Directory</h1>';

$page->head .= '
<meta name="description" content="UNL Peoplefinder is the Faculty, Staff and Student online directory for the University. Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff. Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden." />
<meta name="keywords" content="university of nebraska-lincoln student faculty staff directory vcard" />
<meta name="author" content="Brett Bieber, UNL Office of University Communications" />
<meta name="viewport" content="width = 320" />
';

if ($context->options['mobile'] === true) {
    $page->head .='<link href="'.UNL_Peoplefinder::getURL().'css/small_devices.css" type="text/css" rel="stylesheet" />';
} else {
    $page->head .='<link href="'.UNL_Peoplefinder::getURL().'css/all_peoplefinder.css" type="text/css" rel="stylesheet" />
                <!--[if IE]>
                <link rel="stylesheet" type="text/css" media="screen" href="'.UNL_Peoplefinder::getURL().'css/ie.css" />
                <![endif]-->
                <script type="text/javascript">var PF_URL = "'.UNL_Peoplefinder::getURL().'";</script>
                <script type="text/javascript" src="'.UNL_Peoplefinder::getURL().'scripts/peoplefinder.js"></script>';
}

if ($context->getRawObject() instanceof UNL_Officefinder) {
    $page->head .= '<link rel="login" href="https://login.unl.edu/cas/login?service='.urlencode(UNL_Officefinder::getURL(null, $context->options)).'" />';
}

if (UNL_Officefinder::getUser()
    && 
        (
        UNL_Officefinder::isAdmin(UNL_Officefinder::getUser())
        || count(new UNL_Officefinder_User_Departments(array('uid'=>UNL_Officefinder::getUser())))
        )
    ) {
    $page->head .= '
    <script type="text/javascript">
    WDN.loadJS("wdn/templates_3.0/scripts/plugins/ui/jQuery.ui.js");
    WDN.loadJS("'.UNL_Peoplefinder::getURL().'scripts/edit_functions.js");
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

if ($context->options['mobile'] === true) {
    $page->maincontentarea = $savvy->render($context->output);
} else {
    $page->maincontentarea = '<div class="four_col">' . $savvy->render($context->output) . '<a id="reportProblem" href="http://www1.unl.edu/comments/">Report a Problem</a><div id="commentProblem"><h3>Report a Problem</h3><form id="wdn_feedback_comments2" method="post" action="http://www1.unl.edu/comments/"><textarea name="comment" id="comment" rows="" cols=""></textarea><input type="submit" value="Submit Comment" /></form></div></div>';
}
$page->footercontent = 'UNL | Office of University Communications | <a href="http://www1.unl.edu/wdn/wiki/About_Peoplefinder" onclick="window.open(this.href); return false;">About Peoplefinder</a> | <a href="http://www1.unl.edu/comments/" title="Click here to direct your comments and questions">comments?</a><br /><br />';
$page->footercontent .= 'Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff.<br />Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden.<br />';

echo $page;