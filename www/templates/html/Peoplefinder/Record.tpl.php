<?php

$preferredFirstName = $context->getPreferredFirstName();
$preferredName = $preferredFirstName . ' ' . $context->sn;

$isOrg = $context->ou == 'org';
$itemtype = 'Person';
if ($isOrg) {
    $itemtype = 'Organization';
}

if (isset($page)) {
    // inject a prefix into the document title
    $page = $page->getRawObject();
    $page->doctitle = substr_replace($page->doctitle, $preferredName . ' | ', strlen('<title>'), 0);
}

$displayEmail = false;
$encodedEmail = '';
if (isset($context->mail) && !$context->isPrimarilyStudent()) {
    $displayEmail = true;
    // attempt to curb lazy email harvesting bots
    $encodedEmail = htmlentities($context->getRaw('mail'), ENT_QUOTES | ENT_HTML5);
}

// check if should display knowledge and that it contains content
$hasKnowledge = !empty(trim($savvy->render($context->getKnowledge())));
$showKnowledge = $context->shouldShowKnowledge() === TRUE && $hasKnowledge === TRUE;
?>
<?php if ($showKnowledge): ?>
<section class="dcf-grid dcf-col-gap-vw">
    <div class="dcf-col-100% dcf-col-25%-start@md directory-knowledge-summary">
<?php endif; ?>


