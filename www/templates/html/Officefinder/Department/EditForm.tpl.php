<?php
if ($context->id) {
    $actionUrl = $context->getURL();
    $idSuffix = '_' . $context->id;
} elseif (isset($context->options['parent_id'])) {
    $actionUrl = $context->getParent()->getURL();
    $idSuffix = '_childof_' . (int)$context->options['parent_id'];
}

$buildings = UNL_Peoplefinder_Record_Avatar::getBuildings();
$emptyBuildingSelection = empty($context->building) || !isset($buildings[$context->building]);

$fields = [
    'room' => '',
    'address' => 'Street address for USPS mailings (do not use university building codes)',
    'city' => '',
    'state' => '',
    'postal_code' => '10 characters max, eg 68588-0424',
    'phone' => 'Use the full number with area code',
    'fax' => 'Use the full number with area code',
    'email' => '',
    'website' => 'Always include http://'
];
?>
<form id="editdepartment_<?php echo $context->id; ?>" action="<?php echo $actionUrl ?>" class="dcf-form edit" method="post">
    <input type="hidden" name="_type" value="department" />
    <?php if (isset($context->id)): ?>
        <input type="hidden" name="id" value="<?php echo $context->id; ?>" />
    <?php elseif (isset($context->options['parent_id'])) : ?>
        <input type="hidden" name="parent_id" value="<?php echo (int)$context->options['parent_id']; ?>" />
    <?php endif; ?>
    <ol>
        <li class="dcf-form-group">
            <label for="name<?php echo $idSuffix ?>">
                <span class="required">*</span>
                Name
                <span class="dcf-form-help">The official name of this entity</span>
            </label>
            <input type="text" id="name<?php echo $idSuffix ?>" name="name" value="<?php echo $context->name; ?>" />
        </li>
        <li class="dcf-form-group">
            <label for="building<?php echo $idSuffix ?>">Campus Building</label>
            <select name="building" id="building<?php echo $idSuffix ?>">
                <option value=""<?php if ($emptyBuildingSelection): ?> selected<?php endif; ?>>N/A</option>
                <?php foreach ($buildings as $code => $name): ?>
                    <option value="<?php echo $savvy->escape($code) ?>"<?php if (!$emptyBuildingSelection && $code === $context->building): ?> selected<?php endif; ?>><?php echo $savvy->escape($name) . ' [' . $savvy->escape($code) . ']' ?></option>
                <?php endforeach; ?>
            </select>
        </li>
        <?php foreach ($fields as $var => $description): ?>
            <li class="dcf-form-group">
                <label for="<?php echo $var . $idSuffix ?>">
                    <?php echo ucwords(str_replace('_', ' ', $var)) ?>
                    <?php if (!empty($description)): ?>
                        <span class="dcf-form-help"><?php echo $description ?></span>
                    <?php endif; ?>
                </label>
                <input type="text" id="<?php echo $var . $idSuffix ?>" name="<?php echo $var ?>" value="<?php echo $context->$var; ?>" />
            </li>
        <?php endforeach; ?>
    </ol>
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <input class="dcf-btn dcf-btn-primary" type="submit" value="Submit" />
</form>
