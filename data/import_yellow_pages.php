<?php
require_once dirname(__FILE__).'/../www/config.inc.php';
error_reporting(E_ALL | E_STRICT);
$db = new mysqli('localhost', UNL_Officefinder::$db_user, UNL_Officefinder::$db_pass, 'officefinder');
echo '<pre>';
// OK Wipe the DB
$db->query('TRUNCATE departments');
$db->query('TRUNCATE department_aliases');
$db->query('TRUNCATE telecom_unidaslt_to_departments');

$sap_dept = new UNL_Peoplefinder_Department(array('d'=>'University of Nebraska - Lincoln'));

if ($root = UNL_Officefinder_Department::getByID(1)) {
    // Found an existing root
} else {
    // Add a new root entry
    // Import OFFICIAL departments into the database
    $root = new UNL_Officefinder_Department();
    $root->setAsRoot();
    updateFields($root, $sap_dept);
}

// Now crawl through all the official departments and update the data.
updateOfficialDepartment($sap_dept);

// Get root again, because the left and right values have changed!
$root = UNL_Officefinder_Department::getById(1);

foreach (array(
    'Buildings',
    'Copy Centers',
    'Religious Groups',
    //'Fax Access Numbers',
    //'Vice Chancellors',
    //'UNOPA (University of Nebraska Office Professionals Association)',
    //'Operated By Follett Higher Education Group University Bookstore',
    //'UN Computing Services Network (University of Nebraska Central Administration)',
    //'UAAD Officers & Non-Committee Chair Executive Board',
    //'Administration',
    //'Colleges',
    //'Graduate Assistants',
    ) as $semi_official_dept_name) {
    if (!($semi_official = UNL_Officefinder_Department::getByname($semi_official_dept_name))) {
        $semi_official           = new UNL_Officefinder_Department();
        $semi_official->name     = $semi_official_dept_name;
        $semi_official->org_unit = '_'.md5($semi_official_dept_name);
        $semi_official->save();
        $root->addChild($semi_official);
    }
}

$cleanup_file = new SplFileObject(dirname(__FILE__).'/Centrex Cleanup.csv');
$cleanup_file->setFlags(SplFileObject::READ_CSV);
//checkCleanupFile($cleanup_file);
//exit();

// Now rename a couple departments that are really mis-named:
foreach (array(
    '50001186'=>'IANR Information Services'
    ) as $old=>$new) {
    $dept = UNL_Officefinder_Department::getByOrg_unit($old);
    if ($dept === false) {
        $dept = UNL_Officefinder_Department::getByName($old);
    }
    $dept->name = $new;
    $dept->save();
}


function updateOfficialDepartment(UNL_Peoplefinder_Department $sap_dept, UNL_Officefinder_Department &$parent = null)
{

    if (!($dept = UNL_Officefinder_Department::getByorg_unit($sap_dept->org_unit))) {
        $dept = new UNL_Officefinder_Department();
    }

    // Now update all fields with the official data from SAP
    updateFields($dept, $sap_dept);
    echo '.';
    flush();

    if ($parent) {
        if ($dept->isChildOf($parent)) {
            // All OK!
        } else {
            if ($dept->hasChildren()) {
                throw new Exception('Err, hmm. This is an existing department with children has moved, I can\'t handle that yet! The department name is '.$dept->name.' with org_unit id = '.$dept->org_unit);
            } else {
                $parent->addChild($dept, true);
            }
        }
    }

    if ($sap_dept->hasChildren()) {
        foreach ($sap_dept->getChildren() as $sap_sub_dept) {

            updateOfficialDepartment($sap_sub_dept, $dept);

        }
    }
}

function updateFields($old, $new) {
    foreach ($old as $key=>$val) {
        if (isset($new->$key)
            && $key != 'options') {
            $old->$key = $new->$key;
        }
        // Save it
        $old->save();
    }
}

$known_as = array();

