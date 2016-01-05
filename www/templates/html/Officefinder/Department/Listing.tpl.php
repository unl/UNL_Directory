<?php
$userCanEdit = false;

if ($controller->options['view'] != 'alphalisting') {
    // Check if the user can edit and store this result for later
    $userCanEdit = $context->userCanEdit(UNL_Officefinder::getUser());
}

$encodedEmail = '';
if (!empty($context->email)) {
    // attempt to curb lazy email harvesting bots
    $encodedEmail = htmlentities($context->getRaw('email'), ENT_QUOTES | ENT_HTML5);
}
?>
<div class="listingDetails">
    <?php echo $context->name ?>
    <?php if (isset($context->email)): ?>
    <span class="email icon-email"><a href="mailto:<?php echo $encodedEmail ?>"><?php echo $encodedEmail ?></a></span>
    <?php endif; ?>
    <?php if (!empty($context->phone)): ?>
    <span class="phone icon-phone"><?php echo $savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php') ?></span>
    <?php endif; ?>
    <?php if (!empty($context->fax)): ?>
    <span class="phone icon-print"><?php echo $savvy->render($context->fax, 'Peoplefinder/Record/TelephoneNumber.tpl.php') ?></span>
    <?php endif; ?>
    <?php if (isset($context->building)): ?>
    <span class="room icon-map-pin"><a class="location mapurl" href="https://maps.unl.edu/<?php echo $context->building ?>"><?php echo $context->building ?></a> <?php echo $context->room ?></span>
    <?php endif; ?>
    <?php if (!empty($context->address)): ?>
    <span class="adr"><?php echo $context->address; ?></span>
    <?php endif; ?>
    <?php if (isset($context->postal_code)): ?>
    <span class="postal-code"><?php echo $context->postal_code; ?></span>
    <?php endif; ?>
    <?php if (isset($context->website)): ?>
    <span class="website icon-website"><a href="<?php echo $context->website; ?>"><?php echo $context->website; ?></a></span>
    <?php endif; ?>

    <?php if ($userCanEdit): ?>
        <div class="tools">
            <a href="<?php echo $context->getURL() . '/edit' ?>" class="wdn-button wdn-button-triad icon-pencil"><span class="wdn-text-hidden">edit listing for <?php echo $context->name ?></span></a>
            <div class="forms" data-listing-id="<?php echo $context->id ?>">
                <a class="wdn-button wdn-button-triad listing-add" href="<?php echo $context->getNewChildURL() ?>">Add<span class="wdn-text-hidden"> a new child listing</span></a>
                <div class="form"></div>
                <div class="add-form"></div>
                <?php echo $savvy->render($context, 'Officefinder/Department/DeleteForm.tpl.php') ?>
                <button type="submit" class="wdn-button wdn-button-brand icon-trash" form="deletedepartment_<?php echo $context->id ?>">Delete</button>
            </div>
        </div>
    <?php endif; ?>
</div>
