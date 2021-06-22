<?php

require_once dirname(__FILE__).'/../www/config.inc.php';

set_include_path(get_include_path().PATH_SEPARATOR.'/Users/bbieber/workspace/UNL_WDN_Emailer/src'.PATH_SEPARATOR.'/usr/local/php5/lib/php');

$admin = 'bbieber2';
UNL_Officefinder::setUser($admin);

$users = new UNL_Officefinder_Users();

$email_prefix = <<<PREFIX
<p>
This is a reminder to please review all personnel and department listings currently online at <a style="outline: none;color: #ba0000;text-decoration: none;" href="https://directory.unl.edu/">https://directory.unl.edu/</a>. As your department's SAP coordinator, or assigned editor, you have permission to update your department listings (Note: personnel listings found in "Peoplefinder" are edited using SAP). All changes to departmental listings are made through a web browser and published immediately in the online directory.
</p>
<p>
You are listed as an editor for the following departmental listings:
</p>
PREFIX;

$email_suffix = <<<SUFFIX
<p>
Use the following link <a style="outline: none;color: #ba0000;text-decoration: none;" href="https://mediahub.unl.edu/channels/117">https://mediahub.unl.edu/channels/117</a> to access instructional videos on the procedures for logging in, editing listings, creating aliases and adding additional users.
Please feel free to call or email Linda Geisler (472-3713 or <a href="mailto:lgeisler1@unl.edu" style="outline: none;color: #ba0000;text-decoration: none;">lgeisler1@unl.edu</a>) if you have any questions or problems accessing the system to make edits.
</p>
<p>
Thanks for your help in maintaining an accurate online directory.
</p> 
SUFFIX;

$mailer = new UNL_WDN_Emailer_Main();
// Store all the missin users here.
$missing_users = array();
$missing_email = array();
$count = 0;
foreach ($users as $user) {

    if (!is_object($user)) {
        // Failed to retrieve the user, may not exist in peoplefinder anymore?
        $missing_users[] = $user;
        continue;
    }

    $mail = $user->mail;
    if (empty($mail)) {
        // user has no email in peoplefinder
        $missing_email[] = (string)$user->uid;
        continue;
    }

    $count++;
    $email_body = $email_prefix;
    $departments = new UNL_Officefinder_User_Departments(array('uid'=>$user->uid));
    $email_body .= '<ul>';
    foreach ($departments as $department) {
        $email_body .= '<li><a style="outline: none;color: #ba0000;text-decoration: none;" href="https://directory.unl.edu/departments/'.$department->id.'">'.$department->name.'</a></li>'.PHP_EOL;
    }
    $email_body .= '</ul>';
    $email_body .= $email_suffix;
    
    
    echo $user->uid.':'.$mail.PHP_EOL;
//    $mailer->html_body    = $email_body;
////    $mailer->to_address   = $mail;
//    $mailer->to_address   = 'brett.bieber@gmail.com';
//    $mailer->from_address = 'Linda Geisler <lgeisler1@unl.edu>';
//    $mailer->subject      = 'Update of online department and personnel listings';
//    $mailer->send();
//    exit();
}


echo $count . ' users notified'.PHP_EOL;

echo 'Missing users:'.PHP_EOL;
var_export($missing_users);
echo PHP_EOL;

echo 'Missing email addresses:'.PHP_EOL;
var_export($missing_email);
echo PHP_EOL;