if ($result = $db->query('SELECT * FROM telecom_departments WHERE sLstTyp=1 AND iSeqNbr=0;')) {
    printf("Select returned %d rows.\n", $result->num_rows);

    // Official department search
    $dept_search = new SimpleXMLElement(file_get_contents(UNL_Peoplefinder::getDataDir().'/hr_tree.xml'));

    while($obj = $result->fetch_object()){

//        sanityCheck();

        if (preg_match('/(.*)\(see (.*)\)/', $obj->szLname.' '.$obj->szFname.' '.$obj->szAddtText, $matches)) {
            // known-as listing
            echo 'known as listing!!'.$obj->szLname.' '.$obj->szFname.' '.$obj->szAddtText.PHP_EOL;
            $known_as[] = array($matches[2], $matches[1]);
            continue;
        }

        $clean_name = cleanField($obj->szLname.' '.$obj->szFname.' '.$obj->szAddtText);
        echo $clean_name.', '.$obj->lMaster_id.'<br>'.PHP_EOL;

        $dept          = false;
        $official_dept = false;
        $parent_dept   = false;
        try {
            $official_dept = new UNL_Peoplefinder_Department(array('d'=>$clean_name));
            // Found an official match
            $dept = UNL_Officefinder_Department::getByorg_unit($official_dept->org_unit);
            if (!$dept) {
                echo 'error finding official org record';
                exit();
            }
        } catch (Exception $e) {
            if (isset($postal_code)) {
                $results = $dept_search->xpath('//attribute[@name="org_unit"][@value="50000003"]/..//attribute[@name="postal_code"][@value="'.$postal_code.'"]/..');

                if (count($results)) {
                    // Found a match on the zip code
                    $official_dept = new UNL_Peoplefinder_Department(array('d'=>(string)$results[0][0]->attribute['value']));
                    $dept = UNL_Officefinder_Department::getByorg_unit($official_dept->org_unit);
                    echo $name.'=>'.$official_dept->name.PHP_EOL;
                }
            }
            if (!$dept) {
                // Both those failed, check the cleanup file
                // Not an official department, no clue where this goes, check the cleanup file
                foreach ($cleanup_file as $row) {
                    if ($clean_name == $row[0]) {
                        // Found an entry in the cleanup file
                        if (isset($row[3])) {
                            switch(strtolower($row[3])) {
                                case 'delete':
                                case 'delete?':
                                case 'remove?':
                                case 'remove':
                                    continue 2;
                                    break;
                                default:
                                    break;
                            }
                        }
                        if (!empty($row[1])) {
                            // THis maps to an official entry
                            if (substr($row[1], 0, 1) == '5') {
                                $dept = UNL_Officefinder_Department::getByorg_unit($row[1]);
                            } else {
                                try {
                                    $official_dept = new UNL_Peoplefinder_Department(array('d'=>$row[1]));
                                    $dept = UNL_Officefinder_Department::getByorg_unit($official_dept->org_unit);
                                } catch(Exception $e) {
                                    $dept = UNL_Officefinder_Department::getByname($row[1], 'org_unit IS NOT NULL');
                                }
                            }
                        } elseif (!empty($row[2])) {
                            // Found a parent
                            if (substr($row[2], 0, 1) == '5') {
                                $parent_dept = UNL_Officefinder_Department::getByorg_unit((int)$row[2]);
                                if ($parent_dept == false) {
                                    throw new Exception($row[2].' is an invalid offical org number');
                                }
                            } else {
                                $parent_dept = UNL_Officefinder_Department::getByname($row[2], 'org_unit IS NOT NULL');
                            }
                            if (!$parent_dept) {
                                // Don't know about this department yet, let's add it
                                $parent_dept = new UNL_Officefinder_Department();
                                $parent_dept->name = $row[2];
                                $parent_dept->save();
                                UNL_Officefinder_Department::getByID(1)->addChild($parent_dept, true);
                            }
                        }
                        break;
                    }
                }
            }
        }
//        sanityCheck();
        if (false === $dept) {
            // New department, no clue where this goes
            $dept = new UNL_Officefinder_Department();
            $dept->name = $clean_name;
        }
        
        // Existing SAP Fields
//        $dept->org_unit    = NULL;
//        $dept->building    = NULL;
//        $dept->room        = NULL;
//        $dept->city        = NULL;
//        $dept->state       = NULL;
//        $dept->postal_code = NULL;
        if (trim($obj->sZipCd5) != '' || trim($obj->sZipCd4) != '') {
            if (trim($obj->sZipCd5) == '') {
                // Assume 68588
                $obj->sZipCd5 = '68588';
            }
            $dept->postal_code = trim($obj->sZipCd5).'-'.trim($obj->sZipCd4);
        }

        // Added fields
        $dept->address  = trim($obj->szAddress);
        $dept->phone    = '';
        if (trim($obj->sNPA1) !== '') {
            $dept->phone = trim($obj->sNPA1).'-'.preg_replace('/([\d]{3})([\d]{4})/', '$1-$2', trim($obj->sPhoneNbr1));
        }
//        $dept->fax      = NULL;
//        $dept->email    = NUll;
//        $dept->website  = NULL;
//        $dept->acronym  = NULL;
//        $dept->known_as = NULL;
//        sanityCheck();
        $dept->save();
        $db->query('INSERT INTO telecom_unidaslt_to_departments VALUES ('.$obj->lMaster_id.','.$dept->id.');');
//        sanityCheck();
        if (false === $official_dept
            && !isset($dept->org_unit)) {
            // Find where the parent is
            if ($parent_dept
                && !$dept->isChildOf($parent_dept)) {
                $parent_dept->addChild($dept, true);
            } else {
                if (!$dept->isChildOf(UNL_Officefinder_Department::getById(1))) {
                    UNL_Officefinder_Department::getById(1)->addChild($dept, true);
                }
            }
        }
//        sanityCheck();

        $sql = "SELECT * FROM telecom_departments WHERE lGroup_id={$obj->lGroup_id} AND iSeqNbr !=0 ORDER BY iSeqNbr ASC;";
        $listings = $db->query($sql);

        if ($listings->num_rows) {
            $k = 0;
            $indentation_levels = array();
            $indentation_levels[0] = $dept;
            $last_added            = $dept;
            while ($listing = $listings->fetch_object()) {
                $child_clean_name = cleanField($listing->szDirLname.' '.$listing->szDirFname.' '.$listing->szDirAddText, false);
                $child = UNL_Officefinder_Department::getByname($child_clean_name, 'org_unit IS NULL AND parent_id = '.$dept->id);
                if ($child instanceof UNL_Officefinder_Department) {
                    continue;
                }
                echo str_repeat('-', $listing->tiIndDrg).$child_clean_name.PHP_EOL;
                $child = new UNL_Officefinder_Department();
                $child->name = $child_clean_name;
                if (trim($listing->sNPA1) !== '') {
                    $child->phone = trim($listing->sNPA1).'-'.preg_replace('/([\d]{3})([\d]{4})/', '$1-$2', trim($listing->sPhoneNbr1));
                }
                $child->address = trim($listing->szAddress);
//                $child->email   = NULL;
//                $child->uid     = NULL;

                $child->save();
                $db->query('INSERT INTO telecom_unidaslt_to_departments VALUES ('.$listing->lMaster_id.','.$child->id.');');

                $i = $listing->tiIndDrg-1;

                if (!isset($indentation_levels[$i])) {
                    $last_added->reload();
                    $last_added->addChild($child, true);
                } else {
                    $indentation_levels[$i]->reload();
                    $indentation_levels[$i]->addChild($child, true);
                }
                // Store the last child at this level
                $indentation_levels[$listing->tiIndDrg] = $child;
                $last_added = $child;
//                sanityCheck();
            }
        }
        $listings->close();

//        echo PHP_EOL;
    }

    /* free result set */
    $result->close();
    include(dirname(__FILE__).'/minor_data_cleanups.php');
}

