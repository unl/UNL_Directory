<nav class="dcf-pagination">
    <ol class="dcf-list-bare dcf-list-inline">
        <?php if ($context->offset != 0) :?>
        <li><a class="dcf-pagination-prev" href="<?php echo $context->url.'&amp;limit='.$context->limit.'&amp;offset='.($context->offset-$context->limit); ?>">Prev</a></li>
        <?php endif; ?>
        <?php for ($page = 1; $page*$context->limit < $context->total+$context->limit; $page++ ) {
            $link = $context->url.'&amp;limit='.$context->limit.'&amp;offset='.($page-1)*$context->limit;
            $currentPage = false;
            if (($page-1)*$context->limit == $context->offset) {
	            $currentPage = true;
            }
        ?>
        <li>
            <?php if ($currentPage): ?>
                <span class="dcf-pagination-selected"><?php echo $page; ?></span>
            <?php else: ?>
                <a href="<?php echo $link; ?>"><?php echo $page; ?></a>
            <?php endif; ?>
        </li>
        <?php } // end for?>
        <?php if (($context->offset+$context->limit) < $context->total) :?>
        <li><a class="dcf-pagination-next" href="<?php echo $context->url.'&amp;limit='.$context->limit.'&amp;offset='.($context->offset+$context->limit); ?>">Next</a></li>
        <?php endif; ?>
    </ol>
</nav>
<?php $page->addScriptDeclaration("WDN.initializePlugin('pagination');"); ?>
