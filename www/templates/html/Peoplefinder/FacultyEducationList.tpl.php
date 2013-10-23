<?php
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/lib/StaticCache/src/StaticCache.php';
$_GET = array();
StaticCache::autoCache();
$page->doctitle  = '<title>Faculty Educational Credentials | University of Nebraska-Lincoln</title>';
$page->pagetitle = '<h2>Faculty Educational Credentials</h2>';
?>
<p>The following is a list of UNL faculty educational credentials as of <?php echo $context->getDateLastUpdated(); ?></p>
<p><?php echo count($context); ?> results</p>
<div class="grid3 first">
<?php echo $savvy->render(null, 'Peoplefinder/SearchResults/Filters.tpl.php'); ?>
</div>
<div class="grid9">
<div class="results affiliation faculty">
<h3>Faculty</h3>
<table class="zentable cool">
<thead>
    <tr>
        <th>Name</th>
        <th>Education</th>
    </tr>
</thead>
<tbody>
<?php
$limited = new LimitIterator($context, $context->options['offset'], $context->options['limit']);
foreach ($limited as $faculty) {
    try {
        $record = $faculty->getRecord();
    } catch (Exception $e) {
        $record = false;
    }

    echo '<tr class="ppl_Sresult faculty">';
    echo '<td>';
    if ($record) {
        echo '<a href="'.$controller->getURL().'?uid='.$record->uid.'">'.$faculty->employee_name.'</a>';
        if (isset($record->unlHROrgUnitNumber)) {
            foreach ($record->unlHROrgUnitNumber as $orgUnit) {
                if ($name = UNL_Officefinder_Department::getNameByOrgUnit($orgUnit)) {
                    echo '        <div class="organization-unit">'.$name.'</div>'.PHP_EOL;
                }
            }
        }
    } else {
        echo $faculty->employee_name;
    }
    echo '</td>';
    echo '<td>';
    $degrees = array();
    foreach ($faculty->getEducation() as $degree) {
        $degrees[] = $degree;
    }
    echo implode(', ', $degrees);
    echo '</td>';
    echo '</tr>';
}
?>
</tbody>
</table>
<?php
$url = $controller->getURL();
if (count($context) > $context->options['limit']) {
    $pager = new stdClass();
    $pager->total  = count($context);
    $pager->limit  = $context->options['limit'];
    $pager->offset = $context->options['offset'];
    $pager->url    = $url.'?view=facultyedu';
    echo $savvy->render($pager, 'PaginationLinks.tpl.php');
}
?>
</div>
</div>
<script type="text/javascript">
WDN.loadJS('../scripts/filters.js', function(){
	filters.initialize();
});
</script>
