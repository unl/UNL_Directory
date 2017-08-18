<?php
$page->doctitle  = '<title>Faculty Educational Credentials | University of Nebraska-Lincoln</title>';
$page->pagetitle = '<h1>Faculty Educational Credentials</h1>';
$page->head      = $page->getRaw('head').'<link rel="stylesheet" type="text/css" media="screen" href="//directory.unl.edu/wdn/templates_3.0/css/content/pagination.css" />';
?>
<p>The following is a list of faculty educational credentials as of <?php echo $context->getDateLastUpdated(); ?></p>
<div class="wdn-grid-set">
    <div class="wdn-col-one-fourth">
        <?php echo $savvy->render(null, 'Peoplefinder/SearchResults/Filters.tpl.php'); ?>
    </div>
    <div class="wdn-col-three-fourths">
        <div class="results affiliation faculty">
        <h3>Faculty</h3>
        <div class="result_head">Results 1 - <?php echo count($context); ?></div>
        <ul class="wdn_pagination">
        <?php foreach (range('A', 'Z') as $letter): ?>
            <li><a href="#<?php echo $letter; ?>"><?php echo $letter; ?></a></li>
        <?php endforeach; ?>
        </ul>
        <table class="zentable cool">
        <thead>
            <tr>
                <th>Name</th>
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
<script type="text/javascript">
WDN.loadJS('<?php echo $url; ?>scripts/filters.js', function(){
	filters.initialize();
});
</script>
