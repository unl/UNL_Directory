<form action="<?php echo $context->getURL(); ?>" method="post" id="deletedepartment_<?php echo $context->id ?>" class="dcf-form delete">
    <input type="hidden" name="_type" value="delete_department" />
    <input type="hidden" name="department_id" value="<?php echo $context->id; ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <input class="dcf-btn dcf-btn-primary" type="submit" value="Delete" />
</form>
