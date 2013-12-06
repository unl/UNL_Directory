<?php
$address = $context->address;
if (preg_match('/^([A-Z]+)\s/', $context->address, $matches)) {
    $address = str_replace($matches[1], '<a class="location mapurl" href="http://maps.unl.edu/#'.$matches[1].'">'.$matches[1].'</a>', $context->address);
}

$userCanEdit = false;

if ($controller->options['view'] != 'alphalisting') {
    // Check if the user can edit and store this result for later
    $userCanEdit = $context->userCanEdit(UNL_Officefinder::getUser());
}

if ($userCanEdit) {
    echo ' <a href="'.$context->getURL().'?format=editing" class="minibutton edit" title="Edit"><span class="icon">Edit</span></a>'.PHP_EOL;
    echo '<div class="action_control">'.PHP_EOL;
    $edit_url = UNL_Officefinder::getURL(null, array('view'      => 'department',
                                                     'parent_id' => $context->id,
                                                     'format'    => 'editing'));
    echo ' <a href="'.htmlentities($edit_url, ENT_QUOTES).'" class="maxbutton add" title="Add Child"><span class="icon">Add a child listing</span></a>';
    echo '<div class="form"></div>'.PHP_EOL;
    echo $savvy->render($context, 'Officefinder/Department/Listing/SortForm.tpl.php');
    include dirname(__FILE__).'/../../../editing/Officefinder/Department/DeleteForm.tpl.php';
    echo '</div>';
}
?>
<div class="listingDetails">
    <?php echo $context->name ?>
    <?php if (isset($context->email)): ?>
    <span class="email"><?php echo '<a href="mailto:'.$context->email.'">'.$context->email.'</a>'; ?></span>
    <?php endif; ?>
    <?php if (!empty($context->phone)): ?>
    <span class="tel"><?php echo $savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php') ?></span>
    <?php endif; ?>
    <?php if (isset($context->building)): ?>
    <span class="room"><?php echo $context->room.' <a class="location mapurl" href="http://maps.unl.edu/#'.$context->building.'">'.$context->building.'</a>'; ?></span>
    <?php endif; ?>
    <?php if (isset($address)): ?>
    <span class="adr"><?php echo $address; ?></span>
    <?php endif; ?>
    <?php if (isset($context->postal_code)): ?>
    <span class="postal-code"><?php echo $context->postal_code; ?></span>
    <?php endif; ?>
    <?php if (isset($context->website)): ?>
    <span class="website"><a href="<?php echo $context->website; ?>"><?php echo $context->website; ?></a></span>
    <?php endif; ?>
</div>
