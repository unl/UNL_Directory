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
    <span class="dcf-d-block dcf-txt-lg dcf-bold"><?php echo $context->name ?></span>
    <?php if (isset($context->email)): ?>
    <span class="email dcf-d-block dcf-txt-sm"><span class="icon-email" aria-hidden="true"></span><span class="dcf-sr-only">Email:</span><a href="mailto:<?php echo $encodedEmail ?>"><?php echo $encodedEmail ?></a></span>
    <?php endif; ?>
    <?php if (!empty($context->phone)): ?>
    <span class="phone dcf-d-block dcf-txt-sm"><span class="icon-phone" aria-hidden="true"></span><span class="dcf-sr-only">Phone number:</span><?php echo $savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php') ?></span>
    <?php endif; ?>
    <?php if (!empty($context->fax)): ?>
    <span class="phone dcf-d-block dcf-txt-sm"><span class="icon-print" aria-hidden="true"></span><span class="dcf-sr-only">fax:</span><?php echo $savvy->render($context->fax, 'Peoplefinder/Record/TelephoneNumber.tpl.php') ?></span>
    <?php endif; ?>
    <?php if (isset($context->building)): ?>
    <span class="room dcf-d-block dcf-txt-sm"><span class="icon-map-pin" aria-hidden="true"></span><span class="dcf-sr-only">Location:</span><a class="location mapurl" href="https://maps.unl.edu/<?php echo $context->building ?>"><?php echo $context->building ?></a> <?php echo $context->room ?></span>
    <?php endif; ?>
    <?php if (!empty($context->address)): ?>
    <span class="adr dcf-d-block dcf-txt-sm"><?php echo $context->address; ?></span>
    <?php endif; ?>
    <?php if (isset($context->postal_code)): ?>
    <span class="postal-code dcf-d-block dcf-txt-sm"><?php echo $context->postal_code; ?></span>
    <?php endif; ?>
    <?php if (isset($context->website)): ?>
    <span class="website dcf-d-block dcf-txt-sm"><span class="icon-website" aria-hidden="true"></span><span class="dcf-sr-only">Website:</span><a href="<?php echo $context->website; ?>"><?php echo $context->website; ?></a></span>
    <?php endif; ?>

    <?php if ($userCanEdit): ?>
        <div class="tools">
            <a href="<?php echo $context->getURL() . '/edit' ?>" class="dcf-btn wdn-button-triad edit-button">
                <svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current dcf-txt-top" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
                    <path d="M22.94 2.732l-1.672-1.671a2.502 2.502 0 00-3.535 0L2.647 16.146a.595.595 0 00-.125.207l-2 6.5a.5.5 0 00.625.625c.193-.059 6.326-1.944 6.516-2.01a.493.493 0 00.19-.115L22.94 6.268c.471-.471.73-1.098.73-1.768a2.49 2.49 0 00-.73-1.768zM19.5 8.293L15.707 4.5l.793-.793L20.293 7.5l-.793.793zM4.2 21.492l-1.692-1.691.726-2.36.413.413A.5.5 0 004 18h2v2a.5.5 0 00.147.354l.413.413-2.36.725zM6.293 17H4.207l-.5-.5L15 5.207l1.543 1.543L6.293 17zm-4.118 3.882l.943.943-1.362.419.419-1.362zm5.325-.589l-.5-.5v-2.086l10.25-10.25L18.793 9 7.5 20.293zM22.232 5.561L21 6.793 17.207 3l1.232-1.232a1.503 1.503 0 012.121 0l1.672 1.671c.282.282.438.659.438 1.061 0 .403-.155.779-.438 1.061z"></path>
                </svg>
                <span class="dcf-sr-only">Edit listing for <?php echo $context->name ?></span>
            </a>
            <div class="forms" data-listing-id="<?php echo $context->id ?>">
                <a class="dcf-btn wdn-button-triad listing-add" href="<?php echo $context->getNewChildURL() ?>">Add<span class="dcf-sr-only"> a new child listing</span></a>
                <div class="form"></div>
                <div class="add-form"></div>
                <?php echo $savvy->render($context, 'Officefinder/Department/DeleteForm.tpl.php') ?>
                <button type="submit" class="dcf-btn dcf-btn-primary" form="deletedepartment_<?php echo $context->id ?>"><span class="icon-trash" aria-hidden="true"></span>Delete</button>
            </div>
        </div>
    <?php endif; ?>
</div>
