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
        $name .= ' <span class="given-name">'.$context->givenName.'</span>';
    }
}
$class .= ' '.$context->eduPersonPrimaryAffiliation;

$onclick = '';
if (isset($controller->options, $controller->options['onclick'])) {
    $onclick .= ' onclick="return ' . $controller->options['onclick'] . "('" . addslashes($context->uid) . '\');"';
}

$title = $context->formatTitle();
?>

<li class="<?php echo $class ?>" data-href="<?php echo $context->getUrl() ?>" data-uid="<?php echo $context->uid ?>">
    <div class="overflow dcf-d-flex" itemscope itemtype="http://schema.org/Person">
        <?php if ($controller->options['view'] != 'alphalisting'): ?>
            <div class="profile_pic dcf-flex-shrink-0 dcf-mr-4 dcf-h-9 dcf-w-9 dcf-ratio dcf-ratio-1x1">
                <img class="photo dcf-ratio-child dcf-circle dcf-d-block dcf-obj-fit-cover" itemprop="image" src="<?php echo $context->getImageUrl(UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_LARGE) ?>" alt="Avatar for <?php echo $context->displayName ?>" />
            </div>
        <?php endif; ?>

        <div class="recordDetails unl-font-sans">
            <div class="fn dcf-txt-lg dcf-bold unl-lh-crop" itemprop="name">
              <a class="dcf-txt-decor-hover" itemprop="url" href="<?php echo $context->getUrl() ?>"<?php echo $onclick ?> aria-label="Show more information about <?php echo $name ?>"><?php echo $name ?></a>
            </div>
            <?php if (isset($context->unlHROrgUnitNumber)): ?>
                <?php
                $roles = $context->getRoles();
                $roles->enableRenderLinks(false);
                $title = $context->formatTitle();
                ?>
                <?php if (count($roles)): ?>
                    <?php echo $savvy->render($roles) ?>
                <?php elseif ($title): ?>
                    <div class="title dcf-txt-sm" itemprop="jobTitle"><?php echo $title ?></div>
                <?php endif; ?>
            <?php endif; ?>

        <?php if (!empty($context->telephoneNumber)): ?>
            <div class="tel dcf-txt-sm"><?php echo $savvy->render($context->telephoneNumber, 'Peoplefinder/Record/TelephoneNumber.tpl.php') ?></div>
        <?php endif; ?>

        </div>
    <?php if (isset($controller->options['chooser'])): ?>
        <div class="pfchooser"><a href="#" onclick="return pfCatchUID('<?php echo $context->uid ?>');">Choose this person</a></div>
    <?php endif; ?>
    </div>
</li>
