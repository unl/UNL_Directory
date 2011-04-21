<form method="post" action="<?php echo UNL_Officefinder::getURL().$context->parent_id; ?>" class="sortform">
    <input type="hidden" name="_type" value="department" />
    <input type="hidden" name="id" value="<?php echo $context->id; ?>" />
    <input type="hidden" name="sort_order" value="<?php echo $context->sort_order; ?>" />
</form>
