<?php
$baseUrl = UNL_Peoplefinder::getURL();

if (isset($page)) {
    // inject a prefix into the document title
    $page = $page->getRawObject();
    $page->doctitle = substr_replace($page->doctitle, 'Help | ', strlen('<title>'), 0);
}
?>
<section class="wdn-band wdn-light-neutral-band" id="data">
    <div class="wdn-inner-wrapper">
        <h1 class="wdn-center clear-top">Finding Your Way Around the UNL Directory</h1>
        <div class="bp2-wdn-grid-set-halves">
            <div class="wdn-col">
                <h2 class="wdn-brand">About the data</h2>
                <p>
                    The data used to build the <a href="http://directory.unl.edu/">Directory</a> is maintained in serveral source databases.
                    Information obtained from the directory may <em>not</em> be used to provide addresses for mailings to students, faculty or staff.
                    Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden.
                </p>
                <p>
                    <em>Student</em> directory data is maintained in the UNL student information system.
                    Because data restrictions (UNL and Board of Regents) don't allow for the distribution of student contact information to the general public, we recommend that students who would like to communicate with others in the UNL community use <a href="https://planetred.unl.edu/">Planet Red</a>, UNL's social networking utility.
                </p>
                <p>
                    <em>Faculty/Staff</em> directory data is maintained in the UNL administrative and finance system, SAP.
                    The SAP data is the official, business-oriented information about each person employed by UNL. This data includes:
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
                    <em>Department/Listing</em> directory data is sourced from UNL's administrative and finance system, SAP, and augmented with information provided by departmental HR coordinators.
                </p>
            </div>
            <div class="wdn-col">
                <h2 class="wdn-brand">Updating your information</h2>
                <p>
                    As a <em>student</em>, you may request that your information not be listed in the directory by contacting the <a href="http://registrar.unl.edu/">Office of the University Registrar</a> to set a privacy flag on your account.
                    If any information is displayed incorrectly, please consult your academic advisor.
                </p>
                <p>
                    As a <em>faculty/staff</em> member, you may update your preferred <code>&commat;unl&period;edu</code> address by visiting the <a href="http://its.unl.edu/emailhome/activation">email activation site</a>.
                    If you are also registered as a student, you will need to contact the <a href="http://registrar.unl.edu/">Office of the University Registrar</a> to set a privacy flag on your account to remove your student information from the directory.
                    For all other data changes, contact your HR representitive or departmental SAP support staff.
                </p>
                <p>
                    As a <em>retiree</em>, if you have no current HR representative, you should contact the <a href="http://hr.unl.edu/benefits/">Benefits Office</a>.
                </p>
                <p>
                    For <em>department (yellow pages) listings</em>, authorized users can make modifications by emailing <a href="mailto:operator&commat;unl&period;edu">operator&commat;unl&period;edu</a> with the directory URL of the listing and the information you'd like to update.
                </p>
            </div>
        </div>
        <div class="bp2-wdn-grid-set-halves">
            <div class="wdn-col centered">
                <h2 class="wdn-brand" id="photo">Adding your photo</h2>
                <p>
                    <img class="frame" src="<?php echo $baseUrl ?>images/130912_Herbie_104.jpg" alt="Profile view of mascot Herbie Husker" />
                    Want to show your photo on your Directory result instead of the default outline? We've got a couple of ways to do that.
                    Photos are pulled from two sources, <a href="https://planetred.unl.edu/">Planet Red</a> and <a href="http://en.gravatar.com/">Gravatar</a>.
                    If you don't currently use either of these services, getting started is pretty simple.
                </p>
                <p>As a member of the UNL community, we've got a <a href="https://planetred.unl.edu/">Planet Red</a> profile waiting for you to upload a photo to. All you need to do is fill out a little profile information and we'll automatically pull your photo from there.</p>
                <p>If you'd prefer not to create a Planet Red profile and you are a UNL faculty or staff member that has been issued a campus email address, you can use that address to create a <a href="http://en.gravatar.com/">Gravatar</a> profile. Once you've got your profile loaded with an avatar, we'll automatically pull your photo from there.</p>
                <p>Department listings may use the email address entered in the directory with the Gravatar service. If an email address is not provided or a Gravatar profile is not found for it, a photo of the building the listing is in will be used.</p>
            </div>
        </div>
    </div>
</section>

<section class="wdn-band centrex-container" id="centrex">
    <div class="wdn-inner-wrapper">
        <h2>Helpful Centrex Information</h2>
        <p>The following information is intended to help users of the old printed Centrex adjust to the online Directory.</p>

        <h3 class="wdn-brand">Emergency and Police Information</h3>
        <p>For information about reporoting emergencies, or procedures to take during emergency situations, visit the <a href="http://emergency.unl.edu/">emergency website</a>.</p>
        <p>To contact University Police for emergencies, crimes in progress, or non-emergency complaints, call 402-472-2222 or visit the <a href="http://police.unl.edu/">police website</a>.</p>
        <p>During an emergency, the UNL community and general public will receive information through the web and news media and, for those registered, by email, phone, and text through <a href="http://emergency.unl.edu/unlalert">UNL Alert</a>.
        <p>For information about radiation safety, occupational safety, biosafety, environmental protection, safety and compliance training, and outreach initiatives, visit the <a href="http://ehs.unl.edu">Environmental Health &amp; Safety website</a>.</p>

        <div class="bp1-wdn-grid-set-halves">
            <div class="wdn-col">
                <h3 class="wdn-brand">Telephone Information</h3>
                <ul>
                    <li><a href="http://its.unl.edu/manuals-downloads">Voicemail and dialing instructions</a></li>
                    <li><a href="http://its.unl.edu/">Office of Information Technology Services</a></li>
                </ul>
            </div>
            <div class="wdn-col">
                <h3 class="wdn-brand">University Information</h3>
                <ul>
                    <li><a href="http://maps.unl.edu/directory/">Building Codes</a></li>
                    <li><a href="http://registrar.unl.edu/academic-calendar">Academic Calendar</a></li>
                    <li><a href="http://nebraska.edu/administration/university-administrative-staff.html">Central Administration contact information</a></li>
                    <li><a href="http://nebraska.edu/board/board-members.html">Board of Regents members</a></li>
                    <li><a href="http://www.unl.edu/chancellor/administration">University of Nebraksa-Lincoln administration</a></li>
                    <li><a href="http://maps.unl.edu/">Campus maps</a></li>
                </ul>
            </div>
            <div class="wdn-col">
                <h3 class="wdn-brand">Other directory-related Information</h3>
                <ul>
                    <li><a href="http://www.whitepages.com/maps">United States dialing area codes</a></li>
                    <li><a href="https://countrycode.org/">Country dialing codes</a></li>
                </ul>
            </div>
            <div class="wdn-col">
                <h3 class="wdn-brand">Parking and Transit Information</h3>
                <p>To find out more about parking, permits, paying citations, or bus/transit information, visit the <a href="http://parking.unl.edu/">Parking and Transit Services website</a>.</p>
                <ul>
                    <li><a href="http://parking.unl.edu/transit">Voicemail and dialing instructions</a></li>
                    <li><a href="http://parking.unl.edu/permits">Permits and Registration</a></li>
                    <li><a href="http://parking.unl.edu/maps">Parking Maps</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
