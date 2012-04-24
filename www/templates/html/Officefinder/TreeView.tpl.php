
<ol>
<?php
$depth = 0;
foreach ($context as $department) {
    
    if ($context->getDepth() > $depth) {
        echo '<ol>';
    } elseif ($context->getDepth() < $depth) {
        // Close the current list item
        echo '</li>';
        // Loop through until we reach the previous depth
        echo str_repeat('</ol></li>', $depth - $context->getDepth());
    } elseif($context->getDepth()==$depth) {
        echo '</li>';
    }
    echo PHP_EOL;
    echo str_repeat(' ', $context->getDepth());
    echo '<li class="d'.$context->getDepth().'">'.$department->name;
    $depth = $context->getDepth();
}
?>
</li>
</ol>
