<form method="post" action="<?php echo UNL_Officefinder::getURL(); ?>?view=department&amp;id=<?php echo $context->department_id; ?>">
    <input type="hidden" name="_type" value="listing" />
    <input type="hidden" name="id" value="<?php echo $context->id; ?>" />
</form>
