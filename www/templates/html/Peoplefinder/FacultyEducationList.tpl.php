<table class="zentable">
<thead>
    <th>Name</th>
    <th>Education</th>
</thead>
<tbody>
<?php
foreach ($context as $faculty) {
    echo '<tr>';
    echo '<td>'.$faculty->employee_name.'</td>';
    echo '<td>'.$faculty->degree_string.'</td>';
    echo '</tr>';
}
?>
</tbody>
</table>