<?php
$baseUrl = UNL_Peoplefinder::getURL();

if (isset($page)) {
    // inject a prefix into the document title
    $page = $page->getRawObject();
    $page->doctitle = substr_replace($page->doctitle, 'Help | ', strlen('<title>'), 0);
}
?>
<section class="dcf-bleed unl-bg-lightest-gray dcf-pt-6  dcf-pb-6" id="data">
    <div class="dcf-wrapper">
        <h2 class="dcf-txt-center clear-top">Finding Your Way Around the Directory</h2>
        <div class="dcf-grid dcf-col-gap-vw dcf-row-gap-3 dcf-pt-3">
            <div class="dcf-col-100% dcf-col-67%-start@md">
                <h3 class="dcf-txt-h5">About the data</h3>
                <p>
                    The data used to build the <a href="https://directory.unl.edu/">Directory</a> is maintained in several source databases.
                    Information obtained from the directory may <em>not</em> be used to provide addresses for mailings to students, faculty or staff.
                    Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden.
                </p>
                <p>
                    <strong><em>Student</em></strong> directory data is maintained in the university student information system.
                </p>
                <p>
                    <strong><em>Faculty/Staff</em></strong> directory data is maintained in the university administrative and finance system, SAP / Firefly.</p>
                <p>
                    The <strong><em>Departments &amp; Units</em></strong> directory is primarily composed of information provided by departmental HR coordinators, and is augmented with data sourced from the university administrative and finance system.
                </p>
                <h3 class="dcf-txt-h5">Updating Your Information</h3>
                <p><strong>Personal information</strong> is managed outside of the directory. See our <a href="https://www.unl.edu/how-manage-personal-information/">How to Manage Personal Information</a> page for options to update your information.</p>
                <p>
                    <b>Department / Unit listings</b> in directory have an HR coordinator who is responsible for their respective listing page. The "Suggest a correction" button on each department / unit listing page will submit a request to the editors for that respective listing.
                </p>
            </div>
            <div class="dcf-col-100% dcf-col-33%-end@md">
                <h3 class="dcf-txt-h5" id="photo">Adding your photo</h3>
                <div>
                    <img class="dcf-obj-fit-cover dcf-mb-3" src="<?php echo $baseUrl ?>images/130912herbie-505.jpg" alt="Profile view of mascot Herbie Husker">
                </div>
                <p>
                    Want to show your photo on your Directory result instead of the default outline? We've got a couple of ways to do that.
                    Photos are pulled from two sources, <a href="https://planetred.unl.edu/">Planet Red</a> and <a href="https://en.gravatar.com/">Gravatar</a>.
                    If you don't currently use either of these services, getting started is pretty simple.
                </p>
                <p>As a member of the university community, we've got a <a href="https://planetred.unl.edu/">Planet Red</a> profile waiting for you to upload a photo to. All you need to do is fill out a little profile information and we'll automatically pull your photo from there.</p>
                <p>If you'd prefer not to create a Planet Red profile and you are a faculty or staff member that has been issued a campus email address, you can use that address to create a <a href="https://en.gravatar.com/">Gravatar</a> profile. Once you've got your profile loaded with an avatar, we'll automatically pull your photo from there.</p>
            </div>
        </div>
    </div>
</section>
