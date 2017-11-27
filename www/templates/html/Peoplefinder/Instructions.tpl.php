<?php
$baseUrl = UNL_Peoplefinder::getURL();
?>

<section class="wdn-band search-band">
    <div class="wdn-inner-wrapper wdn-inner-padding-sm">
        <?php echo $savvy->render($context, 'Peoplefinder/StandardForm.tpl.php') ?>
    </div>
</section>

<section class="wdn-band help-container">
    <div class="wdn-inner-wrapper">
        <h1 class="wdn-text-hidden">Welcome to the University of Nebraska–Lincoln Directory</h1>
        <div class="wdn-grid-set">
            <div class="bp2-wdn-col-one-half" id="instructions_people">
                <div class="card">
                    <img class="hero-img" src="<?php echo $baseUrl ?>images/130912_Herbie_104.jpg" alt="Profile view of mascot Herbie Husker" />
                    <div class="card-content">
                        <h2><span class="wdn-subhead">Search</span>
                        People</h2>
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
            <div class="bp2-wdn-col-one-half" id="instructions_departments">
                <div class="card">
                    <img class="hero-img" src="<?php echo $baseUrl ?>images/110606_Canfield_S_3.jpg" alt="Panoramic view of Canfield Administration Building" />
                    <div class="card-content">
                        <h2><span class="wdn-subhead">Search</span>
                        Departments</h2>
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

<section class="wdn-band results-container">
    <div class="wdn-inner-wrapper wdn-inner-padding-sm">
        <div id="search-notice"></div>
        <div class="wdn-grid-set">
            <div class="bp2-wdn-col-one-fourth result-filters">
                <?php echo $savvy->render(null, 'Peoplefinder/SearchResults/Filters.tpl.php'); ?>
            </div>
            <div id="results" tabindex="-1" class="bp2-wdn-col-three-fourths wdn-pull-right" aria-live="polite"></div>
        </div>
    </div>
</section>

<section class="wdn-band record-container">
    <div class="wdn-inner-wrapper wdn-inner-padding-sm record-single"></div>
</section>