<div class="vcard <?php if (!$showKnowledge): ?>card <?php endif; ?><?php echo $context->eduPersonPrimaryAffiliation ?> dcf-measure" data-uid="<?php echo $context->uid ?>" data-preferred-name="<?php echo $preferredName ?>" itemscope itemtype="https://schema.org/<?php echo $itemtype ?>">
    <div class="dcf-pt-6 dcf-pb-6">
        <a class="card-profile planetred_profile dcf-d-block dcf-mb-3 dcf-h-10 dcf-w-10 dcf-ratio dcf-ratio-1x1" href="<?php echo $context->getProfileUrl() ?>" aria-label="Planet Red profile for <?php echo $preferredName ?>" itemprop="url">
            <img class="photo profile_pic dcf-ratio-child dcf-circle dcf-d-block dcf-obj-fit-cover" itemprop="image" src="<?php echo $context->getImageURL(UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_LARGE) ?>" alt="Avatar for <?php echo $preferredName ?>" />
        </a>

        <div class="vcardInfo<?php if (!$showKnowledge): ?> card-content<?php endif; ?> unl-font-sans">
        <?php if (!$context->isHcardFormat()): ?>
            <h1 class="headline dcf-mb-0 dcf-txt-h2">
        <?php else: ?>
            <div class="headline dcf-txt-h2 dcf-bold dcf-lh-2">
        <?php endif; ?>
            <?php if (!$isOrg): ?>
                <a class="permalink dcf-txt-decor-hover" href="<?php echo $context->getUrl() ?>" itemprop="url">
            <?php endif; ?>
            <?php if ($isOrg): ?>
                <span class="cn" itemprop="name"><?php echo $context->cn ?></span>
            <?php else: ?>
                <span class="fn" itemprop="name"><?php echo $preferredName ?></span>
                <?php if ($context->hasNickname()): ?>
                <span class="n">
                    <span class="given-name" itemprop="givenName"><?php echo $context->givenName ?></span>
                </span>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!$isOrg): ?>
                </a>
            <?php endif; ?>
        <?php if ($context->isHcardFormat()): ?>
            </div>
        <?php else: ?>
            </h1>
        <?php endif; ?>

        <?php
        $affiliations = $context->formatAffiliations();
        ?>
        <?php if ($affiliations): ?>
            <div class="eppa dcf-mt-3 dcf-mb-3 dcf-txt-2xs dcf-uppercase unl-ls-1 unl-dark-gray"><?php echo $affiliations ?></div>
        <?php endif; ?>

        <?php if ($context->affiliationMightIncludeAppointments()): ?>
            <?php
            $roles = $context->getRoles();
            $title = $context->formatTitle();
            ?>
            <?php if (count($roles)): ?>
                <?php echo $savvy->render($roles) ?>
            <?php elseif ($title): ?>
                <div class="title dcf-txt-sm" itemprop="jobTitle"><?php echo $title ?></div>
            <?php endif; ?>
        <?php endif ?>

        <?php if ($context->hasStudentInformation()): ?>
            <div class="sis-title dcf-txt-sm dcf-mt-5">
            <?php if (isset($context->unlSISClassLevel)): ?>
              <div class="grade"><?php echo $context->formatClassLevel() ?></div>
            <?php endif; ?>
            <?php if (isset($context->unlSISMajor)): ?>
                <?php foreach ($context->getRawObject()->unlSISMajor as $major): ?>
                  <div class="major"><?php echo $context->formatMajor($major) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (isset($context->unlSISMinor)): ?>
                <?php foreach ($context->getRawObject()->unlSISMinor as $minor): ?>
                  <div class="minor"><?php echo $context->formatMajor($minor) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
                <?php foreach ($context->getRawObject()->unlSISCollege as $college): ?>
                    <?php
                    $college = $context->formatCollege($college);
                    ?>
                    <?php if (is_string($college)): ?>
                      <div class="icon-academic-cap college"><?php echo $college ?></div>
                    <?php else: ?>
                        <div class="icon-academic-cap college">
                            <?php if (isset($college['link'])): ?>
                                <a href="<?php echo $college['link'] ?>">
                            <?php endif; ?>
                            <abbr title="<?php echo $college['title'] ?>"><?php echo $college['abbr'] ?></abbr>
                            <?php if (isset($college['org_unit_number'])): ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (($address = $context->formatPostalAddress()) && count($address)): ?>
            <div class="adr work attribute dcf-txt-sm" itemprop="workLocation" itemscope itemtype="https://schema.org/Place">
                <span class="icon-map-pin" aria-hidden="true"></span>
                <span class="type">Work</span>
                <?php if (!empty($address['unlBuildingCode'])): ?>
                    <span class="street-address">
                        <?php if (isset($address['unlBuildingName'])) : ?>
                            <a href="https://maps.unl.edu/<?php echo strtoupper($address['unlBuildingCode']) ?>" itemprop="hasMap"><?php echo $address['unlBuildingName'] ?></a> (<?php echo $address['unlBuildingCode'] ?>)
                        <?php else: ?>
                            <a href="https://maps.unl.edu/<?php echo strtoupper($address['unlBuildingCode']) ?>" itemprop="hasMap"><?php echo $address['unlBuildingCode'] ?></a>
                        <?php endif; ?>

                        <?php echo str_replace($address['unlBuildingCode'], '', $address['street-address']) ?>
                    </span>
                <?php endif; ?>
                <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                    <?php if (empty($address['unlBuildingCode'])): ?>
                        <span class="street-address" itemprop="streetAddress"><?php echo $address['street-address'] ?></span>
                    <?php endif; ?>
                    <span class="locality" itemprop="addressLocality"><?php echo $address['locality'] ?></span>
                    <?php echo $savvy->render($address['region'], 'Peoplefinder/Record/Region.tpl.php') ?>
                    <span class="postal-code" itemprop="postalCode"><?php echo $address['postal-code'] ?></span>
                    <div class="country-name" itemprop="addressCountry">US</div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($context->telephoneNumber)): ?>
            <div class="tel work attribute dcf-txt-sm">
                <span class="icon-phone" aria-hidden="true"></span>
                <span class="type">Work</span>
                <span class="value dcf-mr-1"><?php echo $savvy->render((object) [
                    'number' => $context->telephoneNumber,
                    'itemprop' => 'telephone',
                ], 'Peoplefinder/Record/NumberItemprop.tpl.php') ?></span>
                <?php echo $savvy->render($context->telephoneNumber, 'Peoplefinder/Record/CampusNumber.tpl.php') ?>
            </div>
        <?php endif; ?>

        <?php if (isset($context->unlSISLocalPhone)): ?>
            <div class="tel home dcf-txt-sm">
                <span class="type">Phone</span>
                <span class="value dcf-mr-1"><?php echo $savvy->render((object) [
                    'number' => $context->unlSISLocalPhone,
                    'itemprop' => 'telephone',
                ], 'Peoplefinder/Record/NumberItemprop.tpl.php') ?></span>
                <?php echo $savvy->render($context->unlSISLocalPhone, 'Peoplefinder/Record/CampusNumber.tpl.php') ?>
            </div>
        <?php endif; ?>

        <?php if ($displayEmail): ?>
            <div class="attribute dcf-txt-sm">
                <span class="icon-email" aria-hidden="true"></span>
                <a class="email" href="mailto:<?php echo $encodedEmail ?>" itemprop="email"> <?php echo $encodedEmail ?></a>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['allow-knowledge']) && $context->isHcardFormat() && $hasKnowledge === TRUE && !$showKnowledge) :?>
            <div class="dcf-mt-5 dcf-txt-xs dcf-bt-1 dcf-bt-solid unl-bt-light-gray">
                <?php echo $savvy->render($context->getKnowledge()) ?>
            </div>
        <?php endif; ?>

        </div>

        <div class="vcard-tools wdn_vcardTools primary dcf-d-flex dcf-flex-row dcf-flex-wrap dcf-ai-start dcf-jc-around dcf-txt-sm dcf-mt-5 dcf-pt-3 dcf-pb-3 dcf-bt-1 dcf-bt-solid unl-bt-light-gray dcf-d-none@print">
            <a class="dcf-btn dcf-btn-secondary dcf-b-0 dcf-d-flex dcf-flex-col dcf-ai-center" href="<?php echo $context->getVcardUrl() ?>">
                <svg class="dcf-mb-2 dcf-h-7 dcf-w-7 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
                    <path d="M23.5 3H.5a.5.5 0 00-.5.5v17a.5.5 0 00.5.5h4a.5.5 0 00.5-.5V19c0-.689.561-1.25 1.25-1.25S7.5 18.311 7.5 19v1.5a.5.5 0 00.5.5h8a.5.5 0 00.5-.5V19c0-.689.561-1.25 1.25-1.25S19 18.311 19 19v1.5a.5.5 0 00.5.5h4a.5.5 0 00.5-.5v-17a.5.5 0 00-.5-.5zM23 20h-3v-1c0-1.241-1.009-2.25-2.25-2.25S15.5 17.759 15.5 19v1h-7v-1c0-1.241-1.009-2.25-2.25-2.25S4 17.759 4 19v1H1V4h22v16z"></path>
                    <path d="M13 13h8v1h-8zm0-2h8v1h-8zm0-2h8v1h-8zm0-2h4v1h-4zM2.5 15h9a.5.5 0 00.5-.5v-2a.504.504 0 00-.146-.354c-.062-.062-1.43-1.409-3.354-1.619v-.264c.376-.298 1-.986 1-2.263 0-.996 0-2.5-2.5-2.5S4.5 7.004 4.5 8c0 1.277.624 1.965 1 2.263v.264c-1.923.21-3.292 1.557-3.354 1.619A.504.504 0 002 12.5v2a.5.5 0 00.5.5zm.5-2.279c.397-.341 1.563-1.221 3-1.221a.5.5 0 00.5-.5v-1a.51.51 0 00-.27-.443C6.2 9.54 5.5 9.149 5.5 8c0-.998 0-1.5 1.5-1.5s1.5.502 1.5 1.5c0 1.149-.7 1.54-.724 1.553A.5.5 0 007.5 10v1a.5.5 0 00.5.5c1.427 0 2.6.881 3 1.222V14H3v-1.279z"></path>
                </svg>
                <span class="dcf-sr-only">Download </span>vCard<span class="dcf-sr-only"> for <?php echo $preferredName ?></span>
            </a>
            <a class="dir-btn-qr-code-vcard dcf-btn dcf-btn-secondary dcf-b-0 dcf-d-flex dcf-flex-col dcf-ai-center" href="<?php echo $context->getQRCodeUrl($savvy->render($context, 'templates/vcard/Peoplefinder/Record.tpl.php')) ?>">
                <svg class="dcf-mb-2 dcf-h-7 dcf-w-7 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
                    <path d="M7.505 0h-7a.5.5 0 00-.5.5v7a.5.5 0 00.5.5h7a.5.5 0 00.5-.5v-7a.5.5 0 00-.5-.5zm-.5 7h-6V1h6v6z"></path>
                    <path d="M2.505 6h3a.5.5 0 00.5-.5v-3a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5v3a.5.5 0 00.5.5zm.5-3h2v2h-2V3zm4.5 13h-7a.5.5 0 00-.5.5v7a.5.5 0 00.5.5h7a.5.5 0 00.5-.5v-7a.5.5 0 00-.5-.5zm-.5 7h-6v-6h6v6z"></path>
                    <path d="M2.505 22h3a.5.5 0 00.5-.5v-3a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5v3a.5.5 0 00.5.5zm.5-3h2v2h-2v-2zm17-2.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5v3a.5.5 0 00.5.5h3a.5.5 0 00.5-.5v-3zm-1 2.5h-2v-2h2v2zm4.5-19h-7a.5.5 0 00-.5.5v7a.5.5 0 00.5.5h7a.5.5 0 00.5-.5v-7a.5.5 0 00-.5-.5zm-.5 7h-6V1h6v6z"></path>
                    <path d="M18.505 6h3a.5.5 0 00.5-.5v-3a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5v3a.5.5 0 00.5.5zm.5-3h2v2h-2V3zm-9.5 7h3.5a.5.5 0 000-1h-3.5a.5.5 0 000 1zm0 5h3.5a.5.5 0 000-1h-3v-2.5a.5.5 0 00-1 0v3a.5.5 0 00.5.5zm-2-6h-7a.5.5 0 000 1h6.5v1.5a.5.5 0 001 0v-2a.5.5 0 00-.5-.5zm16 0H22.5a.5.5 0 00-.5.5v5a.5.5 0 001 0V10h.505a.5.5 0 000-1zM20.5 15a.5.5 0 00.5-.5v-3a.5.5 0 00-1 0v3a.5.5 0 00.5.5zM15 11.5a.5.5 0 001 0V10h4.005a.5.5 0 000-1H15.5a.5.5 0 00-.5.5v2zM12.505 4a.5.5 0 00-.5.5v2a.5.5 0 00.5.5h2a.5.5 0 00.5-.5v-4a.5.5 0 00-1 0V6h-1V4.5a.5.5 0 00-.5-.5zm-3 4a.5.5 0 00.5-.5v-3a.5.5 0 00-1 0v3a.5.5 0 00.5.5zm2-7h3a.5.5 0 000-1h-3a.5.5 0 000 1zm-2 2h2a.5.5 0 000-1h-1.5V.5a.5.5 0 00-1 0v2a.5.5 0 00.5.5zm-4 10a.5.5 0 000-1h-3a.5.5 0 000 1h3zm-5 2h2a.5.5 0 000-1h-1.5v-2.5a.5.5 0 00-1 0v3a.5.5 0 00.5.5zm23 1h-2a.5.5 0 00-.5.5V23h-8.5a.5.5 0 000 1h9a.5.5 0 00.5-.5V17h1v2.5a.5.5 0 001 0v-3a.5.5 0 00-.5-.5zm-10.5 5.5a.5.5 0 00-.5-.5h-2.5v-3.5a.5.5 0 00-1 0v4a.5.5 0 00.5.5h3a.5.5 0 00.5-.5z"></path>
                    <path d="M14.505 22h5a.5.5 0 000-1h-4.5v-2a.5.5 0 00-1 0v2.5a.5.5 0 00.5.5zM11 16.5v3a.5.5 0 001 0V17h2.505a.5.5 0 000-1H11.5a.5.5 0 00-.5.5zM7.505 14h-2a.5.5 0 000 1h2a.5.5 0 000-1zm5.5-3h-2a.5.5 0 000 1h2a.5.5 0 000-1zm4.495 1a.5.5 0 00-.5.5V14h-1.5a.5.5 0 000 1h3a.5.5 0 000-1H18v-1.5a.5.5 0 00-.5-.5z"></path>
                </svg>
                QR Code<span class="dcf-sr-only"> vCard for <?php echo $preferredName ?></span>
            </a>
            <button class="dir-btn-print-vcard dcf-btn dcf-btn-secondary dcf-b-0 dcf-d-flex dcf-flex-col dcf-ai-center">
                <svg class="dcf-mb-2 dcf-h-7 dcf-w-7 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
                    <path d="M21.5 7h-19A2.503 2.503 0 000 9.5v6C0 16.878 1.122 18 2.5 18H5v4.5a.5.5 0 00.5.5h13a.5.5 0 00.5-.5V18h2.5c1.378 0 2.5-1.122 2.5-2.5v-6C24 8.122 22.878 7 21.5 7zM18 22H6v-7h12v7zm5-6.5c0 .827-.673 1.5-1.5 1.5H19v-2.5a.5.5 0 00-.5-.5h-13a.5.5 0 00-.5.5V17H2.5c-.827 0-1.5-.673-1.5-1.5v-6C1 8.673 1.673 8 2.5 8h19c.827 0 1.5.673 1.5 1.5v6zM5.5 6a.5.5 0 00.5-.5V2h9v2.5a.5.5 0 00.5.5H18v.5a.5.5 0 001 0v-1a.499.499 0 00-.147-.354l-2.999-2.999A.51.51 0 0015.5 1h-10a.5.5 0 00-.5.5v4a.5.5 0 00.5.5zM16 2.707L17.293 4H16V2.707z"></path>
                    <path d="M3.5 9C2.673 9 2 9.673 2 10.5S2.673 12 3.5 12 5 11.327 5 10.5 4.327 9 3.5 9zm0 2a.5.5 0 110-1 .5.5 0 010 1zm13 5h-9a.5.5 0 000 1h9a.5.5 0 000-1zm0 2h-9a.5.5 0 000 1h9a.5.5 0 000-1zm0 2h-9a.5.5 0 000 1h9a.5.5 0 000-1z"></path>
                </svg>
                Print<span class="dcf-sr-only"> listing for <?php echo $preferredName ?></span>
            </button>
        </div>
    </div>
</div>

<?php if ($showKnowledge): ?>
    </div>
    <div class="dcf-col-100% dcf-col-75%-end@md">
        <div class="card">
            <div class="card-content">
                <?php echo $savvy->render($context->getKnowledge()) ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
