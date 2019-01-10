<?php
$campusNumber = UNL_Peoplefinder_Record::getCampusPhoneNumber($context);
?>
<?php if ($campusNumber): ?>
  <small class="on-campus-dialing dcf-pl-2 dcf-txt-xs">On campus, dial <?php echo $campusNumber ?></small>
<?php endif; ?>
