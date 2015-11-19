<?php if ($context->error) { ?>
    <?php echo $context->error; ?>
<?php } ?>

<?php 
    function getKey($item, $section, $tag) {
        if (is_array($item) && is_array($item[$section]) && array_key_exists($tag, $item[$section]) && (is_string($item[$section][$tag]) || is_numeric($item[$section][$tag]))) {
            return $item[$section][$tag];
        } else {
            return FALSE;
        }
    }
?>

<?php try { ?>
    <?php if (is_string($context->bio)) { ?>
      <div class="directory-knowledge-section directory-knowledge-section-bio">
          <?php echo $context->bio; ?>
      </div>
    <?php } ?>
<?php } catch (Exception $e) {
    # don't show this section
    error_log($e->message);
} ?>

<?php if ($context->education) { ?>
    <div class="directory-knowledge-section directory-knowledge-section-education">
        <h3 class="wdn-brand"><img src="images/icons/academic-cap.svg" alt="">Education</h3>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->education as $degree) { ?>
                <li class="directory-knowledge-item">
                    <?php 
                        $deg = array(
                            getKey($degree, 'EDUCATION', 'DEG'),
                            getKey($degree, 'EDUCATION', 'SCHOOL'),
                            getKey($degree, 'EDUCATION', 'YR_COMP')
                        );
                        echo implode(', ', array_filter($deg));
                    ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php if ($context->courses) { ?>
    <div class="directory-knowledge-section directory-knowledge-section-courses">
        <h3 class="wdn-brand"><img src="images/icons/chat-4.svg" alt="">Courses</h3>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->courses as $course) { ?>
                <li class="directory-knowledge-item">
                    <?php
                        $course_nums = getKey($course, 'SCHTEACH', 'COURSEPRE') == FALSE && getKey($course, 'SCHTEACH', 'COURSENUM') == FALSE ? FALSE : getKey($course, 'SCHTEACH', 'COURSEPRE') . ' ' . getKey($course, 'SCHTEACH', 'COURSENUM');
                        $course_date = getKey($course, 'SCHTEACH', 'TYT_TERM') == FALSE && getKey($course, 'SCHTEACH', 'TYY_TERM') == FALSE ? FALSE : getKey($course, 'SCHTEACH', 'TYT_TERM') . ' ' . getKey($course, 'SCHTEACH', 'TYY_TERM');

                        $cou = array(
                            $course_nums,
                            getKey($course, 'SCHTEACH', 'TITLE'),
                            $course_date
                        );
                        echo implode(', ', array_filter($cou));
                    ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php if ($context->papers) { ?>
    <div class="directory-knowledge-section directory-knowledge-section-papers">
        <h3 class="wdn-brand"><img src="images/icons/document-1.svg" alt="">Publications and Other Intellectual Contributions</h3>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->papers as $paper) { ?>
                <li class="directory-knowledge-item">
                    <?php
                        $date = getKey($paper, 'INTELLCONT', 'DTM_PUB') == FALSE && getKey($paper, 'INTELLCONT', 'DTY_PUB') == FALSE ? FALSE : getKey($paper, 'INTELLCONT', 'DTM_PUB') . ' ' . getKey($paper, 'INTELLCONT', 'DTY_PUB');

                        $pap = array(
                            getKey($paper, 'INTELLCONT', 'TITLE'),
                            getKey($paper, 'INTELLCONT', 'JOURNAL_NAME'),
                            getKey($paper, 'INTELLCONT', 'BOOK_TITLE'),
                            $date
                        );
                        echo implode(', ', array_filter($pap));
                    ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php if ($context->grants) { ?>
    <div class="directory-knowledge-section directory-knowledge-section-grants">
        <h3 class="wdn-brand"><img src="images/icons/business-chart-2.svg" alt="">Research &amp; Grants</h3>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->grants as $grant) { ;?>
                <?php if ($grant['CONGRANT']['STATUS'] != 'Declined') { ?>
                    <li class="directory-knowledge-item">
                        <?php
                            $date = getKey($grant, 'CONGRANT', 'DTM_START') == FALSE && getKey($grant, 'CONGRANT', 'DTY_START') == FALSE ? FALSE : getKey($grant, 'CONGRANT', 'DTM_START') . ' ' . getKey($grant, 'CONGRANT', 'DTY_START');

                            $gran = array(
                                getKey($grant, 'CONGRANT', 'TITLE'),
                                getKey($grant, 'CONGRANT', 'SPONORG'),
                                getKey($grant, 'CONGRANT', 'CONGRANT_INVEST')[0]['ROLE'],
                                $date
                            );
                            echo implode(', ', array_filter($gran));
                        ?>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php if ($context->performances) { ?>
    <div class="directory-knowledge-section directory-knowledge-section-grants">
        <h3 class="wdn-brand"><img src="images/icons/column.svg" alt="">Artistic &amp; Professional Performances &amp; Exhibitions</h3>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->performances as $performance) { ?>
                <li class="directory-knowledge-item">
                    <?php
                        $date = getKey($performance, 'PERFORM_EXHIBIT', 'DTM_START') == FALSE && getKey($performance, 'PERFORM_EXHIBIT', 'DTY_START') == FALSE ? FALSE : getKey($performance, 'PERFORM_EXHIBIT', 'DTM_START') . ' ' . getKey($performance, 'PERFORM_EXHIBIT', 'DTY_START');

                        $perf = array(
                            getKey($performance, 'PERFORM_EXHIBIT', 'TITLE'),
                            getKey($performance, 'PERFORM_EXHIBIT', 'LOCATION'),
                            $date
                        );
                        echo implode(', ', array_filter($perf));
                    ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php if ($context->presentations) { ?>
    <div class="directory-knowledge-section directory-knowledge-section-presentations">
        <h3 class="wdn-brand"><img src="images/icons/keynote.svg" alt="">Presentations</h3>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->presentations as $presentation) { ?>
                <li class="directory-knowledge-item">
                    <?php
                        $date = getKey($presentation, 'PRESENT', 'DTM_START') == FALSE && getKey($presentation, 'PRESENT', 'DTY_START') == FALSE ? FALSE : getKey($presentation, 'PRESENT', 'DTM_START') . ' ' . getKey($presentation, 'PRESENT', 'DTY_START');

                        $pres = array(
                            getKey($presentation, 'PRESENT', 'TITLE'),
                            getKey($presentation, 'PRESENT', 'ORG'),
                            getKey($presentation, 'PRESENT', 'LOCATION'),
                            $date
                        );
                        echo implode(', ', array_filter($pres));
                    ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php if ($context->honors) { ?>
    <div class="directory-knowledge-section directory-knowledge-section-honors">
        <h3 class="wdn-brand"><img src="images/icons/star-5.svg" alt="">Awards &amp; Honors</h3>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->honors as $honor) { ?>
                <li class="directory-knowledge-item">
                    <?php
                        $award = array(
                            getKey($honor, 'AWARDHONOR', 'NAME'),
                            getKey($honor, 'AWARDHONOR', 'ORG'),
                            getKey($honor, 'AWARDHONOR', 'DTY_DATE'),
                            $date
                        );
                        echo implode(', ', array_filter($award));
                    ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<style>
    .directory-knowledge-section {
        border-bottom: 1px solid #ccc;
        padding-bottom: 2em;
    }
    .directory-knowledge-section:last-of-type {border-bottom: none}

    .directory-knowledge-section h3 img {
        margin-right: 0.3em;
        width: 0.75em;
    }

    .directory-knowledge-section-inner {
        list-style: outside none none;
    }
    .directory-knowledge-section-inner li {
        margin-bottom: 1em;
    }

</style>
