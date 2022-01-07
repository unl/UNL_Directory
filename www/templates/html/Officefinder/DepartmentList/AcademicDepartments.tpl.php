<?php
UNL_Officefinder::setReplacementData('doctitle', 'Academic Departments | Directory | University of Nebraska&ndash;Lincoln');
UNL_Officefinder::setReplacementData('breadcrumbs', '
    <ul>
        <li><a href="https://www.unl.edu/" title="University of Nebraska&ndash;Lincoln">Nebraska</a></li>
        <li><a href="'.UNL_Peoplefinder::getURL().'">Directory</a></li>
        <li>Academic Departments</li>
    </ul>');
UNL_Officefinder::setReplacementData('pagetitle', '<h1>Academic Departments</h1>');
?>
<?php
$page->addScriptDeclaration("WDN.loadJS('../scripts/filters.js', function(){
	WDN.jQuery('#filters').show();
	filters.findClasses();
});");
?>
<div class="dcf-grid dcf-col-gap-vw dcf-row-gap-4">
    <div class="dcf-grid-100% dcf-grid-25%-start@sm" id="filters">
        <div>
            <h4 class="formal">Filter Results</h4>
            <form class="dcf-form filters" action="#" method="post">
                <fieldset class="affiliation">
                    <legend>
                        <span tabindex="0" role="button" aria-controls="filters_affiliation">
                            By College
                        </span>
                    </legend>
                    <div id="filters_affiliation"  role="region" tabindex="-1" aria-expanded="false">
                        <ol>
                            <li class="dcf-input-checkbox"><input type="checkbox" checked="checked" class="filterAll" value="all" name="all" id="filterAllaffiliation"/><label for="filterAllaffiliation">All</label></li>
                            <?php foreach(array(
                                192 => 'Agricultural Sciences &amp; Natural Resources',
                                55  => 'Architecture',
                                85  => 'Arts &amp; Sciences',
                                60  => 'Business Administration',
                                144 => 'Education &amp; Human Sciences',
                                121 => 'Engineering',
                                70  => 'Fine &amp; Performing Arts',
                                139 => 'Journalism &amp; Mass Communications',
                                75  => 'Law',
                            ) as $college_id=>$college_name): ?>
                            <li class="dcf-input-checkbox"><input type="checkbox" value="parent_<?php echo $college_id; ?>" name="parent_<?php echo $college_id; ?>" id="filterparent_<?php echo $college_id; ?>"/><label for="filterparent_<?php echo $college_id; ?>"><?php echo $college_name; ?></label></li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                    <input class="dcf-btn dcf-btn-primary" type="submit" disabled="disabled" value="Submit" />
                </fieldset>
            </form>
        </div>
    </div>
    <div class="dcf-grid-100% dcf-grid-75%-end@sm">
        <div class="results departments" id="dir_departmentListing">
        <?php
        $used_letters     = array();
        $firstLetter      = '';
        $department_lists = '';
        foreach ($context as $listing)
        {
            /* @var $listing UNL_Officefinder_Department */
            if ($firstLetter != strtoupper($listing->name[0])) {
                // New letter
                $firstLetter = strtoupper($listing->name[0]);
                $used_letters[] = $firstLetter;
                if ($firstLetter != 'A') {
                    $department_lists .= '</ul>';
                }
                $department_lists .= '<h4 id="'.$firstLetter.'" class="section">'.$firstLetter.'<span>&nbsp;<a href="#dir_nav">Back to the top</a></span></h4><ul class="pfResult departments dcf-list-bare">';
            }
            $department_lists .= $savvy->render($listing, 'Officefinder/DepartmentList/ListItem.tpl.php');
        }
        $department_lists .= '</ul>';
        ?>
        <ul class="dcf-list-inline" id="dir_nav">
            <?php foreach ($used_letters as $letter): ?>
            <li><a href="#<?php echo $letter; ?>"><?php echo $letter; ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?php
        echo $department_lists;
        ?>
        </div>
    </div>
</div>
