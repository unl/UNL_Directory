<form action="<?php echo UNL_Officefinder::getURL(); ?>?view=department&amp;id=<?php echo $context->id; ?>" method="post" id="deletedepartment_<?php echo $context->id; ?>" style="width:120px;">
    <input type="hidden" name="_type" value="delete_department" />
    <input type="hidden" name="department_id" value="<?php echo $context->id; ?>" />
    <a href="#" onclick="if (confirm('Are you sure?')) document.getElementById('deletedepartment_<?php echo $context->id; ?>').submit();">Delete</a>
</form>