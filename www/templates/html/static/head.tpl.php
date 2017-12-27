<?php
$baseUrl = UNL_Peoplefinder::getURL();
$version = UNL_Peoplefinder::$staticFileVersion;

$loginService = UNL_Officefinder::getURL() . 'editor';
if (strpos($loginService, '//') === 0) {
    $loginService = 'https:' . $loginService;
}
$loginUrl = 'https://login.unl.edu/cas/login?service=' . urlencode($loginService);
$logoutUrl = 'https://login.unl.edu/cas/logout?url=' . urlencode($loginService);
?>
<meta name="robots" content="noarchive">
<meta name="description" content="The Faculty, Staff and Student online directory for the University. Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff. Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden."/>
<meta name="keywords" content="university of nebraska-lincoln student faculty staff directory vcard"/>
<meta name="author" content="University of Nebraska–Lincoln Office of University Communication"/>

<link rel="home" href="<?php echo $baseUrl ?>"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $baseUrl ?>css/directory.css?v=<?php echo $version ?>"/>
<script>
require(['wdn'], function(WDN) {
	WDN.setPluginParam('idm', 'login', '<?php echo $loginUrl ?>');
	WDN.setPluginParam('idm', 'logout', '<?php echo $logoutUrl ?>');
});
</script>
