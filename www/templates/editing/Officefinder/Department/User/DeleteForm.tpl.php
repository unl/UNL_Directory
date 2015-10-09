<form action="<?php echo $parent->parent->context->getURL(); ?>" method="post" id="deleteuser_<?php echo $context->department_id.$context->uid; ?>" class="delete">
    <input type="hidden" name="_type" value="delete_dept_user" />
    <input type="hidden" name="department_id" value="<?php echo $context->department_id; ?>" />
    <input type="hidden" name="uid" value="<?php echo $context->uid; ?>" />
    <button class="icon-trash" type="submit" onclick="if (!confirm('Are you sure? This will remove editing access for this user.')) return false;"><span class="wdn-text-hidden">Delete</span></button>
</form>
