<?php
$page->doctitle  = '<title>Faculty Educational Credentials | University of Nebraska-Lincoln</title>';
$page->pagetitle = '<h2>Faculty Educational Credentials</h2>';
?>
<p>The following is a list of UNL faculty educational credentials as of <?php echo $context->getDateLastUpdated(); ?></p>
<p><?php echo count($context); ?> results</p>
<table class="zentable cool">
<thead>
    <th>Name</th>
    <th>Education</th>
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

    echo '<tr>';
    echo '<td>';
    if ($record) {
        echo '<a href="'.$controller->getURL().'?uid='.$record->uid.'">'.$faculty->employee_name.'</a>';
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

