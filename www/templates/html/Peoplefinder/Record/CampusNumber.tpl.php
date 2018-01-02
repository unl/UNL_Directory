<?php
$campusNumber = UNL_Peoplefinder_Record::getCampusPhoneNumber($context);
?>
<?php if ($campusNumber): ?>
    <abbr class="on-campus-dialing" title="For on-campus dialing only. Off-campus, dial <?php echo $context ?>"><span class="on-campus-label">On-campus </span><?php echo $campusNumber ?></abbr>
<?php endif; ?>
