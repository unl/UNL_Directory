<?php
/**
 * This page provides the peoplefinder service to applications.
 *
 */

require_once 'config.inc.php';

// Specify domains from which requests are allowed
header('Access-Control-Allow-Origin: *');

// Specify which request methods are allowed
header('Access-Control-Allow-Methods: GET, OPTIONS');

// Additional headers which may be sent along with the CORS request
// The X-Requested-With header allows jQuery requests to go through
header('Access-Control-Allow-Headers: X-Requested-With');

// Set the ages for the access-control header to 20 days to improve speed/caching.
header('Access-Control-Max-Age: 1728000');

// Set expires header for 24 hours to improve speed caching.
header('Expires: '.date('r', strtotime('tomorrow')));

// Exit early so the page isn't fully loaded for options requests
if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
    exit();
}

$options = $_GET;
$options['driver'] = $driver;

$peepObj = new UNL_Peoplefinder($options);

$format = 'html';

$renderer_options = array('uid_onclick' => 'pf_getUID',
                          'uri'         => UNL_PEOPLEFINDER_URI);
if (isset($_GET['chooser'])) {
    $renderer_options['choose_uid'] = true;
}

if (isset($_GET['renderer']) || isset($_GET['format'])) {
    $format = isset($_GET['renderer'])?$_GET['renderer']:$_GET['format'];
}
switch($format) {
case 'vcard':
    $renderer_class = 'vCard';
    break;
case 'serialized':
case 'php':
    $renderer_class = 'Serialized';
    break;
case 'xml':
    $renderer_class = 'XML';
    break;
case 'json':
    $renderer_class = 'JSON';
    break;
default:
case 'hcard':
case 'html':
    $renderer_class = 'HTML';
    break;
}

$method = false;

if (isset($_GET['method'])) {
    switch ($_GET['method']) {
    case 'getLikeMatches':
    case 'getExactMatches':
    case 'getPhoneMatches':
        $method = $_GET['method'];
        break;
    }
}

$affiliation = null;
if (isset($_GET['affiliation'])) {
    switch($_GET['affiliation']) {
        case 'faculty':
        case 'staff':
        case 'student':
            $affiliation = $_GET['affiliation'];
            break;
    }
}

$renderer_class = 'UNL_Peoplefinder_Renderer_'.$renderer_class;
$renderer = new $renderer_class($renderer_options);
if (isset($_GET['q']) && !empty($_GET['q'])) {
    // Basic query, build filter and display results
    if (strlen($_GET['q']) <= 3) {
        $renderer->renderError();
    } else {
        if (is_numeric(str_replace('-','',str_replace('(','',str_replace(')','',$_GET['q']))))) {
            $records = $peepObj->getPhoneMatches($_GET['q'], $affiliation);
            $renderer->renderSearchResults($records);
        } else {
            if ($method) {
                $records = $peepObj->$method($_GET['q'], $affiliation);
                if (count($records) > 0) {
                    $renderer->renderSearchResults($records);
                } else {
                    $renderer->renderError();
                }
            } else {
                // Standard text search
                $by_affiliation = array();
                $by_affiliation['faculty']       = array();
                $by_affiliation['staff']         = array();
                $by_affiliation['student']       = array();
                $by_affiliation['organizations'] = array();
                
                $like_by_affiliation = $by_affiliation;
                
                $records = $peepObj->getExactMatches($_GET['q'], $affiliation);
                
                UNL_Peoplefinder::$displayResultLimit -= count($records);
                
                $like_records = array();
                if (UNL_Peoplefinder::$displayResultLimit) {
                    $like_records = $peepObj->getLikeMatches($_GET['q'], $affiliation, $records);
                }
                
                foreach(array('records'=>'by_affiliation', 'like_records'=>'like_by_affiliation') as $records_var=>$affiliation_var) {
                    foreach ($$records_var as $record) {
                        foreach ($record->ou as $ou) {
                            if ($ou == 'org') {
                                ${$affiliation_var}['organizations'][] = $record;
                                break;
                            }
                        }

                        if (isset($record->eduPersonAffiliation)) {
                            foreach ($record->eduPersonAffiliation as $affiliation) {
                                ${$affiliation_var}[$affiliation][] = $record;
                            }
                        }
                    }
                }
                
                foreach ($by_affiliation as $affiliation=>$records) {
                    if (count($records)
                        || count($like_by_affiliation[$affiliation])) {
                        if ($renderer instanceof UNL_Peoplefinder_Renderer_HTML) {
                            echo '<div class="affiliation '.$affiliation.'">';
                            echo '<h2>'.ucfirst($affiliation).'</h2>';
                        }
                        $renderer->renderSearchResults($records);
                        if (count($like_by_affiliation[$affiliation])) {
                            if ($renderer instanceof UNL_Peoplefinder_Renderer_HTML) {
                                echo '<div class="likeResults">';
                                echo '<h3>similar '.$affiliation.' results</h3>';
                            }
                            $renderer->renderSearchResults($like_by_affiliation[$affiliation]);
                            if ($renderer instanceof UNL_Peoplefinder_Renderer_HTML) {
                                echo '</div>';
                            }
                        }
                        if ($renderer instanceof UNL_Peoplefinder_Renderer_HTML) {
                            echo '</div>';
                        }
                    }
                }
            }
        }
    }
} elseif (isset($_GET['uid']) && !empty($_GET['uid'])) {
    $renderer->renderRecord($peepObj->getUID($_GET['uid']));
} elseif (isset($_GET['d'])) {
    try {
        $department = new UNL_Peoplefinder_Department($_GET['d']);
        foreach ($department as $employee) {
            $renderer->renderRecord($employee);
        }
    } catch(Exception $e) {
        $renderer->renderError($e->getMessage());
    }
} else {
    $renderer->renderError();
}
