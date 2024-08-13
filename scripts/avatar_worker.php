<?php

require_once __DIR__ . '/../www/config.inc.php';

$max_life_time = 24 * 60 * 60;
$numberOfJobsBeforeExit = 5;

$SIGINT = 2;
$SIGTERM = 15;
$SIGQUIT = 3;

function shutdown()
{
    // Image helper __destruct will clean up files
    exit;
}

register_shutdown_function('shutdown');
declare(ticks = 1);
pcntl_signal($SIGINT, 'shutdown');
pcntl_signal($SIGTERM, 'shutdown');
pcntl_signal($SIGQUIT, 'shutdown');

function process_avatar($current_job)
{
    if (!file_exists($current_job->file)) {
        throw new Exception('Missing File');
    }

    $user_record = new UNL_PersonInfo_Record($current_job->uid);

    // Create a new image helper
    $image_helper = new UNL_PersonInfo_ImageHelper(
        $current_job->file
    );

    // Crop the image
    $image_helper->crop_image($current_job->square_x, $current_job->square_y, $current_job->square_size, $current_job->square_size);

    // Make many sizes and resolutions of the image
    $image_helper->resize_image(UNL_PersonInfo::$avatar_sizes, UNL_PersonInfo::$avatar_dpi);

    // Save all the versions to these formats
    $image_helper->save_to_formats(UNL_PersonInfo::$avatar_formats);

    // Save those files to the user
    $image_helper->write_to_user($user_record);
}

$start_date = time();
$currentJobCount = 0;
while (true) {
    $current_record = new UNL_PersonInfo_AvatarJob();
    $got_record = $current_record->getFirstQueued();

    if ($got_record !== false) {
        $currentJobCount = $currentJobCount + 1;

        $current_record->status = UNL_PersonInfo_AvatarJob::STATUS_WORKING;
        $current_record->save();

        try {
            process_avatar($current_record);

            if (file_exists($current_record->file)) {
                unlink($current_record->file);
            }

            $current_record->status = UNL_PersonInfo_AvatarJob::STATUS_FINISHED;
            $current_record->file = null;
            $current_record->save();

            // Delete tmp files associated with job
        } catch (Exception $e) {
            $current_record->status = UNL_PersonInfo_AvatarJob::STATUS_ERROR;
            $current_record->error = $e->getMessage();
            $current_record->save();
        }
        sleep(1);
    } else {
        sleep(10);
    }

    // Checks how long loop has been running and will shut it down if its been too long
    if (time() - $start_date > $max_life_time) {
        break;
    }

    // Checks how many jobs the loop has done and will shut it down if its been too many
    if ($numberOfJobsBeforeExit !== -1 && $currentJobCount >= $numberOfJobsBeforeExit) {
        break;
    }
}
