<form action="<?php echo UNL_Officefinder::getURL() . $context->department_id; ?>" method="post" class="delete">
    <input type="hidden" name="_type" value="delete_dept_alias" />
    <input type="hidden" name="department_id" value="<?php echo $context->department_id; ?>" />
    <input type="hidden" name="name" value="<?php echo $context->name; ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <button class="dcf-btn dcf-btn-inverse-secondary" type="submit">
        <svg class="dcf-h-4 dcf-w-4 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
            <path d="M23 3h-7V.5a.5.5 0 00-.5-.5h-8a.5.5 0 00-.5.5V3H1a.5.5 0 000 1h2v19.5a.5.5 0 00.5.5h16a.5.5 0 00.5-.5V4h3a.5.5 0 000-1zM8 1h7v2H8V1zm11 22H4V4h15v19z"></path>
            <path d="M7.5 6.5A.5.5 0 007 7v12a.5.5 0 001 0V7a.5.5 0 00-.5-.5zm4 0a.5.5 0 00-.5.5v12a.5.5 0 001 0V7a.5.5 0 00-.5-.5zM15 7v12a.5.5 0 001 0V7a.5.5 0 00-1 0z"></path>
        </svg>
        <span class="dcf-sr-only">Delete</span>
    </button>
</form>
