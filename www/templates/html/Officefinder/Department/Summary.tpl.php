<?php
$userCanEdit = $userCanDelete = false;

if ($controller->options['view'] != 'alphalisting') {
    $userCanEdit = $context->userCanEdit(UNL_Officefinder::getUser());
	// Only allow Admins to delete "official" SAP departments
    $userCanDelete = empty($context->org_unit) || UNL_Officefinder::isAdmin(UNL_Officefinder::getUser());
}

$officialParent = $context->getOfficialParent();

$encodedEmail = '';
if (!empty($context->email)) {
    // attempt to curb lazy email harvesting bots
    $encodedEmail = htmlentities($context->getRaw('email'), ENT_QUOTES | ENT_HTML5);
}

$onlySummary = $context->isSummaryView();
?>
<div class="departmentInfo">
    <div class="vcard office<?php if($onlySummary): ?> card<?php endif; ?>">
        <div class="card-profile">
            <img alt="Building Image" src="<?php echo $context->getImageURL(UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_LARGE); ?>" class="photo" />
        </div>
        <div class="vcardInfo<?php if($onlySummary): ?> card-content<?php endif; ?>">
            <?php if (!$onlySummary): ?>
                <h1 class="headline">
            <?php else: ?>
                <div class="headline">
            <?php endif; ?>
                <a class="permalink" href="<?php echo $context->getURL() ?>">
                    <span class="fn org"><?php echo $context->name ?></span>
                    <span class="icon-link"></span>
                </a>
            <?php if ($onlySummary): ?>
                </div>
            <?php else: ?>
                </h1>
            <?php endif; ?>

            <?php if (!$context->isOfficialDepartment()): ?>
                 <div class="title"><?php echo $officialParent->name ?></div>
            <?php endif; ?>

            <?php if ($context->hasAddress()): ?>
                <div class="adr work itemprop icon-map-pin">
                    <span class="type">Address</span>
                    <?php if ($context->building): ?>
                        <span class="room">
                            <a href="https://maps.unl.edu/<?php echo $context->building ?>"><?php echo $context->building ?></a>
                            <?php echo $context->room ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($context->address)): ?>
                        <span class="street-address"><?php echo $context->address ?></span>
                    <?php endif; ?>
                    <?php if (!empty($context->city)): ?>
                        <span class='locality'><?php echo $context->city ?></span>
                    <?php endif; ?>
                    <?php if (!empty($context->state)): ?>
                        <?php echo $savvy->render($context->state, 'Peoplefinder/Record/Region.tpl.php') ?>
                    <?php endif; ?>
                    <?php if (!empty($context->postal_code)): ?>
                        <span class='postal-code'><?php echo $context->postal_code ?></span>
                    <?php endif; ?>
                    <div class="country-name">USA</div>
                </div>
            <?php endif; ?>

            <?php if (!empty($context->phone)): ?>
            <div class="tel work icon-phone itemprop">
                <span class="voice">
                    <span class="type">Phone:</span>
                    <?php echo $savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php') ?>
                </span>
            </div>
            <?php endif; ?>
            <?php if (!empty($context->fax)): ?>
            <div class="tel work icon-print itemprop">
                <span class="fax">
                    <span class="type">Fax:</span>
                    <?php echo $savvy->render($context->fax, 'Peoplefinder/Record/TelephoneNumber.tpl.php'); ?>
                </span>
            </div>
            <?php endif; ?>
            <?php if ($encodedEmail): ?>
            <div class="icon-email itemprop">
                   <a class="email" href="mailto:<?php echo $encodedEmail ?>" itemprop="email"><?php echo $encodedEmail ?></a>
            </div>
            <?php endif; ?>

            <?php if (!empty($context->website)): ?>
            <div class="icon-website itemprop">
                <a class="url" href="<?php echo $context->website; ?>"><?php echo $context->website; ?></a>
            </div>
            <?php endif; ?>

            <?php if ($context->isOfficialDepartment()): ?>
                <div class="icon-hierarchy itemprop">
                    <?php echo $context->org_unit ?>
                </div>
            <?php endif; ?>

            <?php if ($userCanEdit): ?>
                <div class="vcard-tools">
                    <a href="<?php echo $context->getURL() . '/edit' ?>" class="icon-pencil">Edit</a>
                    <?php if ($userCanDelete): ?>
                        <button class="icon-trash">Delete</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
