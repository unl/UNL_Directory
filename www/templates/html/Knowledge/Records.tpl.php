<?php if (count($context->bio)): ?>
  <div class="directory-knowledge-section directory-knowledge-section-bio">
      <?php echo $context->bio; ?>
  </div>
<?php endif; ?>

<?php if ($context->education): ?>
    <div class="directory-knowledge-section directory-knowledge-section-education">
        <h2 class="wdn-brand icon-academic-cap">Education</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->getKeyCollection('education', [
                'DEG',
                'SCHOOL',
                'YR_COMP',
            ]) as $degree): ?>
                <li class="directory-knowledge-item">
                    <?php echo $degree; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($context->courses): ?>
    <div class="directory-knowledge-section directory-knowledge-section-courses">
        <h2 class="wdn-brand icon-chat-user">Courses</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->getKeyCollection('courses', [
                ['COURSEPRE', 'COURSENUM'],
                'TITLE',
                ['TYT_TERM', 'TYY_TERM'],
            ]) as $course): ?>
                <li class="directory-knowledge-item">
                    <?php echo $course ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($context->papers): ?>
    <div class="directory-knowledge-section directory-knowledge-section-papers">
        <h2 class="wdn-brand icon-document">Publications and Other Intellectual Contributions</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->getKeyCollection('papers', [
                'TITLE',
                'JOURNAL_NAME',
                'BOOK_TITLE',
                ['DTM_PUB', 'DTY_PUB'],
            ]) as $paper): ?>
                <li class="directory-knowledge-item">
                    <?php echo $paper ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($context->grants): ?>
    <div class="directory-knowledge-section directory-knowledge-section-grants">
        <h2 class="wdn-brand icon-business-chart">Research &amp; Grants</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->getKeyCollection('grants', [
                'TITLE',
                'SPONORG',
                ['tag' => 'CONGRANT_INVEST', 'dereference' => [0, 'ROLE']],
                ['DTM_START', 'DTY_START'],
            ], ['STATUS' => 'Declined']) as $grant): ?>
                <li class="directory-knowledge-item">
                    <?php echo $grant ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($context->performances): ?>
    <div class="directory-knowledge-section directory-knowledge-section-grants">
        <h2 class="wdn-brand icon-column">Artistic &amp; Professional Performances &amp; Exhibitions</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->getKeyCollection('performances', [
                'TITLE',
                'LOCATION',
                ['DTM_START', 'DTY_START'],
            ]) as $performance): ?>
                <li class="directory-knowledge-item">
                    <?php echo $performance ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($context->presentations): ?>
    <div class="directory-knowledge-section directory-knowledge-section-presentations">
        <h2 class="wdn-brand icon-keynote">Presentations</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->getKeyCollection('presentations', [
                'TITLE',
                'ORG',
                'LOCATION',
                ['DTM_START', 'DTY_START'],
            ]) as $presentation): ?>
                <li class="directory-knowledge-item">
                    <?php echo $presentation ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($context->honors): ?>
    <div class="directory-knowledge-section directory-knowledge-section-honors">
        <h2 class="wdn-brand icon-bookmark-star">Awards &amp; Honors</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->getKeyCollection('honors', [
                'NAME',
                'ORG',
                'DTY_DATE',
            ]) as $honor): ?>
                <li class="directory-knowledge-item">
                    <?php echo $honor ?>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>
