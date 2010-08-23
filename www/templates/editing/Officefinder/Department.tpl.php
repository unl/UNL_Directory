<form method="post" action="<?php echo UNL_Officefinder::getURL(); ?>?view=department&amp;id=<?php echo $context->id; ?>">
<input type="hidden" name="_type" value="department" />
<?php
foreach ($context as $var=>$value) {
    switch($var) {
        case 'id':
            $type = 'hidden';
            break;
        case 'uidlastupdated':
        case 'options':
        case 'lft':
        case 'rgt':
        case 'level':
            continue 2;
        default:
            $type = 'text';
    }
    echo $var . ': <input type="'.$type.'" name="'.$var.'" value="'.$value.'" /><br />';
}
?>
    <input type="submit" />
</form>
<?php
// Disable editing sublistings for now.
//$listings = $context->getChildLeafNodes();
//if (count($listings)) {
//    echo $savvy->render($listings);
//}
?>