<a href="#" onclick="if (confirm('Are you sure? This will remove editing access for this user.')) document.getElementById('deleteuser_<?php echo $context->department_id.$context->uid; ?>').submit(); else return false;">Delete</a>
<form action="<?php echo $parent->parent->context->getURL(); ?>" method="post" id="deleteuser_<?php echo $context->department_id.$context->uid; ?>" style="width:120px;">
    <input type="hidden" name="_type" value="delete_dept_user" />
    <input type="hidden" name="department_id" value="<?php echo $context->department_id; ?>" />
    <input type="hidden" name="uid" value="<?php echo $context->uid; ?>" />
</form>