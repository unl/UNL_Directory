<?php
if (file_exists(dirname(__FILE__).'/www/config.inc.php')) {
    require_once dirname(__FILE__).'/www/config.inc.php';
} else {
    require dirname(__FILE__).'/www/config-sample.inc.php';
}

echo 'Connecting to the database&hellip;';
$mysqli = UNL_Officefinder::getDB();
echo 'connected successfully!<br />'.PHP_EOL;

echo 'Initializing database structure&hellip;';
$result = $mysqli->multi_query(file_get_contents(dirname(__FILE__).'/data/officefinder.sql'));
if (!$result) {
    echo 'There was an error initializing the database.<br />'.PHP_EOL;
    echo $mysqli->error;
    exit();
}

do {
    if ($result = $mysqli->use_result()) {
        $result->close();
    }
} while ($mysqli->next_result());

echo 'initialization complete!<br />'.PHP_EOL;

echo 'Adding academic field to departments...<br />'.PHP_EOL;
$result = $mysqli->query(file_get_contents(__DIR__.'/data/add_academic.sql'));
if (!$result) {
    if (mysqli_errno($mysqli) == 1060) {
        echo 'Field already has been added<br />'.PHP_EOL;
    }
}


echo 'Upgrade complete!'.PHP_EOL;