<?php
$campusNumber = UNL_Peoplefinder_Record::getCampusPhoneNumber($context);
?>
<?php if ($campusNumber): ?>
  <small class="on-campus-dialing dcf-txt-xs dcf-txt-nowrap">On campus, dial <?php echo $campusNumber ?></small>
<?php endif; ?>
