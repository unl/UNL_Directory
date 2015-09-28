<?php
if ($context->ou == 'org') {
    $class = 'org_Sresult';
    $name = $context->cn;
} else {
    $class = 'ppl_Sresult';

    $preferredFirstName = $context->getPreferredFirstName();
    
    $name = $context->sn . ',&nbsp;'. $preferredFirstName;
    if (!empty($context->eduPersonNickname)
        && $context->eduPersonNickname != ' ') {
        $name .= ' <span class="givenName">'.$context->givenName.'</span>';
    }
}
$class .= ' '.$context->eduPersonPrimaryAffiliation;
?>

<li class="<?php echo $class ?>">
    <div class="overflow">
        <?php
        $onclick = '';
        if (isset($controller->options, $controller->options['onclick'])) {
            $onclick .= ' onclick="return ' . $controller->options['onclick'] . "('" . addslashes($context->uid) . '\');"';
        }
        ?>
    <?php if ($controller->options['view'] != 'alphalisting'): ?>
        <img class="profile_pic small photo planetred_profile" src="<?php echo $context->getImageUrl(UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_SMALL) ?>"  alt="Avatar for <?php echo $context->displayName ?>" />
    <?php endif; ?>

        <div class="recordDetails">
            <div class="fn"><a href="<?php echo $context->getUrl() ?>"<?php echo $onclick ?>><?php echo $name ?></a></div>
        <?php if (isset($context->eduPersonPrimaryAffiliation)): ?>
            <div class="eppa">(<?php echo $context->eduPersonPrimaryAffiliation ?>)</div>
        <?php endif; ?>
        <?php if (isset($context->unlHROrgUnitNumber)): ?>
            <?php foreach ($context->unlHROrgUnitNumber as $orgUnit): ?>
            <?php if ($name = UNL_Officefinder_Department::getNameByOrgUnit($orgUnit)): ?>
                <div class="organization-unit"><?php echo $name ?></div>
            <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (isset($context->title) // TODO: Clean this logic mess up (move to model)
        && !(
            isset($orgUnit, $parent->parent, $parent->parent->parent, $parent->parent->parent->parent)
            && $controller->options['view'] == 'department'
            && $orgUnit != $parent->parent->parent->parent->context->org_unit
            )
        && false === strpos(strtolower($context->title), 'retiree') // Let's not share retiree or disabled retiree status
        && false === strpos(strtolower($context->title), 'royalty') // Do not show royalty recipients
        ): ?>
            <div class="title"><?php echo $context->title ?></div>
        <?php endif; ?>

        <?php if (isset($context->telephoneNumber)): ?>
            <div class="tel"><?php echo $savvy->render($context->telephoneNumber, 'Peoplefinder/Record/TelephoneNumber.tpl.php') ?></div>
        <?php endif; ?>

        </div>

        <a data-uid="<?php echo $context->uid ?>" href="<?php echo $context->getUrl() ?>" title="See more information about <?php echo $preferredFirstName ?>" class="cInfo"<?php echo $onclick ?>>More details</a>

    <?php if (isset($parent->parent->context->options['chooser'])): ?>
        <div class="pfchooser"><a href="#" onclick="return pfCatchUID('<?php echo $context->uid ?>');">Choose this person</a></div>
    <?php endif; ?>
    </div>
</li>
