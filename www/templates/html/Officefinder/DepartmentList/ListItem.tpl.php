<?php
$title = '';
$parent = $context->getParent();
if (!$context->isOfficialDepartment()) {
    if ($parent) {
        $title = '<div class="title">('.$parent->name.')</div>';
    }
}
$li_class = 'dep_result parent_'.$context->id;
if ($parent) {
    $li_class .= ' parent_'.$parent->id;
}
?>
<li class="<?php echo $li_class ?>">
    <div class="overflow">
    <a class="planetred_profile" href="<?php echo $context->getURL() ?>">
    <img alt="Generic Icon" src="<?php echo UNL_Peoplefinder::getURL() ?>images/organization40.png" class="profile_pic small photo">
    </a>
    <div class="recordDetails">
    <div class="fn">
    <a href="<?php echo $context->getURL() ?>"><?php echo $context->name ?></a>
    </div>
    <?php echo $title ?>
    <?php if (isset($context->phone)): ?>
        <div class="tel"><?php echo $savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php') ?></div>
    <?php endif; ?>
    </div>
    <a class="cInfo" href="<?php echo $context->getURL() ?>">More Details</a>
    </div>
</li>
