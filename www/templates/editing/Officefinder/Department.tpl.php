<form method="post" action="<?php echo UNL_Peoplefinder::getURL(); ?>departments/?view=department&amp;id=<?php echo $context->id; ?>">
<input type="hidden" name="_type" value="department" />
<?php
foreach ($context as $var=>$value) {
    if ($var == 'options') {
        continue;
    }
    $type = 'text';
    if ($var == 'id') {
        $type = 'hidden';
    }
    echo $var . ': <input type="'.$type.'" name="'.$var.'" value="'.$value.'" /><br />';
}
?>
    <input type="submit" />
</form>
<?php 
$listings = $context->getListings();
if (count($listings)) {
    echo $savvy->render($listings);
}
?>