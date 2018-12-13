<?php
$baseUrl = UNL_Peoplefinder::getURL();
?>

<section class="dcf-bleed search-band">
    <div class="dcf-wrapper dcf-pt-7 dcf-pb-7">
        <?php echo $savvy->render($context, 'Peoplefinder/StandardForm.tpl.php') ?>
    </div>
</section>

<section class="dcf-bleed help-container dcf-pb-8">
    <div class="dcf-wrapper">
        <h1 class="dcf-sr-only">Welcome to the University of Nebraska–Lincoln Directory</h1>
        <div class="dcf-grid-halves@md dcf-col-gap-5 dcf-row-gap-5 dcf-pt-8">
            <div id="instructions_people">
                <div class="card">
                    <img class="hero-img" src="<?php echo $baseUrl ?>images/130912_Herbie_104.jpg" alt="Profile view of mascot Herbie Husker" />
                    <div class="card-content dcf-pt-6">
                        <h3><span class="dcf-subhead">Search</span>
                        People</h3>
                        <p>Find contact information for faculty, staff and students. Search by:</p>
                        <ul>
                            <li>First name</li>
                            <li>Last name</li>
                            <li>Both first and last name</li>
                            <li>Last 3 or more digits of telephone</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="instructions_departments">
                <div class="card">
                    <img class="hero-img" src="<?php echo $baseUrl ?>images/110606_Canfield_S_3.jpg" alt="Panoramic view of Canfield Administration Building" />
                    <div class="card-content dcf-pt-6">
                        <h3><span class="dcf-subhead">Search</span>
                        Departments</h3>
                        <p>Find departments by entering a full or partial department name. Information available:</p>
                        <ul>
                            <li>Department contact information and location on campus</li>
                            <li>Complete list of department employees</li>
                            <li>Organizational hierarchy of department</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="directory-foot">
            <p>A <a href="<?php echo UNL_Officefinder::getURL(); ?>yellow-pages">printer-friendly version</a> of the Yellow Page Directory is available to university users only.<br/>Please be aware this is a very large document and may take some time to fully load.</p>
        </div>
    </div>
</section>

<section class="dcf-bleed results-container">
    <div class="dcf-wrapper dcf-pt-8 dcf-pb-8">
        <div id="search-notice"></div>
        <div class="dcf-grid dcf-col-gap-4">
            <div class="dcf-col-100% dcf-col-25%-start@md result-filters">
                <?php echo $savvy->render(null, 'Peoplefinder/SearchResults/Filters.tpl.php'); ?>
            </div>
            <div id="results" tabindex="-1" class="dcf-col-100% dcf-col-75%-end@md"></div>
        </div>
    </div>
</section>

<section class="dcf-bleed record-container">
    <div class="dcf-wrapper dcf-pt-8 dcf-pb-8 record-single"></div>
</section>
