<?php

/**
 * @package ProgramManager
 */

namespace Inc\Base;

// Use the PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHandler
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true); // Passing `true` enables exceptions
    }

    public function sendEmail($from, $to, $subject, $body)
    {
        try {
            $this->mail->isSMTP();
            $this->mail->Host = 'sandbox.smtp.mailtrap.io';
            $this->mail->SMTPAuth = true;
            $this->mail->Port = 2525;
            $this->mail->Username = 'e3a5a7f1da470a';
            $this->mail->Password = '74edbc4e4d4648';

            $this->mail->setFrom($from);
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;

            // Send the email
            $this->mail->send();

            return true; // Email sent successfully
        } catch (Exception $e) {
            return false; // Email could not be sent
        }
    }
}
