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
    <div class="vcard office<?php if($onlySummary): ?> card<?php endif; ?>" data-listing-id="<?php echo $context->id ?> dcf-measure" data-preferred-name="<?php echo $context->name ?>">
        <div class="card-profile dcf-mb-3 dcf-h-10 dcf-w-10 dcf-ratio dcf-ratio-1x1">
            <img class="photo dcf-ratio-child dcf-d-block dcf-obj-fit-cover dcf-circle" itemprop="image" src="<?php echo $context->getImageURL(UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_LARGE); ?>" alt="Building Image" />
        </div>
        <div class="vcardInfo<?php if($onlySummary): ?> card-content<?php endif; ?> unl-font-sans">
            <div class="dcf-mb-5">
            <?php if (!$onlySummary): ?>
                <h1 class="headline dcf-txt-h3 dcf-mb-0">
            <?php else: ?>
                <div class="headline dcf-txt-h3 dcf-bold dcf-lh-2">
            <?php endif; ?>
                <a class="permalink dcf-txt-decor-hover" href="<?php echo $context->getURL() ?>" itemprop="url">
                    <span class="fn org" itemprop="name"><?php echo $context->name ?></span>
                </a>
            <?php if ($onlySummary): ?>
                </div>
            <?php else: ?>
                </h1>
            <?php endif; ?>

            <?php if (!$context->isOfficialDepartment()): ?>
                 <div class="title dcf-mt-3 dcf-txt-sm" itemprop="parentOrganization" itemscope itemtype="http://schema.org/Organization"><a href="<?php echo $parent->getURL() ?>"><span itemprop="name"><?php echo $parent->name ?></span></a></div>
            <?php endif; ?>
            </div>

            <?php if ($context->hasAddress()): ?>
                <div class="adr work attribute dcf-txt-sm" itemprop="location" itemscope itemtype="http://schema.org/Place">
                    <span class="icon-map-pin" aria-hidden="true"></span>
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
            <div class="tel work attribute dcf-txt-sm">
                <span class="icon-phone" aria-hidden="true"></span>
                <span class="type">Phone:</span>
                <span class="value dcf-mr-1"><?php echo $savvy->render((object) [
                    'number' => $context->phone,
                    'itemprop' => 'telephone',
                ], 'Peoplefinder/Record/NumberItemprop.tpl.php') ?></span>
                <?php echo $savvy->render($context->phone, 'Peoplefinder/Record/CampusNumber.tpl.php') ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($context->fax)): ?>
            <div class="tel work fax attribute dcf-txt-sm">
                <span class="icon-print" aria-hidden="true"></span>
                <span class="type">Fax:</span>
                <span class="value dcf-mr-1"><?php echo $savvy->render((object) [
                    'number' => $context->fax,
                    'itemprop' => 'faxNumber',
                ], 'Peoplefinder/Record/NumberItemprop.tpl.php') ?></span>
                <?php echo $savvy->render($context->fax, 'Peoplefinder/Record/CampusNumber.tpl.php') ?>
            </div>
            <?php endif; ?>
            <?php if ($encodedEmail): ?>
            <div class="attribute dcf-txt-sm">
                   <span class="icon-email" aria-hidden="true"></span>
                   <a class="email" href="mailto:<?php echo $encodedEmail ?>" itemprop="email"><?php echo $encodedEmail ?></a>
            </div>
            <?php endif; ?>

            <?php if (!empty($context->website)): ?>
            <div class="attribute dcf-txt-sm">
                <span class="icon-website" aria-hidden="true"></span>
                <a class="url" href="<?php echo $context->website; ?>"><?php echo $context->website; ?></a>
            </div>
            <?php endif; ?>

            <?php if ($context->isOfficialDepartment()): ?>
                <div class="attribute dcf-txt-sm">
                    <span class="icon-hierarchy" aria-hidden="true"></span>
                    Unit #<?php echo $context->org_unit ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!$onlySummary): ?>
            <?php if ($userCanEdit): ?>
                <div class="vcard-tools dcf-d-none@print">
                    <a href="<?php echo $context->getURL() . '/edit' ?>" class="icon-pencil edit-button">Edit</a>
                    <?php if ($userCanDelete): ?>
                        <button type="submit" form="deletedepartment_<?php echo $context->id ?>"><span class="icon-trash" aria-hidden="true"></span>Delete</button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="department-correction dcf-mt-5 dcf-txt-2xs dcf-italic unl-dark-gray">
                    <?php echo $savvy->render($context->getEditors(), 'Officefinder/Department/UsersOrganizations.tpl.php') ?>
                </div>
            <?php endif; ?>
        <?php elseif (!$userCanEdit): ?>
            <div class="department-correction dcf-mt-5 dcf-txt-2xs dcf-italic unl-dark-gray"></div>
        <?php endif; ?>
    </div>
</div>
