<?php
echo $context->name.' '.$savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php');
echo ' '.$context->address;

if ($context->userCanEdit(UNL_Officefinder::getUser())) {
    echo ' <a href="'.UNL_Officefinder::getURL().'?view=listing&amp;id='.$context->id.'&amp;format=editing">Edit</a>';
    echo $savvy->render($context, 'Officefinder/Department/Listing/SortForm.tpl.php');
}
echo '<br />';