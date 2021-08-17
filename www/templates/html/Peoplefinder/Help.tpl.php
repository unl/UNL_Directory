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
        <div class="dcf-grid-thirds@md dcf-col-gap-vw dcf-row-gap-3 dcf-pt-3">
            <div>
                <h4>About the data</h4>
                <p>
                    The data used to build the <a href="https://directory.unl.edu/">Directory</a> is maintained in several source databases.
                    Information obtained from the directory may <em>not</em> be used to provide addresses for mailings to students, faculty or staff.
                    Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden.
                </p>
                <p>
                    <em>Student</em> directory data is maintained in the university student information system.
                    Because data restrictions don't allow for the distribution of student contact information to the general public, we recommend that students who would like to communicate with others in the university community use <a href="https://planetred.unl.edu/">Planet Red</a>, our social networking utility.
                </p>
                <p>
                    <em>Faculty/Staff</em> directory data is maintained in the university administrative and finance system, SAP.
                    The SAP data is the official, business-oriented information about each person employed by the university. This data includes:
                </p>
                <ul>
                    <li>name</li>
                    <li>employment designation</li>
                    <li>job title(s)</li>
                    <li>department(s)</li>
                    <li>campus address</li>
                    <li>campus phone number</li>
                    <li>campus email address</li>
                    <li>nickname or &ldquo;known as&rdquo; name</li>
                </ul>
                <p>
                    <em>Department/Listing</em> directory data is sourced from the university administrative and finance system, SAP, and augmented with information provided by departmental HR coordinators.
                </p>
            </div>
            <div>
                <h4>Updating your information</h4>
                <p>
                    As a <em>student</em>, you may request that your information not be listed in the directory by contacting the <a href="https://registrar.unl.edu/">Office of the University Registrar</a> to set a privacy flag on your account.
                    If any information is displayed incorrectly, please consult your academic advisor.
                </p>
                <p>
                    As a <em>faculty/staff</em> member, you may update your preferred <code>&commat;unl&period;edu</code> address by visiting the <a href="https://its.unl.edu/emailhome/activation">email activation site</a>.
                    If you are also registered as a student, you will need to contact the <a href="https://registrar.unl.edu/">Office of the University Registrar</a> to set a privacy flag on your account to remove your student information from the directory.
                    For all other data changes, contact your HR representative or departmental SAP support staff.
                </p>
                <p>
                    As a <em>retiree</em>, if you have no current HR representative, you should contact the <a href="https://hr.unl.edu/benefits/">Benefits Office</a>.
                </p>
                <p>
                    For <em>department (yellow pages) listings</em>, authorized users can make modifications by emailing <a href="mailto:operator&commat;unl&period;edu">operator&commat;unl&period;edu</a> with the directory URL of the listing and the information you'd like to update.
                </p>
            </div>
            <div>
                <h4 id="photo">Adding your photo</h4>
                <p>
                    <img class="frame" src="<?php echo $baseUrl ?>images/130912herbie-505.jpg" alt="Profile view of mascot Herbie Husker" />
                    Want to show your photo on your Directory result instead of the default outline? We've got a couple of ways to do that.
                    Photos are pulled from two sources, <a href="https://planetred.unl.edu/">Planet Red</a> and <a href="https://en.gravatar.com/">Gravatar</a>.
                    If you don't currently use either of these services, getting started is pretty simple.
                </p>
                <p>As a member of the university community, we've got a <a href="https://planetred.unl.edu/">Planet Red</a> profile waiting for you to upload a photo to. All you need to do is fill out a little profile information and we'll automatically pull your photo from there.</p>
                <p>If you'd prefer not to create a Planet Red profile and you are a faculty or staff member that has been issued a campus email address, you can use that address to create a <a href="https://en.gravatar.com/">Gravatar</a> profile. Once you've got your profile loaded with an avatar, we'll automatically pull your photo from there.</p>
                <p>Department listings may use the email address entered in the directory with the Gravatar service. If an email address is not provided or a Gravatar profile is not found for it, a photo of the building the listing is in will be used.</p>
            </div>
        </div>
    </div>
</section>
