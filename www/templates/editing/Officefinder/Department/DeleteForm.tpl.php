<form action="<?php echo $context->getURL(); ?>" method="post" id="deletedepartment_<?php echo $context->id; ?>" class="delete">
    <input type="hidden" name="_type" value="delete_department" />
    <input type="hidden" name="department_id" value="<?php echo $context->id; ?>" />
</form>
