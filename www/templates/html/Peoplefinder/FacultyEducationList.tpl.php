<?php
$page->doctitle  = '<title>Faculty Educational Credentials | University of Nebraska-Lincoln</title>';
?>
<p>The following is a list of faculty educational credentials as of <?php echo $context->getDateLastUpdated(); ?></p>
<div class="dcf-grid">
    <div class="dcf-col-100% dcf-col-25%-start@md">
        <?php echo $savvy->render(null, 'Peoplefinder/SearchResults/Filters.tpl.php'); ?>
    </div>
    <div class="dcf-col-100% dcf-col-75%-end@md">
        <div class="results affiliation faculty">
        <h3>Faculty</h3>
        <p class="result_head dcf-txt-xs dcf-mt-1 unl-font-sans unl-dark-gray">Results 1 - <?php echo count($context); ?></p>
        <nav class="dcf-pagination">
            <ol class="dcf-list-bare dcf-list-inline">
            <?php foreach (range('A', 'Z') as $letter): ?>
                <li><a href="#<?php echo $letter; ?>"><?php echo $letter; ?></a></li>
            <?php endforeach; ?>
            </ol>
        </nav>
        <table class="zentable cool dcf-table">
            <caption class="dcf-sr-only">Faculty List</caption>
            <thead>
                <tr>
                    <th scope="col">Name</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $current_letter = false;

            $limited = new LimitIterator($context, $context->options['offset'], $context->options['limit']);
            foreach ($limited as $faculty) {
                try {
                    $record = $faculty->getRecord();
                } catch (Exception $e) {
                    $record = false;
                }

                // If we're in a section with a new first letter, add the appropriate ID attribute
                $id = '';
                if (substr($faculty->employee_name, 0, 1) !== $current_letter) {
                    $current_letter = substr($faculty->employee_name, 0, 1);
                    $id = 'id="'.$current_letter.'"';
                }

                echo '<tr class="ppl_Sresult faculty" '.$id.'>';
                echo '<td>';
                if ($record) {
                    echo '<a href="'.$controller->getURL().'?uid='.$record->uid.'">'.$faculty->employee_name.'</a>';
                    if (isset($record->unlHROrgUnitNumber)) {
                        foreach ($record->unlHROrgUnitNumber as $orgUnit) {
                            if ($name = UNL_Officefinder_Department::getNameByOrgUnit($orgUnit)) {
                                echo '        <div class="organization-unit">'.$name.'</div>'.PHP_EOL;
                            }
                        }
                    }
                } else {
                    echo $faculty->employee_name;
                }
                $degrees = array();
                foreach ($faculty->getEducation() as $degree) {
                    $degrees[] = $degree;
                }
                echo implode(', ', $degrees);
                echo '</td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
        <?php
        $url = $controller->getURL();
        if (count($context) > $context->options['limit']) {
            $pager = new stdClass();
            $pager->total  = count($context);
            $pager->limit  = $context->options['limit'];
            $pager->offset = $context->options['offset'];
            $pager->url    = $url.'?view=facultyedu';
            echo $savvy->render($pager, 'PaginationLinks.tpl.php');
        }
        ?>
        </div>
    </div>
</div>
<?php
$page->addScriptDeclaration("WDN.loadJS('" . $url . "scripts/filters.js', function(){ filters.initialize(); });");
$page->addScriptDeclaration("WDN.initializePlugin('pagination');");
?>
