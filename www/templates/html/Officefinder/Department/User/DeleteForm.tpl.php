<form action="<?php echo UNL_Officefinder::getURL() . $context->department_id; ?>" method="post" class="delete">
    <input type="hidden" name="_type" value="delete_dept_user" />
    <input type="hidden" name="department_id" value="<?php echo $context->department_id; ?>" />
    <input type="hidden" name="uid" value="<?php echo $context->uid; ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <button class="icon-trash dcf-btn dcf-btn-primary" type="submit"><span class="dcf-sr-only">Delete</span></button>
</form>
