<?php
echo $context->name.' '.$savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php');
echo ' '.$context->address;

if ($context->userCanEdit(UNL_Officefinder::getUser())) {
    echo ' <a href="?view=listing&amp;id='.$context->id.'&amp;format=editing">Edit</a>';
}
echo '<br />';