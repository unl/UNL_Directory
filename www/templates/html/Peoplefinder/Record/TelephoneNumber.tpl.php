<?php if ($controller->options['view'] != 'alphalisting') : ?>
<a href="tel:<?php echo UNL_Peoplefinder_Record::getCleanPhoneNumber($context) ?>">
    <?php echo UNL_Peoplefinder_Record::getFormattedPhoneNumber($context) ?>
</a>
<?php echo $savvy->render($context, 'Peoplefinder/Record/CampusNumber.tpl.php'); ?>
<?php else : ?>
<?php echo $savvy->render($context, 'Peoplefinder/Record/CampusNumber.tpl.php'); ?>
<a href="tel:<?php echo UNL_Peoplefinder_Record::getCleanPhoneNumber($context) ?>">
  <?php echo UNL_Peoplefinder_Record::getFormattedPhoneNumber($context) ?>
</a>
<?php endif; ?>
