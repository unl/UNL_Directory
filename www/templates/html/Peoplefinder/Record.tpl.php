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

$showKnowledge = $context->shouldShowKnowledge();
?>
<?php if ($showKnowledge): ?>
<section class="dcf-grid dcf-col-gap-vw">
    <div class="dcf-col-100% dcf-col-25%-start@md directory-knowledge-summary">
<?php endif; ?>


<div class="vcard <?php if (!$showKnowledge): ?>card <?php endif; ?><?php echo $context->eduPersonPrimaryAffiliation ?>" data-uid="<?php echo $context->uid ?>" data-preferred-name="<?php echo $preferredName ?>" itemscope itemtype="http://schema.org/<?php echo $itemtype ?>">
    <a class="card-profile planetred_profile" href="<?php echo $context->getProfileUrl() ?>" aria-label="Planet Red profile for <?php echo $preferredName ?>" itemprop="url">
        <img class="photo profile_pic" itemprop="image" src="<?php echo $context->getImageURL(UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_LARGE) ?>" alt="Avatar for <?php echo $preferredName ?>" />
    </a>

    <div class="vcardInfo<?php if (!$showKnowledge): ?> card-content<?php endif; ?>">
    <?php if (!$context->isHcardFormat()): ?>
        <h1 class="headline">
    <?php else: ?>
        <div class="headline">
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
        <div class="eppa">(<?php echo $affiliations ?>)</div>
    <?php endif; ?>

    <?php if ($context->affiliationMightIncludeAppointments()): ?>
        <?php
        $roles = $context->getRoles();
        $title = $context->formatTitle();
        ?>
        <?php if (count($roles)): ?>
            <?php echo $savvy->render($roles) ?>
        <?php elseif ($title): ?>
            <div class="title" itemprop="jobTitle"><?php echo $title ?></div>
        <?php endif; ?>
    <?php endif ?>

    <?php if ($context->hasStudentInformation()): ?>
        <div class="sis-title">
        <?php if (isset($context->unlSISClassLevel)): ?>
            <span class="grade"><?php echo $context->formatClassLevel() ?></span>
        <?php endif; ?>
        <?php if (isset($context->unlSISMajor)): ?>
            <?php foreach ($context->getRawObject()->unlSISMajor as $major): ?>
                <span class="major"><?php echo $context->formatMajor($major) ?></span>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (isset($context->unlSISMinor)): ?>
            <?php foreach ($context->getRawObject()->unlSISMinor as $minor): ?>
                <span class="minor"><?php echo $context->formatMajor($minor) ?></span>
            <?php endforeach; ?>
        <?php endif; ?>
            <?php foreach ($context->getRawObject()->unlSISCollege as $college): ?>
                <?php
                $college = $context->formatCollege($college);
                ?>
                <?php if (is_string($college)): ?>
                    <span class="icon-academic-cap college"><?php echo $college ?></span>
                <?php else: ?>
                    <span class="icon-academic-cap college">
                        <?php if (isset($college['link'])): ?>
                            <a href="<?php echo $college['link'] ?>">
                        <?php endif; ?>
                        <abbr title="<?php echo $college['title'] ?>"><?php echo $college['abbr'] ?></abbr>
                        <?php if (isset($college['org_unit_number'])): ?>
                            </a>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (($address = $context->formatPostalAddress()) && count($address)): ?>
        <div class="adr work attribute icon-map-pin" itemprop="workLocation" itemscope itemtype="http://schema.org/Place">
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
            <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
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
        <div class="tel work icon-phone attribute">
            <span class="type">Work</span>
            <span class="value"><?php echo $savvy->render((object) [
                'number' => $context->telephoneNumber,
                'itemprop' => 'telephone',
            ], 'Peoplefinder/Record/NumberItemprop.tpl.php') ?></span>
            <?php echo $savvy->render($context->telephoneNumber, 'Peoplefinder/Record/CampusNumber.tpl.php') ?>
        </div>
    <?php endif; ?>

    <?php if (isset($context->unlSISLocalPhone)): ?>
        <div class="tel home">
            <span class="type">Phone</span>
            <span class="value"><?php echo $savvy->render((object) [
                'number' => $context->unlSISLocalPhone,
                'itemprop' => 'telephone',
            ], 'Peoplefinder/Record/NumberItemprop.tpl.php') ?></span>
            <?php echo $savvy->render($context->unlSISLocalPhone, 'Peoplefinder/Record/CampusNumber.tpl.php') ?>
        </div>
    <?php endif; ?>

    <?php if ($displayEmail): ?>
        <div class="icon-email attribute">
            <a class="email" href="mailto:<?php echo $encodedEmail ?>" itemprop="email"> <?php echo $encodedEmail ?></a>
        </div>
    <?php endif; ?>
    </div>

    <div class="vcard-tools wdn_vcardTools primary">
        <a href="<?php echo $context->getVcardUrl() ?>"><span class="icon-vcard" aria-hidden="true"></span><span class="dcf-sr-only">v-card icon </span><span class="dcf-sr-only">Download </span>vCard<span class="dcf-sr-only"> for <?php echo $preferredName ?></span></a>
        <a href="<?php echo $context->getQRCodeUrl($savvy->render($context, 'templates/vcard/Peoplefinder/Record.tpl.php')) ?>"><span class="icon-qr-code" aria-hidden="true"></span><span class="dcf-sr-only">Q R code icon </span>QR Code<span class="dcf-sr-only"> vCard for <?php echo $preferredName ?></span></a>
        <button><span class="icon-print" aria-hidden="true"></span><span class="dcf-sr-only">printer icon </span>Print<span class="dcf-sr-only"> listing for <?php echo $preferredName ?></span></button>
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
