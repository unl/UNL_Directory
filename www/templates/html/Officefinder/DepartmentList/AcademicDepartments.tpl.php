<script type="text/javascript">
WDN.loadJS('../scripts/filters.js', function(){
	filters.initialize();
});
</script>
<div id="filters" class="grid3 first">
    <div class="wdn_filterset">
        <h4 class="formal">Filter Results</h4>
        <form class="filters" action="#" method="post">
            <fieldset class="affiliation">
                <legend>By College</legend>
                <ol>
                    <li><input type="checkbox" checked="checked" class="filterAll" value="all" name="all" id="filterAllaffiliation"/><label for="filterAllaffiliation">All</label></li>
                    <li><input type="checkbox" value="parent_85" name="parent_85" id="filterparent_85"/><label for="filterparent_85">Arts &amp; Sciences</label></li>
                    <li><input type="checkbox" value="parent_70" name="parent_70" id="filterparent_70"/><label for="filterparent_70">Fine &amp; Performing Arts</label></li>
                </ol>
    
            </fieldset>
        </form>
    </div>
</div>
<div class="grid9">
    <div class="results departments" id="dir_departmentListing">
    <h3>Academic Departments</h3>
    <ul id="dir_nav">
        <?php foreach (range('A', 'Z') as $letter): ?>
        <li><a href="#<?php echo $letter; ?>"><?php echo $letter; ?></a></li>
        <?php endforeach; ?>
    </ul>
    <?php
    $firstLetter = '';
    foreach ($context as $listing)
    {
        /* @var $listing UNL_Officefinder_Department */
        if ($firstLetter != strtoupper($listing->name[0])) {
            // New letter
            $firstLetter = strtoupper($listing->name[0]);
            if ($firstLetter != 'A') {
                echo '</ul>';
            }
            echo '<h4 id="'.$firstLetter.'" class="section">'.$firstLetter.'<span><a href="#dir_nav">Back to the top</a></span></h4><ul class="pfResult departments">';
        }
        echo $savvy->render($listing, 'Officefinder/DepartmentList/ListItem.tpl.php');
    }
    ?>
    </ul>
    </div>
</div>
