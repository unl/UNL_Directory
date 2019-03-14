<?php
$baseUrl = UNL_Peoplefinder::getURL();
$version = UNL_Peoplefinder::$staticFileVersion;
?>
<meta name="robots" content="noarchive">
<meta name="description" content="The Faculty, Staff and Student online directory for the University. Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff. Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden.">
<meta name="keywords" content="university of nebraska-lincoln student faculty staff directory vcard">
<meta name="author" content="University of Nebraska–Lincoln Office of University Communication">

<link rel="home" href="<?php echo $baseUrl ?>">
<link rel="stylesheet" href="<?php echo $baseUrl ?>css/directory.css?v=<?php echo $version ?>">
<link rel="stylesheet" href="<?php echo $baseUrl ?>css/directory-print.css?v=<?php echo $version ?>" media="print">
