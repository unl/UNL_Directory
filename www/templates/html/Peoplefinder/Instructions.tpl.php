<div class="two_col left" id="results">
    <div class="intro">
    <img width="100" height ="100" class="profile_pic medium planetred_profile" src="images/peopleHerbie.png" style="float:left;" />
    <h6 class="recordDetails">
        Search <span class="search_context">People</span>
    </h6>
    <div class="clear"></div>
    <?php
	   echo $savvy->render($context, 'Peoplefinder/StandardForm.tpl.php');
	?>
    <div class="intro_support">
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
<div class="two_col right" id="pfShowRecord">
    <div class="intro">
    <img width="100" height ="100" class="profile_pic medium planetred_profile" src="images/organizationVC.png" />
    <h6 class="recordDetails">
        Search <span class="search_context">Departments</span>
    </h6>
    <div class="clear"></div>
    <div class="intro_support">
        <h3>Coming soon!</h3>
    </div>
    </div>
</div>