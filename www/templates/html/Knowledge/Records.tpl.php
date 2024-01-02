<?php
$formattedData = $context->jsonSerialize();
$formatLabelMap = [
    'bio' => '',
    'education' => 'Education',
    'courses' => 'Courses',
    'papers' => 'Publications and Other Intellectual Contributions',
    'grants' => 'Research & Grants',
    'performances' => 'Artistic & Professional Performances & Exhibitions',
    'presentations' => 'Presentations',
    'honors' => 'Awards & Honors',
];
$sectionClassMap = [
    'education' => 'icon-academic-cap',
    'courses' => 'icon-chat-user',
    'papers' => 'icon-document',
    'grants' => 'icon-business-chart',
    'performances' => 'icon-column',
    'presentations' => 'icon-keynote',
    'honors' => 'icon-bookmark-star',
];
?>

<?php foreach ($formatLabelMap as $section => $sectionLabel): ?>
    <?php if ($formattedData[$section]): ?>
        <div class="directory-knowledge-section directory-knowledge-section-<?php echo $section ?> dcf-mt-7">
            <?php if ($sectionLabel): ?>
                <h2 class="directory-knowledge-section-heading dcf-relative dcf-txt-h4 dcf-regular"><span class="dcf-absolute dcf-pin-left unl-dark-gray <?php echo $sectionClassMap[$section] ?>" aria-hidden="true"></span><span class="dcf-sr-only"><?php echo $sectionClassMap[$section] ?></span><?php echo $savvy->escape($sectionLabel) ?></h2>
            <?php endif; ?>
            <?php if ($formattedData[$section] instanceof Traversable): ?>
                <ul class="directory-knowledge-section-inner dcf-ml-7" role="list">
                    <?php foreach ($formattedData[$section] as $sectionListItem): ?>
                        <li class="directory-knowledge-item"><?php echo $sectionListItem ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <?php echo $formattedData[$section] ?>
            <?php endif; ?>
        </div>
    <?php endif;?>
<?php endforeach; ?>
