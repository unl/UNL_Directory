<?php
$db = new mysqli('localhost', 'officefinder', 'officefinder', 'officefinder');

$db->query('TRUNCATE departments');

if ($result = $db->query('SELECT * FROM telecom_departments WHERE sLstTyp=1 AND iSeqNbr=0;')) {
    printf("Select returned %d rows.\n", $result->num_rows);
    $id = 0;

    // Insert the department
    $sql = 'INSERT INTO departments (id, name, org_unit, building, room, city, state, postal_code, address, phone, fax, email, website, acronym, alternate) 
                             VALUES (?,  ?,    ?,        ?,        ?,    ?,     ?,      ?,          ?,         ?,  ?,   ?,      ?,          ?,      ?)';

    $dept_stmt = $db->prepare($sql);

    while($obj = $result->fetch_object()){

        if (preg_match('/\(see (.*)\)/', $obj->szLname.' '.$obj->szFname.' '.$obj->szAddtText)) {
            // known-as listing
            echo 'known as listing!!'.$obj->szLname.' '.$obj->szFname.' '.$obj->szAddtText.PHP_EOL;
            continue;
        }

        $id++;
        // Existing SAP Fields
        $name        = cleanField($obj->szLname.' '.$obj->szFname.' '.$obj->szAddtText);
        $org_unit    = '';
        $building    = '';
        $room        = '';
        $city        = '';
        $state       = '';
        $postal_code = '';

        // Added fields
        $address  = '';
        $phone    = '';
        $fax      = '';
        $email    = '';
        $website  = '';
        $acronym  = '';
        $known_as = '';

        $dept_stmt->bind_param('issssssssssssss', $id, $name, $org_unit, $building, $room, $city, $state, $postal_code, $address, $phone, $fax, $email, $website, $acronym, $known_as);
        $dept_stmt->execute();

        $listings = $db->query("SELECT * FROM telecom_departments WHERE lGroup_id={$obj->lGroup_id} AND sLstTyp !=1 AND iSeqNbr != 0 ORDER BY iSeqNbr;");

        if ($listings->num_rows) {
            while ($listing = $listings->fetch_object()) {
                
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
    $text = str_replace('  ', ' ', trim($text));
    $text = strtolower($text);
    $text = ucwords($text);

    $text = preg_replace('/ Of([\s]|$)/', ' of$1', $text);
    $text = str_replace(' And ', ' & ', $text);
    $text = preg_replace('/ Unl([\s]|$)/', ' UNL$1', $text);
    if (preg_match('/"([a-z])"/', $text, $matches)) {
        $text = str_replace($matches[0], strtoupper($matches[0]), $text);
    }

    if (preg_match('/, (.*)/', $text, $matches)) {
        if ($matches[1] !== 'Inc') {
            $text = $matches[1].' '.str_replace($matches[0], '', $text);
            $text = cleanField($text);
        }
    }

    $text = preg_replace_callback('/\(([a-z])/', function($matches) {return '('.ucfirst($matches[1]);}, $text);
    $text = preg_replace('/\(ec\)/i', '(EC)', $text);
    $text = str_replace('(Apc)', '(APC)', $text);
    echo $text.PHP_EOL;

    return $text;
}