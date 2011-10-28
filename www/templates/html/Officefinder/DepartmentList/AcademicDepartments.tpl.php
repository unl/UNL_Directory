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
<?php
$firstLetter = '';
foreach ($context as $listing)
{
    /* @var $listing UNL_Officefinder_Department */
    if ($firstLetter != strtoupper($listing->name[0])) {
        // New letter
        $firstLetter = strtoupper($listing->name[0]);
        echo '<h2 id="'.$firstLetter.'">'.$firstLetter.'</h3>';
    }
    $website = $listing->getURL();
    if (!empty($listing->website)) {
        $website = $listing->website;
    }
    $parent = $listing->getParent();
    echo '
    <div class="dept parent_'.$parent->id.'">
            <a href="'.$website.'">'.$listing->name.'</a> ('.$parent->name.')
    </div>';
}
?>
</div>