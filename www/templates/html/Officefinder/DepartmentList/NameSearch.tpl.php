<div class="affiliation">
<h2>Departments</h2>
<?php
if (count($context)) {
    echo '<div class="result_head">'.count($context).' result(s) found</div>';
    echo '<ul class="pfResult departments">';
    foreach ($context as $department) {
        echo '<li>
                <div class="overflow">
                    <a class="planetred_profile" href="?view=department&amp;id='.$department->id.'">
                        <img alt="Generic Icon" src="images/organization40.png" class="profile_pic small photo">
                    </a>
                    <div class="recordDetails">
                        <div class="fn">
                            <a href="'.UNL_Peoplefinder::getURL().'departments/?view=department&amp;id='.$department->id.'">'.$department->name.'</a>
                        </div>
                    </div>
                    <a class="cInfo"" href="'.UNL_Peoplefinder::getURL().'departments/?view=department&amp;id='.$department->id.'">More Details</a>
                </div>
             </li>';
    }
    echo '</ul>';
} else {
    echo "No results could be found";
}
?>
</div>