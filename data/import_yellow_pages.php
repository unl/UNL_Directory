<?php
require_once '../www/config.inc.php';
$db = new mysqli('localhost', 'officefinder', 'officefinder', 'officefinder');

$db->query('TRUNCATE departments');
$db->query('TRUNCATE listings');

if ($result = $db->query('SELECT * FROM telecom_departments WHERE sLstTyp=1 AND iSeqNbr=0;')) {
    printf("Select returned %d rows.\n", $result->num_rows);
    $id = 0;

    // Insert the department
    $sql = 'INSERT INTO departments (id, name, org_unit, building, room, city, state, postal_code, address, phone, fax, email, website, acronym, alternate) 
                             VALUES (?,  ?,    ?,        ?,        ?,    ?,     ?,      ?,          ?,         ?,  ?,   ?,      ?,          ?,      ?)';

    $dept_stmt = $db->prepare($sql);

    $sql = 'INSERT INTO listings (department_id, name, phone, sort, address, email, uid)
                          VALUES (?,              ?,     ?,      ?,  ?,      ?,      ?)';
    $listing_stmt = $db->prepare($sql);

    // Official department search
    $dept_search = new SimpleXMLElement(file_get_contents(UNL_Peoplefinder::getDataDir().'/hr_tree.xml'));

    while($obj = $result->fetch_object()){

        if (preg_match('/\(see (.*)\)/', $obj->szLname.' '.$obj->szFname.' '.$obj->szAddtText)) {
            // known-as listing
            //echo 'known as listing!!'.$obj->szLname.' '.$obj->szFname.' '.$obj->szAddtText.PHP_EOL;
            continue;
        }

        $id++;
        // Existing SAP Fields
        $name        = cleanField($obj->szLname.' '.$obj->szFname.' '.$obj->szAddtText);
        $org_unit    = NULL;
        $building    = NULL;
        $room        = NULL;
        $city        = NULL;
        $state       = NULL;
        $postal_code = NULL;
        if (trim($obj->sZipCd5) != '' || trim($obj->sZipCd4) != '') {
            if (trim($obj->sZipCd5) == '') {
                // Assume 68588
                $obj->sZipCd5 = '68588';
            }
            $postal_code = trim($obj->sZipCd5).'-'.trim($obj->sZipCd4);
        }
        $official_dept = false;
        try {
            $official_dept = new UNL_Peoplefinder_Department(array('d'=>$name));
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
        }
        if ($official_dept) {
            $org_unit    = $official_dept->org_unit;
            $building    = $official_dept->building;
            $room        = $official_dept->room;
            $city        = $official_dept->city;
            $state       = $official_dept->state;
            $postal_code = $official_dept->postal_code;
        }

        // Added fields
        $address  = trim($obj->szAddress);
        $phone    = '';
        if (trim($obj->sNPA1) !== '') {
            $phone = trim($obj->sNPA1).'-'.preg_replace('/([\d]{3})([\d]{4})/', '$1-$2', trim($obj->sPhoneNbr1));
        }
        $fax      = NULL;
        $email    = NUll;
        $website  = NULL;
        $acronym  = NULL;
        $known_as = NULL;

        $dept_stmt->bind_param('issssssssssssss', $id, $name, $org_unit, $building, $room, $city, $state, $postal_code, $address, $phone, $fax, $email, $website, $acronym, $known_as);
        $dept_stmt->execute();

        $sql = "SELECT * FROM telecom_departments WHERE lGroup_id={$obj->lGroup_id} AND iSeqNbr !=0 ORDER BY iSeqNbr;";
        $listings = $db->query($sql);

        if ($listings->num_rows) {
            $k = 0;
            while ($listing = $listings->fetch_object()) {
                $k++;
                $l_name    = cleanField($listing->szDirLname.' '.$listing->szDirFname.' '.$listing->szDirAddText);
                $l_phone   = NULL;
                $l_sort    = $k;
                $l_address = NULL;
                $l_email   = NULL;
                $l_uid     = NULL;

                $listing_stmt->bind_param('ississs', $id, $l_name, $l_phone, $l_sort, $l_address, $l_email, $l_uid);
                $listing_stmt->execute();

            }
        }

//        echo PHP_EOL;
    }

    /* free result set */
    $result->close();
}

$db->close();

function cleanField($text)
{
    $text = strtolower($text);
    $text = ucwords($text);

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