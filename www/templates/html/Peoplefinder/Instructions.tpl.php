<section class="wdn-band search-band">
    <div class="wdn-inner-wrapper wdn-inner-padding-sm">
        <?php if (isset($context->options['adv'])): ?>
            <?php echo $savvy->render($context, 'Peoplefinder/AdvancedForm.tpl.php') ?>
        <?php else: ?>
            <?php echo $savvy->render($context, 'Peoplefinder/StandardForm.tpl.php') ?>
        <?php endif; ?>
    </div>
</section>

<section class="wdn-band help-container">
    <div class="wdn-inner-wrapper">
        <h1 class="heading-block wdn-center">Welcome to the University of Nebraska–Lincoln Directory</h1>
        <div class="wdn-grid-set">
            <div class="bp2-wdn-col-one-half" id="instructions_people">
                <div class="card">
                    <img class="hero-img" src="<?php echo UNL_Peoplefinder::getURL(); ?>images/130912_Herbie_104.jpg" alt="Profile view of mascot Herbie Husker" />
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
                    <img class="hero-img" src="<?php echo UNL_Peoplefinder::getURL(); ?>images/110606_Canfield_S_3.jpg" alt="Panoramic view of Canfield Administration Building" />
                    <div class="card-content">
                        <h2><span class="wdn-subhead">Search</span>
                        Departments</h2>
                        <p>Find UNL departments by entering a full or partial department name. Information available:</p>
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
            <p>A printer-friendly version of the Yellow Page Directory is available to university users only.<br/>Please be aware this is a very large document and may take some time to fully load.</p>
            <p><a class="wdn-button wdn-button-brand" href="<?php echo UNL_Officefinder::getURL(); ?>yellow-pages">Print yellow pages</a></p>
        </div>
    </div>
</section>

<section class="wdn-band results-container">
    <div class="wdn-inner-wrapper wdn-inner-padding-sm">
        <div class="wdn-grid-set">
            <div class="bp2-wdn-col-one-fourth">
                <?php echo $savvy->render(null, 'Peoplefinder/SearchResults/Filters.tpl.php'); ?>
            </div>
            <div id="results" tabindex="-1" class="bp2-wdn-col-three-fourths"></div>
        </div>
    </div>
</section>