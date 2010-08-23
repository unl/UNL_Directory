<?php
    echo $savvy->render($context, 'Peoplefinder/StandardForm.tpl.php');
?>
<div class="two_col left">
    <div class="intro">
    <img width="100" height ="100" class="profile_pic medium planetred_profile" src="images/peopleHerbie.png" />
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
    <img width="100" height ="100" class="profile_pic medium planetred_profile" src="images/organizationVC.png" />
    <h6 class="recordDetails">
        Search <span class="search_context">Departments</span>
    </h6>
    <div class="intro_support clear">
        
    </div>
    </div>
</div>
<div id="filters" class="one_col left">
<div class="zenbox energetic wdn_filterset">
    <h3>Filter People Results</h3>
    <form method="post" action="#" class="filters">

    <fieldset class="affiliation">
        <legend><span>By Affiliation</span></legend>
        <ol>
           <li>
           </li>
        </ol>
    </fieldset>
    <fieldset class="department">
        <legend><span>By Department</span></legend>
        <ol>
        </ol>
    </fieldset>
    </form>
</div>
</div>
<div id="results" class="three_col right"></div>