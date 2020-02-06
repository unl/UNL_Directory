<form class="dcf-form add" action="<?php echo $context->getURL(); ?>" method="post" id="addalias_<?php echo $context->id; ?>">
    <input type="hidden" name="_type" value="add_dept_alias">
    <input type="hidden" name="department_id" value="<?php echo $context->id; ?>">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <div class="dcf-input-group">
        <input type="text" name="name" aria-label="Also known as">
        <input class="dcf-btn dcf-btn-primary" type="submit" value="Add">
    </div>
</form>
