<?php
echo $context->name.' '.$context->phone;

if ($context->userCanEdit(UNL_Officefinder::getUser())) {
    echo ' <a href="?view=listing&amp;id='.$context->id.'&amp;format=editing">Edit</a>';
}
echo '<br />';