<?php
$baseUrl = UNL_Peoplefinder::getURL();
?>
<?php if (count($context->bio)): ?>
  <div class="directory-knowledge-section directory-knowledge-section-bio">
      <?php echo $context->bio; ?>
  </div>
<?php endif; ?>

<?php if ($context->education): ?>
    <div class="directory-knowledge-section directory-knowledge-section-education">
        <h2 class="wdn-brand icon-academic-cap">Education</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->education as $degree): ?>
                <li class="directory-knowledge-item">
                    <?php echo $degree['EDUCATION']['DEG']; ?> <?php echo $degree['EDUCATION']['YR_COMP']; ?> <?php echo $degree['EDUCATION']['SCHOOL']; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($context->courses): ?>
    <div class="directory-knowledge-section directory-knowledge-section-courses">
        <h2 class="wdn-brand icon-chat-user">Courses</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->courses as $course) { ?>
                <li class="directory-knowledge-item">
                    <?php echo $course['SCHTEACH']['COURSEPRE']; ?> <?php echo $course['SCHTEACH']['COURSENUM']; ?> (<?php echo $course['SCHTEACH']['TYT_TERM']; ?> <?php echo $course['SCHTEACH']['TYY_TERM']; ?>): <?php echo $course['SCHTEACH']['TITLE']; ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($context->papers): ?>
    <div class="directory-knowledge-section directory-knowledge-section-papers">
        <h2 class="wdn-brand icon-document">Publications and Other Intellectual Contributions</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->papers as $paper): ?>
                <li class="directory-knowledge-item">
                    <?php echo $paper['INTELLCONT']['TITLE']; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($context->grants): ?>
    <div class="directory-knowledge-section directory-knowledge-section-grants">
        <h2 class="wdn-brand icon-business-chart">Research &amp; Grants</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->grants as $grant): ?>
                <?php if ($grant['CONGRANT']['STATUS'] != 'Declined'): ?>
                    <li class="directory-knowledge-item">
                        <?php echo $grant['CONGRANT']['TITLE']; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($context->performances): ?>
    <div class="directory-knowledge-section directory-knowledge-section-grants">
        <h2 class="wdn-brand icon-column">Artistic &amp; Professional Performances &amp; Exhibitions</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->performances as $performance): ?>
                <li class="directory-knowledge-item">
                    <?php echo $performance['PERFORM_EXHIBIT']['TITLE']; ?> &ndash; <em><?php echo $performance['PERFORM_EXHIBIT']['LOCATION']; ?></em> &ndash; <?php echo $performance['PERFORM_EXHIBIT']['DTM_START']; ?> <?php echo $performance['PERFORM_EXHIBIT']['DTD_START']; ?>, <?php echo $performance['PERFORM_EXHIBIT']['DTY_START']; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($context->presentations): ?>
    <div class="directory-knowledge-section directory-knowledge-section-presentations">
        <h2 class="wdn-brand icon-keynote">Presentations</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->presentations as $presentation): ?>
                <li class="directory-knowledge-item">
                    <?php echo $presentation['PRESENT']['TITLE']; ?> &ndash; <em><?php echo $presentation['PRESENT']['ORG']; ?>, <?php echo $presentation['PRESENT']['LOCATION']; ?></em> &ndash; <?php echo $presentation['PRESENT']['DTM_DATE']; ?> <?php echo $presentation['PRESENT']['DTD_DATE']; ?>, <?php echo $presentation['PRESENT']['DTY_DATE']; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($context->honors): ?>
    <div class="directory-knowledge-section directory-knowledge-section-honors">
        <h2 class="wdn-brand icon-bookmark-star">Awards &amp; Honors</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->honors as $honor): ?>
                <li class="directory-knowledge-item">
                    <?php echo $honor['AWARDHONOR']['NAME']; ?> &ndash; <em><?php echo $honor['AWARDHONOR']['ORG']; ?></em>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>
