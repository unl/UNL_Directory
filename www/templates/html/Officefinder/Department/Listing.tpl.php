<?php
$address = $context->address;
if (preg_match('/^([A-Z]+)\s/', $context->address, $matches)) {
    $address = str_replace($matches[1], '<a class="location mapurl" href="http://maps.unl.edu/#'.$matches[1].'">'.$matches[1].'</a>', $context->address);
}

echo '<div class="edit">';
if ($context->userCanEdit(UNL_Officefinder::getUser())) {
    echo ' <a href="'.$context->getURL().'&amp;format=editing" class="action edit">edit</a>'.PHP_EOL;
    echo $savvy->render($context, 'Officefinder/Department/Listing/SortForm.tpl.php');
    include dirname(__FILE__).'/../../../editing/Officefinder/Department/DeleteForm.tpl.php';
    echo ' <a href="'.UNL_Officefinder::getURL(null, array('view'      => 'department',
                                                          'parent_id' => $context->id)).'&amp;format=editing" class="action addchild">Add child</a>';
}
echo '</div>';

echo '<div class="listingDetails">';
echo $context->name.' <span class="tel">'.$savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php').'</span>';
echo ' <span class="adr">'.$address.'</span>';
echo '</div>';
?>
