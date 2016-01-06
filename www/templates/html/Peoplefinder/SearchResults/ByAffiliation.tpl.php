
<div id="results_<?php echo $context->affiliation ?>" class="results affiliation <?php echo $context->affiliation ?>">
	<h2 class="wdn-brand"><?php echo ucfirst($context->affiliation) ?></h2>
	<?php echo $savvy->render(new UNL_Peoplefinder_SearchResults(array('results'=>$context->getRaw('results'), 'options'=>$context->options ))); ?>
	<?php if (count($context->like_results)): ?>
		<div class="likeResults">
			<h3><span class="wdn-subhead">similar <?php echo $context->affiliation ?> results</h3>
			<?php echo $savvy->render(new UNL_Peoplefinder_SearchResults(array('results'=>$context->getRaw('like_results'), 'options'=>$context->options))); ?>
		</div>
	<?php endif; ?>
</div>
