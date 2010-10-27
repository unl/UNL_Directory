<?php
    if (isset($context->options['adv'])) {
        echo $savvy->render($context, 'Peoplefinder/AdvancedForm.tpl.php');
    } else {
        echo $savvy->render($context, 'Peoplefinder/StandardForm.tpl.php');
    }
?>
<div class="two_col left">
    <div class="intro">
    <img width="100" height ="100" class="profile_pic medium planetred_profile" src="<?php echo UNL_Peoplefinder::getURL(); ?>images/peopleHerbie.png" alt="sample people results" />
    <h6 class="recordDetails">
        Search <span class="search_context">People</span>
    </h6>
    <div class="intro_support clear">
        <p>Find contact information for faculty, staff and students.</p>
        <p>Search by:</p>
        <ul>
            <li>First Name</li>
            <li>Last Name</li>
            <li>Both first and last name</li>
            <li>Last 3 or more digits of telephone</li>
        </ul>
    </div>
    </div>
</div>

<div class="two_col right">
    <div class="intro">
	    <img width="100" height ="100" class="profile_pic medium planetred_profile" src="<?php echo UNL_Peoplefinder::getURL(); ?>images/organizationVC.png" alt="sample department results" />
	    <h6 class="recordDetails">
	        Search <span class="search_context">Departments</span>
	    </h6>
	    <div class="intro_support clear">
	        <p>Search the UNL directory by entering a department name, person's name, or last three or more digits of a telephone number.</p>
	    </div>
    </div>
</div>
<?php echo $savvy->render(null, 'Peoplefinder/SearchResults/Filters.tpl.php'); ?>
<div id="results" class="three_col right"></div>