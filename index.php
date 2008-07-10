<?php
require_once 'config.inc.php';


require_once 'UNL/Peoplefinder/Renderer/HTML.php';
session_start();
session_cache_expire(5);

$renderer_options = array('uri'=>UNL_PEOPLEFINDER_URI);
if (isset($_GET['chooser'])) {
	$renderer_options['choose_uid'] = true;
}

$peepObj  = new UNL_Peoplefinder();
$renderer = new UNL_Peoplefinder_Renderer_HTML($renderer_options);

$myself = htmlentities(str_replace('index.php', '', $_SERVER['PHP_SELF']), ENT_QUOTES);

if (!isset($_SESSION['lastResultDisplayed']))
	$_SESSION['lastResultDisplayed']=0;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>UNL | Peoplefinder</title>

<!-- Codebase:UNLFramework 20070105 -->
<link rel="stylesheet" type="text/css" media="screen" href="/ucomm/templatedependents/templatecss/layouts/main.css" />
<script type="text/javascript" src="/ucomm/templatedependents/templatesharedcode/scripts/sifr.js"></script>

<?php virtual('/ucomm/templatedependents/templatesharedcode/includes/browsersniffers/ie.html'); ?>
<?php virtual('/ucomm/templatedependents/templatesharedcode/includes/comments/developersnote.html'); ?>
<?php virtual('/ucomm/templatedependents/templatesharedcode/includes/metanfavico/metanfavico.html'); ?>

<meta name="description" content="UNL Peoplefinder is the Faculty, Staff and Student online directory for the University. Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff. Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden." />
<meta name="keywords" content="university of nebraska-lincoln student faculty staff directory vcard" />
<meta name="author" content="Brett Bieber, UNL Office of University Communications" />
<meta name="viewport" content="width = 320" />
<link rel="stylesheet" type="text/css" media="screen" href="peoplefinder_default.css" />
<link media="only screen and (max-device-width: 480px)" href="small_devices.css" type="text/css" rel="stylesheet" />

<?php if(isset($_GET['q'])
         || isset($_GET['uid'])
         || isset($_GET['cn'])
         || isset($_GET['sn'])) { ?>
<meta name="robots" content="NOINDEX, NOFOLLOW" />
<?php } ?>
</head>

<body <?php if(!(isset($_GET['uid']))) echo 'onload="self.focus();document.getElementById(\'form1\').elements[0].focus()"'; ?> id="popup">
 
<div id="header">
	<div class="clear"> <a href="http://www.unl.edu/" title="UNL website"><img src="/ucomm/templatedependents/templatecss/images/logo.png" alt="UNL graphic identifier" id="logo" /></a>

	</div>
</div>

<div id="red-header">
	<div class="clear">
		<h1>University of Nebraska&ndash;Lincoln</h1>
		<div id="breadcrumbs">   </div>
	</div>
</div>
<!-- close red-header -->
 
