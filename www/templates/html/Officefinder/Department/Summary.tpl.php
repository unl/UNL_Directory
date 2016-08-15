<?php
$userCanEdit = $userCanDelete = false;

if ($controller->options['view'] != 'alphalisting') {
    $userCanEdit = $context->userCanEdit(UNL_Officefinder::getUser());
	// Only allow Admins to delete "official" SAP departments
    $userCanDelete = empty($context->org_unit) || UNL_Officefinder::isAdmin(UNL_Officefinder::getUser());
}

$parent = $context->getParent();

$encodedEmail = '';
if (!empty($context->email)) {
    // attempt to curb lazy email harvesting bots
    $encodedEmail = htmlentities($context->getRaw('email'), ENT_QUOTES | ENT_HTML5);
}

$onlySummary = $context->isSummaryView();
?>
<div class="departmentInfo"<?php if ($onlySummary): ?> itemscope itemtype="http://schema.org/Organization"<?php endif; ?>>
    <div class="vcard office<?php if($onlySummary): ?> card<?php endif; ?>" tabindex="-1">
        <div class="card-profile">
            <img alt="Building Image" itemprop="image" src="<?php echo $context->getImageURL(UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_LARGE); ?>" class="photo" />
        </div>
        <div class="vcardInfo<?php if($onlySummary): ?> card-content<?php endif; ?>">
            <?php if (!$onlySummary): ?>
                <h1 class="headline">
            <?php else: ?>
                <div class="headline">
            <?php endif; ?>
                <a class="permalink" href="<?php echo $context->getURL() ?>" itemprop="url">
                    <span class="fn org" itemprop="name"><?php echo $context->name ?></span>
                    <span class="icon-link"></span>
                </a>
            <?php if ($onlySummary): ?>
                </div>
            <?php else: ?>
                </h1>
            <?php endif; ?>

            <?php if (!$context->isOfficialDepartment()): ?>
                 <div class="title" itemprop="parentOrganization" itemscope itemtype="http://schema.org/Organization"><a href="<?php echo $parent->getURL() ?>"><span itemprop="name"><?php echo $parent->name ?></span></a></div>
            <?php endif; ?>

            <?php if ($context->hasAddress()): ?>
                <div class="adr work attribute icon-map-pin" itemprop="location" itemscope itemtype="http://schema.org/Place">
                    <span class="type">Address</span>
                    <?php if ($context->building): ?>
                        <span class="room">
                            <a href="https://maps.unl.edu/<?php echo $context->building ?>" itemprop="hasMap"><?php echo $context->building ?></a>
                            <?php echo $context->room ?>
                        </span>
                    <?php endif; ?>
                    <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                        <?php if (!empty($context->address)): ?>
                            <span class="street-address" itemprop="streetAddress"><?php echo $context->address ?></span>
                        <?php endif; ?>
                        <?php if (!empty($context->city)): ?>
                            <span class="locality" itemprop="addressLocality"><?php echo $context->city ?></span>
                        <?php endif; ?>
                        <?php if (!empty($context->state)): ?>
                            <?php echo $savvy->render($context->state, 'Peoplefinder/Record/Region.tpl.php') ?>
                        <?php endif; ?>
                        <?php if (!empty($context->postal_code)): ?>
                            <span class="postal-code" itemprop="postalCode"><?php echo $context->postal_code ?></span>
                        <?php endif; ?>
                        <div class="country-name" itemprop="addressCountry">US</div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($context->phone)): ?>
            <div class="tel work icon-phone attribute">
                <span class="type">Phone:</span>
                <span class="value"><?php echo $savvy->render((object) [
                    'number' => $context->phone,
                    'itemprop' => 'telephone',
                ], 'Peoplefinder/Record/NumberItemprop.tpl.php') ?></span>
                <?php echo $savvy->render($context->phone, 'Peoplefinder/Record/CampusNumber.tpl.php') ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($context->fax)): ?>
            <div class="tel work fax icon-print attribute">
                <span class="type">Fax:</span>
                <span class="value"><?php echo $savvy->render((object) [
                    'number' => $context->fax,
                    'itemprop' => 'faxNumber',
                ], 'Peoplefinder/Record/NumberItemprop.tpl.php') ?></span>
                <?php echo $savvy->render($context->phone, 'Peoplefinder/Record/CampusNumber.tpl.php') ?>
            </div>
            <?php endif; ?>
            <?php if ($encodedEmail): ?>
            <div class="icon-email attribute">
                   <a class="email" href="mailto:<?php echo $encodedEmail ?>" itemprop="email"><?php echo $encodedEmail ?></a>
            </div>
            <?php endif; ?>

            <?php if (!empty($context->website)): ?>
            <div class="icon-website attribute">
                <a class="url" href="<?php echo $context->website; ?>"><?php echo $context->website; ?></a>
            </div>
            <?php endif; ?>

            <?php if ($context->isOfficialDepartment()): ?>
                <div class="icon-hierarchy attribute">
                    Unit #<?php echo $context->org_unit ?>
                </div>
            <?php endif; ?>

            <?php if (!$onlySummary && $userCanEdit): ?>
                <div class="vcard-tools">
                    <a href="<?php echo $context->getURL() . '/edit' ?>" class="icon-pencil">Edit</a>
                    <?php if ($userCanDelete): ?>
                        <button type="submit" class="icon-trash" form="deletedepartment_<?php echo $context->id ?>">Delete</button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="department-correction"></div>
            <?php endif; ?>
        </div>
    </div>
</div>
