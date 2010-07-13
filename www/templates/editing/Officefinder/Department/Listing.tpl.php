<form method="post" action="?view=listing&amp;id=<?php echo $context->id; ?>">
<input type="hidden" name="_type" value="listing" />
<?php
foreach ($context as $var=>$value) {
    $type = 'text';
    if ($var == 'id'
        || $var == 'department_id') {
        $type = 'hidden';
    }
    echo $var . ': <input type="'.$type.'" name="'.$var.'" value="'.$value.'" /><br />';
}
?>
    <input type="submit" />
</form>