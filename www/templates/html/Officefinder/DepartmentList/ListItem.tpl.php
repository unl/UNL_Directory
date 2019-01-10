<?php
$title = '';
$li_class = 'dep_result parent_'.$context->id;

$parent = $context->getParent();
if ($parent) {
    $li_class .= ' parent_'.$parent->id;

    if (!$context->isOfficialDepartment()) {
        $title = $parent->name;
    }
}
?>
<li class="<?php echo $li_class ?>" data-href="<?php echo $context->getURL() ?>">
    <div class="overflow">
        <div class="profile_pic" href="<?php echo $context->getURL() ?>">
            <img alt="Building Image" src="<?php echo $context->getImageUrl(UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_SMALL) ?>" class="photo" />
        </div>
        <div class="recordDetails unl-font-sans">
            <div class="fn dcf-txt-lg dcf-bold">
                <a class="dcf-txt-decor-hover" href="<?php echo $context->getURL() ?>"><?php echo $context->name ?></a>
            </div>
            <?php if ($title): ?>
                <div class="title dcf-txt-sm"><?php echo $title ?></div>
            <?php endif; ?>
            <?php if (!empty($context->phone)): ?>
                <div class="tel dcf-txt-sm dcf-mt-1"><?php echo $savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php') ?></div>
            <?php endif; ?>
        </div>
    </div>
</li>
