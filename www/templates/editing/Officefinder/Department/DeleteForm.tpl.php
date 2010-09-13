<a href="#" class="action delete" onclick="if (confirm('Are you sure? This will delete this record and <?php echo count($context->getChildren()); ?> children.')) document.getElementById('deletedepartment_<?php echo $context->id; ?>').submit(); else return false;">delete</a>
<form action="<?php echo $context->getURL(); ?>" method="post" id="deletedepartment_<?php echo $context->id; ?>" style="width:120px;">
    <input type="hidden" name="_type" value="delete_department" />
    <input type="hidden" name="department_id" value="<?php echo $context->id; ?>" />
</form>