<pre>
<?php
foreach ($context as $department) {
    echo '|'.str_repeat('-', $context->getDepth()).$department->name.'<br />';
}
?>
</pre>