<?php
$organizations = count($context) ? $context->getUniqueOrganizations() : [];
$displayOrgs = [];
foreach ($organizations as $org) {
    $displayOrgs[] = $org->name;
}
?>
<?php if (count($organizations)): ?>
    <p>The information on this page is maintained by <?php echo implode(', ', $displayOrgs) ?>.</p>
<?php endif; ?>
