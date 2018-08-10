<form action="<?php echo $context->getURL(); ?>" method="post" id="adduser_<?php echo $context->id; ?>" class="add">
    <input type="hidden" name="_type" value="add_dept_user" />
    <input type="hidden" name="department_id" value="<?php echo $context->id; ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <div class="wdn-input-group">
        <input type="text" name="uid" aria-label="Editor username" />
    	<span class="wdn-input-group-btn">
    		<input type="submit" value="Add" />
    	</span>
    </div>
</form>
