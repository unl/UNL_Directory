<?php
//echo '<pre>';var_dump($context);echo '</pre>';
?>

<?php if (isset($context->bio->ABSTRACT)) { ?>
  <div class="directory-knowledge-section directory-knowledge-section-bio">
      <?php echo $context->bio->ABSTRACT ?>
  </div>
<?php } ?>


<?php if ($context->education) { ?>
    <div class="directory-knowledge-section directory-knowledge-section-education">
        <h3 class="wdn-brand wdn-icon-ok">Education</h3>
        <div class="directory-knowledge-section-inner">
            <?php foreach ($context->education as $degree) { ?>
                <div class="directory-knowledge-item">
                    <?php echo $degree->DEG; ?> <?php echo $degree->YR_COMP; ?> <?php echo $degree->SCHOOL; ?>
                </div>

            <?php } ?>
        </div>
    </div>
<?php } ?>

<?php if ($context->courses) { ?>
    <div class="directory-knowledge-section directory-knowledge-section-courses">
        <h3 class="wdn-brand wdn-icon-user">Courses</h3>
        <div class="directory-knowledge-section-inner">
            <?php foreach ($context->courses as $course) { ?>
                <div class="directory-knowledge-item">
                    <?php echo $course->COURSEPRE; ?> <?php echo $course->COURSENUM; ?> (<?php echo $course->TYT_TERM; ?> <?php echo $course->TYY_TERM; ?>): <?php echo $course->TITLE; ?>
                </div>

            <?php } ?>
        </div>
    </div>
<?php } ?>

<?php if ($context->papers) { ?>
    <div class="directory-knowledge-section directory-knowledge-section-papers">
        <h3 class="wdn-brand wdn-icon-star-circled">Papers</h3>
        <div class="directory-knowledge-section-inner">
            <?php foreach ($context->papers as $paper) { ?>
                <div class="directory-knowledge-item">
                    <?php echo $paper->TITLE; ?>
                </div>

            <?php } ?>
        </div>
    </div>
<?php } ?>


<?php if ($context->grants) { ?>
    <div class="directory-knowledge-section directory-knowledge-section-grants">
        <h3 class="wdn-brand wdn-icon-rocket">Research &amp; Grants</h3>
        <div class="directory-knowledge-section-inner">
            <?php foreach ($context->grants as $grant) { ?>
                <?php if ($grant->STATUS != 'Declined') { ?>
                    <div class="directory-knowledge-item">
                        <?php echo $grant->TITLE; ?>
                    </div>
                <?php } ?>

            <?php } ?>
        </div>
    </div>
<?php } ?>


<style>
    .directory-knowledge-section {
        border-bottom: 1px solid #ccc;
        padding-bottom: 2em;
    }
    .directory-knowledge-section:last-of-type {border-bottom: none}

    .directory-knowledge-section-inner {
        padding-left: 3em;
    }

</style>
