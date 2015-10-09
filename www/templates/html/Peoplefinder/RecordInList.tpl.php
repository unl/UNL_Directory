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

$onclick = '';
if (isset($controller->options, $controller->options['onclick'])) {
    $onclick .= ' onclick="return ' . $controller->options['onclick'] . "('" . addslashes($context->uid) . '\');"';
}

$title = $context->formatTitle();
?>

<li class="<?php echo $class ?>" tabindex="0" data-href="<?php echo $context->getUrl() ?>" data-uid="<?php echo $context->uid ?>">
    <div class="overflow">
        <?php if ($controller->options['view'] != 'alphalisting'): ?>
            <div class="profile_pic">
                <img class="photo" src="<?php echo $context->getImageUrl(UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_SMALL) ?>" alt="Avatar for <?php echo $context->displayName ?>" />
            </div>
        <?php endif; ?>

        <div class="recordDetails">
            <div class="fn"><a href="<?php echo $context->getUrl() ?>"<?php echo $onclick ?>><?php echo $name ?></a></div>
            <?php if (isset($context->unlHROrgUnitNumber)): ?>
                <?php foreach ($context->unlHROrgUnitNumber as $orgUnit): ?>
                    <?php if ($name = UNL_Officefinder_Department::getNameByOrgUnit($orgUnit)): ?>
                        <div class="organization-unit"><?php echo $name ?></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php if ($title): ?>
            <div class="title"><?php echo $title ?></div>
        <?php endif; ?>

        <?php if (!empty($context->telephoneNumber)): ?>
            <div class="tel"><?php echo $savvy->render($context->telephoneNumber, 'Peoplefinder/Record/TelephoneNumber.tpl.php') ?></div>
        <?php endif; ?>

        </div>
    <?php if (isset($controller->options['chooser'])): ?>
        <div class="pfchooser"><a href="#" onclick="return pfCatchUID('<?php echo $context->uid ?>');">Choose this person</a></div>
    <?php endif; ?>
    </div>
</li>
