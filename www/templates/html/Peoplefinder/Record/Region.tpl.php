<?php if ($context == 'NE'): ?>
	<abbr class="region" title="Nebraska" itemprop="addressRegion"><?php echo $context ?></abbr>
<?php else: ?>
	<span class="region" itemprop="addressRegion"><?php echo $context ?></span>
<?php endif; ?>
