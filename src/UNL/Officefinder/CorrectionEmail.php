<?php

abstract class UNL_Officefinder_CorrectionEmail
{
	public static $from = ['wdn+directory@unl.edu' => 'Directory'];

	public static $defaultRecipient;

	public static $override;

	public static $logMessages = true;

	public static $disableMail = false;

	public static function send($record, $name, $email, $correction, $source)
	{
        $recipients = self::getRecipients($record);
        if (!$recipients) {
            throw new Exception('Unable to find recipient', 400);
        }

        if (!Swift_Validate::email($email)) {
            throw new Exception('Bad email address provided', 400);
        }

        if (strpos($source, UNL_Peoplefinder::getURL()) !== 0) {
            throw new Exception('Bad correction source provided', 400);
        }

		$message = Swift_Message::newInstance()
            ->setSubject('Directory information correction request')
            ->setFrom(self::$from)
            ->setTo($recipients)
            ->setReplyTo([$email => $name]);

        $editorUrl = UNL_Officefinder::getURL() . 'editor/';
		$now = (new DateTime())->format('F j, Y \a\t g:i A');
		$ip = '';
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
		    $ip = $_SERVER['REMOTE_ADDR'];
        }

        $body = <<<EOD
Name: $name

Email: $email

Correction: $correction

Time: $now\n
EOD;

        if ($ip) {
            $body .= "IP Address: $ip\n";
        }

        $body .= <<<EOD
Correction submitted from: $source

--
You are receiving this because you are a directory editor.
$editorUrl
EOD;

        $message->setBody($body);

        if (self::$logMessages) {
            $mailLog = UNL_Peoplefinder::getTmpDir() . '/mail.log';
            if (!file_exists($mailLog)) {
                touch($mailLog);
            }

            file_put_contents($mailLog, $message . "\n\n", FILE_APPEND);
        }

        if (self::$disableMail) {
            return;
        }

        $transport = Swift_SendmailTransport::newInstance();
        $mailer = Swift_Mailer::newInstance($transport);
        $mailer->send($message);
	}

	protected static function getRecipients($record)
    {
        if (self::$override) {
            return self::$override;
        }

        $recipients = [];
        $editors = $record->getEditors();
        foreach ($editors as $user) {
            $person = $user->getPerson();
            if (!$person || !$person->mail || !Swift_Validate::email($person->mail)) {
                continue;
            }

            $recipients[(string)$person->mail] = (string) $person->displayName;
        }

        if (!$recipients) {
            return self::$defaultRecipient;
        }

        return $recipients;
    }
}
