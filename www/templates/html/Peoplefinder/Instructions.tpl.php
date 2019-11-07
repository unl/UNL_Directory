<?php
$baseUrl = UNL_Peoplefinder::getURL();
?>

<div class="dcf-bleed dcf-txt-center dcf-bg-center dcf-bg-no-repeat dcf-bg-cover unl-bg-darker-gray dir-search-bg search-band">
    <div class="dcf-wrapper dcf-pt-7 dcf-pb-7">
        <?php echo $savvy->render($context, 'Peoplefinder/StandardForm.tpl.php') ?>
    </div>
</div>

<div class="dcf-bleed dcf-wrapper help-container dcf-pb-8 unl-bg-lighter-gray">
    <h1 class="dcf-sr-only">Welcome to the University of Nebraska–Lincoln Directory</h1>
    <div class="dcf-grid-halves@sm dcf-col-gap-vw dcf-row-gap-5 dcf-pt-8 dcf-pb-4">
        <div class="dcf-card unl-frame-quad unl-bg-cream" id="instructions_people">
            <picture>
                <source
                  type="image/webp"
                  srcset="<?php echo $baseUrl ?>images/130912herbie-505.webp 505w,
                          <?php echo $baseUrl ?>images/130912herbie-673.webp 673w,
                          <?php echo $baseUrl ?>images/130912herbie-898.webp 898w,
                          <?php echo $baseUrl ?>images/130912herbie-1197.webp 1197w,
                          <?php echo $baseUrl ?>images/130912herbie-1597.webp 1597w"
                  sizes="(min-width: 41.956em) 43vw, 89vw">
                <source
                  srcset="<?php echo $baseUrl ?>images/130912herbie-505.jpg 505w,
                          <?php echo $baseUrl ?>images/130912herbie-673.jpg 673w,
                          <?php echo $baseUrl ?>images/130912herbie-898.jpg 898w,
                          <?php echo $baseUrl ?>images/130912herbie-1197.jpg 1197w,
                          <?php echo $baseUrl ?>images/130912herbie-1597.jpg 1597w"
                  sizes="(min-width: 41.956em) 43vw, 89vw">
              	<img class="hero-img" src="data:image/gif;base64,R0lGODlhAQABAAAAADs=" alt="Portrait photo of mascot Herbie Husker">
            </picture>
            <div class="dcf-card-block">
                <h2 class="dcf-txt-h3"><span class="dcf-subhead unl-dark-gray dcf-mb-1">Search </span>People</h2>
                <p class="dcf-txt-sm">Find contact information for faculty, staff and students. Search by:</p>
                <ul class="dcf-txt-sm dcf-mb-0">
                    <li>First name</li>
                    <li>Last name</li>
                    <li>Both first and last name</li>
                    <li class="dcf-mb-0">Last 3 or more digits of telephone</li>
                </ul>
            </div>
        </div>
        <div class="dcf-card unl-frame-quad unl-bg-cream" id="instructions_departments">
            <picture>
                <source
                  type="image/webp"
                  srcset="<?php echo $baseUrl ?>images/110606canfield-505.webp 505w,
                          <?php echo $baseUrl ?>images/110606canfield-673.webp 673w,
                          <?php echo $baseUrl ?>images/110606canfield-898.webp 898w,
                          <?php echo $baseUrl ?>images/110606canfield-1197.webp 1197w,
                          <?php echo $baseUrl ?>images/110606canfield-1597.webp 1597w"
                  sizes="(min-width: 41.956em) 43vw, 89vw">
                <source
                  srcset="<?php echo $baseUrl ?>images/110606canfield-505.jpg 505w,
                          <?php echo $baseUrl ?>images/110606canfield-673.jpg 673w,
                          <?php echo $baseUrl ?>images/110606canfield-898.jpg 898w,
                          <?php echo $baseUrl ?>images/110606canfield-1197.jpg 1197w,
                          <?php echo $baseUrl ?>images/110606canfield-1597.jpg 1597w"
                  sizes="(min-width: 41.956em) 43vw, 89vw">
              	<img class="hero-img" src="data:image/gif;base64,R0lGODlhAQABAAAAADs=" alt="Exterior view of Canfield Administration Building on a sunny day">
            </picture>
            <div class="dcf-card-block">
                <h2 class="dcf-txt-h3"><span class="dcf-subhead unl-dark-gray dcf-mb-1">Search </span>Departments</h2>
                <p class="dcf-txt-sm">Find departments by entering a full or partial department name. Information available:</p>
                <ul class="dcf-txt-sm dcf-mb-0">
                    <li>Department contact information and location on campus</li>
                    <li>Complete list of department employees</li>
                    <li class="dcf-mb-0">Organizational hierarchy of department</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="dcf-pt-4 dcf-bt-1 dcf-bt-solid unl-bt-light-gray">
        <small class="dcf-d-block dcf-w-max-lg dcf-mb-0 dcf-txt-xs">A <a href="<?php echo UNL_Officefinder::getURL(); ?>yellow-pages">printer-friendly version</a> of the Yellow Page Directory is available to university users only. Please be aware this is a very large document and may take some time to fully load.</small>
    </div>
</div>

<div class="dcf-bleed results-container unl-bg-lightest-gray">
    <div class="dcf-wrapper dcf-pt-8 dcf-pb-8">
        <div id="search-notice"></div>
        <div class="dcf-grid dcf-col-gap-vw">
            <div class="dcf-col-100% dcf-col-25%-start@md result-filters">
                <?php echo $savvy->render(null, 'Peoplefinder/SearchResults/Filters.tpl.php'); ?>
            </div>
            <div id="results" tabindex="-1" class="dcf-col-100% dcf-col-75%-end@md"></div>
        </div>
    </div>
</div>

<div class="dcf-bleed record-container unl-bg-lightest-gray">
    <div class="dcf-wrapper dcf-pt-8 dcf-pb-8 record-single"></div>
</div>