<div id="container">
	<div class="clear">
		<div id="title">  
			<div id="titlegraphic">
				<!-- WDN: see glossary item 'title graphics' -->
				<h1>UNL Peoplefinder</h1>
			</div>
			<!-- maintitle -->
		</div>
		<!-- close title -->
		
		
		<!-- close navigation -->
		
		<div id="main_right" class="mainwrapper">
			<!--THIS IS THE MAIN CONTENT AREA; WDN: see glossary item 'main content area' -->
			
			<div id="maincontent"> 
				<!--THIS IS THE MAIN CONTENT AREA; WDN: see glossary item 'main content area' -->
				<?php
							if (isset($_GET['uid'])) {
								$renderer->renderRecord($peepObj->getUID($_GET['uid']));
							} else {
								// Display form
								(@$_GET['adv'] == 'y')?$peepObj->displayAdvancedForm():$peepObj->displayStandardForm();
								if (isset($_GET['p'])) {
									// Display the next page
									if ($_SESSION['lastResultCount']>0) {
										// Old results have been set.
										$renderer->renderSearchResults($_SESSION['lastResult'], $_GET['p']*$_SESSION['lastResultDisplayed']);
									} else {
										echo 'Your session has expired, please search again.';
									}
								} else {
									if (isset($_GET['q']) && !empty($_GET['q'])) {
										// Basic query, build filter and display results
										if (strlen($_GET['q']) > 3) {
											if (is_numeric(str_replace("-","",str_replace("(","",str_replace(")","",$_GET['q']))))) {
												$records = $peepObj->getPhoneMatches($_GET['q']);
												$renderer->renderSearchResults($records);
											} else {
												$records = $peepObj->getExactMatches($_GET['q']);
												if (count($records) > 0) {
													echo '<div class="cMatch">Exact matches:';
													if (count($records) >= UNL_Peoplefinder::$resultLimit) {
											            echo "<p>Your search could only return a subset of the results. ";
											            if (@$_GET['adv'] != 'y')    echo "Would you like to <a href='".$renderer->uri."?adv=y' title='Click here to perform a detailed Peoplefinder search'>try a Detailed Search?</a>\n";
											            else                         echo 'Try refining your search.';
											            echo '</p>';
											        }
											        echo '</div>';
													$renderer->renderSearchResults($records);
												} else {
												    echo '<p>Sorry, I couldn\'t find anyone matching '.htmlentities($_GET['q']).'.</p>';
												}
												if (count($records) < UNL_Peoplefinder::$displayResultLimit) {
													// More room to display LIKE results
											        UNL_Peoplefinder::$displayResultLimit = UNL_Peoplefinder::$displayResultLimit - count($records);
													$records = $peepObj->getLikeMatches($_GET['q'], $records);
													if (count($records) > 0) {
														echo '<div class="cMatch">Possible matches:';
														if (count($records) >= UNL_Peoplefinder::$resultLimit) {
												            echo "<p>Your search could only return a subset of the results. ";
												            if (@$_GET['adv'] != 'y')    echo "Would you like to <a href='".$renderer->uri."?adv=y' title='Click here to perform a detailed Peoplefinder search'>try a Detailed Search?</a>\n";
												            else                         echo 'Try refining your search.';
												            echo '</p>';
												        }
											        echo '</div>';
														$renderer->renderSearchResults($records);
													}
												}
											}
										}
										else	echo "<p>Please enter more information or <a href='".$_SERVER['PHP_SELF']."?adv=y' title='Click here to perform a detailed Peoplefinder search'>try a Detailed Search.</a></p>";
									} elseif (isset($_GET['sn']) || isset($_GET['cn'])) {
										// Advanced search
										$records = $peepObj->getAdvancedSearchMatches($_GET['sn'],$_GET['cn'],$_GET['eppa']);
										$renderer->renderSearchResults($records);
									}
									if (isset($records)) {
										// Prepare for sleep
										$_SESSION['lastResult']          = $records;
										$_SESSION['lastResultCount']     = count($records);
										$_SESSION['lastResultDisplayed'] = UNL_Peoplefinder::$displayResultLimit;
									}
								}
							}
						
							if (isset($_GET['q']) || isset($_GET['uid']) || isset($_GET['cn']) || isset($_GET['p'])) { ?>
		                    <div id="backButton"><a class="imagelink" href="<?php echo $myself; ?>" onclick="history.go(-1); return false;" title="Go back to search results"><img src="images/btn_back.gif" alt="Back" /></a></div>
		                    <?php }
							if (!isset($_GET['uid'])) { ?>
		                     	<a href="<?php echo $myself; ?>" title="Click here to run a basic People Finder search">Basic</a>&nbsp;|&nbsp;<a href="<?php echo $myself; ?>?adv=y" title="Click here to perform a detailed Peoplefinder search">Detailed</a>
		                    <?php } 
							//show instructions if no results are showing
							if (!isset($_GET['uid']) && !isset($records)) {
								$peepObj->displayInstructions((@$_GET['adv'] == 'y')?true:false);
							} ?>
                    <div style="padding-top:3.5em;"> <a href="#" class="imagelink" onclick="document.getElementById('disclaimer').style.display='block'; return false;" title="More information about Peoplefinder"><img src="images/icon_question.gif" alt="Question Mark" width="15" height="14" /></a> UNL | Office of University Communications
                        <div id="disclaimer" style="display:none;">
                            <p><a href="http://www1.unl.edu/wdn/wiki/How_to_update_information_in_Peoplefinder">How to update your information listed in this directory.</a></p>
                            <p><strong>Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff. Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden.</strong></p>
                        </div>
                    </div>
				<!--THIS IS THE END OF THE MAIN CONTENT AREA.-->
				 </div>
			 </div>
		<!-- close main right -->
	</div>
</div>
<!-- close container -->

<!-- close footer -->
<!-- sifr -->
<script type="text/javascript" src="/ucomm/templatedependents/templatesharedcode/scripts/sifr_replacements.js"></script>
</body>
</html>
