<form action="<?php echo $parent->parent->context->getURL(); ?>" method="post" id="deletealias_<?php echo $context->department_id.$context->name; ?>" class="delete">
    <input type="hidden" name="_type" value="delete_dept_alias" />
    <input type="hidden" name="department_id" value="<?php echo $context->department_id; ?>" />
    <input type="hidden" name="name" value="<?php echo $context->name; ?>" />
	<button class="icon-trash wdn-button-brand" type="submit" onclick="if (!confirm('Are you sure? This will delete the alias.')) return false;"><span class="wdn-text-hidden">Delete</span></button>
</form>
