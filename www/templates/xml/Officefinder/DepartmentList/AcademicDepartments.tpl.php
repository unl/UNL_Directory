<?php
foreach ($context as $listing) {
    echo $savvy->render($listing->department, 'Officefinder/Department.tpl.php');
}