$db->close();

foreach ($known_as as $data) {
    addKnownAs($data[0], $data[1]);
}

function cleanField($text, $correct_case = true)
{
    if ($correct_case) {
        $text = strtolower($text);
        $text = ucwords($text);
    }

    $text = preg_replace('/Of([\s]|$)/', ' of$1', $text);
    $text = str_replace('And ', '& ', $text);
    $text = str_replace('For ', 'for ', $text);
    $text = preg_replace('/Unl([\s]|$)/', 'UNL$1', $text);
    $text = preg_replace('/Uno([\s]|$)/', 'UNO$1', $text);
    $text = preg_replace('/Unk([\s]|$)/', 'UNK$1', $text);
    $text = preg_replace('/Unmc([\s]|$)/', 'UNMC$1', $text);
    $text = preg_replace('/Uaad([\s]|$)/', 'UAAD$1', $text);
    $text = preg_replace('/Unopa([\s]|$)/', 'UNOPA$1', $text);
    while(preg_match('/"([a-z])"/', $text, $matches)) {
        $text = str_replace($matches[0], strtoupper($matches[0]), $text);
    }

    if (preg_match('/, (.*)/', $text, $matches)) {
        if ($matches[1] !== 'Inc') {
            $text = $matches[1].' '.str_replace($matches[0], '', $text);
            $text = cleanField($text);
        }
    }

    $text = preg_replace_callback('/\(([a-z])/', function($matches) {return '('.ucfirst($matches[1]);}, $text);
    $text = preg_replace_callback('/\-([a-z])/', function($matches) {return '-'.ucfirst($matches[1]);}, $text);
    $text = preg_replace('/\(ec\)/i', '(EC)', $text);
    $text = str_replace('(Apc)', '(APC)', $text);
    $text = str_replace('Ianr', 'IANR', $text);
    $text = str_replace('   ', ' ', trim($text));
    $text = str_replace('  ', ' ', trim($text));

    $text = preg_replace('/^Dept\.? of /', '', $text);
    //echo $text.PHP_EOL;

    return trim($text);
}

