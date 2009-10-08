<?php
require_once '../config.inc.php';
set_include_path(dirname(dirname(__FILE__)).PATH_SEPARATOR.get_include_path());
require_once 'UNL/Autoload.php';

UNL_Templates::$options['version'] = 3;
$page = UNL_Templates::factory('Popup');
$page->doctitle = '<title>UNL | Officefinder</title>';
$page->titlegraphic = '<h1>Officefinder</h1>';
$page->addStylesheet('../peoplefinder_default.css');
$page->head .= <<<META
<meta name="description" content="UNL Officefinder is the searchable department directory for the University. Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff. Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden." />
<meta name="keywords" content="university of nebraska-lincoln student faculty staff directory vcard" />
<meta name="author" content="Brett Bieber, UNL Office of University Communications" />
<meta name="viewport" content="width = 320" />
<link media="only screen and (max-device-width: 480px)" href="../small_devices.css" type="text/css" rel="stylesheet" />
META;

if(isset($_GET['q'])) {
    $page->head .= '<meta name="robots" content="NOINDEX, NOFOLLOW" />';
}

$q = '';
if (!empty($_GET['q'])) {
    $q = $_GET['q'];
    $departments = new UNL_Peoplefinder_Department_Search($q);
}
if (!empty($_GET['d'])) {
    $q = $_GET['d'];
    $department = new UNL_Peoplefinder_Department($q);
    $q = htmlentities($q, ENT_QUOTES);
}
$page->maincontentarea = <<<FORM
<p>Search for UNL departments:</p>
<form method="get" action="?">
    <div>
    <label for="q">Search:&nbsp;</label> 
    <input style="width:18em;" type="text" value="$q" id="q" name="q" /> 
    <input style="margin-bottom:-7px;" name="submitbutton" type="image" src="/ucomm/templatedependents/templatecss/images/go.gif" value="Submit" id="submitbutton" />
    </div> 
</form>
FORM;

if (isset($department)) {
    if (count($department)) {
        $renderer_options = array('uri'=>UNL_PEOPLEFINDER_URI);
        $renderer = new UNL_Peoplefinder_Renderer_HTML($renderer_options);
        $page->maincontentarea .= count($department).' results.';
        $page->maincontentarea .= '<h2>'.htmlentities($department->name).'</h2>';
        $page->maincontentarea .= "<p>{$department->room} <span class='location'>{$department->building}</span><br />{$department->city}, {$department->state} {$department->postal_code}</p>".'<ul class="department">';
        ob_start();
        foreach ($department as $employee) {
            echo '<li class="ppl_Sresult">';
            $renderer->renderListRecord($employee);
            echo '</li>';
        }
        $page->maincontentarea .= ob_get_clean().'</ul>';
    } else {
        $page->maincontentarea .= 'No results could be found.';
    }
}
if (isset($departments)) {
    if (count($departments)) {
        $page->maincontentarea .= '<h2>Search results for '.$q.'</h2><ul class="departments">';
        foreach($departments as $department) {
            $page->maincontentarea .= '<li class="ppl_Sresult"><a href="'.UNL_PEOPLEFINDER_URI.'departments/?d='.urlencode($department->name).'">'.$department->name.'</a></li>';
        }
        $page->maincontentarea .= '</ul>';
    } else {
        $page->maincontentarea .= 'No results could be found.';
    }
}

echo $page;
?>