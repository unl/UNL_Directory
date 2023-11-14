<?php
require_once dirname(__DIR__) . '/www/config.inc.php';

// Get planet red users
$planet_red_usernames_dump_file_path = "/Users/tneumann9/Downloads/elgg_users_entity.csv";
$planet_red_usernames_dump = file_get_contents($planet_red_usernames_dump_file_path);
if ($planet_red_usernames_dump === false) {
    echo "COULD NOT FIND FILE";
    die();
}
$planet_red_usernames = explode("\n", $planet_red_usernames_dump);

// Set up size map for planet red images and their size equivalents
$size_map = array(
    'master' => array(800, 400, 240),
    'large' => array(200, 120),
    'medium' => array(100, 72, 48),
    'small' => array(40, 24, 16),
);

// Loop through the plant red users
// Checks they are in directory
// Checks if they have an avatar
// Download the avatars and saves it to their user
foreach ($planet_red_usernames as $username) {
    // Make sure they are a UNL person
    $username = str_replace("\"", "", $username);
    if (strpos($username, "unl_") !== 0) {
        continue;
    }
    echo PHP_EOL . $username . PHP_EOL;

    // Creates user record
    $official_username = str_replace("unl_", "", $username);

    //  __   ___   _    ___ ___   _ _____ ___
    //  \ \ / /_\ | |  |_ _|   \ /_\_   _| __|
    //   \ V / _ \| |__ | || |) / _ \| | | _|
    //    \_/_/ \_\____|___|___/_/ \_\_| |___|
    // Check they are in directory now
    $url = 'https://directory.unl.edu/people/' . $official_username;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
    curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT,10);
    curl_exec($ch);
    $http_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_status_code !== 200) {
        echo 'Not In Directory' . PHP_EOL;
        continue;
    }

    // Set up user record
    $user_record = new UNL_PersonInfo_Record($official_username);

    // Check if user has image (We do not want to overwrite images)
    if ($user_record->has_images()) {
        echo 'Already has images' . PHP_EOL;
        continue;
    }

    // Set up users tmp directory
    $user_uniqid = uniqid("import_");
    $tmp_directory = dirname(__DIR__) . "/www/person_images/tmp/" . $user_uniqid;
    if (!file_exists($tmp_directory)) {
        mkdir($tmp_directory);
    }

    //   ___ ___ ___ ___ ___ ___ ___ _____
    //  | _ \ __|   \_ _| _ \ __/ __|_   _|
    //  |   / _|| |) | ||   / _| (__  | |
    //  |_|_\___|___/___|_|_\___\___| |_|
    // Check to see if we redirect to the default image
    $planet_red_url = UNL_Peoplefinder_Record::PLANETRED_BASE_URL .
        'icon/' .
        $username .
        '/master/';
    
    echo $planet_red_url . PHP_EOL;
    $curl_for_redirect = curl_init();
    curl_setopt($curl_for_redirect, CURLOPT_URL, $planet_red_url);
    curl_setopt($curl_for_redirect, CURLOPT_HEADER, true); // true to include the header in the output.
    curl_setopt($curl_for_redirect, CURLOPT_FOLLOWLOCATION, true); // Must be set to true true to follow any "Location: " header that the server sends as part of the HTTP header.
    curl_setopt($curl_for_redirect, CURLOPT_RETURNTRANSFER, true); // true to return the transfer as a string of the return value of curl_exec() instead of outputting it directly.
    $results = curl_exec($curl_for_redirect); // $a will contain all headers
    $finalUrl = curl_getinfo($curl_for_redirect, CURLINFO_EFFECTIVE_URL); // This is what you need, it will return you the last effective URL

    echo $finalUrl . PHP_EOL;
    if (strpos($finalUrl, 'defaultmaster.gif') !== false) {
        echo 'NO GOOD' . PHP_EOL;
        rmdir($tmp_directory);
        continue;
    }

    //    ___  ___ ___ ___ ___ _  _   _   _
    //   / _ \| _ \_ _/ __|_ _| \| | /_\ | |
    //  | (_) |   /| | (_ || || .` |/ _ \| |__
    //   \___/|_|_\___\___|___|_|\_/_/ \_\____|
    // Get master image and save it as the original in all sizes
    $planet_red_url = UNL_Peoplefinder_Record::PLANETRED_BASE_URL .
        'icon/' .
        $username .
        '/master/';
    $tmp_file_name = $tmp_directory . "/original.jpg";

    // copy image from URL to file
    $copy_success = copy($planet_red_url, $tmp_file_name);
    if (!$copy_success) {
        rmdir($tmp_directory);
        continue;
    }
    echo "Saved original" . PHP_EOL;

    // Save all the different versions
    $image_helper = new UNL_PersonInfo_ImageHelper($tmp_file_name);
    $image_helper->resize_image(array(16, 24, 40, 48, 72, 100, 120, 200, 240, 400, 800), array(72, 144));
    $image_helper->save_to_formats(array('JPEG', 'AVIF'));
    $image_helper->write_to_user($user_record);
    unset($image_helper);

    // Delete the tmp file
    unlink($tmp_file_name);

    //    ___ ___  ___  ___ ___ ___ ___
    //   / __| _ \/ _ \| _ \ _ \ __|   \
    //  | (__|   / (_) |  _/  _/ _|| |) |
    //   \___|_|_\\___/|_| |_| |___|___/
    // Loop through the size map so the closes size from planet red will be uses to make the images
    // Planet red doesn't crop the images correctly so this will maximize the amount of correctly cropped images
    foreach ($size_map as $planet_red_size => $sizes) {
        // Set up file and url for the image
        $planet_red_url = UNL_Peoplefinder_Record::PLANETRED_BASE_URL .
            'icon/' .
            $username .
            '/' .
            $planet_red_size .
            '/';
        $tmp_file_name = $tmp_directory . "/" . $planet_red_size . ".jpg";

        // Copy file from url to file
        $copy_success = copy($planet_red_url, $tmp_file_name);
        if (!$copy_success) {
            continue;
        }
        echo "Saved " . $planet_red_size . PHP_EOL;

        // Save all the different "cropped" versions (They might not be cropped correctly)
        $image_helper = new UNL_PersonInfo_ImageHelper($tmp_file_name);
        $image_helper->rename_original('cropped');
        $image_helper->resize_image($sizes, array(72, 144));
        $image_helper->save_to_formats(array('JPEG', 'AVIF'));
        $image_helper->write_to_user($user_record, false);
        unset($image_helper);

        // Delete the tmp file
        unlink($tmp_file_name);
    }

    rmdir($tmp_directory);
}
