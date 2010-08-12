<?php
require_once dirname(__FILE__).'/../www/config.inc.php';

$db = new mysqli('localhost', UNL_Officefinder::$db_user, UNL_Officefinder::$db_pass, 'officefinder');

// OK Wipe the DB
$db->query('TRUNCATE departments');

$sap_dept = new UNL_Peoplefinder_Department(array('d'=>'University of Nebraska - Lincoln'));

if ($root = UNL_Officefinder_Department::getBylft(1)) {
    // Found an existing root
} else {
    // Add a new root entry
    // Import OFFICIAL departments into the database
    $root = new UNL_Officefinder_Department();
    updateFields($root, $sap_dept);
    $root->setAsRoot();
}

// Now crawl through all the official departments and update the data.
updateOfficialDepartment($sap_dept);

foreach (array('Buildings', 'Copy Centers', 'Religious Groups', 'Fax Numbers') as $semi_official_dept_name) {
    if (!($semi_official = UNL_Officefinder_Department::getByname($semi_official_dept_name))) {
        $semi_official       = new UNL_Officefinder_Department();
        $semi_official->name = $semi_official_dept_name;
        $semi_official->save();
        $root->addChild($semi_official);
    }
}

$cleanup_file = new SplFileObject(dirname(__FILE__).'/Centrex Cleanup.csv');
$cleanup_file->setFlags(SplFileObject::READ_CSV);
checkCleanupFile($cleanup_file);
exit();


function updateOfficialDepartment(UNL_Peoplefinder_Department &$sap_dept, UNL_Officefinder_Department &$parent = null)
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
                //$parent->addChild($dept);
            }
        }
    }

    if ($sap_dept->hasChildren()) {
        foreach ($sap_dept->getChildren() as $sap_sub_dept) {

            updateOfficialDepartment($sap_sub_dept, $dept);

        }
    }
}

function updateFields(&$old, &$new) {
    foreach ($old as $key=>$val) {
        if (isset($new->$key)
            && $key != 'options') {
            $old->$key = $new->$key;
        }
        // Save it
        $old->save();
    }
}



if ($result = $db->query('SELECT * FROM telecom_departments WHERE sLstTyp=1 AND iSeqNbr=0;')) {
    printf("Select returned %d rows.\n", $result->num_rows);

    // Official department search
    $dept_search = new SimpleXMLElement(file_get_contents(UNL_Peoplefinder::getDataDir().'/hr_tree.xml'));

    while($obj = $result->fetch_object()){

        if (preg_match('/\(see (.*)\)/', $obj->szLname.' '.$obj->szFname.' '.$obj->szAddtText)) {
            // known-as listing
            //echo 'known as listing!!'.$obj->szLname.' '.$obj->szFname.' '.$obj->szAddtText.PHP_EOL;
            continue;
        }

        $clean_name = cleanField($obj->szLname.' '.$obj->szFname.' '.$obj->szAddtText);

        $official_dept = false;
        $parent_dept   = false;
        try {
            $official_dept = new UNL_Peoplefinder_Department(array('d'=>$clean_name));
            // Found an official match
            $org_unit = $official_dept->org_unit;
        } catch (Exception $e) {
            if (isset($postal_code)) {
                $results = $dept_search->xpath('//attribute[@name="org_unit"][@value="50000003"]/..//attribute[@name="postal_code"][@value="'.$postal_code.'"]/..');

                if (count($results)) {
                    // Found a match on the zip code
                    $official_dept = new UNL_Peoplefinder_Department(array('d'=>(string)$results[0][0]->attribute['value']));
                    
                    echo $name.'=>'.$official_dept->name.PHP_EOL;
                }
            }
            // Both those failed, check the cleanup file
            // Not an official department, no clue where this goes, check the cleanup file
            foreach ($cleanup_file as $row) {
                if ($clean_name == $row[0]) {
                    // Found an entry in the cleanup file
                    if (!empty($row[1])) {
                        // THis maps to an official entry
                        if (is_int($row[1])) {
                            $official_dept = UNL_Peoplefinder_Department::getById($row[1]);
                        } else {
                            $official_dept = new UNL_Peoplefinder_Department(array('d'=>$row[1]));
                        }
                    }
                    if (!empty($row[2])) {
                        // Found a parent
                        if (is_int($row[2])) {
                            $parent_dept = UNL_Officefinder_Department::getByorg_unit($row[2]);
                        } else {
                            $parent_dept = UNL_Officefinder_Department::getByname($row[2]);
                            if (!$parent_dept) {
                                // Don't know about this department yet, let's add it
                                $parent_dept = new UNL_Officefinder_Department();
                                $parent_dept->name = $row[2];
                                $parent_dept->save();
                                $root->addChild($parent_dept);
                            }
                        }
                    }
                    break;
                }
            }
        }
        if ($official_dept) {
            $dept = UNL_Officefinder_Department::getByorg_unit($official_dept->org_unit);
        } else {
            // New department, no clue where this goes
            $dept = new UNL_Officefinder_Department();
        }
        
        // Existing SAP Fields
        $dept->name        = $clean_name;
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

        $dept->save();
        if (!$official_dept) {
            // Find where the parent is
            if ($parent_dept) {
                $parent_dept->addChild($dept);
            } else {
                $root->addChild($dept);
            }
        }

        $sql = "SELECT * FROM telecom_departments WHERE lGroup_id={$obj->lGroup_id} AND iSeqNbr !=0 ORDER BY iSeqNbr DESC;";
        $listings = $db->query($sql);

        if ($listings->num_rows) {
            $k = 0;
            while ($listing = $listings->fetch_object()) {
                $child = new UNL_Officefinder_Department();
                $child->name    = cleanField($listing->szDirLname.' '.$listing->szDirFname.' '.$listing->szDirAddText, false);
                if (trim($listing->sNPA1) !== '') {
                    $child->phone = trim($listing->sNPA1).'-'.preg_replace('/([\d]{3})([\d]{4})/', '$1-$2', trim($listing->sPhoneNbr1));
                }
                $child->address = trim($listing->szAddress);
//                $child->email   = NULL;
//                $child->uid     = NULL;

                $child->save();
                $dept->addChild($child);
            }
        }
        $listings->close();

//        echo PHP_EOL;
    }

    /* free result set */
    $result->close();
}

$db->close();



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
                $official_dept = UNL_Officefinder_Department::getByname($row[1]);
            }
            if (!$official_dept) {
                echo 'I Dunno who this OFFICIAL dept is '.$row[0].'=>'.$row[1].'<br />';
            }
            continue;
        }
        if (!empty($row[2])) {
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
