<?php
/**
 * This page provides the peoplefinder service to applications.
 *
 */

require_once 'config.inc.php';
require_once 'UNL/Peoplefinder.php';

$peepObj = new UNL_Peoplefinder();

$renderer_options = array('uid_onclick' => 'pf_getUID',
                          'uri'         => UNL_PEOPLEFINDER_URI);
if (isset($_GET['chooser'])) {
    $renderer_options['choose_uid'] = true;
}

if (isset($_GET['q']) && !empty($_GET['q'])) {
    require_once 'UNL/Peoplefinder/Renderer/HTML.php';
    if (isset($_GET['renderer'])) {
        switch($_GET['renderer']) {
            default:
            case 'html':
                $renderer = new UNL_Peoplefinder_Renderer_HTML($renderer_options);
                break;
            case 'serialized':
            case 'php':
                include_once 'UNL/Peoplefinder/Renderer/Serialized.php';
                $renderer = new UNL_Peoplefinder_Renderer_Serialized($renderer_options);
                break;
            case 'xml':
                $renderer = new UNL_Peoplefinder_Renderer_XML($renderer_options);
                break;
        }
    } else {
        $renderer = new UNL_Peoplefinder_Renderer_HTML($renderer_options);
    }
	// Basic query, build filter and display results
	if (strlen($_GET['q']) > 3) {
		if (is_numeric(str_replace('-','',str_replace('(','',str_replace(')','',$_GET['q']))))) {
			$records = $peepObj->getPhoneMatches($_GET['q']);
			$renderer->renderSearchResults($records);
		} else {
			
			$records = $peepObj->getExactMatches($_GET['q']);
			if (count($records) > 0) {
			    if ($renderer instanceof UNL_Peoplefinder_Renderer_HTML) {
				    echo "<div class='cMatch'>Exact matches:</div>\n";
			    }
				$renderer->renderSearchResults($records);
			} else {
				echo 'No exact matches found.';
			}
			
			if (count($records) < UNL_Peoplefinder::$displayResultLimit) {
				// More room to display LIKE results
				UNL_Peoplefinder::$displayResultLimit = UNL_Peoplefinder::$displayResultLimit-$peepObj->lastResultCount;
				$records = $peepObj->getLikeMatches($_GET['q'], $records);
				if (count($records) > 0) {
					echo "<div class='cMatch'>Possible matches:</div>\n";
					$renderer->renderSearchResults($records);
				}
			}
		}
	}
	else	echo "<p>Please enter more information or <a href='".$_SERVER['PHP_SELF']."?adv=y' title='Click here to perform a detailed Peoplefinder search'>try a Detailed Search.</a></p>";
} elseif (isset($_GET['uid']) && !empty($_GET['uid'])) {
	switch(@$_GET['format']) {
		case 'vcard':
		    require_once 'UNL/Peoplefinder/Renderer/vCard.php';
		    $renderer = new UNL_Peoplefinder_Renderer_vCard();
		break;
		case 'json':
		    require_once 'UNL/Peoplefinder/Renderer/JSON.php';
		    $renderer = new UNL_Peoplefinder_Renderer_JSON();
		    break;
		case 'serialized':
		case 'php':
		    require_once 'UNL/Peoplefinder/Renderer/Serialized.php';
            $renderer = new UNL_Peoplefinder_Renderer_Serialized();
		    break;
		case 'hcard':
	    default:
		    require_once 'UNL/Peoplefinder/Renderer/HTML.php';
		    $renderer = new UNL_Peoplefinder_Renderer_HTML($renderer_options);
		break;
	}
	$renderer->renderRecord($peepObj->getUID($_GET['uid']));
}
