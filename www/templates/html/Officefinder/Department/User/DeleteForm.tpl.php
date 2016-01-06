<form action="<?php echo UNL_Officefinder::getURL() . $context->department_id; ?>" method="post" class="delete">
    <input type="hidden" name="_type" value="delete_dept_user" />
    <input type="hidden" name="department_id" value="<?php echo $context->department_id; ?>" />
    <input type="hidden" name="uid" value="<?php echo $context->uid; ?>" />
    <button class="icon-trash wdn-button-brand" type="submit"><span class="wdn-text-hidden">Delete</span></button>
</form>
