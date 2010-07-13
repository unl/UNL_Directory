<form method="post" action="?view=department&amp;id=<?php echo $context->id; ?>">
<input type="hidden" name="_type" value="department" />
<?php
foreach ($context as $var=>$value) {
    $type = 'text';
    if ($var == 'id') {
        $type = 'hidden';
    }
    echo $var . ': <input type="'.$type.'" name="'.$var.'" value="'.$value.'" /><br />';
}
?>
    <input type="submit" />
</form>