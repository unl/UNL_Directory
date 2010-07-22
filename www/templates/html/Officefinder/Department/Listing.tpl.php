<?php
echo '<div class="edit">';
if ($context->userCanEdit(UNL_Officefinder::getUser())) {
    echo ' <a href="'.UNL_Officefinder::getURL().'?view=listing&amp;id='.$context->id.'&amp;format=editing" class="action edit">Edit</a>';
    echo $savvy->render($context, 'Officefinder/Department/Listing/SortForm.tpl.php');
}
echo '</div>';

echo '<div class="listingDetails">';
echo $context->name.' <span class="tel">'.$savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php').'</span>';
echo ' <span class="adr">'.$context->address.'</span>';
echo '</div>';

