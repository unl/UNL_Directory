<form action="<?php echo $context->getURL(); ?>" method="post" id="addalias_<?php echo $context->id; ?>" class="add">
    <input type="hidden" name="_type" value="add_dept_alias" />
    <input type="hidden" name="department_id" value="<?php echo $context->id; ?>" />
    <div class="wdn-input-group">
    	<input type="text" name="name" />
    	<span class="wdn-input-group-btn">
    		<input type="submit" value="Add" />
    	</span>
    </div>
</form>
