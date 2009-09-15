<?php
require_once '../config.inc.php';
set_include_path(dirname(dirname(__FILE__)).PATH_SEPARATOR.get_include_path());
require_once 'UNL/Autoload.php';

UNL_Templates::$options['version'] = 3;
$page = UNL_Templates::factory('Popup');
$page->doctitle = '<title>UNL | Officefinder</title>';
$page->titlegraphic = '<h1>Officefinder</h1>';
$page->addStylesheet('../peoplefinder_default.css');
$q = '';
if (!empty($_GET['q'])) {
    $q = $_GET['q'];
    $department = new UNL_Peoplefinder_Department($q);
    $q = htmlentities($q, ENT_QUOTES);
}

$page->maincontentarea = <<<FORM
<form method="get" action="?">
    <div>
    <label for="q">Search Departments:&nbsp;</label> 
    <input style="width:18ex;" type="text" value="$q" id="q" name="q" /> 
    <input style="margin-bottom:-7px;" name="submitbutton" type="image" src="/ucomm/templatedependents/templatecss/images/go.gif" value="Submit" id="submitbutton" />
    </div> 
</form>
FORM;

if (isset($department)) {
    if (count($department)) {
        $renderer_options = array('uri'=>UNL_PEOPLEFINDER_URI);
        $renderer = new UNL_Peoplefinder_Renderer_HTML($renderer_options);
        $page->maincontentarea .= count($department).' results.';
        $page->maincontentarea .= '<h2>'.htmlentities($department->name).'</h2><ul>';
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

echo $page;
?>