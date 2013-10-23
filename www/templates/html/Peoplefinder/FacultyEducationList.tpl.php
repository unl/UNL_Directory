<?php
$page->doctitle  = '<title>Faculty Educational Credentials | University of Nebraska-Lincoln</title>';
$page->pagetitle = '<h2>Faculty Educational Credentials</h2>';
?>
<p>The following is a list of UNL faculty educational credentials as of <?php echo $context->getDateLastUpdated(); ?></p>
<table class="zentable cool">
<thead>
    <th>Name</th>
    <th>Education</th>
</thead>
<tbody>
<?php
foreach ($context as $faculty) {
    echo '<tr>';
    echo '<td>'.$faculty->employee_name.'</td>';
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