function checkCleanupFile($cleanup_file)
{
    foreach ($cleanup_file as $row) {
        // Found an entry in the cleanup file
        if (!empty($row[1])) {
            if ($row[1] == 'Official SAP #/Name') {
                continue;
            }
            // THis maps to an official entry
            if (substr($row[1], 0, 1) == '5') {
                $official_dept = UNL_Officefinder_Department::getByorg_unit($row[1]);
            } else {
                $official_dept = UNL_Officefinder_Department::getByname($row[1], 'org_unit IS NOT NULL');
            }
            if (!$official_dept) {
                echo 'I Dunno who this OFFICIAL dept is '.$row[0].'=>'.$row[1].'<br />';
            }
            continue;
        } elseif (!empty($row[2])) {
            // Found a parent
            if (substr($row[2], 0, 1) == '5') {
                $parent_dept = UNL_Officefinder_Department::getByorg_unit($row[2]);
            } else {
                $parent_dept = UNL_Officefinder_Department::getByname($row[2]);
                if (!$parent_dept) {
                    // Don't know about this department yet, let's add it
//                    $parent_dept = new UNL_Officefinder_Department();
//                    $parent_dept->name = $row[2];
//                    $parent_dept->save();
                    //$root->addChild($parent_dept);
                }
            }
            if (!$parent_dept) {
                echo 'I Dunno who this PARENT dept is '.$row[0].'=>'.$row[2].'<br />';
            }
            continue;
        }
    }
}

function sanityCheck()
{
    // Begin sanity check!
    $ucomm = UNL_Officefinder_Department::getByID(355);
    if ($ucomm->getParent()->id != 2) {
        throw new Exception('UHOH');
    }
}

function addKnownAs($official_dept_name, $alias) {
    $parent_dept = UNL_Officefinder_Department::getByname($official_dept_name, 'org_unit IS NOT NULL');
    if (!$parent_dept) {
        echo 'Could not find the alias!'.$official_dept_name.'<br>'.PHP_EOL;
    } else {
        $parent_dept->addAlias($alias);
    }
}