<form action="<?php echo UNL_Officefinder::getURL() . $context->department_id; ?>" method="post" class="delete">
    <input type="hidden" name="_type" value="delete_dept_alias" />
    <input type="hidden" name="department_id" value="<?php echo $context->department_id; ?>" />
    <input type="hidden" name="name" value="<?php echo $context->name; ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <button class="dcf-btn dcf-btn-inverse-secondary" type="submit"><span class="icon-trash" aria-hidden="true"></span><span class="dcf-sr-only">Delete</span></button>
</form>
