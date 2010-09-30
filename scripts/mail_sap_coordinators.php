<?php

require_once dirname(__FILE__).'/../www/config.inc.php';

set_include_path(get_include_path().PATH_SEPARATOR.'/Users/bbieber/workspace/UNL_WDN_Emailer/src'.PATH_SEPARATOR.'/usr/local/php5/lib/php');

$admin = 'bbieber2';
UNL_Officefinder::setUser($admin);

$users = new UNL_Officefinder_Users();

$email_prefix = <<<PREFIX
Greetings,<br />
<p>
One of the budget reductions approved by APC and the Chancellor is ending the printing and distribution of the UNL Faculty/Staff Directory, which requires improvements to online services to compensate for this change. This email requests your help in ensuring the new online version contains up-to-date information about your department for the 'yellow pages.'
</p><p>
Some of you may recall the process used in the past. Printed listing sheets were mailed out to departments, changes were made if necessary, and those changes were incorporated back into the directory by UNL Telecommunications. That process has now moved into an online environment. All changes for the 'yellow pages' will be made through a web browser and be published immediately in the new online directory. White page/personnel listings will continue to be edited through SAP.
</p><p>
You, and other departmental SAP coordinators, are being asked by receipt of this email to review and edit your departmental 'yellow page' listings.
</p><p>
Human Resources has identified you as the SAP coordinator for the following departmental listings:
</p>
PREFIX;

$email_suffix = <<<SUFFIX
<p>
If this is not correct, please let us know!
</p><p>
The system allows you to grant edit permissions to another UNL user if you would like to delegate this responsibility to others in your department. (See the documentation note at the end of this email).
</p><p>
Right now the online directory contains information from the 2009-2010 printed directory. We would like to give you the opportunity to correct the listings before the online directory goes live, and have set up a <a href="http://peoplefinder-test.unl.edu/">staging server</a> to make edits on. This interface is exactly the same as the current Peoplefinder website, but now supports searching by department names. Please review your listings carefully. It is possible that some listing content may have been lost, out of order, or altered in the data transfer. Use your 2009-2010 UNL Directory as your guide.
</p><p>
You can access the staging server at <a href="http://peoplefinder-test.unl.edu/">http://peoplefinder-test.unl.edu/</a> to make your edits.
</p><p>
We have documented common editing procedures, including how to log in, editing department listings, adjusting the order of listings, adding child listings, department aliases, and other users. The link below will take you to videos which describe each part of the process.
</p><p>
<a href="http://mediahub.unl.edu/channels/117">http://mediahub.unl.edu/channels/117</a>
</p><p>
You will need to log in with your My.UNL username (for example, lgeisler1 or bbieber2) and password, which is the same username and password used to access Blackboard, the UNL Events system, the new ENews/UNL Announce system and many other UNL web resources. If you don't know your username, you can find out what it is by following the instructions on this web page: <a href="https://login.unl.edu/faq/account-initial.shtml">https://login.unl.edu/faq/account-initial.shtml</a>
If you don't know your password, you can reset it yourself here or have the Help Desk reset it for you: <a href="https://login.unl.edu/faq/account-resetpw.shtml">https://login.unl.edu/faq/account-resetpw.shtml</a>
</p><p>
Please have your departmental listings up-to-date by the 15th of October, as the new online directory, with added 'yellow pages' searching, will go live shortly thereafter. After October 15th, all edits will have to be made on the live site, http://peoplefinder.unl.edu/ and the test site will be disabled.
</p><p>
Please feel free to call or email Linda Geisler (lgeisler1@unl.edu) if you have any questions or problems accessing the system and making any edits.
</p>
SUFFIX;

$mailer               = new UNL_WDN_Emailer_Main();


foreach ($users as $user) {
    
    $email_body = $email_prefix;
    $departments = new UNL_Officefinder_User_Departments(array('uid'=>$user->uid));
    $email_body .= '<ul>';
    foreach ($departments as $department) {
        $email_body .= '<li><a href="http://peoplefinder-test.unl.edu/departments/?view=department&amp;id='.$department->id.'">'.$department->name.'</a></li>'.PHP_EOL;
    }
    $email_body .= '</ul>';
    $email_body .= $email_suffix;
    
    
    echo $user->mail;
    $mailer->html_body    = $email_body;
    $mailer->to_address   = 'smeranda2@unl.edu';
    $mailer->from_address = 'Linda Geisler <lgeisler1@unl.edu>';
    $mailer->subject      = 'New UNL Online Directory';
    $mailer->send();
    exit();
}