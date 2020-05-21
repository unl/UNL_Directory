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
<div class="departmentInfo"<?php if ($onlySummary): ?> itemscope itemtype="https://schema.org/Organization"<?php endif; ?>>
    <div class="vcard office<?php if($onlySummary): ?> card<?php endif; ?>" data-listing-id="<?php echo $context->id ?> dcf-measure" data-preferred-name="<?php echo $context->name ?>">
        <div class="dcf-pt-6 dcf-pb-6">
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
                     <div class="title dcf-mt-3 dcf-txt-sm" itemprop="parentOrganization" itemscope itemtype="https://schema.org/Organization"><a href="<?php echo $parent->getURL() ?>"><span itemprop="name"><?php echo $parent->name ?></span></a></div>
                <?php endif; ?>
                </div>

                <?php if ($context->hasAddress()): ?>
                    <div class="adr work attribute dcf-txt-sm" itemprop="location" itemscope itemtype="https://schema.org/Place">
                        <span class="icon-map-pin" aria-hidden="true"></span>
                        <span class="type">Address</span>
                        <?php if ($context->building): ?>
                            <span class="room">
                                <a href="https://maps.unl.edu/<?php echo $context->building ?>" itemprop="hasMap"><?php echo $context->building ?></a>
                                <?php echo $context->room ?>
                            </span>
                        <?php endif; ?>
                        <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
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

                <?php if ($onlySummary): ?>
                <div class="dcf-d-flex dcf-jc-center dcf-txt-sm dcf-pt-3 dcf-pb-3 dcf-d-none@print">
                    <a class="dcf-btn dcf-btn-secondary dcf-b-0" href="<?php echo $context->getURL() ?>">View Full Department</a>
                </div>
                <?php endif; ?>
            </div>
            <?php if (!$onlySummary): ?>
                <?php if ($userCanEdit): ?>
                    <div class="vcard-tools dcf-d-none@print dcf-mt-4">
                        <a class="edit-button dcf-btn dcf-btn-primary" href="<?php echo $context->getURL() . '/edit' ?>">
                            <svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current dcf-txt-text-top" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
                                <path d="M22.94 2.732l-1.672-1.671a2.502 2.502 0 00-3.535 0L2.647 16.146a.595.595 0 00-.125.207l-2 6.5a.5.5 0 00.625.625c.193-.059 6.326-1.944 6.516-2.01a.493.493 0 00.19-.115L22.94 6.268c.471-.471.73-1.098.73-1.768a2.49 2.49 0 00-.73-1.768zM19.5 8.293L15.707 4.5l.793-.793L20.293 7.5l-.793.793zM4.2 21.492l-1.692-1.691.726-2.36.413.413A.5.5 0 004 18h2v2a.5.5 0 00.147.354l.413.413-2.36.725zM6.293 17H4.207l-.5-.5L15 5.207l1.543 1.543L6.293 17zm-4.118 3.882l.943.943-1.362.419.419-1.362zm5.325-.589l-.5-.5v-2.086l10.25-10.25L18.793 9 7.5 20.293zM22.232 5.561L21 6.793 17.207 3l1.232-1.232a1.503 1.503 0 012.121 0l1.672 1.671c.282.282.438.659.438 1.061 0 .403-.155.779-.438 1.061z"></path>
                            </svg>
                            Edit
                        </a>
                        <?php if ($userCanDelete): ?>
                            <button class="dcf-btn dcf-btn-primary dcf-mt-1" type="submit" form="deletedepartment_<?php echo $context->id ?>">
                                <svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current dcf-txt-text-top" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
                                    <path d="M23 3h-7V.5a.5.5 0 00-.5-.5h-8a.5.5 0 00-.5.5V3H1a.5.5 0 000 1h2v19.5a.5.5 0 00.5.5h16a.5.5 0 00.5-.5V4h3a.5.5 0 000-1zM8 1h7v2H8V1zm11 22H4V4h15v19z"></path>
                                    <path d="M7.5 6.5A.5.5 0 007 7v12a.5.5 0 001 0V7a.5.5 0 00-.5-.5zm4 0a.5.5 0 00-.5.5v12a.5.5 0 001 0V7a.5.5 0 00-.5-.5zM15 7v12a.5.5 0 001 0V7a.5.5 0 00-1 0z"></path>
                                </svg>
                                Delete
                            </button>
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
</div>
