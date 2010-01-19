<?php
require_once 'config.inc.php';

session_start();
session_cache_expire(5);

$renderer_options = array('uri'=>UNL_PEOPLEFINDER_URI);
if (isset($_GET['chooser'])) {
    $renderer_options['choose_uid'] = true;
}

$peepObj  = new UNL_Peoplefinder($driver);
$renderer = new UNL_Peoplefinder_Renderer_HTML($renderer_options);

$myself = htmlentities(str_replace('index.php', '', $_SERVER['PHP_SELF']), ENT_QUOTES);
UNL_Templates::$options['version'] = 3;
$page = UNL_Templates::factory('Document');

$page->doctitle = '<title>UNL | Peoplefinder</title>';

$page->head .= '
<meta name="description" content="UNL Peoplefinder is the Faculty, Staff and Student online directory for the University. Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff. Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden." />
<meta name="keywords" content="university of nebraska-lincoln student faculty staff directory vcard" />
<meta name="author" content="Brett Bieber, UNL Office of University Communications" />
<meta name="viewport" content="width = 320" />
<link rel="stylesheet" type="text/css" media="screen" href="peoplefinder_default.css" />
<link media="only screen and (max-device-width: 480px)" href="small_devices.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
WDN.jQuery(document).ready(function() {
    self.focus();
    document.getElementById("form1").elements[0].focus();
});
</script>
';

if (isset($_GET['q']) 
    || isset($_GET['uid'])
    || isset($_GET['cn'])
    || isset($_GET['sn'])) {
    $page->head .= '<meta name="robots" content="NOINDEX, NOFOLLOW" />';
}

$page->titlegraphic = '<h1>UNL Peoplefinder</h1>';

ob_start();

if (isset($_GET['uid'])) {
    try {
        $renderer->renderRecord($peepObj->getUID($_GET['uid']));
    } catch (Exception $e) {
        header('HTTP/1.0 404 Not Found');
        echo '<p><br />Sorry, no one with that name could be found!</p>';
    }
} else {
    // Display form
    (@$_GET['adv'] == 'y')?$renderer->displayAdvancedForm():$renderer->displayStandardForm();
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
            else    echo "<p>Please enter more information or <a href='".$_SERVER['PHP_SELF']."?adv=y' title='Click here to perform a detailed Peoplefinder search'>try a Detailed Search.</a></p>";
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
    $renderer->displayInstructions((@$_GET['adv'] == 'y')?true:false);
} ?>
<div style="padding-top:3.5em;"> <a href="#" class="imagelink" onclick="document.getElementById('disclaimer').style.display='block'; return false;" title="More information about Peoplefinder"><img src="images/icon_question.gif" alt="Question Mark" width="15" height="14" /></a> UNL | Office of University Communications | <a href="http://www1.unl.edu/wdn/wiki/About_Peoplefinder" onclick="window.open(this.href); return false;">About Peoplefinder</a>
    <div id="disclaimer" style="display:none;">
        <p><strong>Information obtained from this directory may not be used to provide addresses for mailings to students, faculty or staff. Any solicitation of business, information, contributions or other response from individuals listed in this publication by mail, telephone or other means is forbidden.</strong></p>
    </div>
</div>
<?php
$page->maincontentarea = ob_get_clean();
echo $page;
