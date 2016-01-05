<?php
$baseUrl = UNL_Peoplefinder::getURL();
?>
<?php if (count($context->bio)): ?>
  <div class="directory-knowledge-section directory-knowledge-section-bio">
      <?php echo $context->bio; ?>
  </div>
<?php endif; ?>

<?php
    function getKey($item, $section, $tag) {
        if (isset($item[$section][$tag]) && is_scalar($item[$section][$tag])) {
            return $item[$section][$tag];
        } else {
            return false;
        }
    }
?>

<?php if ($context->education): ?>
    <div class="directory-knowledge-section directory-knowledge-section-education">
        <h2 class="wdn-brand icon-academic-cap">Education</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->education as $degree): ?>
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
<?php endif; ?>

<?php if ($context->papers): ?>
    <div class="directory-knowledge-section directory-knowledge-section-papers">
        <h2 class="wdn-brand icon-document">Publications and Other Intellectual Contributions</h2>
        <ul class="directory-knowledge-section-inner">
            <?php foreach ($context->papers as $paper): ?>
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
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>
