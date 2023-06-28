<?php

/**
 * @package ProgramManager
 */

namespace Inc\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHandler
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true); // Passing `true` enables exceptions
    }

    public function sendEmail($to, $subject, $body, $from = "brianmunyao6@gmail.com")
    {
        try {
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp-relay.sendinblue.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Port = 587;
            $this->mail->Username = 'brianmunyao6@gmail.com';
            $this->mail->Password = '8Y5bJd0a4Rv1gpUZ';

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

    // $mailHandler  = new MailHandler();
    // $from = 'brianmunyao6@gmail.com';
    // $to = 'brianmunyao6@gmail.com';
    // $subject = 'Subject of the email';
    // $body = 'Content of the email';

    // $result = $mailHandler->sendEmail($from, $to, $subject, $body);
    // if ($result) {
    //     // Return a success response
    //     http_response_code(200);
    //     echo json_encode(['message' => 'Email sent successfully']);
    // } else {
    //     // Return an error response
    //     http_response_code(500);
    //     echo json_encode(['error' => 'Email could not be sent']);
    // }
}
